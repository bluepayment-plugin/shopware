<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Processor;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Context\BlueMediaContext;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayDefinition;
use BlueMedia\ShopwarePayment\Exception\ClientException;
use BlueMedia\ShopwarePayment\Exception\IntegrationNotEnabledException;
use BlueMedia\ShopwarePayment\Provider\GatewayProvider;
use BlueMedia\ShopwarePayment\Provider\GatewaySalesChannelProvider;
use BlueMedia\ShopwarePayment\Transformer\GatewayResponseTransformer;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;

use function array_map;

class GatewaySyncProcessor
{
    private ClientFactory $clientFactory;

    private EntityRepositoryInterface $gatewayRepository;

    private GatewayResponseTransformer $responseTransformer;

    private GatewayProvider $gatewayProvider;

    private GatewaySalesChannelProvider $gatewaySalesChannelProvider;

    public function __construct(
        ClientFactory $clientFactory,
        EntityRepositoryInterface $blueMediaGatewayRepository,
        GatewayResponseTransformer $gatewayResponseTransformer,
        GatewayProvider $gatewayProvider,
        GatewaySalesChannelProvider $gatewaySalesChannelProvider
    ) {
        $this->clientFactory = $clientFactory;
        $this->gatewayRepository = $blueMediaGatewayRepository;
        $this->responseTransformer = $gatewayResponseTransformer;
        $this->gatewayProvider = $gatewayProvider;
        $this->gatewaySalesChannelProvider = $gatewaySalesChannelProvider;
    }

    /**
     * @throws ClientException|IntegrationNotEnabledException
     */
    public function process(BlueMediaContext $context): void
    {
        $client = $this->clientFactory->createFromPluginConfig($context->getSalesChannelId());

        $response = $client->getPaywayList();

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
            'id' => $orphanedId,
            'gatewaySalesChannels' => [
                [
                    'id' => $this->gatewaySalesChannelProvider->getIdByGatewayAndSalesChannelId(
                        $orphanedId,
                        $context->getSalesChannelId(),
                        $context->getContext()
                    ),
                    'gatewayId' => $orphanedId,
                    'salesChannelId' => $context->getSalesChannelId(),
                    'active' => false,
                ],
            ],
        ], $orphanedIds);

        $this->gatewayRepository->update($payloads, $context->getContext());
    }
}
