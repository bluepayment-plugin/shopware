import '../core/component/autopay-rule';

Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('blue_media_rule', {
        component: 'autopay-rule',
        label: 'Is any Autopay Gateway Avaliable',
        scopes: ['global']
    });

    return ruleConditionService;
});