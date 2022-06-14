<?php

namespace Mytheresa\Challenge\Tests\Persistence\Factory;

use Mytheresa\Challenge\DTO\ProductDTO;
use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Persistence\Factory\ProductFactory;
use PHPUnit\Framework\TestCase;

class ProductFactoryTest extends TestCase
{
    private ProductFactory $factory;

    public function testFill()
    {
        $dto = new ProductDTO('00001', 'Test product', 'test', 5000000);
        $category = \Mockery::mock(Category::class);
        $discount = 15;

        $entity = \Mockery::mock(Product::class);
        $entity->shouldReceive('setName')->with($dto->getName())->andReturnSelf();
        $entity->shouldReceive('setPrice')->with($dto->getPrice())->andReturnSelf();
        $entity->shouldReceive('setDiscount')->with($discount)->andReturnSelf();
        $entity->shouldReceive('setCategory')->with($category)->andReturnSelf();


        $actual = $this->factory->fill($entity, $dto, $category, $discount);

        self::assertEquals($entity, $actual);
    }

    public function testCreate()
    {
        $dto = new ProductDTO('00001', 'Test product', 'test', 5000000);

        $actual = $this->factory->create($dto);

        self::assertEquals($dto->getSku(), $actual->getSku());
    }

    protected function setUp(): void
    {
        $this->factory = new ProductFactory();
    }
}
