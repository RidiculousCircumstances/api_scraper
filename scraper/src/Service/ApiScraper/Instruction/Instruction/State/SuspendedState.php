<?php

namespace App\Service\ApiScraper\Instruction\Instruction\State;

use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;

class SuspendedState extends AbstractInstructionState
{

    public function extractSchema(): ScraperSchemaData
    {

        if (!$this->instruction->isSuspended()) {
            return $this->delegateTo(new IterableState());
        }

        $list = $this->instruction->getSchemasList();

        $current = $list->current();

        return deep_copy($current, ScraperSchemaData::class);
    }

    public function done(): bool
    {
        return !$this->instruction->isSuspended();
    }
}