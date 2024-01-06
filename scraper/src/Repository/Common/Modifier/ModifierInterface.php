<?php

namespace App\Repository\Common\Modifier;

use Doctrine\ORM\QueryBuilder;

interface ModifierInterface
{
    public function apply(QueryBuilder $queryBuilder);
}