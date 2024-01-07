<?php

namespace App\Message\Parsing;

use App\Helper\Attribute\Constraints\Enum\Enum;
use App\Message\Parsing\Enum\OutputFormatsEnum;
use Symfony\Component\Validator\Constraints\Type;

readonly class StartParsingCommand
{
    public function __construct(

        #[Type('integer')]
        private int       $schema,

        #[Type('string')]
        #[Enum(enumType: OutputFormatsEnum::class)]
        private string    $format,

        #[Type('string')]
        private string    $path,

        #[Type('boolean')]
        private bool|null $useProxy,

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

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUseProxy(): bool
    {
        return (bool)$this->useProxy;
    }

}