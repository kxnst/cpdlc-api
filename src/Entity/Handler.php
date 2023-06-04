<?php

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Repository\HandlerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HandlerRepository::class)]
class Handler
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: Standard::class, inversedBy: 'standard')]
    #[ORM\JoinColumn(name: 'standard_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Standard $standard;

    #[ORM\Column(type: Types::JSON)]
    private array $rules = [];

    #[ORM\Column(type: Types::JSON)]
    private array $normalizationRules = [];

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getStandard(): ?Standard
    {
        return $this->standard;
    }

    public function setStandard(?Standard $standard): void
    {
        $this->standard = $standard;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    public function getNormalizationRules(): array
    {
        return $this->normalizationRules;
    }

    public function setNormalizationRules(array $normalizationRules): void
    {
        $this->normalizationRules = $normalizationRules;
    }
}
