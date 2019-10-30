<?php

namespace App\Entity;

use App\Entity\Enum\EventType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="event")
 * @ORM\Entity()
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var EventType
     * @ORM\Column(name="event_type", type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @var Campaign
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Campaign")
     * @ORM\JoinColumn(name="campaign_id", nullable=false)
     */
    private $campaign;

    /**
     * @var Publisher
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Publisher")
     * @ORM\JoinColumn(name="publisher_id", nullable=false)
     */
    private $publisher;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return EventType
     */
    public function getType(): EventType
    {
        return new EventType($this->type);
    }

    /**
     * @param EventType $type
     * @return Event
     */
    public function setType(EventType $type): Event
    {
        $this->type = $type->getValue();
        return $this;
    }

    /**
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     * @return Event
     */
    public function setCampaign(Campaign $campaign): Event
    {
        $this->campaign = $campaign;
        return $this;
    }

    /**
     * @return Publisher
     */
    public function getPublisher(): Publisher
    {
        return $this->publisher;
    }

    /**
     * @param Publisher $publisher
     * @return Event
     */
    public function setPublisher(Publisher $publisher): Event
    {
        $this->publisher = $publisher;
        return $this;
    }
}

