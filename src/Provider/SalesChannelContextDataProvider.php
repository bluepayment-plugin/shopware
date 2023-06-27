<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Exception\NoActiveGatewayIdSelected;
use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class SalesChannelContextDataProvider
{
    private GatewayProvider $gatewayProvider;

    private SalesChannelContextPersister $salesChannelContextPersister;

    public function __construct(
        GatewayProvider $gatewayProvider,
        SalesChannelContextPersister $salesChannelContextPersister
    ) {
        $this->gatewayProvider = $gatewayProvider;
        $this->salesChannelContextPersister = $salesChannelContextPersister;
    }

    /**
     * @throws NoActiveGatewayIdSelected
     */
    public function getSelectedGateway(
        SalesChannelContext $salesChannelContext,
        ?float $cartAmount = null
    ): GatewayEntity {
        $id = $this->getSelectedId($salesChannelContext);

        if ($id !== null) {
            $gateway = $this->gatewayProvider->getActiveGatewayById($id, $salesChannelContext, $cartAmount);
            if ($gateway !== null) {
                return $gateway;
            }
        }

        throw new NoActiveGatewayIdSelected();
    }

    public function getSelectedId(SalesChannelContext $salesChannelContext): ?string
    {
        $payload = $this->salesChannelContextPersister->load(
            $salesChannelContext->getToken(),
            $salesChannelContext->getSalesChannelId()
        );

        return (array_key_exists(Constants::SESSION_SELECTED_GATEWAY_ID, $payload))
            ? (string) $payload[Constants::SESSION_SELECTED_GATEWAY_ID]
            : null;
    }
}
