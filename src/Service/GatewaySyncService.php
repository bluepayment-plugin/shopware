<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Service;

use BlueMedia\ShopwarePayment\Context\BlueMediaContextFactory;
use BlueMedia\ShopwarePayment\Processor\GatewaySyncProcessor;
use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Throwable;

class GatewaySyncService
{
    private ConfigProvider $configProvider;

    private BlueMediaContextFactory $contextFactory;

    private GatewaySyncProcessor $processor;

    private LoggerInterface $logger;

    public function __construct(
        ConfigProvider $configProvider,
        BlueMediaContextFactory $contextFactory,
        GatewaySyncProcessor $processor,
        LoggerInterface $blueMediaGatewaySyncLogger
    ) {
        $this->configProvider = $configProvider;
        $this->contextFactory = $contextFactory;
        $this->processor = $processor;
        $this->logger = $blueMediaGatewaySyncLogger;
    }

    public function syncGateways(Context $context): void
    {
        $salesChannelIds = $this->configProvider->getEnabledSalesChannelIds($context);

        foreach ($salesChannelIds as $salesChannelId) {
            $bmContext = $this->contextFactory->create($salesChannelId, $context);

            try {
                $this->processor->process($bmContext);
            } catch (Throwable $e) {
                $this->logger->error('Failed to process Blue Media Gateways.', [
                    'salesChannelId' => $salesChannelId,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
