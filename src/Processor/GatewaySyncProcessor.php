<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Processor;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Context\BlueMediaContext;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayDefinition;
use BlueMedia\ShopwarePayment\Exception\ClientException;
use BlueMedia\ShopwarePayment\Exception\IntegrationNotEnabledException;
use BlueMedia\ShopwarePayment\Provider\GatewayProvider;
use BlueMedia\ShopwarePayment\Transformer\GatewaySyncTransformer;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;

use function array_map;

class GatewaySyncProcessor
{
    private ClientFactory $clientFactory;

    private EntityRepositoryInterface $gatewayRepository;

    private EntityRepositoryInterface $gatewaySalesChannelActiveRepository;

    private GatewaySyncTransformer $responseTransformer;

    private GatewayProvider $gatewayProvider;

    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        ClientFactory $clientFactory,
        EntityRepositoryInterface $blueMediaGatewayRepository,
        EntityRepositoryInterface $blueMediaGatewaySalesChannelActiveRepository,
        GatewaySyncTransformer $gatewayResponseTransformer,
        GatewayProvider $gatewayProvider
    ) {
        $this->clientFactory = $clientFactory;
        $this->gatewayRepository = $blueMediaGatewayRepository;
        $this->gatewaySalesChannelActiveRepository = $blueMediaGatewaySalesChannelActiveRepository;
        $this->responseTransformer = $gatewayResponseTransformer;
        $this->gatewayProvider = $gatewayProvider;
    }

    /**
     * @throws ClientException|IntegrationNotEnabledException
     */
    public function process(BlueMediaContext $context): void
    {
        $client = $this->clientFactory->createFromPluginConfig($context->getSalesChannelId());

        $response = $client->getGatewayList();

        $payloads = $this->responseTransformer->transform($response, $context);
        if (empty($payloads)) {
            return;
        }

        $writtenEvent = $this->gatewayRepository->upsert($payloads, $context->getContext());

        $this->deactivateOrphanedGateways($writtenEvent, $context);
    }

    private function deactivateOrphanedGateways(EntityWrittenContainerEvent $event, BlueMediaContext $context): void
    {
        $gatewayIds = $event->getPrimaryKeys(GatewayDefinition::ENTITY_NAME);

        $orphanedIds = $this->gatewayProvider->getOrphanedIdsForSalesChannel(
            $gatewayIds,
            $context->getSalesChannelId(),
            $context->getContext()
        );
        if (empty($orphanedIds)) {
            return;
        }

        $payloads = array_map(fn(string $orphanedId) => [
            'gatewayId' => $orphanedId,
            'salesChannelId' => $context->getSalesChannelId(),
        ], $orphanedIds);

        $this->gatewaySalesChannelActiveRepository->delete($payloads, $context->getContext());
    }
}
