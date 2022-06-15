<?php

namespace Mytheresa\Challenge\Tests\Service\Import;

use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use Mytheresa\Challenge\DTO\DiscountListDTO;
use Mytheresa\Challenge\DTO\ImportDTO;
use Mytheresa\Challenge\DTO\ProductDTO;
use Mytheresa\Challenge\DTO\ProductListDTO;
use Mytheresa\Challenge\Persistence\Entity\Category;
use Mytheresa\Challenge\Persistence\Entity\Product;
use Mytheresa\Challenge\Service\Import\DataMapper;
use Mytheresa\Challenge\Service\Import\ProductDiffProcessor;
use Mytheresa\Challenge\Service\Import\ProductImportService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use function Webmozart\Assert\Tests\StaticAnalysis\true;

class ProductImportServiceTest extends TestCase
{
    private DataMapper|MockInterface $dataMapper;
    private ProductDiffProcessor|MockInterface $diffProcessor;
    private EntityManagerInterface|MockInterface $em;

    private ProductImportService $service;

    public function testImport()
    {
        $productsData = [];
        foreach (range(0, 20) as $i) {
            $product = \Mockery::mock(ProductDTO::class);
            $productsData["sku$i"] = $product;
        }

        $list = \Mockery::mock(ProductListDTO::class, ['getProducts' => $productsData]);
        $discounts = \Mockery::mock(DiscountListDTO::class);
        $data = \Mockery::mock(ImportDTO::class);

        $list->shouldReceive('mapProducts');
        $this->dataMapper->shouldReceive('loadCategories')->with($list, $discounts)->andReturn($data);

        $categories = array_map(function ($i) {
            $cat = \Mockery::mock(Category::class);
            $this->em->shouldReceive('persist')->with($cat)->once();

            return $cat;
        }, range(0, 6));
        $this->diffProcessor->shouldReceive('updateCategories')->with($data)->andReturn(array_slice($categories, 0, 3));
        $this->diffProcessor->shouldReceive('createNewCategories')->with($data)->andReturn(array_slice($categories, 3));

        $this->dataMapper->shouldReceive('loadProducts')->with($data);
        $products = [];
        foreach (range(0, 20) as $i) {
            $product = \Mockery::mock(Product::class, ['getSku' => "sku{$i}"]);
            $products["sku{$i}"] = $product;
            $this->em->shouldReceive('persist')->with($product)->once();
        }
        $this->diffProcessor->shouldReceive('updateProducts')->with($data, $list)->andReturn(array_slice($products, 0, 10));

        $newProducts = array_slice($productsData, 10);
        $this->diffProcessor->shouldReceive('createNewProducts')->with($newProducts, $data)->andReturn(array_slice($products, 10));

        // 1 for categories + 3 batches for update + 1 final + 3 batches for create + 1 final
        $this->em->shouldReceive('flush')->times(9);
        $this->em->shouldReceive('clear')->times(9);

        $this->service->import($list, $discounts, 3);

        self::assertTrue(true);
    }

    protected function setUp(): void
    {
        $this->dataMapper = \Mockery::mock(DataMapper::class);
        $this->diffProcessor = \Mockery::mock(ProductDiffProcessor::class);
        $this->em = \Mockery::mock(EntityManagerInterface::class);
        $this->service = new ProductImportService($this->dataMapper, $this->diffProcessor, $this->em);
        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info');
        $this->service->setLogger($logger);
    }
}
