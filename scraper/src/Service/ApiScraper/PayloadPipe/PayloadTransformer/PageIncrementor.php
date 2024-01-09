<?php

namespace App\Service\ApiScraper\PayloadPipe\PayloadTransformer;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\Context\ScraperStateEnum;
use App\Service\ApiScraper\PayloadPipe\Interface\PayloadTransformerInterface;

/**
 * Увеличивает значение поля с каждым вызовом
 */
class PageIncrementor implements PayloadTransformerInterface
{
    private static int|null $currentValue = null;
    private string $pattern = '/\s*{{:page}}\s*/';

    public function __construct(private readonly ScraperContext $ctx)
    {
    }

    public function transform(array $parameters, array &$payload): void
    {
        foreach ($parameters as $parameter) {
            if (!preg_match($this->pattern, $parameter->getValue())) {
                continue;
            }

            if (self::$currentValue === null) {
                self::$currentValue = 1;
            } else {
                self::$currentValue++;
            }

            $payload[$parameter->getKey()] = self::$currentValue;
            break;
        }

        /**
         * Если нечего инкрементировать - значит, нечего итерировать
         */
        if (self::$currentValue === null) {
            $this->ctx->setState(ScraperStateEnum::STOPPED);
            return;
        }

        $this->ctx->setIterationNumber(self::$currentValue);
    }
}