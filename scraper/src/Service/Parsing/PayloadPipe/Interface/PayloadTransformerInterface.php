<?php

namespace App\Service\Parsing\PayloadPipe\Interface;

use App\Service\Parsing\Instruction\DTO\RequestParameterData;

interface PayloadTransformerInterface
{

    /**
     * @param array<RequestParameterData> $parameters
     * @param array $payload
     * @return void
     */
    public function transform(array $parameters, array &$payload): void;
}