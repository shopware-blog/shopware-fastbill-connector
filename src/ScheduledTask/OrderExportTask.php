<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class OrderExportTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'fastbill.export-orders';
    }

    public static function getDefaultInterval(): int
    {
        return 5;
    }
}
