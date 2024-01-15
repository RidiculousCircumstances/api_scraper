<?php

namespace App\Service\ApiScraper\Proxy;

use App\Entity\Settings\SettingsTypeEnum;
use RuntimeException;

readonly class ProxyData
{
    public function __construct(
        private array $httpProxies,
        private array $httpsProxies
    ) {}

    public function getHttpProxies(): array
    {
        return $this->httpProxies;
    }

    public function getHttpsProxies(): array
    {
        return $this->httpsProxies;
    }

    public function getRandProxy(string $type): int|array|string
    {

        $validType = SettingsTypeEnum::from($type);

        if($validType === SettingsTypeEnum::HTTP_PROXIES) {
            return $this->httpProxies[array_rand($this->httpProxies)];
        }

        if($validType === SettingsTypeEnum::HTTPS_PROXIES) {
            return $this->httpsProxies[array_rand($this->httpsProxies)];
        }

        throw new RuntimeException('[ProxyData] Передан невалидный тип прокси.');

    }

}