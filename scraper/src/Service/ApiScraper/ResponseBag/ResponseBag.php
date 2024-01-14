<?php

namespace App\Service\ApiScraper\ResponseBag;

/**
 * Сохраняет ответы с мета-информацией, позволяющей
 * сопоставить впоследствии каждый ответ с ParsingInstruction
 */
class ResponseBag
{
    /**
     * @var array<ResponseRecord>
     */
    private array $registry = [];

    public function addResponseRecord(ResponseRecord $item): static
    {
        $this->registry[] = $item;
        return $this;
    }

    public function getResponseRecordByRequestId(string $requestId): ResponseRecord|null
    {
        if (!count($this->registry)) {
            return null;
        }
        $record = array_filter($this->registry, static fn(ResponseRecord $record) => $record->getRequestId() === $requestId);
        return $record[0];
    }

    public function getResponseRecords(): array
    {
        return $this->registry;
    }
}