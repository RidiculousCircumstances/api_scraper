<?php

namespace App\Service\ApiScraper\ScraperClient\SuccessRecognizer;

use App\Service\ApiScraper\ScraperClient\SuccessRecognizer\Interface\SuccessRecognizerInterface;
use App\Service\ApiScraper\ScraperClient\SuccessRecognizer\SmithWaterman\SmithWatermanGotoh;

class DifferenceSuccessRecognizer implements SuccessRecognizerInterface
{

    private const SIMILARITY_THRESHOLD = 0.26;

    private const SLICE_CAPACITY = 15;
    private static array|null $previousObject = null;
    private SmithWatermanGotoh $comparator;

    public function __construct()
    {
        $this->comparator = new SmithWatermanGotoh();
    }

    public function setPrevious(array $prevData): void
    {
        self::$previousObject = $prevData;
    }

    public function recognize(array $data): bool
    {
        if (self::$previousObject === null) {
            return false;
        }
        $prevArr = str_split(urldecode(http_build_query(self::$previousObject)), self::SLICE_CAPACITY);
        $currentArr = str_split(urldecode(http_build_query($data)), self::SLICE_CAPACITY);

        $prevArrCount = count($prevArr);
        $currentArrCount = count($currentArr);

        $count = floor(($prevArrCount + $currentArrCount) / 2);
        $similarityAbs = 0;
        for ($i = 0; $i < $count; $i++) {
            /**
             * Скипаем произвольные айтемы. Точность ниже, производительность выше
             */
            if ($i % 2 === 0 || $i % 3 === 0) {
                continue;
            }
            $prev = m($prevArr)(maybe_key($i, ''))();
            $current = m($currentArr)(maybe_key($i, ''))();
            $similarityAbs += $this->comparator->compare($prev, $current);
        }

        $similarity = $similarityAbs / $count;

        return $similarity >= self::SIMILARITY_THRESHOLD;
    }

}