<?php

namespace Mytheresa\Challenge\API\Endpoint;

use Mytheresa\Challenge\API\Request\ProductRequest;
use Mytheresa\Challenge\API\Response\ProductResponseFactory;
use Mytheresa\Challenge\API\Response\ProductsResponse;
use Mytheresa\Challenge\Service\ProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductEndpoint
{
    public function __construct(
        private readonly ProductService         $service,
        private readonly ProductResponseFactory $responseFactory,
        private readonly SerializerInterface    $serializer
    )
    {

    }

    /**
     * List product models
     *
     * Provides at most 5 items, filtered by query string parameters
     */
    #[Route(path: '/products', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Returns at most 5 products',
        content: new OA\JsonContent(
            ref: new Model(type: ProductsResponse::class)
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Error response for incorrect filter values',
        content: new OA\JsonContent(
            properties: [
                new OA\Property('type', type: 'string'),
                new OA\Property('title', type: 'string'),
                new OA\Property('status', type: 'integer'),
                new OA\Property('detail', type: 'string'),
            ],
            type: 'object'
        )
    )]
    #[OA\Parameter(
        name: 'category',
        description: 'Filters orders by category',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'priceLessThan',
        description: 'Filters out orders with price lower than or equal to value',
        in: 'query',
        schema: new OA\Schema(type: 'integer', minimum: 1)
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Pagination offset',
        in: 'query',
        schema: new OA\Schema(type: 'integer', minimum: 0)
    )]
    #[OA\Tag(name: 'products')]
    public function __invoke(ProductRequest $request): JsonResponse
    {
        $products = $this->service->getFiltered(...$request->getAttributes());
        $response = $this->responseFactory->create($products, $request);

        return JsonResponse::fromJsonString($this->serializer->serialize($response, 'json'));
    }
}