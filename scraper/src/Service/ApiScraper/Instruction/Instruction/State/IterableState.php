<?php

namespace App\Service\ApiScraper\Instruction\Instruction\State;

use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;

class IterableState extends AbstractInstructionState
{

    private bool $loopPassed = false;

    private bool $firstTime = true;

    public function extractSchema(): ScraperSchemaData
    {

        if ($this->instruction->isSuspended()) {
            return $this->delegateTo(new SuspendedState());
        }

        if ($this->loopPassed) {
            $this->loopPassed = false;
        }

        $list = $this->instruction->getSchemasList();


        if (!$this->firstTime) {
            $list->next();
        }

        if ($this->firstTime) {
            $this->firstTime = false;
        }

        $current = $list->current();

        if (!$list->valid()) {
            $list->rewind();
            $this->loopPassed = true;
        }

        return deep_copy(($current), ScraperSchemaData::class);

    }

    public function done(): bool
    {
        return $this->loopPassed;
    }
}