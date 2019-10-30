<?php

namespace App\Repository;

use App\Entity\Campaign;
use Doctrine\Common\Persistence\ManagerRegistry;
use Rb\Specification\Doctrine\Specification;

/**
 * Class CampaignRepository
 * @package App\Repository
 * @method Campaign[] findAllBySpecification(Specification $specification, $limit = null, $offset = null)
 */
class CampaignRepository extends BaseRepository
{
    const DQL_ALIAS = 'campaign';
    protected $dqlAlias = self::DQL_ALIAS;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Campaign::class);
    }
}
