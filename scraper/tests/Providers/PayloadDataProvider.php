<?php

namespace App\Tests\Providers;

use App\Message\Parsing\Enum\HttpMethodsEnum;
use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\Instruction\DTO\RequestParameterData;
use App\Service\ApiScraper\ResponseBag\ResponseBag;
use App\Service\ApiScraper\ResponseBag\ResponseRecord;

class PayloadDataProvider
{
    public static function providePayload(): array
    {
        $responseRecord = new ResponseRecord('request_1', [
            'data' => [
                [
                    'name' => 'foo',
                    'whatever' => 11
                ],
                [
                    'name' => 'bar',
                    'whatever' => 14
                ],
                [
                    'name' => 'baz',
                    'whatever' => 14
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
            new RequestParameterData(
                key: 'timestamp',
                value: '{{:timestamp}}',
            ),
            new RequestParameterData(
                key: 'page',
                value: '{{:page}}',
            ),
            new RequestParameterData(
                key: '{{:url_parameter=p1}}',
                value: 'data.*.name',
                externalSourceId: 'request_1'
            ),
            new RequestParameterData(
                key: '{{:url_parameter=p2}}',
                value: 'data.*.name',
                externalSourceId: 'request_1'
            ),
        ];

        $payload = [];
        /**
         * @var RequestParameterData $requestParameter
         */
        foreach ($requestParameters as $requestParameter) {
            $payload[$requestParameter->getKey()] = $requestParameter->getValue();
        }

        $requestData = new RequestData(
            targetUrl: 'https://api.drom.com/v1.3/{{:url_parameter=p2}}/etc/{{:url_parameter=p1}}}}',
            httpMethod: HttpMethodsEnum::GET,
            requestParameters: $requestParameters,
            crudePayload: $payload
        );

        $registry = new ResponseBag();
        $registry->add($responseRecord);

        return [
            [$registry, $requestData]
        ];
    }
}