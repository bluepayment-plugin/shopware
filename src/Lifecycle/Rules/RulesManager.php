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

    public function addPaymentRules(Context $context): void
    {
        $this->addPaymentRule(DetailedPaymentRule::RULE_ID, new DetailedPaymentRule(), $context);
        $this->addPaymentRule(PayByLinkPaymentRule::RULE_ID, new PayByLinkPaymentRule(), $context);
        $this->addPaymentRule(QuickTransferPaymentRule::RULE_ID, new QuickTransferPaymentRule(), $context);
        $this->addPaymentRule(ApplePayPaymentRule::RULE_ID, new ApplePayPaymentRule(), $context);
        $this->addPaymentRule(CardPaymentRule::RULE_ID, new CardPaymentRule(), $context);
        $this->addPaymentRule(BlikPaymentRule::RULE_ID, new BlikPaymentRule(), $context);
        $this->addPaymentRule(GooglePayPaymentRule::RULE_ID, new GooglePayPaymentRule(), $context);
    }

    private function addPaymentRule(string $ruleId, AbstractRule $rule, Context $context): void
    {
        if (null === $this->getRuleById($ruleId, $context)) {
            $payload = $rule->jsonSerialize();

            $this->ruleRepository->upsert([$payload], $context);
        }
    }

    public function ensureAdvancedPaymentRule(AbstractPayment $payment, Context $context): void
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

    private function ensureRuleHasBlueMediaCondition(
        RuleEntity $detailedRule,
        string $handlerIdentifier,
        Context $context
    ): void {
        $ruleCondition = $detailedRule->getConditions();

        $requiredRule = new BlueMediaGatewayAvailable();

        $rule = $ruleCondition ? $ruleCondition->filter(
            static fn (RuleConditionEntity $ruleConditionEntity) =>
                $ruleConditionEntity->getType() === $requiredRule->getName()
        )->first() : null;

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

        $payload = (new GatewayAvailableRuleCondition(
            $detailedRule->getId(),
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

    private function getRuleById(string $ruleId, Context $context): ?RuleEntity
    {
        return $this->ruleRepository->search(
            (new Criteria([$ruleId]))->addAssociation('conditions'),
            $context
        )->first();
    }
}
