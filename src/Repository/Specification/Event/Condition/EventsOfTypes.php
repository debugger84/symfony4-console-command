<?php

namespace App\Repository\Specification\Event\Condition;

use App\Entity\Campaign;
use App\Entity\Enum\EventType;
use App\Entity\Event;
use App\Repository\EventRepository;
use Rb\Specification\Doctrine\Condition\In;
use Rb\Specification\Doctrine\Condition\Like;
use Rb\Specification\Doctrine\Exception\InvalidArgumentException;
use Rb\Specification\Doctrine\Query\Select;
use Rb\Specification\Doctrine\Specification;

/**
 * Class EventsOfTypes
 * @package App\Repository\Specification\Event\Condition
 */
class EventsOfTypes extends Specification
{
    /**
     * EventsOfTypes constructor.
     * @param EventType[] $eventTypes
     * @param string $dqlAlias
     * @throws InvalidArgumentException
     */
    public function __construct(array $eventTypes, $dqlAlias = EventRepository::DQL_ALIAS)
    {
        $types = [];
        if (!$eventTypes) {
            throw new InvalidArgumentException('Event types array should not be empty');
        }
        foreach ($eventTypes as $type) {
            $types[] = $type->getValue();
        }
        $specs = [
            new In('type', $types, $dqlAlias),
        ];
        parent::__construct($specs);
    }

    public function isSatisfiedBy($value)
    {
        return $value === Event::class;
    }
}
