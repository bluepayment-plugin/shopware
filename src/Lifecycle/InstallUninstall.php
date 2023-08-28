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
use BlueMedia\ShopwarePayment\Lifecycle\Rules\RulesManager;
use BlueMedia\ShopwarePayment\Provider\PaymentProvider;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;

class InstallUninstall
{
    private EntityRepositoryInterface $paymentMethodRepository;

    private PluginIdProvider $pluginIdProvider;

    private PaymentProvider $paymentProvider;

    private DatabaseUninstall $databaseUninstall;

    private RulesManager $rulesManager;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        PluginIdProvider $pluginIdProvider,
        PaymentProvider $paymentProvider,
        DatabaseUninstall $databaseUninstall,
        RulesManager $rulesManager
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->pluginIdProvider = $pluginIdProvider;
        $this->paymentProvider = $paymentProvider;
        $this->databaseUninstall = $databaseUninstall;
        $this->rulesManager = $rulesManager;
    }

    public function install(InstallContext $installContext): void
    {
        $this->rulesManager->addPaymentRules($installContext->getContext());

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

    private function addPaymentMethod(InstallContext $installContext, AbstractPayment $payment): void
    {
        if ($this->getPaymentMethodId($installContext->getContext(), $payment)) {
            return;
        }

        $payment->setPluginId($this->getPluginId($installContext->getPlugin(), $installContext->getContext()));
        $payload = $payment->jsonSerialize();

        $this->paymentMethodRepository->create([$payload], $installContext->getContext());
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

    private function getPluginId(Plugin $plugin, Context $context): string
    {
        return $this->pluginIdProvider->getPluginIdByBaseClass(
            get_class($plugin),
            $context
        );
    }
}
