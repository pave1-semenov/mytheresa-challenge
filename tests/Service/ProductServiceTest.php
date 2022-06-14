<?php

namespace Mytheresa\Challenge\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Persistence\Repository\ProductRepository;
use Mytheresa\Challenge\Service\ProductService;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ProductRepository|MockInterface $repository;
    private ProductService $service;

    public function testGetFiltered(): void
    {
        $category = 'test-category';
        $price = 500;
        $offset = 2;

        $expected = new ArrayCollection(array_map(static fn($i) => \Mockery::mock(Product::class), range(0, 5)));
        $this->repository->shouldReceive('getFiltered')->with(5, $offset, $category, $price)->andReturn($expected);

        $actual = $this->service->getFiltered($category, $price, $offset);

        self::assertEquals($expected, $actual);
    }

    protected function setUp(): void
    {
        $this->repository = \Mockery::mock(ProductRepository::class);
        $this->service = new ProductService($this->repository);
    }
}
