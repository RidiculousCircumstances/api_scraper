<?php

namespace App\Service\Parsing\Instruction\DTO;

use App\Message\Parsing\Enum\HttpMethodsEnum;

readonly class ParsingInstructionData
{

    private string $fqcn;

    public function __construct(
        private RequestData     $requestData,
        private ResponseData    $responseData,
        private string          $secret,
        private HttpMethodsEnum $method,

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

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getMethod(): HttpMethodsEnum
    {
        return $this->method;
    }


}