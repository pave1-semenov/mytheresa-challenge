<?php

namespace Mytheresa\Challenge\DTO;

class ProductPriceDTO
{
    public function __construct(
        private readonly int     $original,
        private readonly int     $final,
        private readonly ?string $discountPercentage,
        private readonly string  $currency
    )
    {
    }

    public function getOriginal(): int
    {
        return $this->original;
    }

    public function getFinal(): int
    {
        return $this->final;
    }

    public function getDiscountPercentage(): ?string
    {
        return $this->discountPercentage;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}