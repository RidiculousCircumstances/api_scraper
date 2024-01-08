<?php

namespace App\Service\Parsing\Client\RequestPayloadBuilder;

class GetPayloadBuilder extends AbstractPayloadBuilder
{

    protected function getPayloadArray(array $payload): array
    {
        return [
            'query' => $payload
        ];
    }
}