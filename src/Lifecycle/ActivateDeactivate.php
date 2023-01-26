<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle;

use BlueMedia\ShopwarePayment\Lifecycle\Payments\AbstractPayment;
use BlueMedia\ShopwarePayment\Lifecycle\Payments\DefaultPaymentIcon;
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

    private DefaultPaymentIcon $defaultPaymentIcon;

    private AbstractPayment $payment;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        EntityRepositoryInterface $mediaRepository,
        MediaService $mediaService,
        PaymentProvider $paymentProvider,
        DefaultPaymentIcon $defaultPaymentIcon,
        AbstractPayment $payment
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->mediaRepository = $mediaRepository;
        $this->mediaService = $mediaService;
        $this->paymentProvider = $paymentProvider;
        $this->defaultPaymentIcon = $defaultPaymentIcon;
        $this->payment = $payment;
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->setPaymentMethodIsActive(true, $activateContext->getContext());
        $this->ensurePaymentMethodIcon($activateContext->getContext());
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->setPaymentMethodIsActive(false, $deactivateContext->getContext());
    }

    private function setPaymentMethodIsActive(bool $active, Context $context): void
    {
        $paymentMethodId = $this->paymentProvider->getPaymentMethodIdByHandler(
            $this->payment->getHandlerIdentifier(),
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

    private function ensurePaymentMethodIcon(Context $context): void
    {
        $paymentMethod = $this->getPaymentMethod($context);
        if ($paymentMethod !== null && $paymentMethod->getMediaId() === null) {
            $paymentMethod = [
                'id' => $paymentMethod->getId(),
                'mediaId' => $this->getMediaId($this->payment->getName(), $context),
            ];
            $this->paymentMethodRepository->update([$paymentMethod], $context);
        }
    }

    private function getPaymentMethod(Context $context): ?PaymentMethodEntity
    {
        return $this->paymentProvider->getPaymentMethodByHandler(
            $this->payment->getHandlerIdentifier(),
            $context
        );
    }

    private function getMediaId(string $paymentMethodName, Context $context): string
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
            $this->defaultPaymentIcon->getBlob(),
            $this->defaultPaymentIcon->getExtension(),
            $this->defaultPaymentIcon->getMime(),
            $fileName,
            $context,
            DefaultPaymentIcon::BLUE_MEDIA_PAYMENTS_ICONS_FOLDER,
            null,
            false
        );
    }
}
