<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Subscriber;

use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use BlueMedia\ShopwarePayment\Provider\TransactionDataProvider;
use Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\StateMachine\Aggregation\StateMachineTransition\StateMachineTransitionActions;
use Shopware\Core\System\StateMachine\Event\StateMachineStateChangeEvent;
use Shopware\Core\System\StateMachine\StateMachineRegistry;
use Shopware\Core\System\StateMachine\Transition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderTransactionStateChangeEventListener implements EventSubscriberInterface
{
    private const ORDER_PAID_EVENT = 'state_enter.order_transaction.state.paid';

    private StateMachineRegistry $stateMachineRegistry;

    private ConfigProvider $configProvider;

    private LoggerInterface $logger;

    private TransactionDataProvider $transactionDataProvider;

    public function __construct(
        StateMachineRegistry $stateMachineRegistry,
        ConfigProvider $configProvider,
        TransactionDataProvider $transactionDataProvider,
        LoggerInterface $blueMediaWebhookLogger
    ) {
        $this->stateMachineRegistry = $stateMachineRegistry;
        $this->configProvider = $configProvider;
        $this->logger = $blueMediaWebhookLogger;
        $this->transactionDataProvider = $transactionDataProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'state_machine.order_transaction.state_changed' => 'onOrderTransactionStateChange',
        ];
    }

    public function onOrderTransactionStateChange(StateMachineStateChangeEvent $event): void
    {
        if (
            $event->getStateEventName() !== self::ORDER_PAID_EVENT
            || !$this->configProvider->isOrderStatusProcessingEnabled($event->getSalesChannelId())
        ) {
            return;
        }

        $orderTransaction = $this->transactionDataProvider->getTransactionById(
            $event->getTransition()->getEntityId(),
            $event->getContext()
        );

        if ($orderTransaction === null) {
            return;
        }

        $this->processOrderProcessTransition($orderTransaction->getOrderId(), $event->getContext());
    }

    private function processOrderProcessTransition(string $orderId, Context $context): void
    {
        try {
            $this->stateMachineRegistry->transition(
                new Transition(
                    OrderDefinition::ENTITY_NAME,
                    $orderId,
                    StateMachineTransitionActions::ACTION_PROCESS,
                    'stateId'
                ),
                $context
            );
            $this->logger->info(
                sprintf(
                    'Order Paid by Blue Media (%s) set to (%s) based on payment status change',
                    $orderId,
                    StateMachineTransitionActions::ACTION_PROCESS
                )
            );
        } catch (Exception $exception) {
            $this->logger->error(sprintf(
                'ERROR on OrderTransactionStateChangeEventListener::onOrderTransactionStateChange: %s',
                $exception->getMessage(),
            ));
        }
    }
}
