<?php

namespace Mytheresa\Challenge\DTO;

use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

class ImportDTO
{
    public function __construct(
        private array          $categories,
        private array          $newCategories,
        private readonly array $skuList,
        private readonly array $discounts,
        private iterable       $products = []
    )
    {
    }

    public function getNewCategories(): array
    {
        return $this->newCategories;
    }

    public function getSkuList(): array
    {
        return $this->skuList;
    }

    public function addCategory(Category $category): void
    {
        $this->categories[$category->getName()] = $category;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getCategory(string $key): ?Category
    {
        return $this->categories[$key] ?? null;
    }

    /**
     * @return iterable<Product>
     */
    public function getProducts(): iterable
    {
        return $this->products;
    }

    public function getDiscount(string $key): ?int
    {
        return $this->discounts[$key] ?? null;
    }

    public function setProducts(iterable $products): ImportDTO
    {
        $this->products = $products;
        return $this;
    }
}