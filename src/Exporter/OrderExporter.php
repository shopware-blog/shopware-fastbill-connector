<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector\Exporter;

use FastBillSdk\Customer\CustomerEntity;
use FastBillSdk\Customer\CustomerSearchStruct;
use FastBillSdk\Customer\CustomerService;
use FastBillSdk\Invoice\InvoiceEntity;
use FastBillSdk\Invoice\InvoiceService;
use FastBillSdk\Item\ItemEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class OrderExporter
{
    /**
     * @var EntityRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var CustomerService
     */
    private $customerService;

    public function __construct(
        EntityRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        CustomerService $customerService
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->customerService = $customerService;
    }

    public function exportOrders(): bool
    {
        $defaultContext = Context::createDefaultContext();
        $orderCriteria = new Criteria();
        $orderCriteria->addAssociations(['addresses', 'lineItems']);
        $orders = $this->orderRepository->search(
            ($orderCriteria),
            $defaultContext
        );

        /** @var OrderEntity $order */
        foreach ($orders as $order) {
            if ($order->getCustomFields()['swb_fastbill_invoice_id'] > 0) {
                continue;
            }

            $fbInvoice = new InvoiceEntity();
            $fbInvoice->customerId = $this->getCustomerId($order);

            /** @var OrderLineItemEntity $lineItem */
            foreach ($order->getLineItems() as $lineItem) {
                $fbItem = new ItemEntity();
                $fbItem->description = $lineItem->getLabel();
                $fbItem->vatPercent = $lineItem->getPrice()->getCalculatedTaxes()->first()->getTaxRate();
                $fbItem->unitPrice = $lineItem->getPrice()->getUnitPrice() -
                    ($lineItem->getPrice()->getCalculatedTaxes()->getAmount() / $lineItem->getPrice()->getQuantity());
                $fbItem->quantity = $lineItem->getPrice()->getQuantity();
                $fbInvoice->items[] = $fbItem;
            }

            $fbInvoice = $this->invoiceService->createInvoice($fbInvoice);

            $this->orderRepository->upsert(
                [
                    [
                        'id' => $order->getId(),
                        'customFields' => [
                            'swb_fastbill_invoice_id' => (int) $fbInvoice->invoiceId,
                        ],
                    ],
                ],
                $defaultContext
            );
        }

        return true;
    }

    private function getCustomerId(OrderEntity $order)
    {
        /** @var OrderCustomerEntity $orderCustomer */
        $orderCustomer = $order->getOrderCustomer();
        $searchStruct = new CustomerSearchStruct();

        $searchStruct->setCustomerNumberFilter($orderCustomer->getCustomerNumber());
        $customer = $this->customerService->getCustomer($searchStruct);
        if (count($customer) > 0) {
            return $customer[0]->customerId;
        }

        $newCustomer = new CustomerEntity();
        $newCustomer->customerType = 'consumer';
        $newCustomer->customerNumber = $orderCustomer->getCustomerNumber();
        $newCustomer->firstName = $orderCustomer->getFirstName();
        $newCustomer->lastName = $orderCustomer->getLastName();
        $address = $order->getAddresses()->first();
        $newCustomer->address = $address->getStreet();
        $newCustomer->zipcode = $address->getZipcode();
        $newCustomer->city = $address->getCity();

        $newCustomer = $this->customerService->createCustomer($newCustomer);

        return $newCustomer->customerId;
    }
}
