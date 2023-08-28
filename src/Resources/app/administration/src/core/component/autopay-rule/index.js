import template from './autopay-rule.html.twig';

Shopware.Component.extend('autopay-rule', 'sw-condition-base', {
    template,

    computed: {
        selectValues() {
            return [
                {
                    label: "DetailedPaymentHandler",
                    value: "BlueMedia\\ShopwarePayment\\PaymentHandler\\DetailedPaymentHandler"
                },
                {
                    label: "PayByLinkPaymentHandler",
                    value: "BlueMedia\\ShopwarePayment\\PaymentHandler\\PayByLinkPaymentHandler"
                },
                {
                    label: "QuickTransferPaymentHandler",
                    value: "BlueMedia\\ShopwarePayment\\PaymentHandler\\QuickTransferPaymentHandler"
                },
                {
                    label: "ApplePayPaymentHandler",
                    value: "BlueMedia\\ShopwarePayment\\PaymentHandler\\ApplePayPaymentHandler"
                },
                {
                    label: "CardPaymentHandler",
                    value: "BlueMedia\\ShopwarePayment\\PaymentHandler\\CardPaymentHandler"
                },
                {
                    label: "BlikPaymentHandler",
                    value: "BlueMedia\\ShopwarePayment\\PaymentHandler\\BlikPaymentHandler"
                },
                {
                    label: "GooglePayPaymentHandler",
                    value: "BlueMedia\\ShopwarePayment\\PaymentHandler\\GooglePayPaymentHandler"
                }
            ];
        },

        paymentHandler: {
            get() {
                this.ensureValueExist();

                if (this.condition.value.paymentHandler == null) {
                    this.condition.value.paymentHandler =
                        "BlueMedia\\ShopwarePayment\\PaymentHandler\\DetailedPaymentHandler";
                }

                return this.condition.value.paymentHandler;
            },
            set(paymentHandler) {
                this.ensureValueExist();
                this.condition.value = { ...this.condition.value, paymentHandler };
            }
        }
    }
});