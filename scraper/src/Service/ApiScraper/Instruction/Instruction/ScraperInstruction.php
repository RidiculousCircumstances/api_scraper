<?php

namespace App\Service\ApiScraper\Instruction\Instruction;

use App\Service\ApiScraper\Instruction\DTO\RequestConfigData;
use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;
use App\Service\ApiScraper\Instruction\Instruction\State\AbstractInstructionState;
use App\Service\ApiScraper\Instruction\Instruction\State\IterableState;
use App\Service\ApiScraper\Instruction\Instruction\State\RepeatOnceState;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use SplDoublyLinkedList;

/**
 * Основной объект для выполнения парсинга. Содержит последовательную коллекцию связанных запросов
 */
class ScraperInstruction implements SuspendableInterface
{
    /**
     * @var SplDoublyLinkedList<ScraperSchemaData> $schemasList
     */
    private SplDoublyLinkedList $schemasList;

    private bool $suspended = false;

    private AbstractInstructionState $state;

    public function __construct(
        private readonly RequestConfigData $requestConfig
    )
    {
        $this->schemasList = new SplDoublyLinkedList();
        $defaultState = new IterableState();
        $defaultState->setInstruction($this);
        $this->state = $defaultState;
    }

    public function push(ScraperSchemaData $instructionData): self
    {
        $this->schemasList->push($instructionData);
        $this->schemasList->rewind();
        return $this;
    }

    public function rewind(): void
    {
        $this->schemasList->rewind();
        $plainState = new IterableState();
        $plainState->setInstruction($this);
        $this->state = $plainState;
    }

    /**
     * Получить схему из очереди(связного списка)). Перемещает курсор к следующей схеме,
     * если очередь не заблокирована
     * @return ScraperSchemaData
     */
    public function extractSchema(): ScraperSchemaData
    {
        return $this->state->extractSchema();
    }

    /**
     * Выполнена ли очередь схем
     * @return bool
     */
    public function isExecuted(): bool
    {
        return $this->state->done();
    }

    /**
     * Является ли очередь инструкций заблокированной
     * @return bool
     */
    public function isSuspended(): bool
    {
        return (bool)$this->suspended;
    }

    /**
     * Установить флаг принудительной блокировки курсора на текущей схеме
     *
     * @param bool $suspended
     */
    public function setSuspended(bool $suspended): void
    {
        $this->suspended = $suspended;
    }

    public function getRequestConfig(): RequestConfigData
    {
        return $this->requestConfig;
    }

    public function getSchemasList(): SplDoublyLinkedList
    {
        return $this->schemasList;
    }

    public function repeatLastSchema(): void
    {
        $repeatOnceState = new RepeatOnceState($this->state);
        $this->transitionTo($repeatOnceState);
    }

    public function transitionTo(AbstractInstructionState $instructionState): void
    {
        $this->state = $instructionState;
        $this->state->setInstruction($this);
    }

}