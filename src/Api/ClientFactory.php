<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api;

use BlueMedia\Common\Enum\ClientEnum;
use BlueMedia\ShopwarePayment\Api\Transformer\DtoTransformer;
use BlueMedia\ShopwarePayment\BlueMediaShopwarePayment;
use BlueMedia\ShopwarePayment\Exception\IntegrationNotEnabledException;
use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\PluginEntity;

class ClientFactory
{
    private LoggerInterface $apiLogger;

    private ConfigProvider $configProvider;

    private DtoTransformer $dtoTransformer;

    private EntityRepositoryInterface $pluginRepository;

    private string $shopwareVersion;

    public function __construct(
        LoggerInterface $blueMediaApiLogger,
        ConfigProvider $configProvider,
        DtoTransformer $dtoTransformer,
        EntityRepositoryInterface $pluginRepository,
        string $shopwareVersion
    ) {
        $this->apiLogger = $blueMediaApiLogger;
        $this->configProvider = $configProvider;
        $this->dtoTransformer = $dtoTransformer;
        $this->pluginRepository = $pluginRepository;
        $this->shopwareVersion = $shopwareVersion;
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
        ?GuzzleClientInterface $httpClient = null,
        ?LoggerInterface $logger = null
    ): Client {
        return new Client(
            $this->shopwareVersion,
            $this->getBlueMediaPluginVersion(),
            $serviceId,
            $sharedKey,
            $gatewayUrl,
            $logger ?? $this->apiLogger,
            $httpClient ?? new GuzzleClient(),
            $this->dtoTransformer,
            $hashMode
        );
    }

    private function getBlueMediaPluginVersion(): string
    {
        $technicalName = explode('\\', BlueMediaShopwarePayment::class);
        $technicalName = array_pop($technicalName);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('baseClass', BlueMediaShopwarePayment::class));

        $entity = $this->pluginRepository->search($criteria, $this->getContext())->first();
        if (!$entity instanceof PluginEntity) {
            return '';
        }

        return $entity->getVersion();
    }

    private function getContext(): Context
    {
        return new Context(new SystemSource());
    }
}
