<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class CurrencyProvider
{
    private EntityRepositoryInterface $currencyRepository;

    public function __construct(
        EntityRepositoryInterface $currencyRepository
    ) {
        $this->currencyRepository = $currencyRepository;
    }

    public function getIdByIso(string $iso, Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('isoCode', $iso));

        return $this->currencyRepository->searchIds($criteria, $context)->firstId();
    }
}
