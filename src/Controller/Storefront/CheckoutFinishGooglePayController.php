<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Controller\Storefront;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\PaymentHandler\GooglePayPaymentHandler;
use Exception;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class CheckoutFinishGooglePayController extends StorefrontController
{
    private ClientFactory $clientFactory;

    public function __construct(
        ClientFactory $clientFactory
    ) {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @Route(
     *     "/blue-payment/checkout/google-pay",
     *     name="payment.blue-payment.checkout.google-pay",
     *     options={"seo"=false},
     *     methods={"POST"},
     *     defaults={"XmlHttpRequest"=true}
     * )
     */
    public function cartOrder(
        RequestDataBag $data,
        SalesChannelContext $context,
        Request $request
    ): JsonResponse {
        try {
            $client = $this->clientFactory->createFromPluginConfig($context->getSalesChannelId());
            $response = $client->getGooglePayMerchantInfo($this->getCurrentDomain($context));
        } catch (Exception $e) {
            return new JsonResponse(['error' => true]);
        }

        return new JsonResponse([
            'authJwt' => $response->getAuthJwt(),
            'merchantId' => $response->getMerchantId(),
            'merchantOrigin' => $response->getMerchantOrigin(),
            'merchantName' => $response->getMerchantName(),
            'gatewayMerchantId' => (string)$response->getAcceptorId(),
            'allowedAuthMethods' => GooglePayPaymentHandler::ALLOWED_AUTH_METHODS,
            'allowedCardNetworks' => GooglePayPaymentHandler::ALLOWED_CARD_NETWORKS,
        ]);
    }

    private function getCurrentDomain(SalesChannelContext $context): string
    {
        $domainId = $context->getDomainId();
        $domains = $context->getSalesChannel()->getDomains();

        /** @var SalesChannelDomainEntity $domain */
        $domain = $domains->first();
        if (null !== $domainId) {
            $domain = $domains->filter(fn(SalesChannelDomainEntity $domain) => $domainId === $domain->getId())->first();
        }

        $url = $this->removeProtocol($domain->getUrl());
        $url = explode('/', $url);

        return $url[0];
    }

    private function removeProtocol(string $url): string
    {
        $disallowed = ['http://', 'https://'];
        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;
    }
}
