<?php

namespace App\Tests\Service\ApiScraper\PayloadPipe;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer\UrlSegmentReplacer;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;
use PHPUnit\Framework\TestCase;

class UrlSegmentReplacerTest extends TestCase
{
    public static function payloadProvider(): array
    {
        return PayloadDataProvider::providePayload();
    }

    /**
     * @dataProvider payloadProvider
     * @param ResponseRegistry $registry
     * @param RequestData $requestData
     * @return void
     */
    public function testSegmentReplacing(ResponseRegistry $registry, RequestData $requestData): void
    {
        $replacer = new UrlSegmentReplacer();

        $instruction = $this->createStub(SuspendableInterface::class);

        $loader = new ExternalValueLoader($registry, $instruction);

        $loader->transform($requestData);
        $loader->transform($requestData);
        $loader->transform($requestData);
        $loader->transform($requestData);

        $replacer->transform($requestData);

        $a = 1;
    }
}