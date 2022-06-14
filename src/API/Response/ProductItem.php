<?php

namespace Mytheresa\Challenge\API\Response;

use Mytheresa\Challenge\DTO\ProductPriceDTO;

class ProductItem
{
    public function __construct(
        private readonly string          $sku,
        private readonly string          $name,
        private readonly string          $category,
        private readonly ProductPriceDTO $price
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

    public function getPrice(): ProductPriceDTO
    {
        return $this->price;
    }
}