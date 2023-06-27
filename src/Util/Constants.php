<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Util;

class Constants
{
    public const SUPPORTED_CURRENCIES = [
        self::CURRENCY_PLN,
    ];

    public const CURRENCY_PLN = 'PLN';

    public const INIT_TRANSACTION_RESPONSE_CUSTOM_FIELD = 'blueMediaInitTransactionResponse';

    public const BACKGROUND_TRANSACTION_RESPONSE_CUSTOM_FIELD = 'blueMediaBackgroundTransactionResponse';

    public const SESSION_SELECTED_GATEWAY_ID = 'blueMediaGatewayId';

    public const REQUEST_PARAMETER_GATEWAY_ID = 'blueMediaGatewayId';

    public const GATEWAYS_EXTENSION_NAME = 'blueMediaGatewaysExtension';
}
