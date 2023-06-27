<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle;

use BlueMedia\ShopwarePayment\Lifecycle\Currency\PlnCurrency;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\AbstractPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\ApplePayPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\BlikPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\CardPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\DetailedBlueMediaPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\GeneralBlueMediaPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\GooglePayPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\PayByLinkPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\QuickTransferPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\AbstractRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\ApplePayPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\BlikPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\CardPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\CurrencyPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\DetailedPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\GooglePayPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\PayByLinkPaymentRule;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\QuickTransferPaymentRule;
use BlueMedia\ShopwarePayment\Provider\PaymentProvider;
use BlueMedia\ShopwarePayment\Util\Constants;
use Doctrine\DBAL\Exception;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;
use Shopware\Core\System\Currency\CurrencyDefinition;

class InstallUninstall
{
    private EntityRepositoryInterface $paymentMethodRepository;

    private EntityRepositoryInterface $currencyRepository;

    private EntityRepositoryInterface $ruleRepository;

    private PluginIdProvider $pluginIdProvider;

    private PaymentProvider $paymentProvider;

    private DatabaseUninstall $databaseUninstall;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        EntityRepositoryInterface $currencyRepository,
        EntityRepositoryInterface $ruleRepository,
        PluginIdProvider $pluginIdProvider,
        PaymentProvider $paymentProvider,
        DatabaseUninstall $databaseUninstall
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->currencyRepository = $currencyRepository;
        $this->ruleRepository = $ruleRepository;
        $this->pluginIdProvider = $pluginIdProvider;
        $this->paymentProvider = $paymentProvider;
        $this->databaseUninstall = $databaseUninstall;
    }

    public function install(InstallContext $installContext): void
    {
        $this->addPaymentRules($installContext->getContext());

        $this->addPaymentMethod($installContext, new GeneralBlueMediaPayment());
        $this->addPaymentMethod($installContext, new DetailedBlueMediaPayment());
        $this->addPaymentMethod($installContext, new PayByLinkPayment());
        $this->addPaymentMethod($installContext, new QuickTransferPayment());
        $this->addPaymentMethod($installContext, new ApplePayPayment());
        $this->addPaymentMethod($installContext, new CardPayment());
        $this->addPaymentMethod($installContext, new BlikPayment());
        $this->addPaymentMethod($installContext, new GooglePayPayment());
    }

    /**
     * @throws Exception
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        // Only set the payment method to inactive when uninstalling. Removing the payment method would
        // cause data consistency issues, since the payment method might have been used in several orders
        // do not delete payment rule
        $this->deactivatePaymentMethod($uninstallContext->getContext(), new GeneralBlueMediaPayment());
        $this->deactivatePaymentMethod($uninstallContext->getContext(), new DetailedBlueMediaPayment());
        $this->deactivatePaymentMethod($uninstallContext->getContext(), new PayByLinkPayment());
        $this->deactivatePaymentMethod($uninstallContext->getContext(), new QuickTransferPayment());
        $this->deactivatePaymentMethod($uninstallContext->getContext(), new ApplePayPayment());
        $this->deactivatePaymentMethod($uninstallContext->getContext(), new CardPayment());
        $this->deactivatePaymentMethod($uninstallContext->getContext(), new BlikPayment());
        $this->deactivatePaymentMethod($uninstallContext->getContext(), new GooglePayPayment());

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->databaseUninstall->uninstall($uninstallContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        if ($updateContext->getCurrentPluginVersion() <= '1.0.0') {
            $this->addPaymentRules($updateContext->getContext());

            $this->addPaymentMethodIfNotExists($updateContext, new DetailedBlueMediaPayment());
            $this->addPaymentMethodIfNotExists($updateContext, new PayByLinkPayment());
            $this->addPaymentMethodIfNotExists($updateContext, new QuickTransferPayment());
            $this->addPaymentMethodIfNotExists($updateContext, new ApplePayPayment());
            $this->addPaymentMethodIfNotExists($updateContext, new CardPayment());
            $this->addPaymentMethodIfNotExists($updateContext, new BlikPayment());
            $this->addPaymentMethodIfNotExists($updateContext, new GooglePayPayment());
        }
    }

    private function addPaymentMethod(InstallContext $installContext, AbstractPayment $payment): void
    {
        if ($this->getPaymentMethodId($installContext->getContext(), $payment)) {
            return;
        }

        $payment->setPluginId($this->getPluginId($installContext->getPlugin(), $installContext->getContext()));
        $payload = $payment->jsonSerialize();

        $this->paymentMethodRepository->create([$payload], $installContext->getContext());
    }

    private function addPaymentRules(Context $context): void
    {
        $currencyIds = $this->getSupportedCurrencyIds($context);

        $this->addPaymentRule(CurrencyPaymentRule::RULE_ID, new CurrencyPaymentRule($currencyIds), $context);
        $this->addPaymentRule(DetailedPaymentRule::RULE_ID, new DetailedPaymentRule($currencyIds), $context);
        $this->addPaymentRule(PayByLinkPaymentRule::RULE_ID, new PayByLinkPaymentRule($currencyIds), $context);
        $this->addPaymentRule(QuickTransferPaymentRule::RULE_ID, new QuickTransferPaymentRule($currencyIds), $context);
        $this->addPaymentRule(ApplePayPaymentRule::RULE_ID, new ApplePayPaymentRule($currencyIds), $context);
        $this->addPaymentRule(CardPaymentRule::RULE_ID, new CardPaymentRule($currencyIds), $context);
        $this->addPaymentRule(BlikPaymentRule::RULE_ID, new BlikPaymentRule($currencyIds), $context);
        $this->addPaymentRule(GooglePayPaymentRule::RULE_ID, new GooglePayPaymentRule($currencyIds), $context);
    }

    private function addPaymentRule(string $ruleId, AbstractRule $rule, Context $context): void
    {
        if (null === $this->getRuleById($ruleId, $context)) {
            $payload = $rule->jsonSerialize();

            $this->ruleRepository->upsert([$payload], $context);
        }
    }

    private function deactivatePaymentMethod(Context $context, AbstractPayment $payment): void
    {
        $paymentMethodId = $this->getPaymentMethodId($context, $payment);
        if (!$paymentMethodId) {
            return;
        }

        $paymentMethod = [
            'id' => $paymentMethodId,
            'active' => false,
        ];
        $this->paymentMethodRepository->update([$paymentMethod], $context);
    }

    private function getPaymentMethodId(Context $context, AbstractPayment $payment): ?string
    {
        return $this->paymentProvider->getPaymentMethodIdByHandler(
            $payment->getHandlerIdentifier(),
            $context
        );
    }

    private function getRuleById(string $ruleId, Context $context): ?RuleEntity
    {
        return $this->ruleRepository->search(new Criteria([$ruleId]), $context)->first();
    }

    /**
     * @return string[]
     */
    private function getSupportedCurrencyIds(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('isoCode', Constants::CURRENCY_PLN));

        $result = $this->currencyRepository->search($criteria, $context)->getEntities();

        // create currency if not exist
        if (0 === $result->count()) {
            $created = $this->currencyRepository->create([(new PlnCurrency())->jsonSerialize()], $context);

            return $created->getPrimaryKeys(CurrencyDefinition::ENTITY_NAME);
        }

        return array_values($result->getIds());
    }

    private function getPluginId(Plugin $plugin, Context $context): string
    {
        return $this->pluginIdProvider->getPluginIdByBaseClass(
            get_class($plugin),
            $context
        );
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
}
