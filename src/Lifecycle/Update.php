<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle;

use BlueMedia\ShopwarePayment\Lifecycle\Icons\AbstractPaymentIcon;
use BlueMedia\ShopwarePayment\Lifecycle\Icons\IconsFactory;
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
use Shopware\Core\Content\Media\Exception\DuplicatedMediaFileNameException;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;
use Shopware\Core\Framework\Update\Exception\UpdateFailedException;

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

    private IconsFactory $iconsFactory;

    private MediaService $mediaService;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        EntityRepositoryInterface $ruleRepository,
        PluginIdProvider $pluginIdProvider,
        PaymentProvider $paymentProvider,
        MediaService $mediaService,
        IconsFactory $iconsFactory,
        ?RulesManager $rulesManager = null
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->ruleRepository = $ruleRepository;
        $this->pluginIdProvider = $pluginIdProvider;
        $this->paymentProvider = $paymentProvider;
        $this->mediaService = $mediaService;
        $this->iconsFactory = $iconsFactory;
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

        if (version_compare($updateContext->getCurrentPluginVersion(), '1.3.0', '<')) {
            $this->updateTo130($updateContext);
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

    public function updateTo130(UpdateContext $updateContext): void
    {
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

        $pluginId = $this->getPluginId($updateContext->getPlugin(), $updateContext->getContext());

        foreach ($payments as $payment) {
            $this->ensurePluginIdExist($payment, $pluginId, $updateContext->getContext());
        }

        /** @var AbstractPayment[] $payments */
        $payments = [
            new GeneralBlueMediaPayment(),
            new DetailedBlueMediaPayment(),
            new PayByLinkPayment(),
            new QuickTransferPayment(),
        ];

        foreach ($payments as $payment) {
            $this->overwrittePaymentMethodIcon($payment, $updateContext->getContext());
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

    private function ensurePluginIdExist(AbstractPayment $paymentMethodData, string $pluginId, Context $context): void
    {
        $paymentMethod = $this->paymentProvider->getPaymentMethodByHandler(
            $paymentMethodData->getHandlerIdentifier(),
            $context
        );

        if (null === $paymentMethod) {
            throw new UpdateFailedException(
                'The required payment method cannot be found. Uninstall and install the plugin again.'
            );
        }

        if (null === $paymentMethod->getPluginId()) {
            $data = [
                'id' => $paymentMethod->getId(),
                'pluginId' => $pluginId,
            ];

            $this->paymentMethodRepository->update([$data], $context);
        }
    }

    private function overwrittePaymentMethodIcon(AbstractPayment $payment, Context $context): void
    {
        $paymentMethod = $this->paymentProvider->getPaymentMethodByHandler(
            $payment->getHandlerIdentifier(),
            $context
        );
        if ($paymentMethod !== null) {
            $expectedPaymentIcon = $this->iconsFactory->createFromPayment($paymentMethod);
            if (null === $expectedPaymentIcon) {
                return;
            }

            try {
                $mediaId = $this->getMediaId($paymentMethod->getName(), $expectedPaymentIcon, $context);
            } catch (DuplicatedMediaFileNameException $exception) {
                return;
            }

            $paymentMethod = [
                'id' => $paymentMethod->getId(),
                'mediaId' => $mediaId,
            ];
            $this->paymentMethodRepository->update([$paymentMethod], $context);
        }
    }

    private function getMediaId(string $paymentMethodName, AbstractPaymentIcon $icon, Context $context): string
    {
        $fileName = preg_replace('/[^a-z0-9]+/', '-', strtolower($paymentMethodName)) . '-icon';

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('fileName', $fileName));

        //Apply Default Icon if not created or empty
        return $this->mediaService->saveFile(
            $icon->getBlob(),
            $icon->getExtension(),
            $icon->getMime(),
            $fileName,
            $context,
            $icon::BLUE_MEDIA_PAYMENTS_ICONS_FOLDER,
            null,
            false
        );
    }
}
