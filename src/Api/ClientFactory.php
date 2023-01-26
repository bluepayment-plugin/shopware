<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api;

use BlueMedia\Common\Enum\ClientEnum;
use BlueMedia\ShopwarePayment\Exception\IntegrationNotEnabledException;
use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use Psr\Log\LoggerInterface;

class ClientFactory
{
    private LoggerInterface $apiLogger;

    private ConfigProvider $configProvider;

    public function __construct(
        LoggerInterface $blueMediaApiLogger,
        ConfigProvider $configProvider
    ) {
        $this->apiLogger = $blueMediaApiLogger;
        $this->configProvider = $configProvider;
    }

    /**
     * @throws IntegrationNotEnabledException
     */
    public function createFromPluginConfig(?string $salesChannelId = null): Client
    {
        if (false === $this->configProvider->isEnabled($salesChannelId)) {
            throw new IntegrationNotEnabledException($salesChannelId);
        }

        return $this->create(
            (string)$this->configProvider->getServiceId($salesChannelId),
            $this->configProvider->getSharedKey($salesChannelId),
            $this->configProvider->getGatewayUrl($salesChannelId),
            $this->configProvider->getHashAlgorithm($salesChannelId)
        );
    }

    public function createDummyClient(): Client
    {
        return $this->create('', '', '');
    }

    public function create(
        string $serviceId,
        string $sharedKey,
        string $gatewayUrl,
        $hashMode = ClientEnum::HASH_SHA256,
        ?LoggerInterface $blueMediaApi = null
    ): Client {
        return new Client(
            $serviceId,
            $sharedKey,
            $gatewayUrl,
            $blueMediaApi ?? $this->apiLogger,
            $hashMode
        );
    }
}
