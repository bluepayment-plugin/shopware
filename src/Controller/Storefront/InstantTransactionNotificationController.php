<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Controller\Storefront;

use BlueMedia\ShopwarePayment\Processor\PaymentStatusProcessor;
use Exception;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class InstantTransactionNotificationController extends StorefrontController
{
    private PaymentStatusProcessor $paymentStatusProcessor;

    public function __construct(
        PaymentStatusProcessor $paymentStatusProcessor
    ) {
        $this->paymentStatusProcessor = $paymentStatusProcessor;
    }

    /**
     * @Route(
     *     "/blue-payment/status",defaults={"auth_required"=false, "csrf_protected"=false},
     *     name="blue-payment.status", methods={"POST"}
     * )
     *
     * @param Request $request
     * @param SalesChannelContext $salesChannelContext
     *
     * @return Response
     */
    public function status(Request $request, SalesChannelContext $salesChannelContext): Response
    {
        try {
            $response = $this->paymentStatusProcessor->process($request, $salesChannelContext);
        } catch (Exception $exception) {
            $this->paymentStatusProcessor->logException($exception);
            return new Response(null, Response::HTTP_NO_CONTENT);
        }
        return new Response($response);
    }
}
