<?php

namespace App\Service\ApiScraper\HttpClient;

use App\Service\ApiScraper\HttpClient\Interface\ClientInterface;
use App\Service\ApiScraper\HttpClient\Interface\DataSourceInterface;
use GuzzleHttp\Exception\GuzzleException;

readonly class Client implements ClientInterface
{
    private \GuzzleHttp\ClientInterface $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }


    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function requestSource(DataSourceInterface $source): array
    {
        $payload = $source->getBody();
        $url = $source->getUrl();
        $method = $source->getMethod();
        $delay = $source->getDelay();

        usleep($delay);

        $response = $this->client->request($method, $url, $payload);
        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}