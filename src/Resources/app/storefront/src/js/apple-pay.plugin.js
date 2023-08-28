import Plugin from 'src/plugin-system/plugin.class';
import { canUseApplePay } from "./common/apple-pay";

export default class ApplePayPlugin extends Plugin {
    static options = {
        paymentMethodSelector: '.payment-method',
        gatewayGroupSelector: '.autopay-gateway-group',
        gatewaySelector: '.blue-media-gateway',
        collapseSelector: '.collapse',
        handlePaymentCollapse: true,
        checkoutConfirmBtnSelector: '#confirmFormSubmit'
    }

    init() {
        this.isSeparatedPayment = this.el.parentElement.matches(this.options.paymentMethodSelector);

        this.checkoutConfirmBtn = document.querySelector(this.options.checkoutConfirmBtnSelector);

        this.paymentMethod = this.el.closest(this.options.paymentMethodSelector);
        this.gatewayGroup = this.el.closest(this.options.gatewayGroupSelector);
        this.gateway = this.el.closest(this.options.gatewaySelector);
        this.gatewaysCollapse = this.gateway && this.gateway.closest(this.options.collapseSelector);

        this.collapseBtn = document.querySelector('[data-collapse-checkout-confirm-methods]');
        this.collapse = this.paymentMethod && this.paymentMethod.closest(this.options.collapseSelector);

        this._handleGatewayVisibility();
        this._handlePaymentVisibility();
    }

    _handleGatewayVisibility() {
        if (this.isSeparatedPayment || canUseApplePay()) {
            return;
        }

        if (this.gateway) {
            if (this.gateway.querySelector('input').checked) {
                this._disableBtn(this.checkoutConfirmBtn);
            }

            this._removeNode(this.gateway);

            if (this.gatewayGroup && this._isNodeEmpty(this.gatewaysCollapse)) {
                this._removeNode(this.gatewayGroup);
            }
        }
    }

    _handlePaymentVisibility() {
        if (false === this.isSeparatedPayment || canUseApplePay()) {
            return;
        }

        if (this.paymentMethod) {
            this._removeNode(this.paymentMethod);
            this._handlePaymentCollapse();

            if (0 === document.querySelectorAll(`${this.options.paymentMethodSelector} input:checked`).length) {
                this._disableBtn(this.checkoutConfirmBtn);
            }
        }
    }

    _handlePaymentCollapse() {
        if (this.options.handlePaymentCollapse && this._isNodeEmpty(this.collapse)) {
            this._removeNode(this.collapse);
            this._removeNode(this.collapseBtn);
        }
    }

    _removeNode(node) {
        node.remove();
    }

    _disableBtn(btn) {
        btn.setAttribute('disabled', 'disabled');
    }

    _isNodeEmpty(node) {
        return node instanceof Node && !node.textContent.trim();
    }
}
