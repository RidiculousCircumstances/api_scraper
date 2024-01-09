<?php

namespace App\Service\ApiScraper\PayloadPipe\Interface;

use App\Service\ApiScraper\Instruction\DTO\RequestParameterData;

interface PayloadTransformerInterface
{

    /**
     * @param array<RequestParameterData> $parameters
     * @param array $payload
     * @return void
     */
    public function transform(array $parameters, array &$payload): void;
}