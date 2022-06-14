<?php

namespace Mytheresa\Challenge\Persistence\Entity\Field;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Generic trait which helps to automatically fill updated_at field with current time on entity creation and update
 */
trait UpdatedAtTrait
{
    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $updatedAt;

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setupUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateDate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
