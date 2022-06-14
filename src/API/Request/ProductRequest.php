<?php

namespace Mytheresa\Challenge\API\Request;

use Mytheresa\Challenge\API\ParamConverter\RequestData;
use Mytheresa\Challenge\API\ParamConverter\RequestType;
use Symfony\Component\Validator\Constraints\{GreaterThanOrEqual};

/**
 * This class represents all possible query params for requests to /products endpoint
 */
#[RequestData(type: RequestType::QUERY)]
class ProductRequest
{
    public function __construct(
        private readonly ?string                       $category = null,
        #[GreaterThanOrEqual(1)] private readonly ?int $priceLessThan = null,
        #[GreaterThanOrEqual(0)] private readonly ?int $offset = null
    )
    {
    }

    public function getOffset(): int
    {
        return $this->offset ?: 0;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getPriceLessThan(): ?int
    {
        return $this->priceLessThan;
    }

    public function getAttributes(): array
    {
        return [
            'category'      => $this->category,
            'priceLessThan' => $this->priceLessThan,
            'offset'        => $this->getOffset()
        ];
    }
}