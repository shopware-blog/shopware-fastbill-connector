<?php declare(strict_types=1);

namespace ShopwareBlog\FastBillConnector\Api;

use FastBillSdk\Api\ApiClient as FastBillApiClient;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ApiClientFactory
{
    /**
     * @var string
     */
    private $fastBillUserName;

    /**
     * @var string
     */
    private $fastBillApiKey;

    /**
     * @var FastBillApiClient
     */
    private $fastBillClient;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->fastBillUserName = (string) $systemConfigService->get('FastBillConnector.config.fastBillUserName');
        $this->fastBillApiKey = (string) $systemConfigService->get('FastBillConnector.config.fastBillApiKey');
    }

    public function getApiClient(): FastBillApiClient
    {
        if (!$this->fastBillClient) {
            $this->fastBillClient = new FastBillApiClient($this->fastBillUserName, $this->fastBillApiKey);
        }

        return $this->fastBillClient;
    }
}
