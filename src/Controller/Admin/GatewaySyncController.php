<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Controller\Admin;

use BlueMedia\ShopwarePayment\ScheduledTask\GatewaySynchronizationTask;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class GatewaySyncController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    /**
     * @Route(
     *     "/api/_action/blue-payment/sync-gateways",
     *     name="api.admin.blue-payment.sync-gateways",
     *     methods={"POST"}
     * )
     */
    public function syncGateways(): JsonResponse
    {
        $this->messageBus->dispatch(new GatewaySynchronizationTask());

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
