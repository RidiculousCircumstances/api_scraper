<?php

namespace App\Service\ApiScraper\HttpClient\Interface;

use App\Service\ApiScraper\HttpClient\Exceptions\HttpClientException;

interface ClientInterface
{
    /**
     * @param DataSourceInterface $source
     * @return mixed
     * @throws HttpClientException
     */
    public function request(DataSourceInterface $source);

}