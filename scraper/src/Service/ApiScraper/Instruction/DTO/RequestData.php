<?php

namespace App\Service\ApiScraper\Instruction\DTO;

use App\Message\Scraper\Enum\HttpMethodsEnum;

class RequestData
{
    public function __construct(
        private string                   $targetUrl,

        private readonly HttpMethodsEnum $httpMethod,

        /**
         * @var array<RequestParameterData> $requestParameters
         */
        private readonly array           $requestParameters,

        private array                    $crudePayload = [],
    )
    {
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function getHttpMethod(): HttpMethodsEnum
    {
        return $this->httpMethod;
    }

    public function getRequestParameters(): array
    {
        return $this->requestParameters;
    }

    public function &getCrudePayloadReference(): array
    {
        return $this->crudePayload;
    }

    public function getCrudePayload(): array
    {
        return $this->crudePayload;
    }

    public function setTargetUrl(string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;
        return $this;
    }

    public function setCrudePayload(array $crudePayload): self
    {
        $this->crudePayload = $crudePayload;
        return $this;
    }

}