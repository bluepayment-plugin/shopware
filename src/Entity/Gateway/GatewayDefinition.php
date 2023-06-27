<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\Gateway;

use BlueMedia\ShopwarePayment\Entity\Gateway\Aggregate\GatewaySalesChannelsActiveDefinition;
use BlueMedia\ShopwarePayment\Entity\Gateway\Aggregate\GatewaySalesChannelsEnabledDefinition;
use BlueMedia\ShopwarePayment\Entity\GatewayCurrency\GatewayCurrencyDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class GatewayDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'blue_media_gateway';

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return static::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return GatewayCollection::class;
    }

    public function getEntityClass(): string
    {
        return GatewayEntity::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new IntField('gateway_id', 'gatewayId'))->addFlags(new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            new LongTextField('description', 'description'),
            (new StringField('type', 'type'))->addFlags(new Required()),
            new StringField('bank_name', 'bankName'),
            new FkField('logo_media_id', 'logoMediaId', MediaDefinition::class),
            (new BoolField('is_supported', 'isSupported'))
                ->addFlags(new WriteProtected(), new Runtime()),
            (new ManyToOneAssociationField(
                'logoMedia',
                'logo_media_id',
                MediaDefinition::class
            ))->addFlags(new SetNullOnDelete()),
            new OneToManyAssociationField(
                'currencies',
                GatewayCurrencyDefinition::class,
                'gateway_id'
            ),
            new ManyToManyAssociationField(
                'salesChannelsActive',
                SalesChannelDefinition::class,
                GatewaySalesChannelsActiveDefinition::class,
                'gateway_id',
                'sales_channel_id'
            ),
            new ManyToManyAssociationField(
                'salesChannelsEnabled',
                SalesChannelDefinition::class,
                GatewaySalesChannelsEnabledDefinition::class,
                'gateway_id',
                'sales_channel_id'
            ),
        ]);
    }
}
