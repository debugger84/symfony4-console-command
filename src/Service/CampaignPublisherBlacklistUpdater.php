<?php

namespace App\Service;

use App\Entity\Campaign;
use App\Entity\Embedded\OptimizationProps;
use App\Entity\Enum\EventType;
use App\Entity\Publisher;
use App\Event\Campaign\PublisherAddedToBlacklistEvent;
use App\Event\Campaign\PublisherRemovedFromBlacklistEvent;
use App\Finder\Event\EventCountsFinder;
use App\Repository\PublisherRepository;
use App\Repository\Specification\IdInList;
use Doctrine\ORM\EntityManagerInterface;
use Rb\Specification\Doctrine\Exception\InvalidArgumentException;
use Rb\Specification\Doctrine\Exception\LogicException;
use Rb\Specification\Doctrine\Specification;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CampaignPublisherBlacklistUpdater
{
    /**
     * @var EventCountsFinder
     */
    private $countsFinder;

    /**
     * @var PublisherRepository
     */
    private $publisherRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * CampaignPublisherBlacklistUpdater constructor.
     * @param EventCountsFinder $countsFinder
     * @param PublisherRepository $publisherRepository
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventCountsFinder $countsFinder, PublisherRepository $publisherRepository, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->countsFinder = $countsFinder;
        $this->publisherRepository = $publisherRepository;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }


    /**
     * @param Campaign $campaign
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function updatePublishers(Campaign $campaign)
    {
        $eventTypes = $this->getEventTypes($campaign);

        $counts = $this->countsFinder->findCountsForOneCampaign($eventTypes, $campaign);
        $map = $this->convertCountsToMap($counts, $eventTypes);
        $savedIds = $this->getIdsOfPublishers($campaign);
        $actions = $this->getActionsWithPublishers($savedIds, $map, $campaign->getOptimizationProps());
        $this->addPublishers($actions['add'], $campaign);
        $campaign->removePublishers($actions['delete']);

        //commit all changes
        $this->em->flush();

        $this->fireAddingEvents($actions['add'], $campaign);
        $this->fireRemovingEvents($actions['delete'], $campaign);
    }

    /**
     * @param array $publisherIds
     * @param Campaign $campaign
     * @throws LogicException
     */
    private function fireAddingEvents(array $publisherIds, Campaign $campaign)
    {
        $publishers = $this->getPublishersByIds($publisherIds);
        foreach ($publishers as $publisher) {
            $event = new PublisherAddedToBlacklistEvent($publisher, $campaign);
            $this->dispatcher->dispatch($event);
        }
    }

    /**
     * @param array $publisherIds
     * @param Campaign $campaign
     * @throws LogicException
     */
    private function fireRemovingEvents(array $publisherIds, Campaign $campaign)
    {
        $publishers = $this->getPublishersByIds($publisherIds);
        foreach ($publishers as $publisher) {
            $event = new PublisherRemovedFromBlacklistEvent($publisher, $campaign);
            $this->dispatcher->dispatch($event);
        }
    }

    /**
     * @param array $publisherIds
     * @param Campaign $campaign
     * @throws LogicException
     */
    private function addPublishers(array $publisherIds, Campaign $campaign): void
    {
        if (!$publisherIds) {
            return;
        }
        $publishers = $this->getPublishersByIds($publisherIds);
        foreach ($publishers as $publisher) {
            $campaign->blockPublisher($publisher);
        }
    }

    /**
     * @param array $publisherIds
     * @return Publisher[]
     * @throws LogicException
     */
    private function getPublishersByIds(array $publisherIds)
    {
        $spec = new Specification([
            new IdInList($publisherIds, PublisherRepository::DQL_ALIAS)
        ]);

        return $this->publisherRepository->findAllBySpecification($spec);
    }

    /**
     * @param Campaign $campaign
     * @return array
     */
    private function getIdsOfPublishers(Campaign $campaign): array
    {
        $ids = [];
        foreach ($campaign->getBlockedPublishers() as $publisher) {
            $ids[] = $publisher->getId();
        }

        return $ids;
    }

    /**
     * @param array $idsOfSavedPublishers
     * @param array $map
     * @param OptimizationProps $props
     * @return array [
     *      'delete' => [0,1,2],
     *      'add' => [0,1,2],
     * ]
     */
    private function getActionsWithPublishers(
        array $idsOfSavedPublishers,
        array $map,
        OptimizationProps $props
    ): array {
        $checkedPublishers = array_keys($map);
        $mapOfSaved = array_flip($idsOfSavedPublishers);

        $add = [];
        $delete = [];

        foreach ($checkedPublishers as $publisher) {
            if ($this->checkNeedToAdd($map[$publisher], $props)) {
                if (!isset($mapOfSaved[$publisher])) {
                    $add[] = $publisher;
                }
            } elseif (isset($mapOfSaved[$publisher])) {
                $delete[] = $publisher;
            }
        }

        return [
            'add' => $add,
            'delete' => $delete,
        ];
    }

    private function checkNeedToAdd(array $publisherCount, OptimizationProps $props): bool
    {
        $measuredCount = $publisherCount[$props->getMeasuredEvent()->getValue()];
        $sourceCount = $publisherCount[$props->getSourceEvent()->getValue()];

        if ($sourceCount <= $props->getThreshold()) {
            return false;
        }
        if ($sourceCount == 0) {
            $rate = 100;
        } else {
            $rate = ($measuredCount / $sourceCount) * 100;
        }

        if ($rate < $props->getRatioThreshold()) {
            return true;
        }

        return false;
    }

    /**
     * @param Campaign $campaign
     * @return EventType[]
     */
    private function getEventTypes(Campaign $campaign): array
    {
        $props = $campaign->getOptimizationProps();
        $sourceType = $props->getSourceEvent();
        $measuredType = $props->getMeasuredEvent();
        $eventTypes = [
            $sourceType,
            $measuredType
        ];

        return $eventTypes;
    }

    /**
     * @param array $counts
     * @param EventType[] $eventTypes
     * @return array [0 => ["install" => 1]]
     */
    private function convertCountsToMap(array $counts, array $eventTypes): array
    {
        $map = [];
        foreach ($counts as $item) {
            $type = $item['type'];
            $publisher = $item['publisher'];
            if (!isset($map[$publisher])) {
                $map[$publisher] = $this->initMapItem($eventTypes);
            }
            $map[$publisher][$type] = $item['cnt'];
        }

        return $map;
    }

    /**
     * @param EventType[] $eventTypes
     * @return array ["install" => 1]
     */
    private function initMapItem(array $eventTypes): array
    {
        $result = [];
        foreach ($eventTypes as $eventType) {
            $result[$eventType->getValue()] = 0;
        }

        return $result;
    }
}
