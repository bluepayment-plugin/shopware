import HttpClient from 'src/service/http-client.service';
import Plugin from 'src/plugin-system/plugin.class';
import ButtonLoadingIndicator from 'src/utility/loading-indicator/button-loading-indicator.util';

export default class BlueMediaCardPlugin extends Plugin {

    static options = {
        formSubmitButtonSelector: '#confirmFormSubmit',
    }
    init() {
        this._client = new HttpClient();
        this.form = this.el.closest('form');
        this.button = this.el.querySelector(this.options.formSubmitButtonSelector);

        this.registerEvents();
    }

    registerEvents() {
        this.el.addEventListener('submit', (e) => {
            e.preventDefault();
            this.cardOrder();
        });
    }

    cardOrder() {
        const loader = new ButtonLoadingIndicator(this.button);
        loader.create();

        this._client.post(window.router['payment.blue-payment.checkout.card'], new FormData(this.form), (responseText, request) => {
            if (request.status >= 400) {
                window.location.reload();
            }

            const response = JSON.parse(responseText);
            if (
                response.transactionContinueRedirect
                && PayBmCheckout
                && typeof PayBmCheckout.transactionStartByUrl === 'function'
            ) {
                this.registerPayBmEvents(response.finalRedirect, response.checkoutErrorUrl);
                document.body.classList.add('overflow-hidden');
                PayBmCheckout.transactionStartByUrl(response.transactionContinueRedirect);
            } else {
                window.location.replace(response.finalRedirect);
            }
        });
   }

   registerPayBmEvents(finalRedirect, checkoutErrorUrl) {
       PayBmCheckout.transactionSuccess = function () {
           document.body.classList.remove('overflow-hidden');
           window.location.replace(finalRedirect);
       };

       PayBmCheckout.transactionDeclined = function () {
           document.body.classList.remove('overflow-hidden');
           window.location.replace(checkoutErrorUrl);
       };

       PayBmCheckout.transactionError = function () {
           document.body.classList.remove('overflow-hidden');
           window.location.replace(checkoutErrorUrl);
       };
   }
}