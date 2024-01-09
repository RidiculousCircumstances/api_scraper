<?php

namespace App\Service\ApiScraper\Instruction\DTO;

use App\Message\Parsing\Enum\HttpMethodsEnum;

readonly class RequestData
{
    public function __construct(
        private string          $targetUrl,

        private HttpMethodsEnum $httpMethod,

        /**
         * @var array<RequestParameterData> $requestParameters
         */
        private array           $requestParameters,
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

}