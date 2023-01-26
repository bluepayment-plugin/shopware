<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\GatewaySalesChannel;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class GatewaySalesChannelDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'blue_media_gateway_sales_channel';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return GatewaySalesChannelCollection::class;
    }

    public function getEntityClass(): string
    {
        return GatewaySalesChannelEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField(
                'gateway_id',
                'gatewayId',
                GatewayDefinition::class
            ))->addFlags(new Required()),
            (new FkField(
                'sales_channel_id',
                'salesChannelId',
                SalesChannelDefinition::class
            ))->addFlags(new Required()),
            new BoolField('active', 'active'),

            (new ManyToOneAssociationField(
                'gateway',
                'gateway_id',
                GatewayDefinition::class
            ))->addFlags(new CascadeDelete()),

            (new ManyToOneAssociationField(
                'salesChannel',
                'sales_channel_id',
                SalesChannelDefinition::class
            ))->addFlags(new CascadeDelete()),
        ]);
    }
}
