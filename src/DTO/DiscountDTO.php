<?php

namespace Mytheresa\Challenge\DTO;

class DiscountDTO
{
    public function __construct(
        private readonly int     $value,
        private readonly ?string $sku = null,
        private readonly ?string $category = null
    )
    {
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }
}