<?php

namespace App\Service\ApiScraper\ScraperClient;

use App\Entity\Settings\SettingsTypeEnum;
use App\Service\ApiScraper\HttpClient\RequestAdapter;
use App\Service\ApiScraper\HttpClient\RequestPayloadBuilder\RequestPayloadBuilderFactory;
use App\Service\ApiScraper\Instruction\DTO\RequestConfigData;
use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;

class RequestFactory
{
    public static function getRequest(RequestConfigData $configData, ScraperSchemaData $parsingSchemaData): RequestAdapter
    {
        $requestData = $parsingSchemaData->getRequestData();
        $payloadBuilder = RequestPayloadBuilderFactory::getBuilder($configData->getMethod());
        $proxyConfig = $configData->getProxyConfig();

        $proxy = [
            'proxy' => [
                'http' => $proxyConfig->getRandProxy(SettingsTypeEnum::HTTP_PROXIES->value),
                'https' => $proxyConfig->getRandProxy(SettingsTypeEnum::HTTPS_PROXIES->value),
            ]
        ];

        return new RequestAdapter(
            url: $requestData->getTargetUrl(),
            method: $requestData->getHttpMethod()->value,
            body: $payloadBuilder->build($requestData->getCrudePayload()),
            proxy: $configData->useProxy() ? $proxy : [],
            headers: $parsingSchemaData->isNeedsAuth() ? ['X_AUTH_TOKEN' => $configData->getAuthToken()] : []
        );
    }
}