<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PipeHandlerInterface;

/**
 * Увеличивает значение поля с каждым вызовом. Используется для пагинации
 */
class PageIncrementor implements PipeHandlerInterface
{
    private static int|null $currentValue = null;
    private string $pattern = '/\s*{{:page}}\s*/';

    public function __construct(private readonly ScraperContext $ctx)
    {
    }

    public function transform(RequestData $requestData): void
    {

        $parameters = $requestData->getRequestParameters();
        $payloadRef = &$requestData->getCrudePayloadReference();

        foreach ($parameters as $parameter) {
            if (!preg_match($this->pattern, $parameter->getValue())) {
                continue;
            }

            if (self::$currentValue === null) {
                self::$currentValue = 1;
            } else {
                self::$currentValue++;
            }

            $payloadRef[$parameter->getKey()] = self::$currentValue;
            break;
        }

        /**
         * Если нечего инкрементировать - значит, нечего итерировать
         */
        if (self::$currentValue === null) {
            $this->ctx->stop();
            return;
        }

        $this->ctx->setIterationNumber(self::$currentValue);
    }
}