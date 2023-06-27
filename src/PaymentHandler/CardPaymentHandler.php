<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Exception\ConfirmationCheckFailedException;
use BlueMedia\ShopwarePayment\Processor\InitTransactionProcessor;
use BlueMedia\ShopwarePayment\Struct\CartContinueUrlStruct;
use BlueMedia\ShopwarePayment\Util\GatewayIds;
use BlueMedia\ShopwarePayment\Util\GatewayTypes;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentFinalizeException;
use Shopware\Core\Checkout\Payment\Exception\SyncPaymentProcessException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class CardPaymentHandler implements BlueMediaPaymentHandlerInterface, SynchronousPaymentHandlerInterface
{
    private const IFRAME_SCREEN_TYPE = 'IFRAME';

    public const CARD_PAYMENT_CONTINUE_URL = 'blueMediaCardPaymentContinueUrl';

    private InitTransactionProcessor $initTransactionProcessor;

    private Router $router;

    private ClientFactory $clientFactory;

    public function __construct(
        InitTransactionProcessor $initTransactionProcessor,
        Router $router,
        ClientFactory $clientFactory
    ) {
        $this->initTransactionProcessor = $initTransactionProcessor;
        $this->router = $router;
        $this->clientFactory = $clientFactory;
    }

    public function pay(
        SyncPaymentTransactionStruct $transaction,
        RequestDataBag $dataBag,
        SalesChannelContext $salesChannelContext
    ): void {
        try {
            $transactionContinue = $this->initTransactionProcessor->process(
                $transaction,
                $salesChannelContext,
                [
                    InitTransactionProcessor::PARAM_GATEWAY_ID => GatewayIds::GENERAL_CREDIT_CARD,
                    InitTransactionProcessor::PARAM_SCREEN_TYPE => self::IFRAME_SCREEN_TYPE,
                ]
            );
            $salesChannelContext->addExtension(
                self::CARD_PAYMENT_CONTINUE_URL,
                new CartContinueUrlStruct(
                    $transactionContinue->getRedirectUrl(),
                    $this->getErrorUrl($transaction->getOrder()->getId())
                )
            );
        } catch (Throwable $e) {
            throw new SyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                $e->getMessage()
            );
        }
    }

    /**
     * Implemented as a workaround for configuration unexpected 3DSecure redirect.
     * supported by: \BlueMedia\ShopwarePayment\Processor\FinalizeTransactionProcessor::process
     * @throws AsyncPaymentFinalizeException
     */
    public function finalize(
        AsyncPaymentTransactionStruct $transaction,
        Request $request,
        SalesChannelContext $salesChannelContext
    ): void {
        try {
            $client = $this->clientFactory->createFromPluginConfig($salesChannelContext->getSalesChannelId());

            $valid = $client->doConfirmationCheck($request->query->all());
            if (false === $valid) {
                throw new ConfirmationCheckFailedException(
                    $transaction->getOrder()->getId(),
                    $transaction->getOrderTransaction()->getId()
                );
            }
        } catch (Throwable $e) {
            throw new AsyncPaymentFinalizeException(
                $transaction->getOrderTransaction()->getId(),
                $e->getMessage()
            );
        }
    }

    public function isGatewaySupported(GatewayEntity $gatewayEntity): bool
    {
        return $gatewayEntity->getType(true) === GatewayTypes::CARD
            && $gatewayEntity->getGatewayId() === GatewayIds::GENERAL_CREDIT_CARD;
    }

    public function gatewayGroupingSupported(): bool
    {
        return false;
    }

    public function isGatewayParamRequired(): bool
    {
        return false;
    }

    protected function getErrorUrl(string $orderId): string
    {
        return $this->router->generate(
            'frontend.checkout.finish.page',
            ['orderId' => $orderId, 'changedPayment' => false, 'paymentFailed' => true]
        );
    }
}
