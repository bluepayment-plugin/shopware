import template from './blue-media-gateways-detail.html.twig';

import { getComponentTypeLabel } from "../../helper/util";

const {Component, Mixin} = Shopware;
const { Criteria } = Shopware.Data;

Component.register('blue-media-gateways-detail', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            gateway: null,
            isLoading: false,
            processSuccess: false,
        };
    },

    computed: {
        repository() {
            return this.repositoryFactory.create('blue_media_gateway');
        },
        gatewayCriteria() {
            const criteria = new Criteria();

            criteria
                .addAssociation('salesChannelsActive')
                .addAssociation('salesChannelsEnabled')
                .addAssociation('logoMedia');

            return criteria;
        },
    },

    created() {
        this.getGateway();
    },

    methods: {
        getGateway() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api, this.gatewayCriteria)
                .then((entity) => {
                    this.gateway = entity;
                    this.setMedia(entity.logoMedia)
                });
        },
        getTypeLabel(value) {
            return getComponentTypeLabel(value, this);
        },
        setMedia(media) {
            this.gateway.media = media;
        },
        setSalesChannelsEnabled(salesChannelsEnabled) {
            this.gateway.salesChannelsEnabled = salesChannelsEnabled;
        },
        onClickSave() {
            this.isLoading = true;
            this.repository
                .save(this.gateway, Shopware.Context.api)
                .then(() => {
                    this.getGateway();
                    this.isLoading = false;
                    this.processSuccess = true;
                    this.createNotificationSuccess({
                        title: this.$t('BlueMediaPayment.gateways.success'),
                        message: this.$t('BlueMediaPayment.gateways.saveSuccessMessage')
                    });
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('global.default.error'),
                        message: exception
                    });
                });
        },
        saveFinish() {
            this.processSuccess = false;
        }
    }
});
