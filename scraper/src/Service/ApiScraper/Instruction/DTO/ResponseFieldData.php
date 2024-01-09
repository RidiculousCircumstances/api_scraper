<?php

namespace App\Service\ApiScraper\Instruction\DTO;

readonly class ResponseFieldData
{

    public function __construct(
        private string $responsePath,
        private string $outputName,
    )
    {
    }

    public function getResponsePath(): string
    {
        return $this->responsePath;
    }

    public function getOutputName(): string
    {
        return $this->outputName;
    }

}