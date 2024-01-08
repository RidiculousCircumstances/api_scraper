<?php

namespace App\Entity;

use App\Repository\GroupTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupTagRepository::class)]
#[ORM\UniqueConstraint("group_code", ["code"])]
class GroupTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 50)]
    private string|null $code = null;

    #[ORM\OneToMany(mappedBy: 'groupTag', targetEntity: DataSchema::class, cascade: ["remove"])]
    private Collection $dataSchemas;

    #[ORM\OneToMany(mappedBy: 'groupTag', targetEntity: OutputSchema::class)]
    private Collection $outputSchemas;

    public function __construct()
    {
        $this->dataSchemas = new ArrayCollection();
        $this->outputSchemas = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getCode(): string|null
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, DataSchema>
     */
    public function getDataSchemas(): Collection
    {
        return $this->dataSchemas;
    }

    public function addDataSchema(DataSchema $dataSchema): static
    {
        if (!$this->dataSchemas->contains($dataSchema)) {
            $this->dataSchemas->add($dataSchema);
            $dataSchema->setGroupTag($this);
        }

        return $this;
    }

    public function removeDataSchema(DataSchema $dataSchema): static
    {
        if ($this->dataSchemas->removeElement($dataSchema) && $dataSchema->getGroupTag() === $this) {
            $dataSchema->setGroupTag(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, OutputSchema>
     */
    public function getOutputSchemas(): Collection
    {
        return $this->outputSchemas;
    }

    public function addOutputSchema(OutputSchema $outputSchema): static
    {
        if (!$this->outputSchemas->contains($outputSchema)) {
            $this->outputSchemas->add($outputSchema);
            $outputSchema->setGroupTag($this);
        }

        return $this;
    }

    public function removeOutputSchema(OutputSchema $outputSchema): static
    {
        if ($this->outputSchemas->removeElement($outputSchema) && $outputSchema->getGroupTag() === $this) {
            $outputSchema->setGroupTag(null);
        }

        return $this;
    }

}
