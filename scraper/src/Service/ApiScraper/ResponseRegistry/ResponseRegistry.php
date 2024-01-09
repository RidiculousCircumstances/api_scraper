<?php

namespace App\Service\ApiScraper\ResponseRegistry;

class ResponseRegistry
{
    private array $registry;

    public function add(ResponseRecord $item): static
    {
        $this->registry[$item->getRequestId()] = $item;
        return $this;
    }

    public function get(string $responseId): ResponseRecord
    {
        return $this->registry[$responseId];
    }
}