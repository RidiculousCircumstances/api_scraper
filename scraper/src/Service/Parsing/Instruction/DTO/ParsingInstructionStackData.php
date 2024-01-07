<?php

namespace App\Service\Parsing\Instruction\DTO;

use Ds\Stack;

/**
 * Основной объект для выполнения парсинга. Содержит последовательную коллекцию связанных запросов
 */
class ParsingInstructionStackData
{
    /**
     * @var Stack<ParsingInstructionData> $queue
     */
    private Stack $stack;

    public function __construct()
    {
        $this->stack = new Stack();
    }

    public function getStack(): Stack
    {
        return $this->stack;
    }

    public function put(ParsingInstructionData $instructionData): void
    {
        $this->stack[] = $instructionData;
    }

    public function pop(): ParsingInstructionData|null
    {

        if ($this->stack->isEmpty()) {
            return null;
        }

        return $this->stack->pop();
    }

}