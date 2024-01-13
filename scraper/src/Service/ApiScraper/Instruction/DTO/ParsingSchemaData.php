<?php

namespace App\Service\ApiScraper\Instruction\DTO;

class ParsingSchemaData
{

    private string $fqcn;

    public function __construct(
        private RequestData  $requestData,
        private ResponseData $responseData,
        private bool|null    $needsAuth
    )
    {
        $this->fqcn = get_class($this) . '_' . spl_object_id($this);
    }

    public function getRequestData(): RequestData
    {
        return $this->requestData;
    }

    public function getResponseData(): ResponseData
    {
        return $this->responseData;
    }

    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    public function isNeedsAuth(): bool
    {
        return (bool)$this->needsAuth;
    }

}