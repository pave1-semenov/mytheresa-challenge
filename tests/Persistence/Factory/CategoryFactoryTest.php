<?php

namespace Mytheresa\Challenge\Tests\Persistence\Factory;

use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Factory\CategoryFactory;
use PHPUnit\Framework\TestCase;

class CategoryFactoryTest extends TestCase
{
    private CategoryFactory $factory;

    public function testCreate()
    {
        $name = 'test';

        $actual = $this->factory->create($name);

        self::assertEquals($name, $actual->getName());
    }

    public function testFill()
    {
        $category = \Mockery::mock(Category::class);
        $discount = 23;
        $category->shouldReceive('setDiscount')->with($discount)->andReturnSelf();

        $actual = $this->factory->fill($category, $discount);

        self::assertEquals($category, $actual);
    }

    protected function setUp(): void
    {
        $this->factory = new CategoryFactory();
    }
}
