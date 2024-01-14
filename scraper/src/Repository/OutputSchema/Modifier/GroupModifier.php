<?php

namespace App\Repository\OutputSchema\Modifier;

use App\Entity\GroupTag;
use App\Repository\Common\Modifier\ModifierInterface;
use App\Repository\OutputSchema\OutputSchemaRepository;
use Doctrine\ORM\QueryBuilder;

readonly class GroupModifier implements ModifierInterface
{

    public function __construct(private GroupTag $groupTag, private string $alias = OutputSchemaRepository::ALIAS)
    {
    }

    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->where('outputSchema.groupTag = :groupTagId')
            ->setParameters([
                'groupTagId' => $this->groupTag->getId()
            ]);
    }
}