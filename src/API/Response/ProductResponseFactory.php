<?php

namespace Mytheresa\Challenge\API\Response;

use Countable;
use Mytheresa\Challenge\API\Request\ProductRequest;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Service\ProductPriceCalculator;

/**
 * This factory encapsulates logic of transforming Product entities
 * Into response class of appropriate format
 */
class ProductResponseFactory
{
    public function __construct(private readonly ProductPriceCalculator $priceCalculator)
    {
    }

    /**
     * Apart from actual product models response contains metadata with additional pagination information
     * So the API consumer may extract all the products it needs by incrementing offset
     *
     * @param Countable<Product> $items
     */
    public function create(Countable $items, ProductRequest $request): ProductsResponse
    {
        $products = [];
        foreach ($items as $item) {
            $products[] = $this->createItem($item);
        }
        $meta = new ResponseMeta(count($products), $items->count(), $request->getOffset());

        return new ProductsResponse($products, $meta);
    }

    private function createItem(Product $product): ProductItem
    {
        return new ProductItem(
            $product->getSku(),
            $product->getName(),
            $product->getCategory()->getName(),
            $this->priceCalculator->getPrice($product)
        );
    }
}