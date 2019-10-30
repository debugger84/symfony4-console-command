<?php

namespace App\Event\Campaign;

use App\Entity\Campaign;
use App\Entity\Publisher;
use Symfony\Component\EventDispatcher\Event;

class PublisherAddedToBlacklistEvent extends Event
{
    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * PublisherAddedToBlacklistEvent constructor.
     * @param Publisher $publisher
     * @param Campaign $campaign
     */
    public function __construct(Publisher $publisher, Campaign $campaign)
    {
        $this->publisher = $publisher;
        $this->campaign = $campaign;
    }


    /**
     * @return Publisher
     */
    public function getPublisher(): Publisher
    {
        return $this->publisher;
    }

    /**
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }
}
