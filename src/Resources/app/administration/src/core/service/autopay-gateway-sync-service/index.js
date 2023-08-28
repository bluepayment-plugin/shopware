const ApiService = Shopware.Classes.ApiService;

export default class BlueMediaGatewaySyncService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = '') {
        super(httpClient, loginService, apiEndpoint);
    }

    async sendSyncGatewaysRequest() {
        return await this.client.post(
            '_action/blue-payment/sync-gateways',
            { },
            {
                headers: this.getBasicHeaders(),
            }
        );
    }
}
