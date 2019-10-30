<?php

namespace App\Repository\Specification\Event\Condition;

use App\Entity\Campaign;
use App\Entity\Enum\EventType;
use App\Entity\Event;
use App\Repository\EventRepository;
use Rb\Specification\Doctrine\Condition\Equals;
use Rb\Specification\Doctrine\Condition\In;
use Rb\Specification\Doctrine\Condition\Like;
use Rb\Specification\Doctrine\Exception\InvalidArgumentException;
use Rb\Specification\Doctrine\Query\Select;
use Rb\Specification\Doctrine\Specification;

/**
 * Class EventsOfTypes
 * @package App\Repository\Specification\Event\Condition
 */
class EventsOfCampaign extends Specification
{
    /**
     * EventsOfCampaign constructor.
     * @param Campaign $campaign
     * @param string $dqlAlias
     * @throws InvalidArgumentException
     */
    public function __construct(Campaign $campaign, $dqlAlias = EventRepository::DQL_ALIAS)
    {
        $specs = [
            new Equals('campaign', $campaign, $dqlAlias),
        ];
        parent::__construct($specs);
    }

    public function isSatisfiedBy($value)
    {
        return $value === Event::class;
    }
}
