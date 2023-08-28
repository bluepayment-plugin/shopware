import BlueMediaConfigurationService from "./autopay-configuration-service";
import BlueMediaGatewaySyncService from "./autopay-gateway-sync-service";

const {Application, Service} = Shopware;
const initContainer = Application.getContainer('init');

Service().register('blueMediaConfigurationService', (container) => {
    return new BlueMediaConfigurationService(
        initContainer.httpClient,
        container.loginService,
    );
});

Service().register('blueMediaGatewaySyncService', (container) => {
    return new BlueMediaGatewaySyncService(
        initContainer.httpClient,
        container.loginService,
    );
});
