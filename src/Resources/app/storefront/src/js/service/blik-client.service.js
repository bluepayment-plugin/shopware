import StoreApiClient from 'src/service/store-api-client.service';

export default class BlikClientService {

    constructor() {
        this._client = new StoreApiClient();
    }

    sendBlikTransactionInit(data, callback) {
        this._client.post(window.router['store-api.blue-payment.blik.init'], data, callback);
    }

    sendBlikTransactionCheck(data, callback) {
        this._client.post(window.router['store-api.blue-payment.blik.check'], data, callback);
    }

    sendBlikTransactionRetry(data, callback) {
        this._client.post(window.router['store-api.blue-payment.blik.retry'], data, callback);
    }
}
