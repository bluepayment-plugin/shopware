# Blue Media Shopware 6 Payment Plugin

Changelog available here: [en-GB](./CHANGELOG_en-GB.md), [de-DE](./CHANGELOG_de-DE.md).

## Description / Configuration

### Configuration

`Enable integration` (default: No) - enables integration for selected sales channel

`Blue Media Gateway address` (default: https://pay.bm.pl/) - Production API gateway URL

`Blue Media test gateway address` (default: https://pay-accept.bm.pl/) - Production API gateway URL

`Service ID` - Numeric Service ID (same on production and test environment)

`Shared Key` - SHared Key / Hash (same on production and test environment)

`Hash function mechanism` (default: SHA256) - Hash algorithm used by API

`Test mode` (default: Yes) - option to switch between Production and Test environments.

`Verify Credentials Button` - ignores `Enable integration` flag and allow to test provided credentials

### Payment Methods

The plugin automatically created Payment Method, Rule for all supported currencies, and in case a currency does not exist, it creates the currency itself.