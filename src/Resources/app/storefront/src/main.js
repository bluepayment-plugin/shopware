import ImageRotatorPlugin from "./js/image-rotator.plugin";
import ApplePayPlugin from "./js/apple-pay.plugin";
import ApplePayValidationFieldPlugin from "./js/apple-pay-validation-field.plugin";
import AutopayCardPlugin from "./js/autopay-card.plugin";
import AutopayGooglePayPlugin from "./js/autopay-google-pay.plugin";
import BlikPlugin from "./js/blik.plugin";
import BlikMaskPlugin from "./js/blik-mask.plugin";

const PluginManager = window.PluginManager;

PluginManager.register('BlueMediaImageRotator', ImageRotatorPlugin, '[data-autopay-image-rotator]');
PluginManager.register('BlueMediaApplePay', ApplePayPlugin, '[data-autopay-apple-pay]');
PluginManager.register('BlueMediaApplePayValidationField', ApplePayValidationFieldPlugin, '[data-autopay-apple-pay-validation-field]');
PluginManager.register('BlueMediaCardPayment', AutopayCardPlugin, '[data-autopay-card-payment]');
PluginManager.register('BlueMediaGooglePayPayment', AutopayGooglePayPlugin, '[data-autopay-google-pay-payment]');
PluginManager.register('BlueMediaBlik', BlikPlugin, '[data-blue-media-blik]');
PluginManager.register('BlueMediaBlikMask', BlikMaskPlugin, '[data-blue-media-blik-mask]');
