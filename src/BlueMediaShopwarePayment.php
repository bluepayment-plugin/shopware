<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment;

use BlueMedia\ShopwarePayment\Lifecycle\ActivateDeactivate;
use BlueMedia\ShopwarePayment\Lifecycle\DatabaseUninstall;
use BlueMedia\ShopwarePayment\Lifecycle\Icons\IconsFactory;
use BlueMedia\ShopwarePayment\Lifecycle\InstallUninstall;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\RulesManager;
use BlueMedia\ShopwarePayment\Lifecycle\Update;
use BlueMedia\ShopwarePayment\Provider\PaymentProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;

use function dirname;
use function file_exists;

// phpcs:disable
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}
// phpcs:enable

class BlueMediaShopwarePayment extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        $this->getInstallUninstallLifecycle()
            ->install($installContext);

        parent::install($installContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->getActivateDeactivateLifecycle()
            ->activate($activateContext);

        parent::activate($activateContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        $this->getUpdateLifecycle($updateContext)
            ->update($updateContext);

        parent::update($updateContext);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->getActivateDeactivateLifecycle()
            ->deactivate($deactivateContext);

        parent::deactivate($deactivateContext);
    }

    /**
     * @throws Exception
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        $this->getInstallUninstallLifecycle()
            ->uninstall($uninstallContext);

        parent::uninstall($uninstallContext);
    }

    private function getInstallUninstallLifecycle(): InstallUninstall
    {
        $paymentProvider = new PaymentProvider(
            $this->container->get('payment_method.repository')
        );

        return new InstallUninstall(
            $this->container->get('payment_method.repository'),
            $this->container->get(PluginIdProvider::class),
            $paymentProvider,
            new DatabaseUninstall($this->container->get(Connection::class)),
            new RulesManager(
                $this->container->get('payment_method.repository'),
                $this->container->get('rule.repository'),
                $this->container->get('rule_condition.repository'),
                $paymentProvider
            )
        );
    }

    private function getActivateDeactivateLifecycle(): ActivateDeactivate
    {
        return $this->container->get(ActivateDeactivate::class);
    }

    private function getUpdateLifecycle(UpdateContext $updateContext): Update
    {
        $paymentMethodRepository = $this->container->get('payment_method.repository');
        $ruleRepository = $this->container->get('rule.repository');
        $paymentProvider = new PaymentProvider($paymentMethodRepository);
        $mediaService = $this->container->get(MediaService::class);
        $iconsFactory = new IconsFactory();
        $ruleManager =
            $updateContext->getPlugin()->isActive() ?
                $this->container->get(RulesManager::class) :
                new RulesManager(
                    $paymentMethodRepository,
                    $ruleRepository,
                    $this->container->get('rule_condition.repository'),
                    $paymentProvider
                );

        return new Update(
            $paymentMethodRepository,
            $ruleRepository,
            $this->container->get(PluginIdProvider::class),
            $paymentProvider,
            $mediaService,
            $iconsFactory,
            $ruleManager
        );
    }
}
