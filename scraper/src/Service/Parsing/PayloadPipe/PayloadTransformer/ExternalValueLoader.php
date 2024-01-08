<?php

namespace App\Service\Parsing\PayloadPipe\PayloadTransformer;


use App\Service\Parsing\PayloadPipe\Interface\PayloadTransformerInterface;
use App\Service\Parsing\ResponseRegistry\ResponseRegistry;

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