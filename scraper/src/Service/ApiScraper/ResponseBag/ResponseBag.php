<?php

namespace App\Service\ApiScraper\ResponseBag;

class ResponseBag
{
    private array $registry = [];

    public function add(ResponseRecord $item): static
    {
        $this->registry[] = $item;
        return $this;
    }

    public function get(string $requestId): ResponseRecord|null
    {
        if (!count($this->registry)) {
            return null;
        }
        $record = array_filter($this->registry, static fn(ResponseRecord $record) => $record->getRequestId() === $requestId);
        return $record[0];
    }
}