<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class GatewayCurrencyProvider
{
    private EntityRepositoryInterface $gatewayCurrencyRepository;

    public function __construct(
        EntityRepositoryInterface $blueMediaGatewayCurrencyRepository
    ) {
        $this->gatewayCurrencyRepository = $blueMediaGatewayCurrencyRepository;
    }

    public function getIdByCurrencyAndGatewayId(string $currencyId, string $gatewayId, Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('currencyId', $currencyId));
        $criteria->addFilter(new EqualsFilter('gatewayId', $gatewayId));

        return $this->gatewayCurrencyRepository->searchIds($criteria, $context)->firstId();
    }
}
