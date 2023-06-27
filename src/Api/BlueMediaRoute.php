<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api;

abstract class BlueMediaRoute
{
    public const GATEWAY_LIST_ROUTE = '/gatewayList/v2';

    public const GOOGLE_PAY_MERCHANT_INFO_ROUTE = '/webapi/googlePayMerchantInfo';
}
