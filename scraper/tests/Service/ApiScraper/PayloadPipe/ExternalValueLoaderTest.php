<?php

namespace App\Tests\Service\ApiScraper\PayloadPipe;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;
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
        $instruction
            ->method('suspended');

        $valueLoader = new ExternalValueLoader($registry, $instruction);

        $payload = &$requestData->getCrudePayloadReference();

        /**
         * Обрабатываем первый запрос
         * РЕЖИМ ГЕНЕРАТОРА
         */
        $valueLoader->transform($requestData);
        $firstValue = $payload['needs_external_key'];

        /**
         * Запрос отправлен. Так как итерирование инструкций скрапера засуспенжено,
         * он продолжит выполнение текущей схемы
         */

        /**
         * Трансформер обрабатывает следующий айтем из массива во внешнем респонсе
         *
         * РЕЖИМ ГЕНЕРАТОРА
         */
        $valueLoader->transform($requestData);
        $secondValue = $payload['needs_external_key'];

        /**
         * РЕЖИМ ГЕНЕРАТОРА
         */
        $valueLoader->transform($requestData);
        $thirdValue = $payload['needs_external_key'];
        $this->assertFalse(isset($payload['plain']), 'premature plain call');
        $this->assertFalse(isset($payload['plain_second']), 'premature second plain call');

        /**
         * ОБЫЧНЫЙ РЕЖИМ
         */
        $valueLoader->transform($requestData);
        $this->assertTrue(isset($payload['plain']), 'no plain call');
        $this->assertTrue(isset($payload['plain_second']), 'no second plain call');

        /**
         * РЕЖИМ ГЕНЕРАТОРА
         */
        $valueLoader->transform($requestData);
        $fourthValue = $payload['needs_external_key'];

        $this->assertEquals('foo', $firstValue);
        $this->assertEquals('bar', $secondValue);
        $this->assertEquals('baz', $thirdValue);
        $this->assertEquals('baz', $fourthValue);
    }

}