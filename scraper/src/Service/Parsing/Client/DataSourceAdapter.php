<?php

namespace App\Service\Parsing\Client;

use App\Service\Parsing\Client\Interface\DataSourceInterface;
use App\Service\Parsing\Instruction\DTO\ParsingInstructionData;
use App\Service\Parsing\Instruction\DTO\RequestData;
use App\Service\Parsing\ResponseRegistry\ResponseRegistry;

final readonly class DataSourceAdapter implements DataSourceInterface
{

    private RequestData $requestData;

    public function __construct(ParsingInstructionData $data, private ResponseRegistry $registry)
    {
        $this->requestData = $data->getRequestData();
    }

    public function getUrl(): string
    {
        return $this->requestData->getTargetUrl();
    }

    public function getBody(): array
    {
        $params = $this->requestData->getRequestParameters();
        $paramsArray = [];

        foreach ($params as $param) {
            $value = $param->getValue();

            if ($param->getExternalSourceId() !== 0) {
                $path = $value;
                $responseRecord = $this->registry->get($param->getExternalSourceId());

                $content = $responseRecord->getContent();

                $value = m($content)(get_by_dot_keys($path))();

            }

            $paramsArray[$param->getKey()] = $value;
        }

        return $paramsArray;
    }

    public function getMethod(): string
    {
        return $this->requestData->getHttpMethod();
    }

    public function getProxy(): array
    {
        return [];
    }
}