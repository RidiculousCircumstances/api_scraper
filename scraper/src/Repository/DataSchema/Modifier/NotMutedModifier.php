<?php

namespace App\Repository\DataSchema\Modifier;

use App\Repository\Common\Modifier\ModifierInterface;
use App\Repository\DataSchema\DataSchemaRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Фильтрация только активных схем
 */
readonly class NotMutedModifier implements ModifierInterface
{
    public function __construct(private string $alias = DataSchemaRepository::ALIAS)
    {
    }

    public function apply(QueryBuilder $qb): void
    {
        $qb->andWhere($qb->expr()
            ->orX(
                $qb->expr()->isNull($this->alias . '.mute'),
                $qb->expr()->eq($this->alias . '.mute', 'false')
            ));
    }
}