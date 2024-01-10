<?php

namespace App\Service\ApiScraper\StringPathTraverser;

final class StringPathTraverser
{

    private const MULTIPLE_ITEMS_PATH_PATTERN = '/\S+\.\*\.\S+/';

    private const PATH_TO_MULTIPLE_ITEMS_PATTERN = '/(?<array>\S+)\.\*/';

    private const PATH_TO_VALUE_PATTERN = '/\.\*\.(?<value>.*)/';

    public function checkMultipleItemsInPath(string $path): bool
    {
        return (bool)preg_match(self::MULTIPLE_ITEMS_PATH_PATTERN, $path);
    }

    public function extractItems(string $path, array $content): array|null
    {

        $pathToArrayMatches = [];
        preg_match(self::PATH_TO_MULTIPLE_ITEMS_PATTERN, $path, $pathToArrayMatches);

        return m($content)(get_by_dot_keys($pathToArrayMatches['array']))();

    }

    public function extractValue(string $path, array $content): mixed
    {
        $pathToValueMatches = [];
        preg_match(self::PATH_TO_VALUE_PATTERN, $path, $pathToValueMatches);

        return m($content)(get_by_dot_keys($pathToValueMatches['value']))();
    }
}