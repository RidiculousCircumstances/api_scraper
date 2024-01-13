<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\FieldsRandomizer;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PayloadTransformerInterface;

class FieldsRandomizer implements PayloadTransformerInterface
{

    private static string $needsRandomizePattern = '/{{:random_string=(?<length>\d+)}}/';

    public function transform(RequestData $requestData): void
    {
        $payloadRef = &$requestData->getCrudePayloadReference();

        foreach ($payloadRef as $key => $value) {

            if (!is_string($value)) {
                continue;
            }

            $matches = [];

            if (!preg_match(self::$needsRandomizePattern, $value, $matches)) {
                continue;
            }

            $length = $matches['length'];
            $generatedRandomString = $this->generateString($length);
            $payloadRef[$key] = $generatedRandomString;
        }
    }

    private function generateString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}