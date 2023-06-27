const canUseApplePay = () => {
    return Boolean(window.ApplePaySession && ApplePaySession.canMakePayments());
}

export {
    canUseApplePay
};