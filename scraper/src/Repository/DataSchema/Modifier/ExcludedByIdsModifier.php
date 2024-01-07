<?php

namespace App\Repository\DataSchema\Modifier;

use App\Repository\Common\Modifier\ModifierInterface;
use App\Repository\DataSchema\DataSchemaRepository;
use Doctrine\ORM\QueryBuilder;

class ExcludedByIdsModifier implements ModifierInterface
{

    public function __construct(private array $ids) {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere($queryBuilder->expr()->notIn(DataSchemaRepository::ALIAS . '.id', $this->ids));
    }
}