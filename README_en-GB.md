# BluePayment module for the Shopware 6 platform

## Basic information

BluePayment is a payment module that allows you to make cashless transactions in a store based on the Shopware platform. If you don't have the plugin yet, you can download it [here](https://github.com/bluepayment-plugin/shopware/releases).

### Requirements

- Shopware 6 based store (from 6.4.5.0 to 6.4.20.2)
- PHP version compatible with the requirements of a given Shopware version

### Changelog

Available [here](./CHANGELOG_en-GB.md).

## Installation

1. [Download the package .zip](https://github.com/bluepayment-plugin/shopware/releases).
2. Create an account on the site [PayBM](https://platnosci.bm.pl/) providing your company details.
3. Log in to your Shopware administration panel.
4. In the admin panel go to `Extensions > My extensions`.
5. Press `Upload extension`.
6. Select the downloaded .zip package. The page will be refreshed automatically.
7. The package will be visible in the list of extensions.
8. Press `Install app`. The page will be refreshed automatically after installation.
9. Go to activation.

## Activation

1. Press switch on the left.
2. Once installed, the page will refresh automatically. Go to setup.

### Update

1. [Download the .zip package with the new version of the module](https://github.com/bluepayment-plugin/shopware/releases).
2. Log in to your Shopware administration panel.
3. In the admin panel go to `Extensions > My extensions`.
4. Press `Upload extension`.
5. Select the downloaded .zip package. The page will be refreshed automatically.
6. The package will be visible in the list of extensions.
7. Press `Update`). The page will be refreshed automatically after installation.
8. Go to activation.

## Configuration

To enable the store's customers to use Blue Media payments, you need to connect the module with the Blue Media environment and connect the payment method to a given sales channel.

### Blue Media Payment Extension

1. Go to `Extensions > My extensions`.
2. Press the 3 dots `...` next to the Blue Media plugin and then `Configuration`.
3. Select the Sales Channel on which you want to activate the integration. You can also select all channels (`All Sales Channels).
4. Complete the configuration fields:
    - `Enable integration` (by default: `disabled`) - enables integration with Blue Media on the selected sales channel
    - `Test mode` (by default: `enabled`) - switch between production and test integration
    - `Blue Media Gateway address` (by default: `https://pay.bm.pl/`) - blue media gateway production URL
    - `Blue Media test gateway address` (by default: `https://pay-accept.bm.pl/`) - Blue Media gateway test URL (only used when `Test mode` is enabled)
    - `Service ID` - numerical identifier (you will receive it from Blue Media)
    - `Hash key` - a unique key assigned to a given store (you will receive it from Blue Media)
    - `Hash encryption method` (by default: `SHA256`) - hash encryption method used by Blue Media (must be the same as on the Blue Media panel side in the `Hash configuration`)
    - `Verify Credentials Button` - allows verification of the above configuration before saving (ignores the `Enable integration` setting)

    - `Process Order Status on Transaction Capture` - when enabled, the status of the order in Shopware will be changed to `In progress` when the transaction is approved by Blue Media

### Payment method

Blue Media payment methods are created at the time of module installation. To activate a payment method in the store, you must assign it in the settings of a given sales channel.

The Blue Media payment method is activated and deactivated in parallel with the entire module. However, it can also be deactivated manually in the administration panel (`Settings > Payment methods`).

Before proceeding with the configuration of the sales channel, make sure that the payment module and method are active.

1. To enable sales channel customers to use Blue Media payments, go to sales channel settings.
2. In the `Payment methods` field, add the Blue Media Payment method.
3. In the `Currencies` field, add the “Polish Zloty” currency. Only this currency is supported by the plugin.
4. Save the changes by pressing the `Save` button at the top of the screen.
5. The payment method is visible in checkout.

### Managing additional payment methods (List of Payment Gateways)

In addition to assigning the payment method to the sales channel, you must also activate the appropriate Blue Media payment gateway

1. In the menu, select `Orders -> Blue Media Gateways`
2. The list is synchronized automatically, but you can force synchronization on request with the `Synchronize gateways` button.
3. After selecting and editing a particular payment gateway, it is possible to enable support for a given gateway within the selected sales channel.
4. All enabled and supported payment gateways will be visible in the shopping cart. Gateways that are not available or not enabled will be hidden.

### Detailed payment methods

1. Blue Media's detailed payment method with the option of selecting a payment gateway allows you to select the appropriate payment gateway from a grouped list before being redirected to the payment provider.
2. In the case of specific payment methods (White Label), e.g. Quick Transfer, only gateways from the selected group will be available for selection.
3. The Quick Transfer, Pay by Link, etc. methods (not marked with the Blue Media icon) integrate with the store and have their own behavior implementation:
    - Quick transfer gives the transfer details on the order summary page.
    - Pay by link - redirects directly to the bank's website, bypassing BlueMedia.
    - Google Pay - it is possible to pay by card using Google Pay
        - when placing an order, a popup will additionally appear allowing you to choose one of the GooglePay payment cards
        - then the order will be placed and the card token will be transferred to Blue Media to authorize the payment

### Additional information

When installing the module:
- creates Blue Media payment method (general - redirect to BlueMedia)
- creates additional payment methods:
    - Detailed payment method - enabling the management of individual payment gateways,
    - Specific methods (white-label):
        - Payment by Link
        - Quick transfer
        - Apple Pay
        - Google Pay
        - BLIK
- creates PLN currency, if it does not exist in the system
- creates rules for created payment methods (support only for PLN currency)