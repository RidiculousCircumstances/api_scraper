<?php

namespace App\Service\ApiScraper\Instruction\Instruction\State;

use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;
use App\Service\ApiScraper\Instruction\Instruction\ScraperInstruction;

/**
 * Определяет поведение извлечения схем из инструкции
 */
abstract class AbstractInstructionState
{

    protected ScraperInstruction $instruction;

    /**
     * Должен ли быть выход из итерации по схемам (если все состояния возвращают true - терминация текущей цепочки запросов)
     * @return bool
     */
    abstract public function done(): bool;

    protected function delegateTo(self $state): ScraperSchemaData
    {
        $state->setInstruction($this->instruction);
        $this->instruction->transitionTo($state);
        return $this->instruction->extractSchema();
    }

    public function setInstruction(ScraperInstruction $instruction): void
    {
        $this->instruction = $instruction;
    }

    abstract public function extractSchema(): ScraperSchemaData;

}