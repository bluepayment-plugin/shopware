import '../core/component/blue-media-rule';

Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('blue_media_rule', {
        component: 'blue-media-rule',
        label: 'Is any Blue Media Gateway Avaliable',
        scopes: ['global']
    });

    return ruleConditionService;
});