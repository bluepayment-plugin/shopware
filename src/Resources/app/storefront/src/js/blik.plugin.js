import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import ButtonLoadingIndicator from 'src/utility/loading-indicator/button-loading-indicator.util';
import BlikModalUtil from './util/blik-modal.util';
import BlikClientService from "./service/blik-client.service";

const BLIK_CHECK_TIMEOUT = 70000; // 70 sec
const BLIK_CHECK_RETRY_TIME = 2000; // 2 sec
const BLIK_CODE_INPUT_NAME = 'blikCode';

export default class BlikPlugin extends Plugin {
    static options = {
        blikTransactionInitUrl: null,
        isOrderEdit: false,
        paymentMethodId: '',
        blikCodeInputSelector: '#blikCodeInput',
        checkoutConfirmOrderFormSelector: '#confirmOrderForm',
        checkoutConfirmOrderBtnSelector: '#confirmFormSubmit',
        generalErrorMessage: 'Unfortunately, an unexpected payment error occurred. Please try again or contact shop support.',
        blikErrorAlertSelectors: {
            selector: '.blue-media-payment-blik--alert',
            contentSelector: '.alert-content'
        },
        blikModalSelectors: {
            contentSelector: '#blueMediaBlikModalContent',
            waitingContentSelector: '.blue-media-blik--modal-waiting',
            errorContentSelector: '.blue-media-blik--modal-error',
            blikButtonSelector: '#confirmBlikSubmit',
            changePaymentButtonSelector: '#changePaymentMethodButton',
            blikCodeInputSelector: '#blikCodeInput'
        }
    }

    init() {
        this._blikClient = new BlikClientService();
        this._blikCodeInput = DomAccess.querySelector(this.el, 'input');
        this._blikErrorAlertElement = DomAccess.querySelector(this.el, this.options.blikErrorAlertSelectors.selector)
        this._blikCheckStartTime = null;

        this._registerEvent();
    }

    _registerEvent() {
        const orderSubmitButton = DomAccess.querySelector(document, this.options.checkoutConfirmOrderBtnSelector);

        orderSubmitButton.addEventListener('click', this._onOrderFormClick.bind(this));
    }

    _registerModalEvents() {
        const changePaymentMethodButtonElement = DomAccess.querySelector(
            this._modal.getModal(),
            this.options.blikModalSelectors.changePaymentButtonSelector
        );

        changePaymentMethodButtonElement.addEventListener(
            'click',
            this._handleChangePaymentMethodButtonClick.bind(this)
        );

        const blikButtonElement = DomAccess.querySelector(
            this._modal.getModal(),
            this.options.blikModalSelectors.blikButtonSelector
        );

        blikButtonElement.addEventListener('click', this._onRetryBlikButtonClick.bind(this));
    }

    _onOrderFormClick(event) {
        event.preventDefault();

        this._removeError();

        const formElement = DomAccess.querySelector(document, this.options.checkoutConfirmOrderFormSelector);
        if (!formElement.checkValidity()) {
            return;
        }

        this._createButtonLoader(event.currentTarget);

        const form = new FormData(formElement);

        if (false === formElement.reportValidity()) {

            return;
        }

        form.append(BLIK_CODE_INPUT_NAME, this._blikCodeInput.value)

        if (this.options.isOrderEdit) {
            return this._blikClient.sendBlikTransactionRetry(form, this._handleBlikTransactionInit.bind(this));
        }

        this._blikClient.sendBlikTransactionInit(form, this._handleBlikTransactionInit.bind(this));
    }

