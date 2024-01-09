<?php

namespace App\Service\ApiScraper\Instruction\DTO;

use App\Message\Parsing\Enum\HttpMethodsEnum;
use Ds\Queue;

/**
 * Основной объект для выполнения парсинга. Содержит последовательную коллекцию связанных запросов
 */
class ScraperInstructionData
{
    /**
     * @var Queue<ParsingSchemaData> $schemasQueue
     */
    private Queue $schemasQueue;


    public function __construct(
        private HttpMethodsEnum $method,
        private string|null     $secret = null,
        private int             $delay = 100,
    )
    {
        $this->secret ??= '';
        $this->schemasQueue = new Queue();
    }

    public function getSchemasQueue(): Queue
    {
        return $this->schemasQueue;
    }

    public function put(ParsingSchemaData $instructionData): void
    {
        $this->schemasQueue[] = $instructionData;
    }

    public function pop(): ParsingSchemaData
    {
        return $this->schemasQueue->pop();
    }

    public function getSecret(): string|null
    {
        return $this->secret;
    }

    public function isEmpty(): bool
    {
        return $this->schemasQueue->isEmpty();
    }

    public function getMethod(): HttpMethodsEnum
    {
        return $this->method;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

}