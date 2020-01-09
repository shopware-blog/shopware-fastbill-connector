<?php declare(strict_types = 1);

namespace ShopwareBlog\FastBillConnector\Command;

use ShopwareBlog\FastBillConnector\Importer\ProductImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductImportCommand extends Command
{
    protected static $defaultName = 'fastbill:import-products';

    /**
     * @var ProductImporter
     */
    private $productImporter;

    public function __construct(ProductImporter $productImporter)
    {
        parent::__construct();
        $this->productImporter = $productImporter;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->productImporter->importProducts();
    }
}
