<?php

namespace Mytheresa\Challenge\Persistence\Factory;

use Mytheresa\Challenge\DTO\ProductDTO;
use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;

class ProductFactory
{
    public function create(ProductDTO $DTO): Product
    {
        return (new Product)->setSku($DTO->getSku());
    }

    public function fill(Product $product, ProductDTO $DTO, Category $category, ?int $discount): Product
    {
        return $product->setName($DTO->getName())
            ->setPrice($DTO->getPrice())
            ->setCategory($category)
            ->setDiscount($discount);
    }
}