<?php

namespace App\Entity\Trait;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait TimestampableTrait
{
    #[ORM\Column(name: "created_at", type: "datetime", nullable: true)]
    protected ?DateTime $createdAt = null;

    #[ORM\Column(name: "updated_at", type: "datetime", nullable: true)]
    protected ?DateTime $updatedAt = null;

    protected function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new DateTime('now'));
        }
    }
}