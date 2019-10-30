<?php

namespace App\Finder\Event;

use App\Entity\Campaign;
use App\Entity\Enum\EventType;
use App\Repository\EventRepository;
use App\Repository\Specification\Event\Condition\EventsOfCampaign;
use App\Repository\Specification\Event\Condition\EventsOfTypes;
use App\Repository\Specification\Event\Selection\SelectCountsPerTypeForCampaignOfPublisher;
use Rb\Specification\Doctrine\Exception\InvalidArgumentException;
use Rb\Specification\Doctrine\Exception\LogicException;
use Rb\Specification\Doctrine\Specification;

class EventCountsFinder
{
    /**
     * @var EventRepository
     */
    private $cityRepository;

    /**
     * BaseListFinder constructor.
     * @param EventRepository $cityRepository
     */
    public function __construct(EventRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param EventType[] $eventTypes
     * @param Campaign $campaign
     * @return array [[
     *    "type" => "install",
     *    "cnt"=> 1,
     *    "publisher" => 1,
     * ]]
     * @throws InvalidArgumentException
     * @throws LogicException
     * @psalm-type CityBaseDataWithRegion=array{type:string,cnt:int,campaign:int}
     * @psalm-return CityBaseDataWithRegion
     */
    public function findCountsForOneCampaign(array $eventTypes, Campaign $campaign): array
    {
        $spec = $this->getSpecification($eventTypes);
        $spec->add(new EventsOfCampaign($campaign));

        return $this->cityRepository->findArrayResultBySpecification($spec);
    }

    /**
     * @param EventType[] $eventTypes
     * @return Specification
     * @throws InvalidArgumentException
     */
    private function getSpecification(array $eventTypes): Specification
    {
        $spec = new Specification([
            new SelectCountsPerTypeForCampaignOfPublisher(),
            new EventsOfTypes($eventTypes),
        ]);

        return $spec;
    }
}
