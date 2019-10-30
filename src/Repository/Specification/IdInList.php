<?php

namespace App\Repository\Specification;

use Rb\Specification\Doctrine\Condition\In;
use Rb\Specification\Doctrine\Specification;

/**
 * Class IdInList
 * @package App\Repository\Specification
 */
class IdInList extends Specification
{
    /**
     * IdInList constructor.
     * @param array $ids
     * @param string $dqlAlias
     */
    public function __construct(array $ids, string $dqlAlias)
    {
        $specs = [
            new In('id', $ids, $dqlAlias),
        ];
        parent::__construct($specs);
    }

    public function isSatisfiedBy($value)
    {
        return true;
    }
}
