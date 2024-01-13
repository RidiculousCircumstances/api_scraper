<?php

namespace App\Tests\Service\ApiScraper\PayloadPipe;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\TimeStamper;
use App\Service\ApiScraper\ResponseBag\ResponseBag;
use App\Tests\Providers\PayloadDataProvider;
use PHPUnit\Framework\TestCase;

class TimeStamperTest extends TestCase
{
    public static function payloadProvider(): array
    {
        return PayloadDataProvider::providePayload();
    }

    /**
     * @dataProvider payloadProvider
     * @param array $payload
     * @return void
     */
    public function testTimeStamper(ResponseBag $registry, RequestData $requestData): void
    {
        $timeStamper = new TimeStamper();

        $payload = &$requestData->getCrudePayloadReference();

        $timeStamper->transform($requestData);

        $this->assertTrue((bool)preg_match('/\d*/', $payload['timestamp']));

    }
}