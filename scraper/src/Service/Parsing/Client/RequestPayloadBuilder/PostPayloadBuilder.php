<?php

namespace App\Service\Parsing\Client\RequestPayloadBuilder;

class PostPayloadBuilder extends AbstractPayloadBuilder
{

    protected function getPayloadArray(array $payload): array
    {
        return [
            'body' => $payload
        ];
    }
}