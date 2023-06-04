<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Trait\IdTrait;
use App\Repository\StandardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandardRepository::class)]
#[ApiResource(operations: [new GetCollection(), new Get()])]
#[ApiFilter(filterClass:SearchFilter::class, properties: ['slug'=> 'exact'])]
class Standard
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, length: 25)]
    private string $slug;

    #[ORM\OneToOne(targetEntity: Handler::class)]
    private ?Handler $defaultHandler;

    #[ORM\Column(type: Types::JSON)]
    private array $metaData = [];

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getDefaultHandler(): ?Handler
    {
        return $this->defaultHandler;
    }

    public function setDefaultHandler(?Handler $defaultHandler): void
    {
        $this->defaultHandler = $defaultHandler;
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }

    public function setMetaData(array $metaData): void
    {
        $this->metaData = $metaData;
    }
}
