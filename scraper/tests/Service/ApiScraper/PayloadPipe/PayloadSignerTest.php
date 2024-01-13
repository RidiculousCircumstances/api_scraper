<?php

namespace App\Tests\Service\ApiScraper\PayloadPipe;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\PayloadSigner\PayloadSigner;
use App\Service\ApiScraper\ResponseBag\ResponseBag;
use App\Tests\Providers\SignerPayloadDataProvider;
use Monolog\Test\TestCase;

class PayloadSignerTest extends TestCase
{
    public static function payloadProvider(): array
    {
        return SignerPayloadDataProvider::providePayload();
    }

    /**
     * @dataProvider payloadProvider
     * @param ResponseBag $registry
     * @param RequestData $requestData
     * @return void
     */
    public function testSign(ResponseBag $registry, RequestData $requestData): void
    {
        $signer = new PayloadSigner('hbY0qRBVUk5uI9a');

        $instruction = $this->createStub(SuspendableInterface::class);

        $loader = ExternalValueLoader::getFresh($registry, $instruction);

        $payload = &$requestData->getCrudePayloadReference();

        $loader->transform($requestData);
        $signer->transform($requestData);

        $secret = $payload['secret'];
        $except = '8f156588b1d163331a2d3096bbe5cd4f4839fdac1916dc5eb7f2e2e9d88f06a6';

        $this->assertEquals($except, $secret);

    }
}