<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle;

use BlueMedia\ShopwarePayment\Lifecycle\Currency\PlnCurrency;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\GeneralBlueMediaPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\CurrencyPaymentRule;
use BlueMedia\ShopwarePayment\Provider\PaymentProvider;
use BlueMedia\ShopwarePayment\Util\Constants;
use Doctrine\DBAL\Exception;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
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
        $this->addCurrencyPaymentRule($installContext->getContext());
        $this->addPaymentMethod($installContext);
    }

    /**
     * @throws Exception
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        // Only set the payment method to inactive when uninstalling. Removing the payment method would
        // cause data consistency issues, since the payment method might have been used in several orders
        // do not delete payment rule
        $this->deactivatePaymentMethod($uninstallContext->getContext());

        $this->databaseUninstall->uninstall($uninstallContext);
    }

    private function addPaymentMethod(InstallContext $context): void
    {
        if ($this->getPaymentMethodId($context->getContext())) {
            return;
        }

        $payment = new GeneralBlueMediaPayment();
        $pluginId = $this->pluginIdProvider->getPluginIdByBaseClass(
            get_class($context->getPlugin()),
            $context->getContext()
        );
        $payment->setPluginId($pluginId);

        $payment->setAvailabilityRuleId(CurrencyPaymentRule::RULE_ID);

        $payload = $payment->jsonSerialize();

        $this->paymentMethodRepository->create([$payload], $context->getContext());
    }

    private function addCurrencyPaymentRule(Context $context): void
    {
        if (null !== $this->getRuleById(CurrencyPaymentRule::RULE_ID, $context)) {
            return;
        }

        $currencyIds = $this->getSupportedCurrencyIds($context);

        $payload = (new CurrencyPaymentRule($currencyIds))->jsonSerialize();

        $this->ruleRepository->create([$payload], $context);
    }

    private function deactivatePaymentMethod(Context $context): void
    {
        $paymentMethodId = $this->getPaymentMethodId($context);
        if (!$paymentMethodId) {
            return;
        }

        $paymentMethod = [
            'id' => $paymentMethodId,
            'active' => false,
        ];
        $this->paymentMethodRepository->update([$paymentMethod], $context);
    }

    private function getPaymentMethodId(Context $context): ?string
    {
        return $this->paymentProvider->getPaymentMethodIdByHandler(
            (new GeneralBlueMediaPayment())->getHandlerIdentifier(),
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
}
