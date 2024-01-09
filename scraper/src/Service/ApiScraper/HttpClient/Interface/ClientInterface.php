<?php

namespace App\Service\ApiScraper\HttpClient\Interface;

interface ClientInterface
{
    public function requestSource(DataSourceInterface $source);

}