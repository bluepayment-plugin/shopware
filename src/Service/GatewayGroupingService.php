<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Service;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayCollection;
use BlueMedia\ShopwarePayment\Struct\GatewayGroupCollection;
use BlueMedia\ShopwarePayment\Struct\GatewayGroupStruct;
use BlueMedia\ShopwarePayment\Util\GatewayTypes;

class GatewayGroupingService
{
    private const GROUPING_DEFINITION = [
        GatewayTypes::BLIK, // blik
        GatewayTypes::GROUPING_TRANSFER => [
            GatewayTypes::PBL,
            GatewayTypes::FAST_TRANSFER,
        ], // transfers
        GatewayTypes::E_WALLET, // e wallets
        GatewayTypes::CARD, // cards
        GatewayTypes::INSTALLMENTS, // pay later
    ];

    public function groupGateways(GatewayCollection $gateways): GatewayGroupCollection
    {
        $collection = new GatewayGroupCollection();

        foreach (self::GROUPING_DEFINITION as $customTypeName => $type) {
            $types = is_array($type) ? $type : [$type];
            $typeName = is_string($customTypeName) ? $customTypeName : $type;

            $typedGateways = new GatewayCollection();
            foreach ($types as $nestedType) {
                $typedGateways->merge($gateways->filterByType($nestedType, true));
            }

            $group = new GatewayGroupStruct($typeName, $typedGateways);

            if (0 === $typedGateways->count()) {
                continue;
            }

            $collection->add($group);
        }

        return $collection;
    }
}
