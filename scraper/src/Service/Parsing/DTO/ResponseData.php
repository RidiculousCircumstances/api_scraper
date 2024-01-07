<?php

namespace App\Service\Parsing\DTO;

class ResponseData
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