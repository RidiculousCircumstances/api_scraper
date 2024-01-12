<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\PayloadSigner;


use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PayloadTransformerInterface;

/**
 * Должен добавляться последним, после всех трансформаций
 * Подписывает полезную нагрузку запроса для drom
 */
class PayloadSigner implements PayloadTransformerInterface
{

    private string $pattern = '/\s*{{:secret}}\s*/';

    public function __construct(private readonly string $salt)
    {
    }

    public function transform(RequestData $requestData): void
    {
        $parameters = $requestData->getRequestParameters();
        $payloadRef = &$requestData->getCrudePayloadReference();

        foreach ($parameters as $parameter) {
            if (!preg_match($this->pattern, $parameter->getValue())) {
                continue;
            }
            unset($payloadRef[array_search($parameter->getValue(), $payloadRef, true)]);
            $sign = $this->generateSecret($payloadRef);
            $payloadRef[$parameter->getKey()] = $sign;
        }
    }

    private function generateSecret(array $payload): string
    {
        $signer = new SignGenerator($this->salt);

        /**
         *собрать все ключи массива
         */

        foreach ($payload as $key => $value) {
            $signer->parameter($key, $value);
        }

        return $signer->getHash();
    }
}