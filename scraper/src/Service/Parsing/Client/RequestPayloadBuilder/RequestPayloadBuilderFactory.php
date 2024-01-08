<?php

namespace App\Service\Parsing\Client\RequestPayloadBuilder;

use App\Message\Parsing\Enum\HttpMethodsEnum;

class RequestPayloadBuilderFactory
{

    public static function getBuilder(HttpMethodsEnum $httpMethodsEnum): AbstractPayloadBuilder
    {
        return match ($httpMethodsEnum) {
            HttpMethodsEnum::GET => new GetPayloadBuilder(),
            HttpMethodsEnum::POST => new PostPayloadBuilder(),
        };
    }
}