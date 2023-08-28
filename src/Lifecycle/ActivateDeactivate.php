<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle;

use BlueMedia\ShopwarePayment\Lifecycle\Icons\AbstractPaymentIcon;
use BlueMedia\ShopwarePayment\Lifecycle\Icons\IconsFactory;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\AbstractPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\GeneralBlueMediaPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Rules\RulesManager;
use BlueMedia\ShopwarePayment\Provider\PaymentProvider;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;

class ActivateDeactivate
{
    private EntityRepositoryInterface $paymentMethodRepository;

    private EntityRepositoryInterface $mediaRepository;

    private MediaService $mediaService;

    private PaymentProvider $paymentProvider;

    private IconsFactory $iconsFactory;

    private RulesManager $rulesManager;

    /**
     * @var AbstractPayment[]
     */
    private array $payments;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        EntityRepositoryInterface $mediaRepository,
        MediaService $mediaService,
        PaymentProvider $paymentProvider,
        IconsFactory $iconsFactory,
        RulesManager $rulesManager,
        array $payments
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->mediaRepository = $mediaRepository;
        $this->mediaService = $mediaService;
        $this->paymentProvider = $paymentProvider;
        $this->iconsFactory = $iconsFactory;
        $this->payments = $payments;
        $this->rulesManager = $rulesManager;
    }

    public function activate(ActivateContext $activateContext): void
    {
        foreach ($this->payments as $payment) {
            $this->setPaymentMethodStatus(true, $payment, $activateContext->getContext());
            $this->ensurePaymentMethodIcon($payment, $activateContext->getContext());
            if (!$payment instanceof GeneralBlueMediaPayment) {
                $this->rulesManager->ensureAdvancedPaymentRule($payment, $activateContext->getContext());
            }
        }
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        foreach ($this->payments as $payment) {
            $this->setPaymentMethodStatus(false, $payment, $deactivateContext->getContext());
        }
    }

    private function setPaymentMethodStatus(bool $active, AbstractPayment $payment, Context $context): void
    {
        $paymentMethodId = $this->paymentProvider->getPaymentMethodIdByHandler(
            $payment->getHandlerIdentifier(),
            $context
        );
        if (!$paymentMethodId) {
            return;
        }

        $paymentMethod = [
            'id' => $paymentMethodId,
            'active' => $active,
        ];
        $this->paymentMethodRepository->update([$paymentMethod], $context);
    }

    private function ensurePaymentMethodIcon(AbstractPayment $payment, Context $context): void
    {
        $paymentMethod = $this->getPaymentMethod($payment, $context);
        if ($paymentMethod !== null && $paymentMethod->getMediaId() === null) {
            $expectedPaymentIcon = $this->iconsFactory->createFromPayment($paymentMethod);
            if (null === $expectedPaymentIcon) {
                return;
            }

            $paymentMethod = [
                'id' => $paymentMethod->getId(),
                'mediaId' => $this->getMediaId($paymentMethod->getName(), $expectedPaymentIcon, $context),
            ];
            $this->paymentMethodRepository->update([$paymentMethod], $context);
        }
    }

    private function getPaymentMethod(AbstractPayment $payment, Context $context): ?PaymentMethodEntity
    {
        return $this->paymentProvider->getPaymentMethodByHandler(
            $payment->getHandlerIdentifier(),
            $context
        );
    }

    private function getMediaId(string $paymentMethodName, AbstractPaymentIcon $icon, Context $context): string
    {
        $fileName = preg_replace('/[^a-z0-9]+/', '-', strtolower($paymentMethodName)) . '-icon';

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('fileName', $fileName));

        /** @var MediaCollection $icons */
        $icons = $this->mediaRepository->search($criteria, $context);

        if ($icons->count() && $icons->first() !== null) {
            return $icons->first()->getId();
        }

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
