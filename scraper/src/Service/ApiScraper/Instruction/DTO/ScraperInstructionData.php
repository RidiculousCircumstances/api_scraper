<?php

namespace App\Service\ApiScraper\Instruction\DTO;

use App\Message\Parsing\Enum\HttpMethodsEnum;
use SplDoublyLinkedList;

/**
 * Основной объект для выполнения парсинга. Содержит последовательную коллекцию связанных запросов
 */
class ScraperInstructionData
{
    /**
     * @var SplDoublyLinkedList<ParsingSchemaData> $schemasList
     */
    private SplDoublyLinkedList $schemasList;

    private bool $loopPassed = false;

    public function __construct(
        private readonly HttpMethodsEnum $method,
        private string|null              $secret = null,
        private readonly int             $delay = 100,
    )
    {
        $this->secret ??= '';
        $this->schemasList = new SplDoublyLinkedList();
    }

    public function push(ParsingSchemaData $instructionData): self
    {
        $this->schemasList->push($instructionData);
        $this->schemasList->rewind();
        return $this;
    }

    public function rewind(): void
    {
        $this->schemasList->rewind();
        $this->loopPassed = false;
    }

    public function extract(): ParsingSchemaData
    {
        if ($this->loopPassed) {
            $this->loopPassed = false;
        }

        $list = $this->schemasList;
        $current = $list->current();
        $list->next();

        if (!$list->valid()) {
            $list->rewind();
            $this->loopPassed = true;
        }

        return $current;
    }

    public function getSecret(): string|null
    {
        return $this->secret;
    }

    public function executed(): bool
    {
        return $this->loopPassed;
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