import template from './autopay-api-key-validator.html.twig';

import { extractPluginConfig } from "../../helper/util";

const { Component, Mixin } = Shopware;

Component.register(
    'autopay-api-key-validator', {
        inject: ['blueMediaConfigurationService'],

        mixins: [
            Mixin.getByName('notification'),
        ],

        template,

        data: () => ({
            success: null,
            isLoading: false,
        }),

        computed: {
            pluginConfig() {
                return extractPluginConfig(this.$parent);
            },

            variant() {
                return (typeof this.success === 'boolean') ? (this.success ? 'success' : 'danger') : 'neutral';
            },

            color() {
                return (typeof this.success === 'boolean') ? (this.success ? 'lightgreen' : 'red') : 'gray';
            },

            message() {
                return (typeof this.success === 'boolean')
                    ?
                    (
                        this.success
                            ?
                            this.$t('BlueMediaPayment.configuration.verifyCredentials.credentialsCorrect')
                            :
                            this.$t('BlueMediaPayment.configuration.verifyCredentials.credentialsIncorrect')
                    )
                    :
                    this.$t('BlueMediaPayment.configuration.verifyCredentials.verifyCredentialsInitial');
            },
        },

        methods: {
            async verify() {
                this.isLoading = true;
                try {
                    const config = {
                        testMode: this.pluginConfig['BlueMediaShopwarePayment.config.testMode'],
                        serviceId: this.pluginConfig['BlueMediaShopwarePayment.config.serviceId'],
                        sharedKey: this.pluginConfig['BlueMediaShopwarePayment.config.sharedKey'],
                        hashAlgo: this.pluginConfig['BlueMediaShopwarePayment.config.hashAlgo'],
                        gatewayUrl: this.pluginConfig['BlueMediaShopwarePayment.config.gatewayUrl'],
                        testGatewayUrl: this.pluginConfig['BlueMediaShopwarePayment.config.testGatewayUrl'],
                    };

                    let result = await this.blueMediaConfigurationService.verifyCredentials(config);

                    if (!result?.data || typeof result.data.success !== 'boolean') {
                        throw new Error(this.$t('BlueMediaPayment.configuration.verifyCredentials.couldNotVerify'));
                    }
                    this.success = result.data.success;
                    this.notify();
                } catch ({ message }) {
                    console.error(message);
                    this.notify(this.$t('BlueMediaPayment.configuration.verifyCredentials.couldNotVerify'));
                    this.success = undefined;
                } finally {
                    this.isLoading = false;
                }
            },

            notify(message) {
                this[`createNotification${this.success ? 'Success' : 'Error'}`].call(this, {
                    message: message || this.message,
                });
            }
        },
    }
);
