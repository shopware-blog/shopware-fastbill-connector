<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class FastBillConnector extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        /** @var EntityRepositoryInterface $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $count = $customFieldSetRepository->searchIds(
            (new Criteria())->addFilter(new EqualsFilter('name', 'swb_fastbill_product')),
            Context::createDefaultContext()
        )->getTotal();

        if ($count !== 0) {
            return;
        }

        $customFieldSetRepository->upsert(
            [
                [
                    'name' => 'swb_fastbill_product',
                    'customFields' => [
                        [
                            'name' => 'swb_fastbill_id',
                            'type' => CustomFieldTypes::INT,
                        ],
                    ],
                    'relations' => [
                        ['entityName' => 'product'],
                    ],
                ],
                [
                    'name' => 'swb_fastbill_order',
                    'customFields' => [
                        [
                            'name' => 'swb_fastbill_invoice_id',
                            'type' => CustomFieldTypes::INT,
                        ],
                    ],
                    'relations' => [
                        ['entityName' => 'order'],
                    ],
                ],
            ], Context::createDefaultContext()
        );
    }
}

require_once __DIR__ . '/../vendor/autoload.php';
