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
        parent::update($updateContext);

        $this->getInstallUninstallLifecycle()
            ->update($updateContext);
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
        return new InstallUninstall(
            $this->container->get('payment_method.repository'),
            $this->container->get('currency.repository'),
            $this->container->get('rule.repository'),
            $this->container->get(PluginIdProvider::class),
            new PaymentProvider(
                $this->container->get('payment_method.repository')
            ),
            new DatabaseUninstall($this->container->get(Connection::class))
        );
    }

    private function getActivateDeactivateLifecycle(): ActivateDeactivate
    {
        return $this->container->get(ActivateDeactivate::class);
    }
}
