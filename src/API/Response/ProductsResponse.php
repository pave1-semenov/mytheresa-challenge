<?php

namespace Mytheresa\Challenge\API\Response;

class ProductsResponse
{
    public function __construct(
        private readonly array        $products,
        private readonly ResponseMeta $meta
    )
    {
    }

    /**
     * @return ProductItem[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getMeta(): ResponseMeta
    {
        return $this->meta;
    }
}