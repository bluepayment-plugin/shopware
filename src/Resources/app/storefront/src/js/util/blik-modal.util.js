import PseudoModalUtil from 'src/utility/modal-extension/pseudo-modal.util';

const BLIK_MODAL_CLASS = 'blue-media-blik--modal';

export default class BlikModalUtil extends PseudoModalUtil {
    _open(cb) {
        this.getModal();

        this._modalWrapper.classList.add(BLIK_MODAL_CLASS);
        this._$modal.on('hidden.bs.modal', this._modalWrapper.remove);
        this._$modal.on('shown.bs.modal', cb);
        this._$modal.modal({ backdrop: 'static', keyboard: false });
        this._$modal.modal('show');
    }
}
