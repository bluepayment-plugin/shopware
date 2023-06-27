<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Transformer;

use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;

class TransactionTransformer
{
    public function transform(SyncPaymentTransactionStruct $transaction, array $params = []): array
    {
        $transaction = [
            'transaction' => [
                'orderID' => $transaction->getOrder()->getOrderNumber(),
                'amount' => number_format($transaction->getOrder()->getAmountTotal(), 2, '.', ''),
                'currency' => $transaction->getOrder()->getCurrency()->getIsoCode(),
                'customerEmail' => $transaction->getOrder()->getOrderCustomer()->getEmail(),
                'customerIP' => $transaction->getOrder()->getOrderCustomer()->getRemoteAddress(),
            ],
        ];

        $transaction['transaction'] = array_merge($transaction['transaction'], $params);

        return $transaction;
    }

    public function transformBackground(SyncPaymentTransactionStruct $transaction, array $params = []): array
    {
        $transformed = $this->transform($transaction, $params);

        $transformed['transaction']['description'] = sprintf(
            '- %s',
            $transaction->getOrder()->getOrderNumber() ?? ''
        );

        return $transformed;
    }
}
