<?php

namespace Mytheresa\Challenge\DTO;

class DiscountListDTO
{
    public function __construct(private readonly array $discounts)
    {
    }

    /**
     * @return DiscountDTO[]
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }
}