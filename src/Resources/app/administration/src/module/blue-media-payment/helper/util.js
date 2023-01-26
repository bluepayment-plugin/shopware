const extractPluginConfig = (parentComponent) => {
    while (!parentComponent.actualConfigData) {
        parentComponent = parentComponent.$parent;
    }

    // handle sales channel inheritance
    return {
        ...(parentComponent?.actualConfigData?.null || {}),
        ...(trimNulls(parentComponent?.actualConfigData?.[parentComponent.currentSalesChannelId]) || {}),
    };
}

const trimNulls = (obj) => {
    return Object.fromEntries(Object.entries(obj).filter(([_, v]) => v !== null));
}

export {
    extractPluginConfig,
    trimNulls
}