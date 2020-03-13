<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector\ScheduledTask;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use ShopwareBlog\FastBillConnector\Exporter\OrderExporter;

class OrderExportHandler extends ScheduledTaskHandler
{
    /**
     * @var OrderExporter
     */
    private $orderExporter;

    public function __construct(EntityRepositoryInterface $scheduledTaskRepository, OrderExporter $orderExporter)
    {
        parent::__construct($scheduledTaskRepository);
        $this->orderExporter = $orderExporter;
    }

    public static function getHandledMessages(): iterable
    {
        return [OrderExportTask::class];
    }

    public function run(): void
    {
        $this->orderExporter->exportOrders();
    }
}
