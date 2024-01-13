<?php

namespace App\Service\ApiScraper\Instruction\DTO;

use App\Message\Parsing\Enum\HttpMethodsEnum;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use SplDoublyLinkedList;

/**
 * Основной объект для выполнения парсинга. Содержит последовательную коллекцию связанных запросов
 */
class ScraperInstructionData implements SuspendableInterface
{
    /**
     * @var SplDoublyLinkedList<ParsingSchemaData> $schemasList
     */
    private SplDoublyLinkedList $schemasList;

    private bool $loopPassed = false;

    private bool $firstTime = true;

    public function __construct(
        private readonly HttpMethodsEnum $method,
        private bool|null                $suspended = null,
        private string|null              $secret = null,
        private readonly int             $delay = 100,
        private string|null              $authToken = null
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

    /**
     * Получить схему из очереди(связного списка)). Перемещает курсор к следующей схеме,
     * если очередь не заблокирована
     * @return ParsingSchemaData
     */
    public function extract(): ParsingSchemaData
    {
        if ($this->loopPassed) {
            $this->loopPassed = false;
        }

        $list = $this->schemasList;

        if (!$this->firstTime && !$this->isSuspended()) {
            $list->next();
        }

        if ($this->firstTime) {
            $this->firstTime = false;
        }

        $current = $list->current();
//
//        if ($this->isSuspended() && !$this->wasSuspended) {
//            $this->wasSuspended = true;
//            $list->prev();
//            $current = $list->current();
//        }

        if (!$list->valid()) {
            $list->rewind();
            $this->loopPassed = true;
        }

        return $current;
    }

    /**
     * Является ли очередь инструкций заблокированной
     * @return bool
     */
    public function isSuspended(): bool
    {
        return (bool)$this->suspended;
    }

    public function getSecret(): string|null
    {
        return $this->secret;
    }

    /**
     * Выполнена ли очередь схем
     * @return bool
     */
    public function executed(): bool
    {
        return !$this->isSuspended() && $this->loopPassed;
    }

    public function getMethod(): HttpMethodsEnum
    {
        return $this->method;
    }

    /**
     * Получить задержку между запросами
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * Установить флаг принудительной блокировки курсора на текущей схеме
     *
     * @param bool $suspended
     */
    public function suspended(bool $suspended): void
    {
        $this->suspended = $suspended;
    }

    public function getAuthToken(): string|null
    {
        return $this->authToken;
    }

}