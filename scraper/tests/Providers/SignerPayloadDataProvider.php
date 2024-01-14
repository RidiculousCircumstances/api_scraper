<?php

namespace App\Tests\Providers;

use App\Message\Scraper\Enum\HttpMethodsEnum;
use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\Instruction\DTO\RequestParameterData;
use App\Service\ApiScraper\ResponseBag\ResponseBag;
use App\Service\ApiScraper\ResponseBag\ResponseRecord;

class SignerPayloadDataProvider
{
    public static function providePayload(): array
    {

        $responseRecord = new ResponseRecord('response_1', [
            'data' => [
                'cars' => [
                    [
                        'id' => 54028188,
                        'test' => 'foo'
                    ]
                ]
            ]
        ]);

        $requestParameters = [
            new RequestParameterData(
                key: 'mainPhotoWidth',
                value: 'original',
            ),
            new RequestParameterData(
                key: 'thumbnailsWidth',
                value: '480,original',
            ),
//            new RequestParameterData(
//                key: 'thumbnailsWidth[]',
//                value: 'original',
//            ),
            new RequestParameterData(
                key: 'version',
                value: '4',
            ),
            new RequestParameterData(
                key: 'recSysDeviceId',
                value: '8d07d92c740264189e0bf99976f1aa4d',
            ),
            new RequestParameterData(
                key: 'recSysRegionId',
                value: '22',
            ),
            new RequestParameterData(
                key: 'recSysCityId',
                value: '11',
            ),
            new RequestParameterData(
                key: 'app_id',
                value: 'p32',
            ),
            new RequestParameterData(
                key: 'timestamp',
                value: '1705128002567',
            ),
            new RequestParameterData(
                key: '{{:url_parameter=bulletinid}}',
                value: 'data.cars.*.id',
                externalSourceId: 'response_1'
            ),
            new RequestParameterData(
                key: 'secret',
                value: '{{:secret}}',
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
            targetUrl: 'https://api.drom.ru/v1.3/bulls/{{:url_parameter=bulletinid}}',
            httpMethod: HttpMethodsEnum::GET,
            requestParameters: $requestParameters,
            crudePayload: $payload
        );

        $registry = new ResponseBag();
        $registry->addResponseRecord($responseRecord);

        return [
            [$registry, $requestData]
        ];


    }
}