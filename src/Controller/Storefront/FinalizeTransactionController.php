<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Controller\Storefront;

use BlueMedia\ShopwarePayment\Exception\InvalidOrderException;
use BlueMedia\ShopwarePayment\Processor\FinalizeTransactionProcessor;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class FinalizeTransactionController extends StorefrontController
{
    private FinalizeTransactionProcessor $processor;

    public function __construct(
        FinalizeTransactionProcessor $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * @Route(
     *     "/blue-payment/finalize-transaction",
     *     name="blue-payment.finalize-transaction",
     *     methods={"GET"}
     * )
     * @throws InvalidOrderException
     */
    public function finalizeTransaction(Request $request, SalesChannelContext $context): Response
    {
        $parameters = $this->processor->process($request, $context);

        return $this->redirectToRoute('frontend.checkout.finish.page', $parameters);
    }
}
