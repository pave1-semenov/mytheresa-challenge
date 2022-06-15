<?php

namespace Mytheresa\Challenge\Service\Import;

use Doctrine\ORM\EntityManagerInterface;
use Mytheresa\Challenge\DTO\ImportDTO;
use Mytheresa\Challenge\DTO\ProductDTO;
use Mytheresa\Challenge\DTO\ProductListDTO;
use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Persistence\Factory\CategoryFactory;
use Mytheresa\Challenge\Persistence\Factory\ProductFactory;

/**
 * Here we create and/or fill the entities with data
 */
class ProductDiffProcessor
{
    public function __construct(
        private readonly CategoryFactory        $categoryFactory,
        private readonly ProductFactory         $productFactory,
        private readonly EntityManagerInterface $em
    )
    {
    }


    public function createNewCategories(ImportDTO $data): iterable
    {
        foreach ($data->getNewCategories() as $categoryName) {
            $category = $this->categoryFactory->create($categoryName);
            yield $this->fillCategory($data, $category);
            /**
             * We save the category in our ImportDTO to use it
             * Later in products import stage
             */
            $data->addCategory($category);
        }
    }

    private function fillCategory(ImportDTO $data, Category $category): Category
    {
        $categoryDiscount = $data->getDiscount($category->getName());

        return $this->categoryFactory->fill($category, $categoryDiscount);
    }

    public function updateCategories(ImportDTO $data): iterable
    {
        foreach ($data->getCategories() as $category) {
            yield $this->fillCategory($data, $category);
        }
    }

    /**
     * We use generator to reduce the memory consumption,
     * So after we clear EntityManager entities can be freely collected by GC
     */
    public function updateProducts(ImportDTO $data, ProductListDTO $list): iterable
    {
        foreach ($data->getProducts() as $product) {
            $item = $list->getProduct($product->getSku());
            if ($entity = $this->fillProduct($data, $product, $item)) {
                yield $entity;
            }
        }
    }

    private function fillProduct(ImportDTO $data, Product $product, ProductDTO $item): ?Product
    {
        $category = $data->getCategory($item->getCategory());
        $result = null;
        if ($category) {
            $productDiscount = $data->getDiscount($item->getSku());
            /**
             * Assuming we already cleared EntityManager few times,
             * So Category entities are not managed anymore.
             * We're using references so we can save products
             */
            $ref = $this->em->getReference(Category::class, $category->getId());
            $result = $this->productFactory->fill($product, $item, $ref, $productDiscount);
        }

        return $result;
    }

    /**
     * @param ProductDTO[] $newProducts
     */
    public function createNewProducts(array $newProducts, ImportDTO $data): iterable
    {
        foreach ($newProducts as $item) {
            $product = $this->productFactory->create($item);
            if ($entity = $this->fillProduct($data, $product, $item)) {
                yield $entity;
            }
        }
    }
}