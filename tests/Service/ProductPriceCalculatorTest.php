<?php

namespace Mytheresa\Challenge\Tests\Service;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Mytheresa\Challenge\Config\DefaultCurrencyConfig;
use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Service\ProductPriceCalculator;
use PHPUnit\Framework\TestCase;

class ProductPriceCalculatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private string $currency = 'EUR';

    private ProductPriceCalculator $calculator;

    /**
     * @dataProvider provideTestData
     */
    public function testCalculate(?int $productDiscount, ?int $categoryDiscount, ?string $discount, int $originalPrice, int $finalPrice): void
    {
        $product = \Mockery::mock(Product::class, [
            'getPrice'    => $originalPrice,
            'getDiscount' => $productDiscount,
            'getCategory' => \Mockery::mock(Category::class, ['getDiscount' => $categoryDiscount]),
        ]);

        $result = $this->calculator->getPrice($product);

        self::assertEquals($this->currency, $result->getCurrency());
        self::assertEquals($originalPrice, $result->getOriginal());
        self::assertEquals($finalPrice, $result->getFinal());
        self::assertEquals($discount, $result->getDiscountPercentage());
    }

    public function provideTestData(): array
    {
        return [
            [30, 20, '30%', 100, 70],
            [20, 30, '30%', 100, 70],
            [null, 20, '20%', 100, 80],
            [50, null, '50%', 100, 50],
            [null, null, null, 100, 100],
        ];
    }

    protected function setUp(): void
    {
        $config = \Mockery::mock(DefaultCurrencyConfig::class, ['getCurrency' => $this->currency]);
        $this->calculator = new ProductPriceCalculator($config);
    }
}
