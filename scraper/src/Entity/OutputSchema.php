<?php

namespace App\Entity;

use App\Repository\OutputSchema\OutputSchemaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutputSchemaRepository::class)]
class OutputSchema
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 50)]
    private string|null $name = null;

    #[ORM\ManyToOne(inversedBy: 'outputSchemas')]
    #[ORM\JoinColumn(nullable: false)]
    private GroupTag|null $groupTag = null;

    #[ORM\ManyToMany(targetEntity: ResponseField::class, inversedBy: 'outputSchemas')]
    private Collection $responseFields;

    public function __construct()
    {
        $this->responseFields = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGroupTag(): GroupTag|null
    {
        return $this->groupTag;
    }

    public function setGroupTag(GroupTag|null $groupTag): static
    {
        $this->groupTag = $groupTag;

        return $this;
    }

    /**
     * @return Collection<int, ResponseField>
     */
    public function getResponseFields(): Collection
    {
        return $this->responseFields;
    }

    public function addResponseField(ResponseField $responseField): static
    {
        if (!$this->responseFields->contains($responseField)) {
            $this->responseFields->add($responseField);
        }

        return $this;
    }

    public function removeResponseField(ResponseField $responseField): static
    {
        $this->responseFields->removeElement($responseField);

        return $this;
    }

}
