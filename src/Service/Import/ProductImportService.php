<?php

namespace Mytheresa\Challenge\Service\Import;

use Doctrine\ORM\EntityManagerInterface;
use Mytheresa\Challenge\DTO\DiscountListDTO;
use Mytheresa\Challenge\DTO\ImportDTO;
use Mytheresa\Challenge\DTO\ProductDTO;
use Mytheresa\Challenge\DTO\ProductListDTO;
use Mytheresa\Challenge\Utils\LoggerTrait;

/**
 * This service encapsulates all data import logic
 */
class ProductImportService
{
    use LoggerTrait;

    public function __construct(
        private readonly DataMapper             $dataMapper,
        private readonly ProductDiffProcessor   $diffProcessor,
        private readonly EntityManagerInterface $em
    )
    {
    }

    /**
     * Assuming that import files contain data for create/update ONLY
     * So we're not deleting any records if we don't see them in file
     */
    public function import(ProductListDTO $productsList, DiscountListDTO $discountsList, int $batchSize): void
    {
        /**
         * First we process the categories, because they're required for products
         * I don't use cascade persistence to save the possibility to clear EntityManager
         * While processing big batches of data.
         * The ability to partially clear the UnitOfWork marked as deprecated in current
         * Stable version of Doctrine, so we can't keep Categories in memory during all import process
         */
        $data = $this->processCategories($productsList, $discountsList);
        /**
         * Second we update existing products with limited batch size
         * So we can clear EntityManager as much as we need to reduce memory footprint.
         * After clearing EntityManager entities become detached, so by handling
         * Updates before inserts we're making sure that we won't try to insert them
         * In further step
         */
        $newProductsData = $this->updateProducts($productsList, $data, $batchSize);
        /**
         * Third we create the new products using data collected during previous steps
         */
        $this->createNewProducts($newProductsData, $data, $batchSize);
    }

    private function processCategories(ProductListDTO $productsList, DiscountListDTO $discountsList): ImportDTO
    {
        /**
         * Mapping products by SKU highly improves access complexity in further steps
         */
        $productsList->mapProducts();
        $data = $this->dataMapper->loadCategories($productsList, $discountsList);

        $this->updateCategories($data);
        $this->createNewCategories($data);

        $this->em->flush();
        /**
         * Clearing EntityManager to release entities from memory
         */
        $this->em->clear();

        return $data;
    }

    private function updateCategories(ImportDTO $data): void
    {
        /**
         * We're not limiting batch size here assuming there wouldn't be a lot of categories
         */
        $updated = 0;
        foreach ($this->diffProcessor->updateCategories($data) as $category) {
            $this->em->persist($category);
            $updated++;
        }

        $this->logger->info("{$updated} categories updated");
    }

    private function createNewCategories(ImportDTO $data): void
    {
        /**
         * We're not limiting batch size here assuming there wouldn't be a lot of categories
         */
        $created = 0;
        foreach ($this->diffProcessor->createNewCategories($data) as $category) {
            $this->em->persist($category);
            $created++;
        }

        $this->logger->info("{$created} new categories created");
    }

    /**
     * @return ProductDTO[] products to insert
     */
    private function updateProducts(ProductListDTO $productsList, ImportDTO $data, int $batchSize): array
    {
        $this->dataMapper->loadProducts($data);
        $persisted = 0;
        /**
         * We save the updated SKU, so we can know which products
         * From file we didn't process yet and can insert them later
         */
        $updated = [];

        foreach ($this->diffProcessor->updateProducts($data, $productsList) as $product) {
            $this->em->persist($product);
            $updated[$product->getSku()] = true;
            if (++$persisted == $batchSize) {
                $this->em->flush();
                $this->em->clear();
                $persisted = 0;
            }
        }

        $this->em->flush();
        $this->em->clear();
        $updatedCount = count($updated);
        $this->logger->info("{$updatedCount} products updated");

        return array_diff_key($productsList->getProducts(), $updated);
    }

    private function createNewProducts(array $newProducts, ImportDTO $data, int $batchSize): void
    {
        $persisted = 0;
        $created = 0;

        foreach ($this->diffProcessor->createNewProducts($newProducts, $data) as $product) {
            $this->em->persist($product);
            $created++;
            if (++$persisted == $batchSize) {
                $this->em->flush();
                $this->em->clear();
                $persisted = 0;
            }
        }

        $this->em->flush();
        $this->em->clear();

        $this->logger->info("{$created} products created");
    }
}