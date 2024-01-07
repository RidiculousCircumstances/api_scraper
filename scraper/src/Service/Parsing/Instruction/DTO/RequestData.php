<?php

namespace App\Service\Parsing\Instruction\DTO;

readonly class RequestData
{
    public function __construct(
        private string $targetUrl,

        private string $httpMethod,

        /**
         * @var array<RequestParameterData> $requestParameters
         */
        private array $requestParameters,


    ) {}

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getRequestParameters(): array
    {
        return $this->requestParameters;
    }


}