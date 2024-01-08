<?php

namespace App\Service\Parsing\PayloadPipe\PayloadTransformer;


use App\Service\Parsing\PayloadPipe\Interface\PayloadTransformerInterface;

class TimeStamper implements PayloadTransformerInterface
{
    private string $pattern = '/\s*{{:timestamp}}\s*/';

    public function transform(array $parameters, array &$payload): void
    {
        foreach ($parameters as $parameter) {
            if (!preg_match($this->pattern, $parameter->getValue())) {
                continue;
            }

            $payload[$parameter->getKey()] = time();
        }
    }
}