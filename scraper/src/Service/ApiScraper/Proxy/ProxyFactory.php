<?php

namespace App\Service\ApiScraper\Proxy;

use App\Entity\Settings\SettingsTypeEnum;
use App\Repository\SettingsRepository;

readonly class ProxyFactory
{
    public function __construct(private SettingsRepository $settingsRepository) {

    }

    /**
     * @return ProxyData
     */
    public function getProxy(): ProxyData
    {
        $httpProxyString = $this->settingsRepository->findOneBy(['type' => SettingsTypeEnum::HTTP_PROXIES->value]);
        $httpsProxyString = $this->settingsRepository->findOneBy(['type' => SettingsTypeEnum::HTTPS_PROXIES->value]);

        $httpProxies = explode(',', $httpProxyString?->getValue());
        $httpsProxies = explode(',', $httpsProxyString?->getValue());

        return new ProxyData($httpProxies, $httpsProxies);
    }
}