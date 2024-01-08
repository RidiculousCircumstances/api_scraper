<?php

namespace App\Service\Parsing\PayloadPipe;

use App\Service\Parsing\Instruction\DTO\RequestData;
use App\Service\Parsing\PayloadPipe\Interface\PayloadTransformerInterface;
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

    public function add(PayloadTransformerInterface $payloadTransformer): self
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