<?php

namespace Mytheresa\Challenge\DTO;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ProductPriceDTO
{
    public function __construct(
        private readonly int                                              $original,
        private readonly int                                              $final,
        #[SerializedName("discount_percentage")] private readonly ?string $discountPercentage,
        private readonly string                                           $currency
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