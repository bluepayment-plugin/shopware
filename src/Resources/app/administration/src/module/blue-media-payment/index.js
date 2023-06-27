import './acl';
import './component';
import './page/blue-media-gateways';
import './view/blue-media-gateways-detail';

import enGB from '../../snippet/en-GB.json';
import deDE from '../../snippet/de-DE.json';
import plPL from '../../snippet/pl-PL.json';

const { Module } = Shopware;

Module.register('blue-media', {
    type: 'plugin',
    name: 'Blue Media',
    title: 'BlueMediaPayment.menu.mainMenuLabel',
    description: 'BlueMediaPayment.moduleDescription',
    color: '#0063BE',
    icon: 'default-money-wallet',

    snippets: {
        'en-GB': enGB,
        'de-DE': deDE,
        'pl-PL': plPL,
    },

    routes: {
        gateways: {
            component: 'blue-media-gateways',
            path: 'gateways',
            meta: {
                privilege: 'blue_media_gateway.viewer'
            }
        },
        detail: {
            component: 'blue-media-gateways-detail',
            path: 'detail/:id',
            meta: {
                privilege: 'blue_media_gateway.viewer'
            }
        },
    },

    navigation: [{
        id: 'blue-media-gateways',
        path: 'blue.media.gateways',
        label: 'BlueMediaPayment.menu.gatewaysLabel',
        parent: 'sw-order'
    }]
});
