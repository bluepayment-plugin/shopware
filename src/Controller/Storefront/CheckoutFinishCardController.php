<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Controller\Storefront;

use BlueMedia\ShopwarePayment\PaymentHandler\CardPaymentHandler;
use BlueMedia\ShopwarePayment\Struct\CartContinueUrlStruct;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\CheckoutController;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class CheckoutFinishCardController extends StorefrontController
{
    private CheckoutController $checkoutController;

    private Router $router;

    public function __construct(
        CheckoutController $checkoutController,
        Router $router
    ) {
        $this->checkoutController = $checkoutController;
        $this->router = $router;
    }

    /**
     * @Route(
     *     "/blue-payment/checkout/card",
     *     name="blue-payment.checkout.card.order",
     *     options={"seo"="false"},
     *     methods={"POST"},
     *     defaults={"XmlHttpRequest"=true}
     * )
     */
    public function cartOrder(
        RequestDataBag $data,
        SalesChannelContext $context,
        Request $request
    ): JsonResponse {
        $response =  $this->checkoutController->order($data, $context, $request);

        return new JsonResponse([
            'transactionContinueRedirect' => $this->getContinueRedirect($context),
            'checkoutErrorUrl' => $this->getCheckoutErrorUrl($context),
            'finalRedirect' => $response->isRedirect() ? $response->getTargetUrl() : $this->getCheckoutConfirm(),
        ]);
    }

    private function getContinueRedirect(SalesChannelContext $context): ?string
    {
        $extension = $this->getCartContinueUrlStruct($context);
        if (null === $extension) {
            return null;
        }

        return $extension->getTransactionContinueRedirect();
    }

    private function getCheckoutErrorUrl(SalesChannelContext $context): ?string
    {
        $extension = $this->getCartContinueUrlStruct($context);
        if (null === $extension) {
            return null;
        }

        return $extension->getCheckoutErrorUrl();
    }

    private function getCartContinueUrlStruct(SalesChannelContext $context): ?CartContinueUrlStruct
    {
        $extension = $context->getExtension(CardPaymentHandler::CARD_PAYMENT_CONTINUE_URL);

        return ($extension instanceof CartContinueUrlStruct) ? $extension : null;
    }

    private function getCheckoutConfirm(): string
    {
        return $this->router->generate('frontend.checkout.confirm.page');
    }
}
