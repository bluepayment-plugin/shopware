<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

use BlueMedia\ShopwarePayment\Lifecycle\Payments\AbstractPayment;
use BlueMedia\ShopwarePayment\Lifecycle\RuleConditions\GatewayAvailableRuleCondition;
use BlueMedia\ShopwarePayment\Provider\PaymentProvider;
use BlueMedia\ShopwarePayment\Rule\BlueMediaGatewayAvailable;
use Shopware\Core\Content\Rule\Aggregate\RuleCondition\RuleConditionEntity;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Currency\Rule\CurrencyRule;

class RulesManager
{
    private EntityRepositoryInterface $paymentMethodRepository;

    private EntityRepositoryInterface $ruleRepository;

    private EntityRepositoryInterface $ruleConditionRepository;

    private PaymentProvider $paymentProvider;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        EntityRepositoryInterface $ruleRepository,
        EntityRepositoryInterface $ruleConditionRepository,
        PaymentProvider $paymentProvider
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->ruleRepository = $ruleRepository;
        $this->ruleConditionRepository = $ruleConditionRepository;
        $this->paymentProvider = $paymentProvider;
    }

    public function ensureAdvancedPaymentRule(AbstractPayment $payment, Context $context)
    {
        $paymentMethodId = $this->paymentProvider->getPaymentMethodIdByHandler(
            $payment->getHandlerIdentifier(),
            $context
        );
        if (!$paymentMethodId || null === $payment->getAvailabilityRuleId()) {
            return;
        }

        $paymentMethod = [
            'id' => $paymentMethodId,
            'availabilityRuleId' => $payment->getAvailabilityRuleId(),
        ];
        $this->paymentMethodRepository->update([$paymentMethod], $context);

        $detailedRule = $this->getRuleById($payment->getAvailabilityRuleId(), $context);
        if (null !== $detailedRule) {
            $this->ensureRuleHasBlueMediaCondition($detailedRule, $payment->getHandlerIdentifier(), $context);
        }
    }

    private function getRuleById(string $ruleId, Context $context): ?RuleEntity
    {
        return $this->ruleRepository->search(
            (new Criteria([$ruleId]))->addAssociation('conditions'),
            $context
        )->first();
    }

    private function ensureRuleHasBlueMediaCondition(
        RuleEntity $detailedRule,
        string $handlerIdentifier,
        Context $context
    ) {
        $ruleCondition = $detailedRule->getConditions();

        $requiredRule = new BlueMediaGatewayAvailable();

        $rule = $ruleCondition->filter(
            static fn (RuleConditionEntity $ruleConditionEntity) =>
                $ruleConditionEntity->getType() === $requiredRule->getName()
        )->first();

        if ($rule instanceof RuleConditionEntity) {
            if (!$this->isRuleValueValid($rule->getValue(), $handlerIdentifier)) {
                $payload = [
                    'id' => $rule->getId(),
                    'value' => [
                        'paymentHandler' => $handlerIdentifier,
                    ],
                ];
                $this->ruleConditionRepository->update([$payload], $context);
            }
            return;
        }

        $currencyRule = $ruleCondition->filter(
            static fn (RuleConditionEntity $ruleConditionEntity) =>
                $ruleConditionEntity->getType() === (new CurrencyRule())->getName()
        )->first();

        if (null === $currencyRule) {
            return;
        }
        $payload = (new GatewayAvailableRuleCondition(
            $currencyRule->getRuleId(),
            $currencyRule->getParentId(),
            $handlerIdentifier
        ))->jsonSerialize();

        $this->ruleConditionRepository->create([$payload], $context);
    }

    private function isRuleValueValid(?array $value, string $handlerIdentifier): bool
    {
        return is_array($value)
            && key_exists('paymentHandler', $value)
            && $value['paymentHandler'] === $handlerIdentifier;
    }
}
