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

const getComponentTypeLabel = (value, component) => {
    let typeLabels = [
        {'value': 'Szybki Przelew', 'key': 'BlueMediaPayment.typeLabels.quickTransfer'},
        {'value': 'PBL', 'key': 'BlueMediaPayment.typeLabels.payByLink'},
        {'value': 'Raty online', 'key': 'BlueMediaPayment.typeLabels.onlineInstallments'},
        {'value': 'BLIK', 'key': 'BlueMediaPayment.typeLabels.blik'},
        {'value': 'Portfel elektroniczny', 'key': 'BlueMediaPayment.typeLabels.electronicWallet'},
        {'value': 'Płatność automatyczna', 'key': 'BlueMediaPayment.typeLabels.automaticPayment'},
        {'value': 'Karta płatnicza', 'key': 'BlueMediaPayment.typeLabels.creditCard'},
    ];
    const option = typeLabels.find((item) => {
        return item.value === value;
    })
    if (option === undefined) {
        return value.concat(" [", component.$t('BlueMediaPayment.typeLabels.unknown'), "]");
    }
    return component.$t(option.key);
}

export {
    extractPluginConfig,
    trimNulls,
    getComponentTypeLabel
}