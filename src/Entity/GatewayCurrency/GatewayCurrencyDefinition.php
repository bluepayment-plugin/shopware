<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\GatewayCurrency;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Currency\CurrencyDefinition;

class GatewayCurrencyDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'blue_media_gateway_currency';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return GatewayCurrencyCollection::class;
    }

    public function getEntityClass(): string
    {
        return GatewayCurrencyEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField(
                'currency_id',
                'currencyId',
                CurrencyDefinition::class
            ))->addFlags(new Required()),
            (new FkField(
                'gateway_id',
                'gatewayId',
                GatewayDefinition::class
            ))->addFlags(new Required()),
            new FloatField('min_cart_amount', 'minCartAmount'),
            new FloatField('max_cart_amount', 'maxCartAmount'),

            (new ManyToOneAssociationField(
                'currency',
                'currency_id',
                CurrencyDefinition::class
            ))->addFlags(new CascadeDelete()),

            (new ManyToOneAssociationField(
                'gateway',
                'gateway_id',
                GatewayDefinition::class
            ))->addFlags(new CascadeDelete()),
        ]);
    }
}
