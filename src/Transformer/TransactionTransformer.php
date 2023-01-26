<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Transformer;

use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;

class TransactionTransformer
{
    public function transform(SyncPaymentTransactionStruct $transaction): array
    {
        return [
            'transaction' => [
                'orderID' => $transaction->getOrder()->getOrderNumber(),
                'amount' => number_format($transaction->getOrder()->getAmountTotal(), 2, '.', ''),
                'currency' => $transaction->getOrder()->getCurrency()->getIsoCode(),
                'customerEmail' => $transaction->getOrder()->getOrderCustomer()->getEmail(),
            ],
        ];
    }
}
