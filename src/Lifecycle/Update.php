<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle;

use BlueMedia\ShopwarePayment\Lifecycle\Payments\AbstractPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\ApplePayPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\BlikPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\CardPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\DetailedBlueMediaPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\GeneralBlueMediaPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\GooglePayPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\PayByLinkPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\QuickTransferPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\ApplePayPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\BlikPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\CardPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\CurrencyPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\DetailedPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\GooglePayPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\PayByLinkPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\QuickTransferPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\RulesManager;
use BlueMedia\ShopwarePayment\PaymentHandler\ApplePayPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\BlikPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\CardPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\DetailedPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\GeneralPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\GooglePayPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\PayByLinkPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\QuickTransferPaymentHandler;
use BlueMedia\ShopwarePayment\Provider\PaymentProvider;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;

use function array_map;
use function array_values;
use function version_compare;

class Update
{
    private EntityRepositoryInterface $paymentMethodRepository;

    private EntityRepositoryInterface $ruleRepository;

    private PluginIdProvider $pluginIdProvider;

    private PaymentProvider $paymentProvider;

    private ?RulesManager $rulesManager;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        EntityRepositoryInterface $ruleRepository,
        PluginIdProvider $pluginIdProvider,
        PaymentProvider $paymentProvider,
        ?RulesManager $rulesManager = null
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->ruleRepository = $ruleRepository;
        $this->pluginIdProvider = $pluginIdProvider;
        $this->paymentProvider = $paymentProvider;
        $this->rulesManager = $rulesManager;
    }

    public function update(UpdateContext $updateContext): void
    {
        if (version_compare($updateContext->getCurrentPluginVersion(), '1.1.0', '<')) {
            $this->updateTo110($updateContext);
        }

        if (version_compare($updateContext->getCurrentPluginVersion(), '1.2.0', '<')) {
            $this->updateTo120($updateContext);
        }
    }

    private function updateTo110(UpdateContext $updateContext): void
    {
        $this->rulesManager->addPaymentRules($updateContext->getContext());

        $this->addPaymentMethodIfNotExists($updateContext, new DetailedBlueMediaPayment());
        $this->addPaymentMethodIfNotExists($updateContext, new PayByLinkPayment());
        $this->addPaymentMethodIfNotExists($updateContext, new QuickTransferPayment());
        $this->addPaymentMethodIfNotExists($updateContext, new ApplePayPayment());
        $this->addPaymentMethodIfNotExists($updateContext, new CardPayment());
        $this->addPaymentMethodIfNotExists($updateContext, new BlikPayment());
        $this->addPaymentMethodIfNotExists($updateContext, new GooglePayPayment());
    }

    private function updateTo120(UpdateContext $updateContext): void
    {
        $context = $updateContext->getContext();

        $rulesIdsToUnlink = [
            GeneralPaymentHandler::class => CurrencyPaymentRule::RULE_ID,
            ApplePayPaymentHandler::class => ApplePayPaymentRule::RULE_ID,
            BlikPaymentHandler::class => BlikPaymentRule::RULE_ID,
            CardPaymentHandler::class => CardPaymentRule::RULE_ID,
            DetailedPaymentHandler::class => DetailedPaymentRule::RULE_ID,
            GooglePayPaymentHandler::class => GooglePayPaymentRule::RULE_ID,
            PayByLinkPaymentHandler::class => PayByLinkPaymentRule::RULE_ID,
            QuickTransferPaymentHandler::class => QuickTransferPaymentRule::RULE_ID,
        ];

        foreach ($rulesIdsToUnlink as $handlerClass => $ruleId) {
            $criteria = new Criteria();
            $criteria->addFilter(
                new AndFilter([
                    new EqualsFilter('handlerIdentifier', $handlerClass),
                    new EqualsFilter('availabilityRuleId', $ruleId),
                ])
            );

            $paymentId = $this->paymentMethodRepository->searchIds($criteria, $context)->firstId();

            if (null === $paymentId) {
                continue;
            }

            // unlink rule from payment
            $this->paymentMethodRepository->update([
                [
                    'id' => $paymentId,
                    'availabilityRuleId' => null,
                ],
            ], $context);
        }

        // delete all payment rule
        $this->ruleRepository->delete(
            array_map(static fn (string $id) => ['id' => $id], array_values($rulesIdsToUnlink)),
            $context
        );

        /** @var AbstractPayment[] $payments */
        $payments = [
            new GeneralBlueMediaPayment(),
            new ApplePayPayment(),
            new BlikPayment(),
            new CardPayment(),
            new DetailedBlueMediaPayment(),
            new GooglePayPayment(),
            new PayByLinkPayment(),
            new QuickTransferPayment(),
        ];

        $this->rulesManager->addPaymentRules($context);

        if ($updateContext->getPlugin()->isActive()) {
            foreach ($payments as $payment) {
                $this->rulesManager->ensureAdvancedPaymentRule($payment, $context);
            }
        }
    }

    private function addPaymentMethodIfNotExists(UpdateContext $updateContext, AbstractPayment $payment): void
    {
        if ($this->getPaymentMethodId($updateContext->getContext(), $payment)) {
            return;
        }

        $payment->setActive($updateContext->getPlugin()->isActive());
        $payment->setPluginId(
            $this->getPluginId(
                $updateContext->getPlugin(),
                $updateContext->getContext()
            )
        );
        $payload = $payment->jsonSerialize();

        $this->paymentMethodRepository->create([$payload], $updateContext->getContext());
    }

    private function getPluginId(Plugin $plugin, Context $context): string
    {
        return $this->pluginIdProvider->getPluginIdByBaseClass(
            get_class($plugin),
            $context
        );
    }

    private function getPaymentMethodId(Context $context, AbstractPayment $payment): ?string
    {
        return $this->paymentProvider->getPaymentMethodIdByHandler(
            $payment->getHandlerIdentifier(),
            $context
        );
    }
}
