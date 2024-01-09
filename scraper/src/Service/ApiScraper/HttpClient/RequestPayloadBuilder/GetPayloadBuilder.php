<?php

namespace App\Service\ApiScraper\HttpClient\RequestPayloadBuilder;

class GetPayloadBuilder extends AbstractPayloadBuilder
{

    protected function getPayloadArray(array $payload): array
    {
        $query = '';
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subValue) {
                    $query .= $key . '%5B%5D' . '=' . $subValue . '&';
                }
                continue;
            }
            $query .= $key . '=' . $value . '&';
        }
        $query = rtrim($query, '&');

        return [
            'query' => $query
        ];
    }
}