<?php

namespace App\Service\ApiScraper\HttpClient\RequestPayloadBuilder;

use App\Message\Scraper\Enum\HttpMethodsEnum;

class RequestPayloadBuilderFactory
{

    public static function getBuilder(HttpMethodsEnum $httpMethodsEnum): AbstractPayloadBuilder
    {
        return match ($httpMethodsEnum) {
            HttpMethodsEnum::GET => new GetPayloadBuilder(),
        };
    }
}