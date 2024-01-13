<?php

namespace App\Service\ApiScraper\ScraperClient\SuccessRecognizer\Interface;

interface RecognizerInterface
{
    public function recognize(array|string $data): bool;
}