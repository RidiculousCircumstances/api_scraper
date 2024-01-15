<?php

namespace App\Entity\Settings;

enum SettingsTypeEnum: string
{
    case HTTP_PROXIES = 'http_proxies';
    case HTTPS_PROXIES = 'https_proxies';
}
