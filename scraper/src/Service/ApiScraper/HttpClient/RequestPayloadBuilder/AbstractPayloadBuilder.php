<?php

namespace App\Service\ApiScraper\HttpClient\RequestPayloadBuilder;

abstract class AbstractPayloadBuilder
{
    public function build(array $requestParameters): mixed
    {
        return $this->getPayloadArray($requestParameters);
    }

    abstract protected function getPayloadArray(array $payload): mixed;
}