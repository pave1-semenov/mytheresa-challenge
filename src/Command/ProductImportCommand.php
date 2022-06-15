<?php

namespace Mytheresa\Challenge\Command;

use Mytheresa\Challenge\DTO\DiscountListDTO;
use Mytheresa\Challenge\DTO\ProductListDTO;
use Mytheresa\Challenge\Service\Import\ProductImportService;
use Mytheresa\Challenge\Utils\JsonResourceReader;
use Mytheresa\Challenge\Utils\LoggerTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;

/**
 * This command serves as an entrypoint for data import process.
 * We store some initial data in repository, command considers it as a default
 */
#[AsCommand(name: "app:import", description: "Console command to import products from json file source")]
class ProductImportCommand extends Command
{
    use LoggerTrait;

    public function __construct(
        private readonly string               $resourcesDir,
        private readonly JsonResourceReader   $reader,
        private readonly ProductImportService $service
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'file',
            'f',
            InputOption::VALUE_REQUIRED,
            'Absolute path to file to process. Can also be a web URI',
            "{$this->resourcesDir}/products.json"
        );
        $this->addOption(
            'discounts_file',
            'df',
            InputOption::VALUE_REQUIRED,
            'Absolute path to file with discounts data. Can also be a web URI',
            "{$this->resourcesDir}/discounts.json"
        );
        $this->addOption(
            'batch_size',
            'bs',
            InputOption::VALUE_OPTIONAL,
            'Maximum number of database operations to process in one transaction',
            500
        );
        $this->addUsage('Imports products data from specified file to underlying database');
    }

    /**
     * @throws SerializerException
     * @throws IOExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getOption('file');
        $data = $this->reader->read($filePath, ProductListDTO::class);
        $discountFilePath = $input->getOption('discounts_file');
        $discounts = $this->reader->read($discountFilePath, DiscountListDTO::class);
        $batchSize = $input->getOption('batch_size');
        $this->logger->info("Import started, product file {$filePath}, discounts file {$discountFilePath}, batch size ${batchSize}");

        $this->service->import($data, $discounts, $batchSize);
        $this->logger->info("Import finished");

        return self::SUCCESS;
    }
}