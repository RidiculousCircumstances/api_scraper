<?php

namespace App\Service\Parsing\DTO;

/**
 * Параметру запроса может соответствовать значение, получаемое из другого связанного запроса,
 * поэтому в дата объект помещаетсся ссылка на внешнюю инструкцию
 */
class RequestParameterData
{
    public function __construct(
        private readonly string             $key,

        private readonly mixed              $value,

        private ParsingInstructionData|null $externalSource = null,
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getExternalSource(): ParsingInstructionData
    {
        return $this->externalSource;
    }

    public function setExternalSource(ParsingInstructionData $instructionData): static
    {
        $this->externalSource = $instructionData;
        return $this;
    }
}