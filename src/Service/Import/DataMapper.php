<?php

namespace Mytheresa\Challenge\Service\Import;

use Mytheresa\Challenge\DTO\DiscountListDTO;
use Mytheresa\Challenge\DTO\ImportDTO;
use Mytheresa\Challenge\DTO\ProductListDTO;
use Mytheresa\Challenge\Persistence\Repository\CategoryRepository;
use Mytheresa\Challenge\Persistence\Repository\ProductRepository;

/**
 * This class helps to combine import data with current database records
 * Into optimized data structure to speed up import process and decrease memory footprint
 */
class DataMapper
{
    public function __construct(
        private readonly ProductRepository  $productRepository,
        private readonly CategoryRepository $categoryRepository,
    )
    {
    }

    public function loadCategories(ProductListDTO $productsList, ?DiscountListDTO $discountsList): ImportDTO
    {
        $skuList = $names = [];
        foreach ($productsList->getProducts() as $item) {
            $skuList[] = $item->getSku();
            $names[$item->getCategory()] = true;
        }
        /**
         * Here we load all the categories present in file from database.
         * Assuming that there would be not much of them
         */
        $categories = $this->categoryRepository->getMappedByName(array_keys($names));
        /**
         * We map discounts once to reuse it in further steps.
         * Assuming that category names and product SKU won't intersect
         */
        $discounts = $discountsList ? $this->mapDiscounts($discountsList) : [];
        /**
         * We extract the category names not currently present in database
         */
        $newCategories = array_keys(array_diff_key($names, $categories));

        return new ImportDTO($categories, $newCategories, $skuList, $discounts);
    }

    private function mapDiscounts(DiscountListDTO $discounts): array
    {
        $mappedDiscounts = [];
        foreach ($discounts->getDiscounts() as $discount) {
            $key = $discount->getSku() ?: $discount->getCategory();
            $mappedDiscounts[$key] = $discount->getValue();
        }

        return $mappedDiscounts;
    }

    public function loadProducts(ImportDTO $data): void
    {
        /**
         * Here we're using previously stored product SKU to extract entities from database
         */
        $products = $this->productRepository->getMappedBySku($data->getSkuList());

        $data->setProducts($products);
    }
}