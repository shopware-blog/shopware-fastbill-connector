<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ProductImportTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'fastbill.import-products';
    }

    public static function getDefaultInterval(): int
    {
        return 5;
    }
}
