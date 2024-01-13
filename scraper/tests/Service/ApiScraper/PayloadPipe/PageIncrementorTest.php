<?php

namespace App\Tests\Service\ApiScraper\PayloadPipe;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\PageIncrementor;
use App\Service\ApiScraper\ResponseBag\ResponseBag;
use App\Tests\Providers\PayloadDataProvider;
use PHPUnit\Framework\TestCase;

class PageIncrementorTest extends TestCase
{

    public function payloadProvider()
    {
        return PayloadDataProvider::providePayload();
    }

    /**
     * @dataProvider payloadProvider
     * @param ResponseBag $registry
     * @param RequestData $requestData
     * @return void
     */
    public function testIncrementing(ResponseBag $registry, RequestData $requestData): void
    {

        $payloadRef = &$requestData->getCrudePayloadReference();

        $ctx = $this->createStub(ScraperContext::class);

        $incrementor = new PageIncrementor($ctx);

        $incrementor->transform($requestData);
        $this->assertEquals(1, $payloadRef['page']);

        $incrementor->transform($requestData);
        $this->assertEquals(2, $payloadRef['page']);
    }
}
