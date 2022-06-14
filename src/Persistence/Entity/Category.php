<?php

namespace Mytheresa\Challenge\Persistence\Entity;

use Doctrine\ORM\Mapping\{Column, Entity, HasLifecycleCallbacks};
use Mytheresa\Challenge\Persistence\Entity\Field\{CreatedAtUpdatedAtTrait, UuidPkTrait};
use Mytheresa\Challenge\Persistence\Repository\CategoryRepository;

/**
 * This entity represents categories
 * We use synthetic Primary Key just in case entity identification rules may change in future
 * Name used as external identifier
 * Here we store discount value in-place to simplify import process and runtime calculations
 */
#[Entity(repositoryClass: CategoryRepository::class), HasLifecycleCallbacks]
class Category
{
    use CreatedAtUpdatedAtTrait, UuidPkTrait;

    #[Column(type: "text", unique: true)]
    private string $name;

    #[Column(type: "integer", nullable: true)]
    private ?int $discount = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Category
    {
        $this->name = $name;
        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): Category
    {
        $this->discount = $discount;
        return $this;
    }
}