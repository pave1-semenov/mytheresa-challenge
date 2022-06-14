<?php

namespace Mytheresa\Challenge\Persistence\Entity\Field;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Generic trait which helps to automatically fill created_at field with current times on entity creation
 */
trait CreatedAtTrait
{
    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $createdAt;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setupCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
