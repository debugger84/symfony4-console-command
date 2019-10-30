<?php

namespace App\Entity;

use App\Entity\Embedded\OptimizationProps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="campaign")
 * @ORM\Entity()
 */
class Campaign
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var OptimizationProps
     * @ORM\Embedded(class="App\Entity\Embedded\OptimizationProps", columnPrefix=false)
     */
    private $optimizationProps;

    /**
     * @var PublisherBlackListItem[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="PublisherBlackListItem",
     *     mappedBy="campaign",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     */
    private $publisherBlacklist;

    /**
     * Campaign constructor.
     */
    public function __construct()
    {
        $this->publisherBlacklist = new ArrayCollection();
    }

    /**
     * @return OptimizationProps
     */
    public function getOptimizationProps(): OptimizationProps
    {
        return $this->optimizationProps;
    }

    /**
     * @param OptimizationProps $optimizationProps
     * @return Campaign
     */
    public function setOptimizationProps(OptimizationProps $optimizationProps): Campaign
    {
        $this->optimizationProps = $optimizationProps;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Publisher[]
     */
    public function getBlockedPublishers(): array
    {
        $publishers = [];
        foreach ($this->publisherBlacklist as $item) {
            $publishers[] = $item->getPublisher();
        }

        return $publishers;
    }

    public function blockPublisher(Publisher $publisher): self
    {
        $blackListItem = new PublisherBlackListItem();
        $blackListItem->setCampaign($this)
            ->setPublisher($publisher)
        ;
        $this->publisherBlacklist->add($blackListItem);

        return $this;
    }

    public function removePublishers(array $publisherIds): array
    {
        if (!$publisherIds) {
            return [];
        }
        $idsMap = array_flip($publisherIds);
        $removedElements = [];
        foreach ($this->publisherBlacklist as $key => $item) {
            $publisher = $item->getPublisher();
            if(isset($idsMap[$publisher->getId()])) {
                $removedElements[] = $this->publisherBlacklist->remove($key);
            }
        }

        return $removedElements;
    }
}

