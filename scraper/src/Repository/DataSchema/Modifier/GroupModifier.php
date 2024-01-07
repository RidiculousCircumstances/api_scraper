<?php

namespace App\Repository\DataSchema\Modifier;

use App\Entity\GroupTag;
use App\Repository\Common\Modifier\ModifierInterface;
use App\Repository\DataSchema\DataSchemaRepository;
use Doctrine\ORM\QueryBuilder;

readonly class GroupModifier implements ModifierInterface
{
    public function __construct(private GroupTag $groupTag, private string $alias = DataSchemaRepository::ALIAS) {}
    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere($this->alias . '.groupTag = :groupTagId')
        ->setParameters([
            'groupTagId' => $this->groupTag->getId(),
        ]);
    }
}