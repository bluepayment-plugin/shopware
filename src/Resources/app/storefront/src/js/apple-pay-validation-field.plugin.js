import Plugin from 'src/plugin-system/plugin.class';
import { canUseApplePay } from "./common/apple-pay";

export default class ApplePayValidationFieldPlugin extends Plugin {
    init() {
        this._handleInput();
    }

    _handleInput() {
        if (this.el.matches('input')) {
            this.el.value = Number(canUseApplePay());
        }
    }
}
