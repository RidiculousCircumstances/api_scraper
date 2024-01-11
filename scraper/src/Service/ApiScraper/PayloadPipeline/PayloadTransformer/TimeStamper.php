<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer;


use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PayloadTransformerInterface;

class TimeStamper implements PayloadTransformerInterface
{
    private string $pattern = '/\s*{{:timestamp}}\s*/';

    public function transform(RequestData $requestData): void
    {
        $parameters = $requestData->getRequestParameters();
        $payload = &$requestData->getCrudePayloadReference();
        foreach ($parameters as $parameter) {
            if (!preg_match($this->pattern, $parameter->getValue())) {
                continue;
            }

            $payload[$parameter->getKey()] = time();
        }
    }
}