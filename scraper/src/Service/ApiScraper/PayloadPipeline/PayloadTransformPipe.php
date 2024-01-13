<?php

namespace App\Service\ApiScraper\PayloadPipeline;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PayloadTransformerInterface;
use Ds\Queue;

final class PayloadTransformPipe
{

    /**
     * @var Queue<PayloadTransformerInterface> $transformerQueue
     */
    private Queue $transformerQueue;

    private RequestData $requestData;

    private ScraperSchemaData $parsingSchemaData;

    public static function payload(ScraperSchemaData $schemaData): self
    {
        $static = new self();
        /** @var ScraperSchemaData $schemaData */
        $schemaData = deep_copy($schemaData, ScraperSchemaData::class);
        $static->parsingSchemaData = $schemaData;
        $static->requestData = $schemaData->getRequestData();
        $static->transformerQueue = new Queue();
        return $static;
    }

    public function with(PayloadTransformerInterface $payloadTransformer): self
    {
        $this->transformerQueue->push($payloadTransformer);
        return $this;
    }

    public function transform(): ScraperSchemaData
    {
        $requestData = $this->requestData;
        $parameters = $requestData->getRequestParameters();
        $payload = [];
        foreach ($parameters as $parameter) {
            if (preg_match('/\[]$/', $parameter->getKey())) {
                $keyMod = str_replace('[]', '', $parameter->getKey());
                $payload[$keyMod][] = $parameter->getValue();
                continue;
            }
            $payload[$parameter->getKey()] = $parameter->getValue();
        }
        $requestData->setCrudePayload($payload);

        while (!$this->transformerQueue->isEmpty()) {
            $transformer = $this->transformerQueue->pop();
            $transformer->transform($requestData);
        }


        return $this->parsingSchemaData->setRequestData($requestData);
    }
}