<?php

namespace Mytheresa\Challenge\DTO;

class ProductDTO
{
    public function __construct(
        private readonly string $sku,
        private readonly string $name,
        private readonly string $category,
        private readonly int    $price
    )
    {
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}