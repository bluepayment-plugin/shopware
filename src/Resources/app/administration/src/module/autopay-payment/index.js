import './acl';
import './component';
import './page/autopay-gateways';
import './view/autopay-gateways-detail';

import enGB from '../../snippet/en-GB.json';
import deDE from '../../snippet/de-DE.json';
import plPL from '../../snippet/pl-PL.json';

const { Module } = Shopware;

Module.register('blue-media', {
    type: 'plugin',
    name: 'Autopay',
    title: 'BlueMediaPayment.menu.mainMenuLabel',
    description: 'BlueMediaPayment.moduleDescription',
    color: '#0C73BD',
    icon: 'default-money-wallet',

    snippets: {
        'en-GB': enGB,
        'de-DE': deDE,
        'pl-PL': plPL,
    },

    routes: {
        gateways: {
            component: 'autopay-gateways',
            path: 'gateways',
            meta: {
                privilege: 'blue_media_gateway.viewer'
            }
        },
        detail: {
            component: 'autopay-gateways-detail',
            path: 'detail/:id',
            meta: {
                privilege: 'blue_media_gateway.viewer'
            }
        },
    },

    navigation: [{
        id: 'autopay-gateways',
        path: 'blue.media.gateways',
        label: 'BlueMediaPayment.menu.gatewaysLabel',
        parent: 'sw-order'
    }]
});
