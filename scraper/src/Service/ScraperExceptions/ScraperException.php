<?php

namespace App\Service\ScraperExceptions;

use App\Service\ApiScraper\Context\ScraperContext;

class ScraperException extends \RuntimeException
{

    public function __construct($message, private readonly ScraperContext $context)
    {
        parent::__construct($message);
    }

    public function getContext(): ScraperContext
    {
        return $this->context;
    }

}