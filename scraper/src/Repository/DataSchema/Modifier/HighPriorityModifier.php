<?php

namespace App\Repository\DataSchema\Modifier;

use App\Repository\Common\Modifier\ModifierInterface;
use App\Repository\DataSchema\DataSchemaRepository;
use Doctrine\ORM\QueryBuilder;


readonly class HighPriorityModifier implements ModifierInterface
{
    public function __construct(private string $alias = DataSchemaRepository::ALIAS)
    {
    }

    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->orderBy($this->alias . '.executionOrder', 'asc')->setMaxResults(1);
    }
}