<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PayloadTransformerInterface;

/**
 * Подгружает зачения, полученные ExternalValueLoader, в урл, если требуется
 */
class UrlSegmentReplacer implements PayloadTransformerInterface
{

    private UrlExplorer $urlExplorer;

    public function __construct()
    {
        $this->urlExplorer = new UrlExplorer();
    }

    public function transform(RequestData $requestData): void
    {
        $payload = &$requestData->getCrudePayloadReference();
        $url = $requestData->getTargetUrl();

        $segments = $this->urlExplorer->getSegments($url);

        foreach ($segments as $segment) {
            $value = $payload[$segment->getFullCode()];
            $url = str_replace($segment->getFullCode(), $value, $url);
        }

        $requestData->setTargetUrl($url);
    }
}