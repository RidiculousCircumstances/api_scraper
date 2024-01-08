<?php

namespace App\Service\Parsing\Client\RequestPayloadBuilder;

class GetPayloadBuilder extends AbstractPayloadBuilder
{

    protected function getPayloadArray(array $payload): array
    {
//        $query = http_build_query($payload);
//        $query = urldecode($query);
//        $query = preg_replace('/\[\d]=/', '[]=', $query);
//        $query = str_replace(['[', ']'], ['%5B', '%5D'], $query);
//
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