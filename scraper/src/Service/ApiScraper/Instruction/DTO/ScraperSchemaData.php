<?php

namespace App\Service\ApiScraper\Instruction\DTO;

/**
 * Основной компонент инструкции. Описывает поля запроса и ответа
 */
class ScraperSchemaData
{

    public function __construct(
        private RequestData     $requestData,
        private ResponseData    $responseData,
        private bool|null       $needsAuth,
        private readonly string $fqcn,
        private int|null        $executionOrder
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

    public function getExecutionOrder(): int|null
    {
        return $this->executionOrder;
    }

}