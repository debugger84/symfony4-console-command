<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Common\Persistence\ManagerRegistry;

class EventRepository extends BaseRepository
{
    const DQL_ALIAS = 'event';
    protected $dqlAlias = self::DQL_ALIAS;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Event::class);
    }
}
