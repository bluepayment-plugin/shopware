import ImageRotatorPlugin from "./js/image-rotator.plugin";
import ApplePayPlugin from "./js/apple-pay.plugin";
import ApplePayValidationFieldPlugin from "./js/apple-pay-validation-field.plugin";
import BlueMediaCardPlugin from "./js/blue-media-card.plugin";
import BlueMediaGooglePayPlugin from "./js/blue-media-google-pay.plugin";

const PluginManager = window.PluginManager;

PluginManager.register('BlueMediaImageRotator', ImageRotatorPlugin, '[data-blue-media-image-rotator]');
PluginManager.register('BlueMediaApplePay', ApplePayPlugin, '[data-blue-media-apple-pay]');
PluginManager.register('BlueMediaApplePayValidationField', ApplePayValidationFieldPlugin, '[data-blue-media-apple-pay-validation-field]');
PluginManager.register('BlueMediaCardPayment', BlueMediaCardPlugin, '[data-blue-media-card-payment]');
PluginManager.register('BlueMediaGooglePayPayment', BlueMediaGooglePayPlugin, '[data-blue-media-google-pay-payment]');
