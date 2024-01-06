<?php

namespace App\Entity;

use App\Repository\RequestParameter\RequestParameterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestParameterRepository::class)]
class RequestParameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\ManyToOne(cascade: ["remove"], inversedBy: 'requestParameters')]
    #[ORM\JoinColumn(nullable: false)]
    private DataSchema|null $dataSchema = null;

    #[ORM\Column(length: 50)]
    private string|null $key = null;

    #[ORM\Column(length: 255, nullable: true)]
    private string|null $value = null;

    #[ORM\ManyToOne(inversedBy: 'externalRequestParameters')]
    private DataSchema|null $externalSchema = null;

    public function __toString(): string {
        return $this->key;
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getDataSchema(): DataSchema|null
    {
        return $this->dataSchema;
    }

    public function setDataSchema(DataSchema|null $dataSchema): static
    {
        $this->dataSchema = $dataSchema;

        return $this;
    }

    public function getKey(): string|null
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): string|null
    {
        return $this->value;
    }

    public function setValue(string|null $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getExternalSchema(): DataSchema|null
    {
        return $this->externalSchema;
    }

    public function setExternalSchema(DataSchema|null $externalSchema): static
    {
        $this->externalSchema = $externalSchema;

        return $this;
    }

}
