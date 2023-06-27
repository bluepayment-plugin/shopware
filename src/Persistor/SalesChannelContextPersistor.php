<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Persistor;

use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class SalesChannelContextPersistor
{
    private SalesChannelContextPersister $salesChannelContextPersister;

    public function __construct(
        SalesChannelContextPersister $salesChannelContextPersister
    ) {
        $this->salesChannelContextPersister = $salesChannelContextPersister;
    }

    public function persistBlueMediaGateway(string $id, SalesChannelContext $salesChannelContext): void
    {
        $this->salesChannelContextPersister->save(
            $salesChannelContext->getToken(),
            [Constants::SESSION_SELECTED_GATEWAY_ID => $id],
            $salesChannelContext->getSalesChannel()->getId(),
            ($salesChannelContext->getCustomer()) ? $salesChannelContext->getCustomer()->getId() : null
        );
    }
}
