<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\Gateway;

use BlueMedia\ShopwarePayment\Entity\GatewayCurrency\GatewayCurrencyDefinition;
use BlueMedia\ShopwarePayment\Entity\GatewaySalesChannel\GatewaySalesChannelDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

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
            (new IntField('external_id', 'externalId'))->addFlags(new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            new LongTextField('description', 'description'),
            (new StringField('type', 'type'))->addFlags(new Required()),
            new StringField('bank_name', 'bankName'),
            new FkField('logo_media_id', 'logoMediaId', MediaDefinition::class),

            (new ManyToOneAssociationField(
                'logoMedia',
                'logo_media_id',
                MediaDefinition::class
            ))->addFlags(new SetNullOnDelete()),

            new OneToManyAssociationField(
                'gatewayCurrencies',
                GatewayCurrencyDefinition::class,
                'gateway_id'
            ),

            new OneToManyAssociationField(
                'gatewaySalesChannels',
                GatewaySalesChannelDefinition::class,
                'gateway_id'
            ),
        ]);
    }
}
