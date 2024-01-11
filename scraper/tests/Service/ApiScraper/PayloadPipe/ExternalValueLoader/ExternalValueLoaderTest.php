<?php

namespace App\Tests\Service\ApiScraper\PayloadPipe\ExternalValueLoader;

use App\Service\ApiScraper\Instruction\DTO\RequestParameterData;
use App\Service\ApiScraper\PayloadPipe\PayloadTransformer\ExternalValueLoader\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipe\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\ResponseRegistry\ResponseRecord;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;
use PHPUnit\Framework\TestCase;

class ExternalValueLoaderTest extends TestCase
{
    public static function responseProvider(): array
    {

        $responseRecord = new ResponseRecord('request_1', [
            'data' => [
                [
                    'name' => 'foo',
                    'whatever' => 11
                ],
                [
                    'name' => 'bar',
                    'whatever' => [1, 4, 10]
                ],
                [
                    'name' => 'baz',
                ]
            ]
        ]);

        $requestParameters = [
            new RequestParameterData(
                key: 'needs_external_key',
                value: 'data.*.name',
                externalSourceId: 'request_1'
            ),
            new RequestParameterData(
                key: 'plain',
                value: 'inglip',
            ),
            new RequestParameterData(
                key: 'plain_second',
                value: 'inglip',
            ),
            new RequestParameterData(
                key: 'needs_external_key',
                value: 'data.*.name',
                externalSourceId: 'request_1'
            ),
        ];

        $registry = new ResponseRegistry();
        $registry->add($responseRecord);

        return [
            [$registry, $requestParameters]
        ];
    }


    /**
     *
     * @dataProvider responseProvider
     *
     * @param ResponseRegistry $registry
     * @param array $requestParameters
     * @return void
     */
    public function testValueLoading(ResponseRegistry $registry, array $requestParameters): void
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

        $payload = [];

        /**
         * Обрабатываем первый запрос
         * РЕЖИМ ГЕНЕРАТОРА
         */
        $valueLoader->transform($requestParameters, $payload);
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
        $valueLoader->transform($requestParameters, $payload);
        $secondValue = $payload['needs_external_key'];

        /**
         * РЕЖИМ ГЕНЕРАТОРА
         */
        $valueLoader->transform($requestParameters, $payload);
        $thirdValue = $payload['needs_external_key'];
        $this->assertFalse(isset($payload['plain']), 'premature plain call');
        $this->assertFalse(isset($payload['plain_second']), 'premature second plain call');

        /**
         * ОБЫЧНЫЙ РЕЖИМ
         */
        $valueLoader->transform($requestParameters, $payload);
        $this->assertTrue(isset($payload['plain']), 'no plain call');
        $this->assertTrue(isset($payload['plain_second']), 'no second plain call');

        /**
         * РЕЖИМ ГЕНЕРАТОРА
         */
        $valueLoader->transform($requestParameters, $payload);
        $fourthValue = $payload['needs_external_key'];

        $this->assertEquals('foo', $firstValue);
        $this->assertEquals('bar', $secondValue);
        $this->assertEquals('baz', $thirdValue);
        $this->assertEquals('foo', $fourthValue);
    }

}