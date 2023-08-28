Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: null,
    key: 'blue_media_gateways',
    roles: {
        viewer: {
            privileges: ['blue_media_gateway:read'],
            dependencies: []
        },
        editor: {
            privileges: [
                'blue_media_gateway:update',
            ],
            dependencies: []
        },
        creator: {
            privileges: [
                'blue_media_gateway:create',
            ],
            dependencies: []
        },
        deleter: {
            privileges: ['blue_media_gateway:delete'],
            dependencies: []
        }
    }
});
