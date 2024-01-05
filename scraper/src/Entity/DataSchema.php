<?php

namespace App\Entity;

use App\Repository\DataSchemaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @property string $name
 * @property string $url
 */
#[ORM\Entity(repositoryClass: DataSchemaRepository::class)]
class DataSchema
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(type: "string", length: 20)]
    private string $name;

    #[ORM\Column(type: "string", length: 255)]
    private string $url;

    #[ORM\OneToMany(mappedBy: 'dataSchema', targetEntity: RequestParameter::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $requestParameters;

    #[ORM\OneToMany(mappedBy: 'externalSchema', targetEntity: RequestParameter::class, cascade: ["remove"])]
    private Collection $externalRequestParameters;

    #[ORM\OneToMany(mappedBy: 'dataSchema', targetEntity: ResponseField::class, orphanRemoval: true)]
    private Collection $responseFields;

    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'dataSchemas')]
    #[ORM\JoinColumn(nullable: true)]
    private GroupTag|null $groupTag = null;

    public function __construct()
    {
        $this->requestParameters = new ArrayCollection();
        $this->externalRequestParameters = new ArrayCollection();
        $this->responseFields = new ArrayCollection();
    }

    public function __toString(): string {
        return $this->name . ' ' . $this->id;
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return Collection<int, RequestParameter>
     */
    public function getRequestParameters(): Collection
    {
        return $this->requestParameters;
    }

    public function addRequestParameter(RequestParameter $requestParameter): static
    {
        if (!$this->requestParameters->contains($requestParameter)) {
            $this->requestParameters->add($requestParameter);
            $requestParameter->setDataSchema($this);
        }

        return $this;
    }

    public function removeRequestParameter(RequestParameter $requestParameter): static
    {
        if ($this->requestParameters->removeElement($requestParameter) && $requestParameter->getDataSchema() === $this) {
            $requestParameter->setDataSchema(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, RequestParameter>
     */
    public function getExternalRequestParameters(): Collection
    {
        return $this->externalRequestParameters;
    }

    public function addExternalRequestParameter(RequestParameter $externalRequestParameter): static
    {
        if (!$this->externalRequestParameters->contains($externalRequestParameter)) {
            $this->externalRequestParameters->add($externalRequestParameter);
            $externalRequestParameter->setExternalSchema($this);
        }

        return $this;
    }

    public function removeExternalRequestParameter(RequestParameter $externalRequestParameter): static
    {
        if ($this->externalRequestParameters->removeElement($externalRequestParameter) && $externalRequestParameter->getExternalSchema() === $this) {
            $externalRequestParameter->setExternalSchema(null);
        }

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
            $responseField->setDataSchema($this);
        }

        return $this;
    }

    public function removeResponseField(ResponseField $responseField): static
    {
        if ($this->responseFields->removeElement($responseField) && $responseField->getDataSchema() === $this) {
            $responseField->setDataSchema(null);
        }

        return $this;
    }

    public function getGroupTag(): ?GroupTag
    {
        return $this->groupTag;
    }

    public function setGroupTag(?GroupTag $groupTag): static
    {
        $this->groupTag = $groupTag;

        return $this;
    }

}
