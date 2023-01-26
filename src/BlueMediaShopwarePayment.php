<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment;

use BlueMedia\ShopwarePayment\Lifecycle\ActivateDeactivate;
use BlueMedia\ShopwarePayment\Lifecycle\DatabaseUninstall;
use BlueMedia\ShopwarePayment\Lifecycle\InstallUninstall;
use BlueMedia\ShopwarePayment\Provider\PaymentProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
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
        parent::install($installContext);

        $this->getInstallUninstallLifecycle()
            ->install($installContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        $this->getActivateDeactivateLifecycle()
            ->activate($activateContext);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        parent::deactivate($deactivateContext);

        $this->getActivateDeactivateLifecycle()
            ->deactivate($deactivateContext);
    }

    /**
     * @throws Exception
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        $this->getInstallUninstallLifecycle()
            ->uninstall($uninstallContext);
    }

    private function getInstallUninstallLifecycle(): InstallUninstall
    {
        return new InstallUninstall(
            $this->container->get('payment_method.repository'),
            $this->container->get('currency.repository'),
            $this->container->get('rule.repository'),
            $this->container->get(PluginIdProvider::class),
            new PaymentProvider(
                $this->container->get('payment_method.repository')
            ),
            new DatabaseUninstall(
                $this->container->get(Connection::class)
            )
        );
    }

    private function getActivateDeactivateLifecycle(): ActivateDeactivate
    {
        return $this->container->get(ActivateDeactivate::class);
    }
}
