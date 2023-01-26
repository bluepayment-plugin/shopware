import BlueMediaConfigurationService from "./blue-media-configuration-service";

const {Application, Service} = Shopware;
const initContainer = Application.getContainer('init');

Service().register('blueMediaConfigurationService', (container) => {
    return new BlueMediaConfigurationService(
        initContainer.httpClient,
        container.loginService,
    );
});
