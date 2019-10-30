<?php

namespace App\Repository\Specification\Event\Selection;

use App\Entity\Event;
use App\Repository\EventRepository;
use Rb\Specification\Doctrine\Query\GroupBy;
use Rb\Specification\Doctrine\Query\Select;
use Rb\Specification\Doctrine\Specification;

/**
 * Class SelectCountsPerType
 * @package App\Repository\Specification\Event\Selection
 */
class SelectCountsPerTypeForCampaignOfPublisher extends Specification
{
    /**
     * SelectCountsPerType constructor.
     * @param string $dqlAlias
     */
    public function __construct(string $dqlAlias = EventRepository::DQL_ALIAS)
    {
        $add = Select::SELECT;

        $specs = [
            new Select([
                $dqlAlias . '.type',
                'IDENTITY(' .$dqlAlias . '.campaign) as campaign',
                'IDENTITY(' .$dqlAlias . '.publisher) as publisher',
                'count(1) as cnt',
                ],
                $add),
            new GroupBy('type', GroupBy::GROUP_BY, $dqlAlias),
            new GroupBy('campaign', GroupBy::ADD_GROUP_BY, $dqlAlias),
            new GroupBy('publisher', GroupBy::ADD_GROUP_BY, $dqlAlias),
        ];
        parent::__construct($specs);
    }

    public function isSatisfiedBy($value)
    {
        return $value === Event::class;
    }
}
