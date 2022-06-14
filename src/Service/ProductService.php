<?php

namespace Mytheresa\Challenge\Service;

use Countable;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Persistence\Repository\ProductRepository;

/**
 * Domain service which encapsulates logic of retrieving the products from underlying datasource
 */
class ProductService
{
    public function __construct(private readonly ProductRepository $repository)
    {
    }

    /**
     * Here we use separate arguments in case of reusing this service in different environment (console, for example)
     *
     * @return Countable<Product>
     */
    public function getFiltered(?string $category, ?int $priceLessThan, int $offset): Countable
    {
        return $this->repository->getFiltered(5, $offset, $category, $priceLessThan);
    }
}