<?php

namespace Mytheresa\Challenge\Tests\Service\Import;

use Mockery\MockInterface;
use Mytheresa\Challenge\DTO\DiscountDTO;
use Mytheresa\Challenge\DTO\DiscountListDTO;
use Mytheresa\Challenge\DTO\ImportDTO;
use Mytheresa\Challenge\DTO\ProductDTO;
use Mytheresa\Challenge\DTO\ProductListDTO;
use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Persistence\Repository\CategoryRepository;
use Mytheresa\Challenge\Persistence\Repository\ProductRepository;
use Mytheresa\Challenge\Service\Import\DataMapper;
use PHPUnit\Framework\TestCase;

class DataMapperTest extends TestCase
{
    private ProductRepository|MockInterface $productRepository;
    private CategoryRepository|MockInterface $categoryRepository;

    private DataMapper $mapper;

    public function testLoadCategories(): void
    {
        $products = array_map([$this, 'mockProduct'], range(0, 5));
        $productsList = \Mockery::mock(ProductListDTO::class, ['getProducts' => $products]);
        $discounts = [
            \Mockery::mock(DiscountDTO::class, ['getSku' => 'sku1', 'getCategory' => null, 'getValue' => 30]),
            \Mockery::mock(DiscountDTO::class, ['getSku' => null, 'getCategory' => 'category3', 'getValue' => 45]),
        ];
        $discountsList = \Mockery::mock(DiscountListDTO::class, ['getDiscounts' => $discounts]);
        $categories = [];
        foreach (range(3, 7) as $i) {
            $categories["category{$i}"] = \Mockery::mock(Category::class);
        }
        $this->categoryRepository->shouldReceive('getMappedByName')->andReturn($categories);

        $result = $this->mapper->loadCategories($productsList, $discountsList);

        self::assertEquals(['sku0', 'sku1', 'sku2', 'sku3', 'sku4', 'sku5'], $result->getSkuList());
        self::assertEquals($categories, $result->getCategories());
        self::assertEquals(['category0', 'category1', 'category2'], $result->getNewCategories());
        self::assertEquals(['sku1' => 30, 'category3' => 45], $result->getDiscounts());
    }

    public function testLoadProducts(): void
    {
        $skuList = ['00001', '00002'];
        $entities = array_map(static fn($i) => \Mockery::mock(Product::class), range(0, 5));
        $data = \Mockery::mock(ImportDTO::class, ['getSkuList' => $skuList]);
        $this->productRepository->shouldReceive('getMappedBySku')->with($skuList)->andReturn($entities);

        $data->shouldReceive('setProducts')->with($entities);

        $this->mapper->loadProducts($data);

        self::assertTrue(true);
    }

    protected function setUp(): void
    {
        $this->productRepository = \Mockery::mock(ProductRepository::class);
        $this->categoryRepository = \Mockery::mock(CategoryRepository::class);
        $this->mapper = new DataMapper($this->productRepository, $this->categoryRepository);
    }

    private function mockProduct(int $i): ProductDTO
    {
        return \Mockery::mock(ProductDTO::class, ['getSku' => "sku{$i}", 'getCategory' => "category{$i}"]);
    }
}
