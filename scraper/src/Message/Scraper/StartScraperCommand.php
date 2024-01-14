<?php

namespace App\Message\Scraper;

use App\Helper\Attribute\Constraints\Enum\Enum;
use App\Message\Scraper\Enum\HttpMethodsEnum;
use App\Message\Scraper\Enum\OutputFormatsEnum;
use Symfony\Component\Validator\Constraints\Type;

readonly class StartScraperCommand
{
    public function __construct(

        #[Type('integer')]
        private int         $schema,

        #[Type('string')]
        #[Enum(enumType: OutputFormatsEnum::class)]
        private string      $format,

        #[Type('string')]
        private string      $file,

        #[Type('boolean')]
        private bool|null   $useProxy,

        #[Type('string')]
        private string|null $secret,

        #[Enum(enumType: HttpMethodsEnum::class)]
        private string      $method,

        #[Type('integer')]
        private int|null    $delay,

        #[Type('string')]
        private string|null $auth,

    )
    {
    }

    public function getSchema(): int
    {
        return $this->schema;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getFileName(): string
    {
        return $this->file;
    }

    public function getUseProxy(): bool
    {
        return (bool)$this->useProxy;
    }

    public function getSecret(): string|null
    {
        return $this->secret;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getDelay(): int|null
    {
        return $this->delay;
    }

    public function getAuthToken(): string
    {
        return $this->auth;
    }

}