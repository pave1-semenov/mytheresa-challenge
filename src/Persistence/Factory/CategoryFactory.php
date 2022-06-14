<?php

namespace Mytheresa\Challenge\Persistence\Factory;

use Mytheresa\Challenge\Persistence\Entity\Category;

class CategoryFactory
{
    public function create(string $name): Category
    {
        return (new Category)->setName($name);
    }

    public function fill(Category $category, ?int $discount): Category
    {
        return $category->setDiscount($discount);
    }
}