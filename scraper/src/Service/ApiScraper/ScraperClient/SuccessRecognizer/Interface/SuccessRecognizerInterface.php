<?php

namespace App\Service\ApiScraper\ScraperClient\SuccessRecognizer\Interface;

interface SuccessRecognizerInterface
{
    public function recognize(array $data): bool;
}