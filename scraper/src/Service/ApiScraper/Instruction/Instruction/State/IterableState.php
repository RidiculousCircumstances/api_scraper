<?php

namespace App\Service\ApiScraper\Instruction\Instruction\State;

use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;

/**
 * Обычное состояние очереди инструкций. каждый вызов transform
 * приводит к смещению курсора. По выполнению всех инструкций сбрасывает
 * курсор в исходное состояние, оповещает контекст
 */
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

        if (!$list->valid() || $list->count() === 1) {
            $list->rewind();
            $this->loopPassed = true;
        }

        if ($this->firstTime) {
            $this->firstTime = false;
        }

        $current = $list->current();

        return deep_copy(($current), ScraperSchemaData::class);

    }

    public function done(): bool
    {
        return $this->loopPassed;
    }
}