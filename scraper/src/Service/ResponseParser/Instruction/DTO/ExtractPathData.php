<?php

namespace App\Service\ResponseParser\Instruction\DTO;

readonly class ExtractPathData
{
    public function __construct(
        private string $path,
        private string $requestId,
        private string $name
    )
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function getName(): string
    {
        return $this->name;
    }

}