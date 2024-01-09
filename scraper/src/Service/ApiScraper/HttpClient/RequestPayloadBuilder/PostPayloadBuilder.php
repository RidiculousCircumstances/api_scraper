<?php

namespace App\Service\ApiScraper\HttpClient\RequestPayloadBuilder;

class PostPayloadBuilder extends AbstractPayloadBuilder
{

    protected function getPayloadArray(array $payload): array
    {
        return [
            'body' => $payload
        ];
    }
}