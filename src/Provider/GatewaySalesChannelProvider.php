<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class GatewaySalesChannelProvider
{
    private EntityRepositoryInterface $gatewaySalesChannelRepository;

    public function __construct(
        EntityRepositoryInterface $blueMediaGatewaySalesChannelRepository
    ) {
        $this->gatewaySalesChannelRepository = $blueMediaGatewaySalesChannelRepository;
    }

    public function getIdByGatewayAndSalesChannelId(
        string $gatewayId,
        string $salesChannelId,
        Context $context
    ): ?string {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('gatewayId', $gatewayId));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));

        return $this->gatewaySalesChannelRepository->searchIds($criteria, $context)->firstId();
    }
}