    _handleBlikTransactionInit(responseText, response) {
        if (response.status !== 200) {
            this._buttonLoader.remove();

            const responseData = JSON.parse(responseText);
            if (responseData.hasOwnProperty('redirectUrl')) {
                window.location = responseData.redirectUrl;

                return;
            }

            this._createError(responseData.message);

            return;
        }

        const responseData = JSON.parse(responseText);
        const modalContent = DomAccess.querySelector(document, this.options.blikModalSelectors.contentSelector)

        this._modal = new BlikModalUtil(modalContent.outerHTML);
        this._modal.open(this._handleBlikModal.bind(this, responseData));

        this._waitingModalContent = DomAccess.querySelector(
            this._modal.getModal(),
            this.options.blikModalSelectors.waitingContentSelector
        );

        this._errorModalContent = DomAccess.querySelector(
            this._modal.getModal(),
            this.options.blikModalSelectors.errorContentSelector
        );

        this._registerModalEvents();
    }

    _handleBlikModal(responseData) {
        this._orderId = responseData.orderId;
        this._finishUrl = responseData.finishUrl;

        this._sendCheck();
    }

    _sendCheck() {
        if (null === this._blikCheckStartTime) {
            this._blikCheckStartTime = Date.now();
        }

        this._blikClient.sendBlikTransactionCheck(
            JSON.stringify({
                orderId: this._orderId,
            }),
            this._handleBlikTransactionCheck.bind(this));
    }

    _handleBlikTransactionCheck(responseText) {
        let responseData;

        try {
            responseData = JSON.parse(responseText);
        } catch (e) {
            this._modal.close();
            this._createError(this.options.generalErrorMessage);
            this._buttonLoader.remove();
            this._blikCheckStartTime = null;

            return console.error(e);
        }

        if (true === responseData.isSuccess && false === responseData.waiting) {
            return window.location = this._finishUrl;
        }

        if (true === responseData.isSuccess && true === responseData.waiting) {
            if (this._isCheckTimeout()) {
                this._changePaymentUrl = responseData.changePaymentUrl;
                return this._toggleBlikError();
            }
            return setTimeout(this._sendCheck.bind(this), BLIK_CHECK_RETRY_TIME);
        }

        if (false === responseData.isSuccess) {
            this._changePaymentUrl = responseData.changePaymentUrl;
            return this._toggleBlikError();
        }
    }

    _onRetryBlikButtonClick() {
        this._toggleBlikError();

        const blikCodeInputElement = DomAccess.querySelector(
            this._modal.getModal(),
            this.options.blikModalSelectors.blikCodeInputSelector
        );

        const form = new FormData();
        form.append('orderId', this._orderId);
        form.append('paymentMethodId', this.options.paymentMethodId);
        form.append('finishUrl', this._finishUrl);
        form.append('errorUrl', this._changePaymentUrl);
        form.append(BLIK_CODE_INPUT_NAME, blikCodeInputElement.value)

        this._blikClient.sendBlikTransactionRetry(form, this._handleRetryBlikTransaction.bind(this));
    }

    _handleRetryBlikTransaction() {
        this._sendCheck();
    }

    _handleChangePaymentMethodButtonClick() {
        window.location = this._changePaymentUrl;
    }

    _toggleBlikError() {
        window.PluginManager.initializePlugins();

        this._errorModalContent.classList.toggle('d-none');
        this._waitingModalContent.classList.toggle('d-none');
        this._blikCheckStartTime = null;
    }

    /**
     * @param {string} message
     */
    _createError(message) {
        const content = DomAccess.querySelector(
            this._blikErrorAlertElement,
            this.options.blikErrorAlertSelectors.contentSelector
        );

        content.insertAdjacentHTML('beforeend', message);

        this._blikErrorAlertElement.classList.remove('d-none');
        this._blikErrorAlertElement.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
    }

    _removeError() {
        this._blikErrorAlertElement.classList.add('d-none');
    }

    _createButtonLoader(target) {
        if (typeof this._buttonLoader === 'undefined') {
            this._buttonLoader = new ButtonLoadingIndicator(target);
        }

        this._buttonLoader.create();
    }

    _isCheckTimeout() {
        return (Date.now() - this._blikCheckStartTime) > BLIK_CHECK_TIMEOUT;
    }
}