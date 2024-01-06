<?php

namespace App\Repository\ResponseField\Modifier;

use App\Entity\GroupTag;
use App\Repository\Common\Modifier\ModifierInterface;
use App\Repository\ResponseField\ResponseFieldRepository;
use Doctrine\ORM\QueryBuilder;

readonly class GroupModifier implements ModifierInterface
{

    public function __construct(private GroupTag $groupTag, private string $alias = ResponseFieldRepository::ALIAS) {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->join($this->alias . '.dataSchema', 'ds')
            ->andWhere('ds.groupTag = :groupTagId')
            ->setParameters([
                'groupTagId' => $this->groupTag->getId()
            ]);
    }
}