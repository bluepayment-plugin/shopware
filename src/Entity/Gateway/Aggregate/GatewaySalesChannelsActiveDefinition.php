<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\Gateway\Aggregate;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class GatewaySalesChannelsActiveDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'blue_media_gateway_sales_channel_active';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField(
                'sales_channel_id',
                'salesChannelId',
                SalesChannelDefinition::class
            ))->addFlags(new PrimaryKey(), new Required()),
            (new FkField(
                'gateway_id',
                'gatewayId',
                GatewayDefinition::class
            ))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField(
                'salesChannel',
                'sales_channel_id',
                SalesChannelDefinition::class,
                'id',
                false
            ),
            new ManyToOneAssociationField(
                'gateway',
                'gateway_id',
                GatewayDefinition::class,
                'id',
                false
            ),
        ]);
    }
}
