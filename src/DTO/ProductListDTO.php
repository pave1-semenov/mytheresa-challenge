<?php

namespace Mytheresa\Challenge\DTO;

class ProductListDTO
{
    public function __construct(private array $products)
    {
    }

    public function mapProducts(): void
    {
        $mappedProducts = [];
        foreach ($this->getProducts() as $product) {
            $mappedProducts[$product->getSku()] = $product;
        }
        $this->products = $mappedProducts;
    }

    /**
     * @return ProductDTO[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getProduct(string $key): ?ProductDTO
    {
        return $this->products[$key];
    }
}