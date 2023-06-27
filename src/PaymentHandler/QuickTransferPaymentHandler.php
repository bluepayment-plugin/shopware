<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Exception\NoActiveGatewayIdSelected;
use BlueMedia\ShopwarePayment\Processor\BackgroundTransactionProcessor;
use BlueMedia\ShopwarePayment\Provider\SalesChannelContextDataProvider;
use BlueMedia\ShopwarePayment\Util\GatewayTypes;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\SyncPaymentProcessException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Throwable;

class QuickTransferPaymentHandler implements BlueMediaPaymentHandlerInterface, SynchronousPaymentHandlerInterface
{
    private BackgroundTransactionProcessor $backgroundTransactionProcessor;

    private SalesChannelContextDataProvider $channelContextDataProvider;

    public function __construct(
        BackgroundTransactionProcessor $backgroundTransactionProcessor,
        SalesChannelContextDataProvider $channelContextDataProvider
    ) {
        $this->backgroundTransactionProcessor = $backgroundTransactionProcessor;
        $this->channelContextDataProvider = $channelContextDataProvider;
    }

    public function pay(
        SyncPaymentTransactionStruct $transaction,
        RequestDataBag $dataBag,
        SalesChannelContext $salesChannelContext
    ): void {
        try {
            $gatewayEntity = $this->channelContextDataProvider->getSelectedGateway(
                $salesChannelContext,
                $transaction->getOrderTransaction()->getAmount()->getTotalPrice()
            );
            if (!$this->isGatewaySupported($gatewayEntity)) {
                throw new NoActiveGatewayIdSelected();
            }
            $this->backgroundTransactionProcessor->process(
                $transaction,
                $salesChannelContext,
                $gatewayEntity
            );
        } catch (Throwable $e) {
            throw new SyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                $e->getMessage()
            );
        }
    }

    public function isGatewaySupported(GatewayEntity $gatewayEntity): bool
    {
        return $gatewayEntity->getType(true) === GatewayTypes::FAST_TRANSFER;
    }

    public function gatewayGroupingSupported(): bool
    {
        return false;
    }

    public function isGatewayParamRequired(): bool
    {
        return true;
    }
}
