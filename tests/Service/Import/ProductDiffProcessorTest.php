<?php

namespace Mytheresa\Challenge\Tests\Service\Import;

use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use Mytheresa\Challenge\DTO\ImportDTO;
use Mytheresa\Challenge\DTO\ProductDTO;
use Mytheresa\Challenge\DTO\ProductListDTO;
use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Persistence\Factory\CategoryFactory;
use Mytheresa\Challenge\Persistence\Factory\ProductFactory;
use Mytheresa\Challenge\Service\Import\ProductDiffProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class ProductDiffProcessorTest extends TestCase
{
    private CategoryFactory|MockInterface $categoryFactory;
    private ProductFactory|MockInterface $productFactory;
    private EntityManagerInterface|MockInterface $em;

    private ProductDiffProcessor $processor;

    public function testCreateNewCategories()
    {
        $newCategories = ['cat1', 'cat2', 'cat3'];
        $data = \Mockery::mock(ImportDTO::class, ['getNewCategories' => $newCategories]);
        $categories = [];

        foreach ($newCategories as $name) {
            $category = \Mockery::mock(Category::class, ['getName' => $name]);
            $this->categoryFactory->shouldReceive('create')->with($name)->andReturn($category);
            $data->shouldReceive('getDiscount')->with($name)->andReturn(10);
            $this->categoryFactory->shouldReceive('fill')->with($category, 10)->andReturn($category);
            $data->shouldReceive('addCategory')->with($category);
            $categories[] = $category;
        }

        $actual = $this->processor->createNewCategories($data);

        foreach ($actual as $idx => $category) {
            self::assertEquals($categories[$idx], $category);
        }
    }


    public function testCreateNewProducts()
    {
        $products = $items = [];
        $data = \Mockery::mock(ImportDTO::class);

        foreach (range(0, 5) as $i) {
            $sku = "sku{$i}";
            $catName = "cat{$i}";
            $product = \Mockery::mock(Product::class, ['getSku' => $sku]);
            $category = \Mockery::mock(Category::class, ['getId' => new UuidV4()]);
            $item = \Mockery::mock(ProductDTO::class, ['getSku' => $sku, 'getCategory' => $catName]);
            $this->productFactory->shouldReceive('create')->with($item)->andReturn($product);

            $data->shouldReceive('getCategory')->with($catName)->andReturn($category);
            $data->shouldReceive('getDiscount')->with($sku)->andReturn($i);
            $this->em->shouldReceive('getReference')->with(Category::class, $category->getId())
                ->andReturn($category);
            $this->productFactory->shouldReceive('fill')->with($product, $item, $category, $i)->andReturn($product);
            $products[] = $product;
            $items[] = $item;
        }

        $actual = $this->processor->createNewProducts($items, $data);

        foreach ($actual as $idx => $product) {
            self::assertEquals($products[$idx], $product);
        }
    }

    public function testUpdateCategories()
    {
        $data = \Mockery::mock(ImportDTO::class);
        $categories = [];

        foreach (range(0, 3) as $idx) {
            $category = \Mockery::mock(Category::class, ['getName' => "cat{$idx}"]);
            $data->shouldReceive('getDiscount')->with("cat{$idx}")->andReturn(10);
            $this->categoryFactory->shouldReceive('fill')->with($category, 10)->andReturn($category);
            $categories[] = $category;
        }

        $data->shouldReceive('getCategories')->andReturn($categories);

        $actual = $this->processor->updateCategories($data);

        foreach ($actual as $idx => $category) {
            self::assertEquals($categories[$idx], $category);
        }
    }

    public function testUpdateProducts()
    {
        $products = [];
        $list = \Mockery::mock(ProductListDTO::class);
        $data = \Mockery::mock(ImportDTO::class);

        foreach (range(0, 5) as $i) {
            $sku = "sku{$i}";
            $catName = "cat{$i}";
            $product = \Mockery::mock(Product::class, ['getSku' => $sku]);
            $category = \Mockery::mock(Category::class, ['getId' => new UuidV4()]);
            $item = \Mockery::mock(ProductDTO::class, ['getSku' => $sku, 'getCategory' => $catName]);
            $list->shouldReceive('getProduct')->with($sku)->andReturn($item);

            $data->shouldReceive('getCategory')->with($catName)->andReturn($category);
            $data->shouldReceive('getDiscount')->with($sku)->andReturn($i);
            $this->em->shouldReceive('getReference')->with(Category::class, $category->getId())
                ->andReturn($category);
            $this->productFactory->shouldReceive('fill')->with($product, $item, $category, $i)->andReturn($product);
            $products[] = $product;
        }
        $data->shouldReceive('getProducts')->andReturn($products);

        $actual = $this->processor->updateProducts($data, $list);

        foreach ($actual as $idx => $product) {
            self::assertEquals($products[$idx], $product);
        }
    }

    protected function setUp(): void
    {
        $this->categoryFactory = \Mockery::mock(CategoryFactory::class);
        $this->productFactory = \Mockery::mock(ProductFactory::class);
        $this->em = \Mockery::mock(EntityManagerInterface::class);
        $this->processor = new ProductDiffProcessor($this->categoryFactory, $this->productFactory, $this->em);
    }


}
