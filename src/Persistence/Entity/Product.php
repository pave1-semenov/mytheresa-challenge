<?php

namespace Mytheresa\Challenge\Persistence\Entity;

use Doctrine\ORM\Mapping\{Column, Entity, HasLifecycleCallbacks, JoinColumn, ManyToOne};
use Mytheresa\Challenge\Persistence\Entity\Field\{CreatedAtUpdatedAtTrait, UuidPkTrait};
use Mytheresa\Challenge\Persistence\Repository\ProductRepository;

/**
 * This entity represents products
 * We use synthetic Primary Key just in case entity identification rules may change in future
 * SKU serves as external identifier
 * Here we store discount value in-place to simplify import process and runtime calculations
 */
#[Entity(repositoryClass: ProductRepository::class), HasLifecycleCallbacks]
class Product
{
    use CreatedAtUpdatedAtTrait, UuidPkTrait;

    #[Column(type: "text", unique: true)]
    private string $sku;

    #[Column(type: "text", nullable: false)]
    private string $name;

    #[Column(type: "integer", nullable: false)]
    private int $price;

    #[ManyToOne(targetEntity: Category::class, fetch: "EAGER"), JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Category $category;

    #[Column(type: "integer", nullable: true)]
    private ?int $discount = null;

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): Product
    {
        $this->sku = $sku;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): Product
    {
        $this->price = $price;
        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): Product
    {
        $this->category = $category;
        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): Product
    {
        $this->discount = $discount;
        return $this;
    }
}