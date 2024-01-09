<?php

namespace App\Service\ApiScraper\PayloadPipe;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipe\Interface\PayloadTransformerInterface;
use Ds\Queue;

final class PayloadTransformPipe
{

    /**
     * @var Queue<PayloadTransformerInterface> $transformerQueue
     */
    private Queue $transformerQueue;

    private RequestData $requestData;

    public static function payload(RequestData $requestData): self
    {
        $static = new self();
        $static->requestData = $requestData;
        $static->transformerQueue = new Queue();
        return $static;
    }

    public function with(PayloadTransformerInterface $payloadTransformer): self
    {
        $this->transformerQueue->push($payloadTransformer);
        return $this;
    }

    public function exec(): array
    {
        $payload = [];
        $parameters = $this->requestData->getRequestParameters();

        foreach ($parameters as $parameter) {
            if (preg_match('/\[]$/', $parameter->getKey())) {
                $keyMod = str_replace('[]', '', $parameter->getKey());
                $payload[$keyMod][] = $parameter->getValue();
                continue;
            }
            $payload[$parameter->getKey()] = $parameter->getValue();
        }

        while (!$this->transformerQueue->isEmpty()) {
            $transformer = $this->transformerQueue->pop();
            $transformer->transform($parameters, $payload);
        }

        return $payload;
    }
}