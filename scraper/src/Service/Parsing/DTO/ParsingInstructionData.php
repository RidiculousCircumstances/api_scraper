<?php

namespace App\Service\Parsing\DTO;

readonly class ParsingInstructionData
{

    public function __construct(
        private RequestData       $requestData,
        private ResponseData $responseData,
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

}