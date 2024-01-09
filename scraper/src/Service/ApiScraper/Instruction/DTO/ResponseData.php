<?php

namespace App\Service\ApiScraper\Instruction\DTO;

readonly class ResponseData
{
    public function __construct(
        private array $responseFields
    )
    {
    }

    public function getResponseFields(): array
    {
        return $this->responseFields;
    }

}