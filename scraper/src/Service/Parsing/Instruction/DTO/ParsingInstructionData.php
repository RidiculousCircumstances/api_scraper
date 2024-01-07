<?php

namespace App\Service\Parsing\Instruction\DTO;

readonly class ParsingInstructionData
{

    private string $fqcn;
    public function __construct(
        private RequestData       $requestData,
        private ResponseData $responseData,

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

}