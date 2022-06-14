<?php

namespace Mytheresa\Challenge\API\Endpoint;

use Mytheresa\Challenge\API\Request\ProductRequest;
use Mytheresa\Challenge\API\Response\ProductResponseFactory;
use Mytheresa\Challenge\Service\ProductService;
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

    #[Route(path: '/products', methods: ['GET'], format: 'json')]
    public function __invoke(ProductRequest $request): JsonResponse
    {
        $products = $this->service->getFiltered(...$request->getAttributes());
        $response = $this->responseFactory->create($products, $request);

        return JsonResponse::fromJsonString($this->serializer->serialize($response, 'json'));
    }
}