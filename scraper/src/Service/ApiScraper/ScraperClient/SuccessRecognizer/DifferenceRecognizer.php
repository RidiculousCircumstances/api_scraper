<?php

namespace App\Service\ApiScraper\ScraperClient\SuccessRecognizer;

use App\Service\ApiScraper\ScraperClient\SuccessRecognizer\Interface\RecognizerInterface;
use App\Service\ApiScraper\ScraperClient\SuccessRecognizer\SmithWaterman\SmithWatermanGotoh;
use Ds\Stack;

class DifferenceRecognizer implements RecognizerInterface
{

    private const SIMILARITY_THRESHOLD = 0.26;

    private const SLICE_CAPACITY = 15;

    private static Stack $previousStack;

    private static bool $firstTime = true;
    private SmithWatermanGotoh $comparator;

    public function __construct()
    {
        $this->comparator = new SmithWatermanGotoh();
        self::$previousStack = new Stack();
    }

    public function recognize(array|string $data): bool
    {
        if (self::$previousStack->isEmpty()) {
            self::$previousStack->push($data);
            return false;
        }

        $previousItem = self::$previousStack->pop();
        self::$previousStack->push($data);

        $currentArr = $this->stringifyItemAndTurnIntoArray($data);
        $prevArr = $this->stringifyItemAndTurnIntoArray($previousItem);

        $prevArrCount = count($prevArr);
        $currentArrCount = count($currentArr);

        $count = floor(($prevArrCount + $currentArrCount) / 2);
        $similarityAbs = 0;
        for ($i = 0; $i < $count; $i++) {
            /**
             * Скипаем произвольные айтемы. Точность примерно та же, производительность выше
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


    private function stringifyItemAndTurnIntoArray(mixed $item): array
    {
        if (!is_string($item)) {
            return str_split(urldecode(http_build_query($item)), self::SLICE_CAPACITY);
        }

        return str_split($item);
    }

}