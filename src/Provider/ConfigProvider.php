<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigProvider
{
    private const PLUGIN_CONFIG_NAMESPACE = 'BlueMediaShopwarePayment.config.';
    private const PLUGIN_CONFIG_ENABLED = self::PLUGIN_CONFIG_NAMESPACE . 'enabled';
    private const PLUGIN_CONFIG_TEST_MODE = self::PLUGIN_CONFIG_NAMESPACE . 'testMode';
    private const PLUGIN_CONFIG_SERVICE_ID = self::PLUGIN_CONFIG_NAMESPACE . 'serviceId';
    private const PLUGIN_CONFIG_SHARED_KEY = self::PLUGIN_CONFIG_NAMESPACE . 'sharedKey';
    private const PLUGIN_CONFIG_HASH_ALGO = self::PLUGIN_CONFIG_NAMESPACE . 'hashAlgo';
    private const PLUGIN_CONFIG_GATEWAY_URL = self::PLUGIN_CONFIG_NAMESPACE . 'gatewayUrl';
    private const PLUGIN_CONFIG_TEST_GATEWAY_URL = self::PLUGIN_CONFIG_NAMESPACE . 'testGatewayUrl';
    private const PLUGIN_CONFIG_AUTO_PROCESS_ORDERS_STATUS = self::PLUGIN_CONFIG_NAMESPACE . 'autoProcessOrderStatus';

    private SystemConfigService $systemConfigService;

    private EntityRepositoryInterface $salesChannelRepository;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $salesChannelRepository
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->salesChannelRepository = $salesChannelRepository;
    }

    /**
     * @param Context $context
     * @return array
     */
    public function getEnabledSalesChannelIds(Context $context): array
    {
        $salesChannelIds = $this->salesChannelRepository->searchIds(new Criteria(), $context)->getIds();

        return array_filter($salesChannelIds, fn(string $salesChannelId) => $this->isEnabled($salesChannelId));
    }

    public function isEnabled(?string $salesChannelId = null): bool
    {
        return $this->systemConfigService->getBool(self::PLUGIN_CONFIG_ENABLED, $salesChannelId);
    }

    public function isTestMode(?string $salesChannelId = null): bool
    {
        return $this->systemConfigService->getBool(self::PLUGIN_CONFIG_TEST_MODE, $salesChannelId);
    }

    public function getServiceId(?string $salesChannelId = null): int
    {
        return $this->systemConfigService->getInt(self::PLUGIN_CONFIG_SERVICE_ID, $salesChannelId);
    }

    public function getSharedKey(?string $salesChannelId = null): string
    {
        return $this->systemConfigService->getString(self::PLUGIN_CONFIG_SHARED_KEY, $salesChannelId);
    }

    public function getHashAlgorithm(?string $salesChannelId = null): string
    {
        return $this->systemConfigService->getString(self::PLUGIN_CONFIG_HASH_ALGO, $salesChannelId);
    }

    public function getGatewayUrl(?string $salesChannelId = null): string
    {
        $this->isTestMode($salesChannelId)
            ? $configKey = self::PLUGIN_CONFIG_TEST_GATEWAY_URL
            : $configKey = self::PLUGIN_CONFIG_GATEWAY_URL;

        return $this->systemConfigService->getString($configKey, $salesChannelId);
    }

    public function isOrderStatusProcessingEnabled(?string $salesChannelId = null): bool
    {
        return $this->isEnabled($salesChannelId)
            && $this->systemConfigService->getBool(self::PLUGIN_CONFIG_AUTO_PROCESS_ORDERS_STATUS, $salesChannelId);
    }
}
