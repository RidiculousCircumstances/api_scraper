<?php

namespace App\Service\ApiScraper\Instruction\DTO;

use App\Message\Scraper\Enum\HttpMethodsEnum;
use App\Service\ApiScraper\Proxy\ProxyData;

class RequestConfigData
{
    public function __construct(
        private readonly HttpMethodsEnum $method,
        private ProxyData                $proxyConfig,
        private string|null              $secret = null,
        private readonly int             $delay = 100,
        private string|null              $authToken = null,
        private bool|null                $useProxy = null,
    )
    {

    }

    public function getMethod(): HttpMethodsEnum
    {
        return $this->method;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function useProxy(): bool|null
    {
        return $this->useProxy;
    }

    public function getProxyConfig(): ProxyData
    {
        return $this->proxyConfig;
    }

}