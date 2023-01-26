import template from './blue-media-start-banner.html.twig';

import { extractPluginConfig } from "../../helper/util";

const { Component } = Shopware;

Component.register('blue-media-start-banner', {
    template,

    data() {
        return {
            pluginConfig: {}
        }
    },

    computed: {
        cardVisible() {
            return false === this.pluginConfig['BlueMediaShopwarePayment.config.enabled'];
        },

        parentCard() {
            let parent = this.$parent;
            while (parent.$options.name !== 'sw-card') {
                parent = parent.$parent;
            }

            return parent;
        },
    },

    mounted() {
        this.loadConfig();
        this.handleCardVisibility();
    },

    methods: {
        loadConfig() {
            this.pluginConfig = extractPluginConfig(this.$parent);
        },

        handleCardVisibility() {
            this.parentCard.$el.style.display = this.cardVisible ? '' : 'none';
        },
    },
});
