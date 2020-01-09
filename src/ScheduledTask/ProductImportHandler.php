<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector\ScheduledTask;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use ShopwareBlog\FastBillConnector\Importer\ProductImporter;

class ProductImportHandler extends ScheduledTaskHandler
{
    /**
     * @var ProductImporter
     */
    private $productImporter;

    public function __construct(EntityRepositoryInterface $scheduledTaskRepository, ProductImporter $productImporter)
    {
        parent::__construct($scheduledTaskRepository);
        $this->productImporter = $productImporter;
    }

    public static function getHandledMessages(): iterable
    {
        return [ProductImportTask::class];
    }

    public function run(): void
    {
        $this->productImporter->importProducts();
    }
}
