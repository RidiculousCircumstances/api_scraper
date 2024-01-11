<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer;

use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PayloadTransformerInterface;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer\DTO\UrlSegmentData;

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

        foreach ($payload as $key => $value) {

            $match = array_filter($segments, fn(UrlSegmentData $data) => $data->getFullCode() === $key);

            $a = 1;

        }

    }
}