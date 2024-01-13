<?php

namespace App\Tests\Service\ApiScraper\PayloadPipe;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer\UrlSegmentReplacer;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;
use App\Tests\Providers\UrlSegmenterDataProvider;
use PHPUnit\Framework\TestCase;

class UrlSegmentReplacerTest extends TestCase
{
    public static function payloadProvider(): array
    {
        return UrlSegmenterDataProvider::providePayload();
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

        $payload = &$requestData->getCrudePayloadReference();

        $loader = ExternalValueLoader::new($registry, $instruction);

        $loader->transform($requestData);
        $replacer->transform($requestData);
        $fv = $requestData->getTargetUrl();

        $this->assertEquals('https://api.drom.com/v1.3/11/etc/foo', $fv);
    }
}