<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer;

use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer\DTO\UrlSegmentData;

class UrlExplorer
{

    private static string $urlParameterPattern = '/\s*(?<full>{{:url_parameter=(?<id>[a-z0-9]+)}})\s*/';

    /**
     *
     * @param string $url
     * @return array<UrlSegmentData>
     */
    public function getSegments(string $url): array
    {
        $matches = [];
        preg_match_all(self::$urlParameterPattern, $url, $matches);

        if (!$matches['full'] || !$matches['id'] || (count($matches['full']) !== count($matches['id']))) {
            return [];
        }

        $fulls = $matches['full'];
        $ids = $matches['id'];

        $urlParams = [];
        $count = count($fulls);
        for ($i = 0; $i < $count; $i++) {
            $urlParams[] = new UrlSegmentData(
                $fulls[$i],
                $ids[$i]
            );
        }

        return $urlParams;
    }
}