<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector\Importer;

use FastBillSdk\Product\ProductSearchStruct;
use FastBillSdk\Product\ProductService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductImporter
{
    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $taxRepository;

    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(
        EntityRepositoryInterface $productRepository,
        EntityRepositoryInterface $taxRepository,
        ProductService $productService
    ) {
        $this->productRepository = $productRepository;
        $this->taxRepository = $taxRepository;
        $this->productService = $productService;
    }

    public function importProducts(): void
    {
        $products = [];
        $searchStruct = new ProductSearchStruct();

        foreach ($this->productService->getProducts($searchStruct) as $product) {
            $taxId = $this->taxRepository->search(
                (new Criteria())->addFilter(new EqualsFilter('taxRate', $product->vatPercent)),
                Context::createDefaultContext()
            )->getEntities()->first()->getId();

            $productIdEntity = $this->productRepository->search(
                (new Criteria())->addFilter(new EqualsFilter('customFields.swb_fastbill_id', $product->articleId)),
                Context::createDefaultContext()
            )->getEntities();

            $productId = false;
            if ($productIdEntity->count() === 1) {
                $productId = $productIdEntity->first()->getId();
            }

            $products[] = [
                'id' => $productId,
                'taxId' => $taxId,
                'productNumber' => $product->articleNumber,
                'stock' => 0,
                'price' => [
                    [
                        'currencyId' => Defaults::CURRENCY,
                        'gross' => $product->unitPrice * (100 + $product->vatPercent) / 100,
                        'net' => $product->unitPrice,
                        'linked' => false,
                    ],
                ],
                'name' => $product->title,
                'description' => $product->description,
                'customFields' => [
                    'swb_fastbill_id' => (int) $product->articleId,
                ],
            ];
        }

        $this->productRepository->upsert(
            $products,
            Context::createDefaultContext()
        );
    }
}
