<?php

namespace App\Service\ApiScraper\ScraperClient;

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
        return new RequestAdapter(
            url: $requestData->getTargetUrl(),
            method: $requestData->getHttpMethod()->value,
            body: $payloadBuilder->build($requestData->getCrudePayload()),
            headers: $parsingSchemaData->isNeedsAuth() ? ['X_AUTH_TOKEN' => $configData->getAuthToken()] : []
        );
    }
}