<?php

namespace App\Repository\Common\Modifier;

use Doctrine\ORM\QueryBuilder;

class ModifierManager
{
    /**
     * @var array<ModifierInterface> $modifiers
     */
    private array $modifiers;

    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    public function add(ModifierInterface $modifier): self
    {
        $this->modifiers[] = $modifier;
        return $this;
    }

    public function apply(QueryBuilder $queryBuilder): void
    {
        foreach ($this->modifiers as $modifier) {
            $modifier->apply($queryBuilder);
        }
    }
}