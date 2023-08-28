import HttpClient from 'src/service/http-client.service';
import Plugin from 'src/plugin-system/plugin.class';
import ButtonLoadingIndicator from 'src/utility/loading-indicator/button-loading-indicator.util';

export default class AutopayGooglePayPlugin extends Plugin {

    static options = {
        formSubmitButtonSelector: '#confirmFormSubmit',
        paymentTokenSelector: "#autopay-google-payment-token",
        environment: '',
        transactionCurrencyCode: 'PLN',
        transactionCountryCode: 'PL',
        transactionAmount: 0.00,
        transactionStatus: 'FINAL',
        requireShippingAddress: false,
        bmInitRequest: {},
        autopayGooglePayPaymentOptions: ''
    }
    init() {
        this.httpClient = new HttpClient();
        this.button = this.el.querySelector(this.options.formSubmitButtonSelector);

        this.googleClient = null;
        this.authJwt = null;
        this.merchantId = null;
        this.merchantOrigin = null;
        this.merchantName = null;
        this.gatewayMerchantId = null;
        this.allowedAuthMethods = null;
        this.allowedCardNetworks = null;

        this.setHandlers();
        this.registerEvents();
    }

    setHandlers() {
        this.submitHandler = this.handleSubmit.bind(this);
    }

    registerEvents() {
        this.el.addEventListener('submit', this.submitHandler);
    }

    handleSubmit(e) {
        e.preventDefault();
        this.googlePayOrder();
    }

    googlePayOrder() {
        const loader = new ButtonLoadingIndicator(this.button);
        loader.create();
        this.initGoogleClient();
    }

    /**
    * @see {@link https://developers.google.com/pay/api/web/reference/client}
    */
    initGoogleClient() {
        if (typeof google !== 'undefined' && typeof google.payments !== 'undefined') {
            this.googleClient = new google.payments.api.PaymentsClient({
                environment: this.options.environment
            });
            const data = JSON.stringify({
                _csrf_token: this.options.bmInitRequest.token,
            });
            this.httpClient.post(this.options.bmInitRequest.path, data, this.initGoogleClientCallback.bind(this));
        }
    }

    initGoogleClientCallback(responseText, request) {
        if (request.status >= 400) {
            this.onErrorCallback('Can not collect GooglePay init data! Check configuration!')
        }

        const response = JSON.parse(responseText);
        if (response.error === true) {
            this.onErrorCallback('Can not collect GooglePay init data! Check configuration!')
        }

        this.authJwt = response.authJwt;
        this.merchantId = response.merchantId;
        this.merchantOrigin = response.merchantOrigin;
        this.merchantName = response.merchantName;
        this.allowedAuthMethods = response.allowedAuthMethods;
        this.allowedCardNetworks = response.allowedCardNetworks;
        this.gatewayMerchantId = response.gatewayMerchantId;

        this.googleClient.isReadyToPay(this.getIsReadyToPayRequestData())
            .then((response) => {
                if (response.result) {
                    this.prefetchTransactionData()
                    this.createPayButton();
                    this.initiatePaymentWindow();
                } else {
                    this.onErrorCallback(response);
                }
            })
            .catch((errorMessage) => {
                this.onErrorCallback(errorMessage);
            })
    }

    /**
     * @see {@link https://developers.google.com/pay/api/web/reference/client#prefetchPaymentData}
     */
    prefetchTransactionData() {
        this.googleClient.prefetchPaymentData(this.getPaymentDataRequestData());
    };

    /**
     * @see {@link https://developers.google.com/pay/api/web/reference/client#createButton}
     */
    createPayButton() {
        this.googleClient.createButton();
    };

    /**
     * @see {@link https://developers.google.com/pay/api/web/reference/client#loadPaymentData}
     */
    initiatePaymentWindow() {
        this.googleClient.loadPaymentData(this.getPaymentDataRequestData())
            .then((data) => {
                this.updatePaymentToken(data.paymentMethodData.tokenizationData.token);
            })
            .catch((errorMessage) => {
                this.onErrorCallback(errorMessage);
            })
            .finally(() => {
                this.el.removeEventListener('submit', this.submitHandler);
                this.el.submit();
            })
    };

    /**
     * @returns {object} {@link https://developers.google.com/pay/api/web/reference/request-objects#IsReadyToPayRequest}
     */
    getIsReadyToPayRequestData() {
        const requestData = this.getPaymentDataRequestData();

        delete requestData.merchantInfo;
        delete requestData.transactionInfo;
        delete requestData.shippingAddressRequired;

        return requestData;
    };

    /**
     * @returns {object} {@link https://developers.google.com/pay/api/web/reference/object#PaymentDataRequest}
     */
    getPaymentDataRequestData () {
        return {
            apiVersion: 2,
            apiVersionMinor: 0,
            merchantInfo: {
                merchantId: this.merchantId,
                merchantOrigin: this.merchantOrigin,
                merchantName: this.merchantName,
                authJwt: this.authJwt
            },
            allowedPaymentMethods: [
                {
                    type: 'CARD',
                    parameters: {
                        allowedAuthMethods: this.allowedAuthMethods,
                        allowedCardNetworks: this.allowedCardNetworks
                    },
                    tokenizationSpecification: {
                        type: 'PAYMENT_GATEWAY',
                        parameters: {
                            gateway: 'bluemedia',
                            gatewayMerchantId: this.gatewayMerchantId
                        }
                    }
                }
            ],
            transactionInfo: {
                currencyCode: this.options.transactionCurrencyCode,
                countryCode: this.options.transactionCountryCode,
                totalPriceStatus: this.options.transactionStatus,
                totalPrice: String(this.options.transactionAmount)
            },
            shippingAddressRequired: this.options.requireShippingAddress,
        };
    };

    updatePaymentToken(token)
    {
        this.el.querySelector(this.options.paymentTokenSelector).value = JSON.stringify(token);
    }

    onErrorCallback(errorMessage) {
        console.error(errorMessage);
    };
}