<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class FastBillConnector extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $customFieldSetRepository->create(
            [
                [
                    'name' => 'swb_fastbill_product',
                    'customFields' => [
                        ['name' => 'swb_fastbill_id', 'type' => CustomFieldTypes::INT],
                    ],
                    'relations' => [
                        ['entityName' => 'product'],
                    ],
                ],
            ], Context::createDefaultContext()
        );
    }
}

require_once __DIR__ . '/../vendor/autoload.php';
