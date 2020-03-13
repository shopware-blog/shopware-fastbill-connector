<?php declare(strict_types = 1);

namespace ShopwareBlog\FastBillConnector\Command;

use ShopwareBlog\FastBillConnector\Exporter\OrderExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderExportCommand extends Command
{
    protected static $defaultName = 'fastbill:export-orders';

    /**
     * @var OrderExporter
     */
    private $orderExporter;

    public function __construct(OrderExporter $orderExporter)
    {
        parent::__construct();
        $this->orderExporter = $orderExporter;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->orderExporter->exportOrders();
    }
}
