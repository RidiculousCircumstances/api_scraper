<?php

namespace App\Tests\Service\ApiScraper\PayloadPipe;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;
use App\Tests\Providers\PayloadDataProvider;
use PHPUnit\Framework\TestCase;

class ExternalValueLoaderTest extends TestCase
{
    public static function responseProvider(): array
    {

        return PayloadDataProvider::providePayload();
    }


    /**
     *
     * @dataProvider responseProvider
     *
     * @param ResponseRegistry $registry
     * @param RequestData $requestData
     * @return void
     */
    public function testValueLoading(ResponseRegistry $registry, RequestData $requestData): void
    {

        $instruction = $this->createMock(SuspendableInterface::class);

        /**
         * Инсртрукция представляет собой связный список схем запросов.
         * Для обработки связанных запросов происходит блокировка списка, в результате
         * чего скрапер повторяет вызов до тех пор, пока блокировка не будет снята, и не кончатся схемы.
         *
         *В результате инструкция должна быть заблокирована и разблокирована
         */
        $valueLoader = ExternalValueLoader::new($registry, $instruction);

        $payload = &$requestData->getCrudePayloadReference();

        $valueLoader->transform($requestData);
        $fv = $payload;

        $valueLoader->transform($requestData);
        $sv = $payload;

        $valueLoader->transform($requestData);
        $thv = $payload;

        $this->assertEquals('foo', $fv['needs_external_key']);
        $this->assertEquals('bar', $sv['needs_external_key']);
        $this->assertEquals('baz', $thv['needs_external_key']);
    }

}