<?php

namespace Mytheresa\Challenge\Tests\API\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery\MockInterface;
use Mytheresa\Challenge\API\Request\ProductRequest;
use Mytheresa\Challenge\API\Response\ProductResponseFactory;
use Mytheresa\Challenge\DTO\ProductPriceDTO;
use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Service\ProductPriceCalculator;
use PHPUnit\Framework\TestCase;

class ProductResponseFactoryTest extends TestCase
{
    private ProductPriceCalculator|MockInterface $calculator;
    private ProductResponseFactory $factory;

    public function testCreate(): void
    {
        /** @var Product[] $products */
        $products = array_map([$this, 'mockProduct'], range(0, 5));
        $container = new ArrayCollection($products);
        $offset = 2;
        $request = \Mockery::mock(ProductRequest::class, ['getOffset' => $offset]);

        $actual = $this->factory->create($container, $request);

        self::assertSameSize($container, $actual->getProducts());
        self::assertEquals($container->count(), $actual->getMeta()->getCount());
        self::assertEquals($container->count(), $actual->getMeta()->getTotalCount());
        self::assertEquals($offset, $actual->getMeta()->getOffset());

        foreach ($products as $idx => $product) {
            $target = $actual->getProducts()[$idx];
            self::assertEquals($product->getName(), $target->getName());
            self::assertEquals($product->getSku(), $target->getSku());
            self::assertEquals($product->getCategory()->getName(), $target->getCategory());
            self::assertEquals($idx, $target->getPrice()->getOriginal());
        }
    }

    protected function mockProduct(int $idx): Product
    {
        $id = (string)$idx;

        $mock = \Mockery::mock(Product::class, [
            'getSku'      => $id,
            'getName'     => $id,
            'getCategory' => \Mockery::mock(Category::class, ['getName' => $id])
        ]);
        $price = \Mockery::mock(ProductPriceDTO::class, ['getOriginal' => $idx]);

        $this->calculator->shouldReceive('getPrice')->with($mock)->andReturn($price);

        return $mock;
    }

    protected function setUp(): void
    {
        $this->calculator = \Mockery::mock(ProductPriceCalculator::class);
        $this->factory = new ProductResponseFactory($this->calculator);
    }
}
