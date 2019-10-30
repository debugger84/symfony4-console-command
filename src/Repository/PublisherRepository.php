<?php

namespace App\Repository;

use App\Entity\Publisher;
use Doctrine\Common\Persistence\ManagerRegistry;
use Rb\Specification\Doctrine\Specification;

/**
 * Class PublisherRepository
 * @package App\Repository
 * @method Publisher[] findAllBySpecification(Specification $specification, $limit = null, $offset = null)
 * @method Publisher[] findMapBySpecification(Specification $specification, $limit = null, $offset = null)
 * @method Publisher|null findOneBySpecification(Specification $specification)
 */
class PublisherRepository extends BaseRepository
{
    const DQL_ALIAS = 'publisher';
    protected $dqlAlias = self::DQL_ALIAS;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Publisher::class);
    }
}
