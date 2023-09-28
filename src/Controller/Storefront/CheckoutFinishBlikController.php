<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Controller\Storefront;

use BlueMedia\ShopwarePayment\Exception\BlikCodeNotProvidedException;
use BlueMedia\ShopwarePayment\Exception\InvalidBlikCodeException;
use BlueMedia\ShopwarePayment\Provider\TransactionDataProvider;
use BlueMedia\ShopwarePayment\Validator\BlikCodeValidator;
use Shopware\Core\Checkout\Cart\Error\Error;
use Shopware\Core\Checkout\Cart\Exception\InvalidCartException;
use Shopware\Core\Checkout\Cart\Exception\OrderPaymentMethodNotChangeable;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStates;
use Shopware\Core\Checkout\Order\Exception\EmptyCartException;
use Shopware\Core\Checkout\Order\OrderException;
use Shopware\Core\Checkout\Order\SalesChannel\AbstractOrderRoute;
use Shopware\Core\Checkout\Order\SalesChannel\AbstractSetPaymentOrderRoute;
use Shopware\Core\Checkout\Order\SalesChannel\OrderService;
use Shopware\Core\Checkout\Payment\Exception\InvalidOrderException;
use Shopware\Core\Checkout\Payment\Exception\PaymentProcessException;
use Shopware\Core\Checkout\Payment\Exception\UnknownPaymentMethodException;
use Shopware\Core\Checkout\Payment\PaymentService;
use Shopware\Core\Checkout\Payment\SalesChannel\AbstractHandlePaymentMethodRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\AffiliateTracking\AffiliateTrackingListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @RouteScope(scopes={"store-api"})
 */
class CheckoutFinishBlikController extends StorefrontController
{
    private OrderService $orderService;

    private CartService $cartService;

    private PaymentService $paymentService;

    private TransactionDataProvider $transactionDataProvider;

    private AbstractHandlePaymentMethodRoute $handlePaymentMethodRoute;

    private AbstractOrderRoute $orderRoute;

    private AbstractSetPaymentOrderRoute $setPaymentOrderRoute;

    private BlikCodeValidator $blikCodeValidator;

    private TranslatorInterface $translator;

    public function __construct(
        OrderService $orderService,
        CartService $cartService,
        PaymentService $paymentService,
        TransactionDataProvider $transactionDataProvider,
        AbstractHandlePaymentMethodRoute $handlePaymentMethodRoute,
        AbstractOrderRoute $orderRoute,
        AbstractSetPaymentOrderRoute $setPaymentOrderRoute,
        BlikCodeValidator $blikCodeValidator,
        TranslatorInterface $translator
    ) {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
        $this->paymentService = $paymentService;
        $this->transactionDataProvider = $transactionDataProvider;
        $this->handlePaymentMethodRoute = $handlePaymentMethodRoute;
        $this->orderRoute = $orderRoute;
        $this->setPaymentOrderRoute = $setPaymentOrderRoute;
        $this->blikCodeValidator = $blikCodeValidator;
        $this->translator = $translator;
    }

    /**
     * @Route(
     *     "/store-api/blue-payment/blik/init",
     *      name="store-api.blue-payment.blik.init",
     *      options={"seo"="false"},
     *      methods={"POST"},
     *      defaults={"XmlHttpRequest"=true}
     * )
     */
    public function blikOrder(
        RequestDataBag $data,
        SalesChannelContext $context,
        Request $request
    ): JsonResponse {
        if (!$context->getCustomer()) {
            return $this->jsonResponse(
                false,
                [
                    'redirectUrl' => $this->generateUrl('frontend.checkout.register.page'),
                ]
            );
        }

        $errorResponse = $this->validateBlikCode($data->getDigits('blikCode'));

        if ($errorResponse instanceof JsonResponse) {
            return $errorResponse;
        }

        try {
            $this->addAffiliateTracking($data, $request->getSession());

            $orderId = $this->orderService->createOrder($data, $context);
        } catch (InvalidCartException | Error | EmptyCartException $error) {
            $this->addCartErrors(
                $this->cartService->getCart($context->getToken(), $context)
            );

            return $this->jsonResponse(
                false,
                [
                    'redirectUrl' => $this->generateUrl('frontend.checkout.confirm.page'),
                ]
            );
        }

        try {
            $finishUrl = $this->generateUrl('frontend.checkout.finish.page', ['orderId' => $orderId]);
            $errorUrl = $this->generateUrl('frontend.account.edit-order.page', ['orderId' => $orderId]);

            $this->paymentService->handlePaymentByOrder(
                $orderId,
                $data,
                $context,
                $finishUrl,
                $errorUrl
            );

            return $this->jsonResponse(
                true,
                [
                    'finishUrl' => $finishUrl,
                    'orderId' => $orderId,
                ]
            );
        } catch (PaymentProcessException | InvalidOrderException | UnknownPaymentMethodException $e) {
            $finishUrl = $this->generateUrl(
                'frontend.checkout.finish.page',
                ['orderId' => $orderId, 'changedPayment' => false, 'paymentFailed' => true]
            );

            return $this->jsonResponse(
                false,
                [
                    'finishUrl' => $finishUrl,
                    'orderId' => $orderId,
                ]
            );
        }
    }

