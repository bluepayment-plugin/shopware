const ApiService = Shopware.Classes.ApiService;

export default class BlueMediaConfigurationService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'test') {
        super(httpClient, loginService, apiEndpoint);
    }

    async verifyCredentials(config = {}) {
        return await this.client.post(
            '_action/blue-payment/test-credentials', {
                testMode: config?.testMode,
                serviceId: config?.serviceId,
                sharedKey: config?.sharedKey,
                hashAlgo: config?.hashAlgo,
                gatewayUrl: config?.gatewayUrl,
                testGatewayUrl: config?.testGatewayUrl,
            }, {
                headers: this.getBasicHeaders(),
            }
        );
    }
}
