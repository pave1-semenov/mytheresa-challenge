<?php

namespace Mytheresa\Challenge\Service;

use Mytheresa\Challenge\Config\DefaultCurrencyConfig;
use Mytheresa\Challenge\DTO\ProductPriceDTO;
use Mytheresa\Challenge\Persistence\Entity\Product;

/**
 * This class encapsulates logic for calculating discounts.
 * Here we use a separate configuration for default currency, so we can change it easily without any code changes
 */
class ProductPriceCalculator
{
    public function __construct(private readonly DefaultCurrencyConfig $currencyConfig)
    {
    }

    public function getPrice(Product $product): ProductPriceDTO
    {
        $originalPrice = $product->getPrice();
        $discount = $this->getDiscount($product);
        $finalPrice = $discount ? $originalPrice - ($originalPrice * ($discount / 100)) : $originalPrice;
        $discountString = $discount ? "{$discount}%" : null;

        return new ProductPriceDTO($originalPrice, $finalPrice, $discountString, $this->currencyConfig->getCurrency());
    }

    private function getDiscount(Product $product): ?int
    {
        $personalDiscount = $product->getDiscount() ?: 0;
        $categoryDiscount = $product->getCategory()->getDiscount() ?: 0;
        $discount = max($personalDiscount, $categoryDiscount);

        return $discount > 0 ? $discount : null;
    }
}