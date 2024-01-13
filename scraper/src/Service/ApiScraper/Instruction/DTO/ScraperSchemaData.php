<?php

namespace App\Service\ApiScraper\Instruction\DTO;

class ScraperSchemaData
{

    public function __construct(
        private RequestData     $requestData,
        private ResponseData    $responseData,
        private bool|null       $needsAuth,
        private readonly string $fqcn
    )
    {
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

    public function setRequestData(RequestData $requestData): self
    {
        $this->requestData = $requestData;
        return $this;
    }


}