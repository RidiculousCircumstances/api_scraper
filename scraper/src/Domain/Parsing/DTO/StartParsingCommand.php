<?php

namespace App\Domain\Parsing\DTO;

use App\Domain\Parsing\Enum\OutputFormatsEnum;
use App\Helper\Attribute\Constraints\Enum\Enum;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Type;

readonly class StartParsingCommand
{
    public function __construct(

        #[Type('int')]
        public int    $schemas,

        #[Type('string')]
        #[Enum(enumType: OutputFormatsEnum::class)]
        public string $formats,

        #[Type('string')]
        public string $path,

        #[Type('bool')]
        public bool|null   $useProxy,

    ) {
    }


}