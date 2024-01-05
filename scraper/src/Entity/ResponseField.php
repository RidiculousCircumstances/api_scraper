<?php

namespace App\Entity;

use App\Repository\ResponseSchemaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponseSchemaRepository::class)]
class ResponseField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 255)]
    private string|null $dataPath = null;

    #[ORM\Column(length: 50)]
    private string|null $outputName = null;

    #[ORM\ManyToOne(inversedBy: 'responseFields')]
    #[ORM\JoinColumn(nullable: false)]
    private DataSchema|null $dataSchema = null;

    #[ORM\ManyToMany(targetEntity: OutputSchema::class, mappedBy: 'responseFields')]
    private Collection $outputSchemas;


    public function __toString(): string {
        return $this->dataPath;
    }

    public function __construct()
    {
        $this->outputSchemas = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getDataPath(): string|null
    {
        return $this->dataPath;
    }

    public function setDataPath(string $dataPath): static
    {
        $this->dataPath = $dataPath;

        return $this;
    }

    public function getOutputName(): string|null
    {
        return $this->outputName;
    }

    public function setOutputName(string $outputName): static
    {
        $this->outputName = $outputName;

        return $this;
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
            $outputSchema->addResponseField($this);
        }

        return $this;
    }

    public function removeOutputSchema(OutputSchema $outputSchema): static
    {
        if ($this->outputSchemas->removeElement($outputSchema)) {
            $outputSchema->removeResponseField($this);
        }

        return $this;
    }
}
