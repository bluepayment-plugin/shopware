<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Context;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class BlueMediaContext extends Struct
{
    protected Context $context;

    protected SalesChannelEntity $salesChannel;

    public function __construct(
        Context $context,
        SalesChannelEntity $salesChannel
    ) {
        $this->context = $context;
        $this->salesChannel = $salesChannel;
    }

    public function getSalesChannel(): SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannel->getId();
    }
}
