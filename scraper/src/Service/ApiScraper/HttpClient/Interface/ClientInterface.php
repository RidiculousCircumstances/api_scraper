<?php

namespace App\Service\ApiScraper\HttpClient\Interface;

interface ClientInterface
{
    public function request(DataSourceInterface $source);

}