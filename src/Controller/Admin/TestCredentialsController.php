<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Controller\Admin;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @RouteScope(scopes={"api"})
 */
class TestCredentialsController extends AbstractController
{
    private ClientFactory $clientFactory;

    public function __construct(
        ClientFactory $clientFactory
    ) {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @Route(
     *     "/api/_action/blue-payment/test-credentials",
     *     name="api.admin.blue-payment.test-credentials",
     *     methods={"POST"}
     * )
     */
    public function testCredentials(RequestDataBag $dataBag): JsonResponse
    {
        $gatewayUrl = $dataBag->get('testMode')
            ? (string)$dataBag->get('testGatewayUrl')
            : (string)$dataBag->get('gatewayUrl');

        $client = $this->clientFactory->create(
            (string)$dataBag->get('serviceId'),
            (string)$dataBag->get('sharedKey'),
            $gatewayUrl,
            (string)$dataBag->get('hashAlgo')
        );

        $success = false;
        try {
            $client->getPaywayList();
            $success = true;
        } catch (Throwable $exception) {
            //catch all exceptions to return $success as false
        }

        return new JsonResponse(
            [
                'success' => $success,
            ]
        );
    }
}
