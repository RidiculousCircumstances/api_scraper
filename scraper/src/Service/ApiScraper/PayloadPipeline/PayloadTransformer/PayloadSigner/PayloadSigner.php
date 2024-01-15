<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\PayloadSigner;


use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PipeHandlerInterface;

/**
 *
 * Подписывает полезную нагрузку запроса для drom
 * Должен добавляться последним, после всех трансформаций
 *
 */
class PayloadSigner implements PipeHandlerInterface
{

    private static string $secretPattern = '/\s*{{:secret}}\s*/';
    private static string $urlParameterPattern = '/\s*(?<full>{{:url_parameter=(?<id>[a-z0-9]+)}})\s*/';

    public function __construct(private readonly string $salt)
    {
    }

    public function transform(RequestData $requestData): void
    {
        $parameters = $requestData->getRequestParameters();
        $payloadRef = &$requestData->getCrudePayloadReference();
        $cleanupList = [];

        /**
         * Учитываем параметры из url сегментов
         */
        foreach ($payloadRef as $key => $value) {
            $matches = [];
            if (!preg_match(self::$urlParameterPattern, $key, $matches)) {
                continue;
            }

            unset($payloadRef[$key]);

            $payloadRef[$matches['id']] = $value;
            $cleanupList[] = $matches['id'];

        }

        /**
         * Генерируем подпись
         */
        foreach ($parameters as $parameter) {
            if (!preg_match(self::$secretPattern, $parameter->getValue())) {
                continue;
            }
            /**
             * Временно удаляем поле с плейсхолдером подписи
             */
            unset($payloadRef[array_search($parameter->getValue(), $payloadRef, true)]);

            $sign = $this->generateSecret($payloadRef);
            $payloadRef[$parameter->getKey()] = $sign;
        }

        foreach ($cleanupList as $item) {
            unset($payloadRef[$item]);
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