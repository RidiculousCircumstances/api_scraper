<?php

namespace App\Service\Parsing\ResponseRegistry;

readonly class ResponseRecord
{
    public function __construct(
        private string $requestId,

        private array  $content,
    )
    {
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function getContent(): array
    {
        return $this->content;
    }

}