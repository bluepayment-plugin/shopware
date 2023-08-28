import template from './autopay-gateways.html.twig';

import { getComponentTypeLabel } from "../../helper/util";

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('autopay-gateways', {
    template,

    inject: [
        'repositoryFactory',
        'blueMediaGatewaySyncService'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            repository: null,
            gatewayList: null,
            isLoading: false,
            isSaveSuccessful: false,
            sortEnable: true,
            sortBy: 'gatewayId',
            sortDirection: 'ASC',
            columns: [{
                property: 'id',
                visible: false,
            }, {
                property: 'gatewayId',
                label: this.$t('BlueMediaPayment.gateways.fields.gatewayId'),
                align: 'center',
                width: '50px',
                allowResize: true,
            }, {
                property: 'name',
                label: this.$t('BlueMediaPayment.gateways.fields.name'),
                allowResize: true,
                width: '150px'
            }, {
                property: 'description',
                label: this.$t('BlueMediaPayment.gateways.fields.description'),
                allowResize: true,
                width: '200px',
                visible: false,
            }, {
                property: 'type',
                label: this.$t('BlueMediaPayment.gateways.fields.type'),
                allowResize: true,
                visible: true
            }, {
                property: 'bankName',
                label: this.$t('BlueMediaPayment.gateways.fields.bankName'),
                visible: true,
                allowResize: true,
            }, {
                property: 'available',
                dataIndex: 'salesChannelsActive',
                width: '150px',
                label: this.$t('BlueMediaPayment.gateways.fields.available'),
                sortable: false,
                allowResize: true,
            }, {
                property: 'enabled',
                dataIndex: 'salesChannelsEnabled',
                width: '150px',
                label: this.$t('BlueMediaPayment.gateways.fields.enabled'),
                sortable: false,
                allowResize: true,
            }, {
                property: 'isSupported',
                label: this.$t('BlueMediaPayment.gateways.fields.isSupported'),
                visible: false,
                sortable: false,
                allowResize: true,
            }, {
                property: 'createdAt',
                label: this.$t('BlueMediaPayment.gateways.fields.createdAt'),
                allowResize: true,
                visible: false
            }, {
                property: 'updatedAt',
                label: this.$t('BlueMediaPayment.gateways.fields.updatedAt'),
                allowResize: true,
                visible: false
            }]
        }
    },

    created() {
        this.createdComponent();
        this.getList();
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('blue_media_gateway');
        },

        getList() {
            this.isLoading = true;
            const defaultCriteria = new Criteria();
            defaultCriteria
                .addAssociation('salesChannelsActive')
                .addAssociation('salesChannelsEnabled');

            this.repository
                .search(defaultCriteria, Shopware.Context.api)
                .then((result) => {
                    this.isLoading = false;
                    this.gatewayList = this.sortList(result);
                });
        },

        getTypeLabel(value) {
            return getComponentTypeLabel(value, this);
        },

        sortList(result) {
            return result.sort((a, b) => {
                if (a[this.sortBy] > b[this.sortBy]) {
                    return this.sortDirection === 'ASC' ? 1 : -1;
                }
                if (a[this.sortBy] < b[this.sortBy]) {
                    return this.sortDirection === 'ASC' ? -1 : 1;
                }
                return 0;
            })
        },

        onColumnSort() {
            if (this.sortEnable) {
                this.sortEnable = false;
            }
        },

        async reloadGateways() {
            this.isLoading = true;
            try {
                let result = await this.blueMediaGatewaySyncService.sendSyncGatewaysRequest();
                if (!result?.data || result.data.success !== true) {
                    throw new Error(this.$t('global.notification.unspecifiedSaveErrorMessage'));
                }

                this.createNotificationSuccess({
                    title: this.$t('BlueMediaPayment.gateways.success'),
                    message: this.$t('BlueMediaPayment.gateways.reloadSuccessMessage')
                });
            } catch ({message}) {
                this.createNotificationError({
                    title: this.$t('global.default.error'),
                    message: message
                });
            } finally {
                this.getList();
                this.isLoading = false;
            }
        }
    }
});