    /**
     * @Route(
     *      "/store-api/blue-payment/blik/check",
     *       name="store-api.blue-payment.blik.check",
     *       options={"seo"="false"},
     *       methods={"POST"},
     *       defaults={"XmlHttpRequest"=true}
     *  )
     */
    public function checkBlikStatus(
        RequestDataBag $data,
        SalesChannelContext $salesChannelContext
    ): JsonResponse {
        $orderId = $data->get('orderId');
        $orderTransaction = $this->transactionDataProvider->getTransactionByOrderId(
            $orderId,
            $salesChannelContext->getContext()
        );

        if (null === $orderTransaction || null === $orderTransaction->getStateMachineState()) {
            return $this->jsonResponse(
                false,
                [],
                $this->translator->trans('checkout.blue-media-blik.error.generalError'),
            );
        }

        $changePaymentMethodUrl = $this->generateUrl('frontend.account.edit-order.page', ['orderId' => $orderId]);
        $stateName = $orderTransaction->getStateMachineState()->getTechnicalName();

        switch ($stateName) {
            case OrderTransactionStates::STATE_PAID:
                return $this->jsonResponse(
                    true,
                    [
                        'waiting' => false,
                    ]
                );
            case OrderTransactionStates::STATE_OPEN:
                return $this->jsonResponse(
                    true,
                    [
                        'waiting' => true,
                        'changePaymentUrl' => $changePaymentMethodUrl,
                    ]
                );
            default:
                return $this->jsonResponse(
                    false,
                    [
                        'waiting' => false,
                        'changePaymentUrl' => $changePaymentMethodUrl,
                    ]
                );
        }
    }

    /**
     * @Route(
     *      "/store-api/blue-payment/blik/retry",
     *       name="store-api.blue-payment.blik.retry",
     *       options={"seo"="false"},
     *       methods={"POST"},
     *       defaults={"XmlHttpRequest"=true}
     *  )
     */
    public function blikRetryTransaction(
        RequestDataBag $data,
        SalesChannelContext $context,
        Request $request
    ): JsonResponse {
        $orderId = $data->getAlnum('orderId');
        $finishUrl = $this->generateUrl('frontend.checkout.finish.page', ['orderId' => $orderId]);
        $errorUrl = $this->generateUrl('frontend.account.edit-order.page', [
            'orderId' => $orderId,
            'paymentFailed' => true,
        ]);

        $errorResponse = $this->validateBlikCode($data->getDigits('blikCode'));

        if ($errorResponse instanceof JsonResponse) {
            return $errorResponse;
        }

        $request->request->set('finishUrl', $finishUrl);
        $request->request->set('errorUrl', $errorUrl);
        $order = $this->orderRoute->load($request, $context, new Criteria([$orderId]))->getOrders()->first();

        if (!$this->orderService->isPaymentChangeableByTransactionState($order)) {
            if (Feature::isActive('v6.5.0.0')) {
                throw OrderException::paymentMethodNotChangeable();
            }

            throw new OrderPaymentMethodNotChangeable();
        }
        $this->setPaymentOrderRoute->setPayment($request, $context);

        try {
            $this->handlePaymentMethodRoute->load($request, $context);
        } catch (PaymentProcessException | InvalidOrderException | UnknownPaymentMethodException $e) {
            return $this->jsonResponse(
                false,
                [
                    'errorUrl' => $errorUrl,
                ]
            );
        }

        return $this->jsonResponse(true, ['orderId' => $orderId, 'finishUrl' => $finishUrl]);
    }

    private function jsonResponse(
        bool $isSuccess,
        array $params = [],
        ?string $message = null,
        ?string $exceptionMessage = null
    ): JsonResponse {
        $responseData = [
            'isSuccess' => $isSuccess,
            'message' => $message,
            'exceptionMessage' => $exceptionMessage,
        ];

        $responseData = array_merge($responseData, $params);

        return new JsonResponse(
            $responseData,
            $isSuccess ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    private function addAffiliateTracking(RequestDataBag $dataBag, SessionInterface $session): void
    {
        $affiliateCode = $session->get(AffiliateTrackingListener::AFFILIATE_CODE_KEY);
        $campaignCode = $session->get(AffiliateTrackingListener::CAMPAIGN_CODE_KEY);
        if ($affiliateCode) {
            $dataBag->set(AffiliateTrackingListener::AFFILIATE_CODE_KEY, $affiliateCode);
        }

        if ($campaignCode) {
            $dataBag->set(AffiliateTrackingListener::CAMPAIGN_CODE_KEY, $campaignCode);
        }
    }

    private function validateBlikCode(string $blikCode): ?JsonResponse
    {
        try {
            $this->blikCodeValidator->validate($blikCode);

            return null;
        } catch (BlikCodeNotProvidedException $e) {
            return $this->jsonResponse(
                false,
                [],
                $this->translator->trans('checkout.blue-media-blik.error.blikCodeNotProvided'),
                $e->getMessage()
            );
        } catch (InvalidBlikCodeException $e) {
            return $this->jsonResponse(
                false,
                [],
                $this->translator->trans('checkout.blue-media-blik.error.invalidBlikCode'),
                $e->getMessage()
            );
        }
    }
}
