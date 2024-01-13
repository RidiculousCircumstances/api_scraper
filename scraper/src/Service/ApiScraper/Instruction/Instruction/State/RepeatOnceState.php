<?php

namespace App\Service\ApiScraper\Instruction\Instruction\State;

use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;

/**
 * Принудительное повторение схемы инструкции ровно один раз
 */
class RepeatOnceState extends AbstractInstructionState
{

    public function __construct(private readonly AbstractInstructionState $previousState)
    {

    }

    /**
     * Из этого состояния нельзя прервать цикл
     * @return bool
     */
    public function done(): bool
    {
        return false;
    }

    public function extractSchema(): ScraperSchemaData
    {
        $current = $this->instruction->getSchemasList()->current();

        $this->instruction->transitionTo($this->previousState);

        return $current;
    }
}