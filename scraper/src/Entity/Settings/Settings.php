<?php

namespace App\Entity\Settings;

use App\Repository\SettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\UniqueConstraint("settings_type", ["type"])]
#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string|null $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string|null $value = null;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getType(): string|null
    {
        return $this->type;
    }

    public function setType(string|null $type): static
    {
        $this->type = $type;

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
}
