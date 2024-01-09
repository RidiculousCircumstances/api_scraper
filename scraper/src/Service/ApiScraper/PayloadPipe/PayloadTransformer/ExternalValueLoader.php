<?php

namespace App\Service\ApiScraper\PayloadPipe\PayloadTransformer;


use App\Service\ApiScraper\PayloadPipe\Interface\PayloadTransformerInterface;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;

readonly class ExternalValueLoader implements PayloadTransformerInterface
{

    public function __construct(private ResponseRegistry $registry)
    {
    }

    public function transform(array $parameters, array &$payload): void
    {
        foreach ($parameters as $parameter) {
            $externalSourceId = $parameter->getExternalSourceId();
            if (!$externalSourceId) {
                continue;
            }

            $path = $parameter->getValue();
            $externalValues = $this->registry->get($externalSourceId);
            $payload[$parameter->getKey()] = m($externalValues)(get_by_dot_keys($path))();
        }
    }
}