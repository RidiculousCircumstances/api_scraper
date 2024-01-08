<?php

namespace App\Service\Parsing\Instruction\DTO;

use Ds\Queue;

/**
 * Основной объект для выполнения парсинга. Содержит последовательную коллекцию связанных запросов
 */
class ParsingInstructionQueueData
{
    /**
     * @var Queue<ParsingInstructionData> $queue
     */
    private Queue $queue;

    public function __construct()
    {
        $this->queue = new Queue();
    }

    public function getQueue(): Queue
    {
        return $this->queue;
    }

    public function put(ParsingInstructionData $instructionData): void
    {
        $this->queue[] = $instructionData;
    }

    public function pop(): ParsingInstructionData
    {
        return $this->queue->pop();
    }

    public function getSecret(): string|null
    {
        return $this->secret;
    }

    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }
}