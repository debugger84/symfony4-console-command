<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="publisher_blacklist",uniqueConstraints={
 *         @UniqueConstraint(name="publisher_blacklist_unique", columns={"campaign_id", "publisher_id"})
 *     })
 * @ORM\Entity()
 */
class PublisherBlackListItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Campaign|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Campaign", inversedBy="publisherBlacklist")
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
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     * @return PublisherBlackListItem
     */
    public function setCampaign(?Campaign $campaign): PublisherBlackListItem
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
     * @return PublisherBlackListItem
     */
    public function setPublisher(Publisher $publisher): PublisherBlackListItem
    {
        $this->publisher = $publisher;
        return $this;
    }
}

