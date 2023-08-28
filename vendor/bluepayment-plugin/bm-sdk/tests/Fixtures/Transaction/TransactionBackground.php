<?php
declare(strict_types=1);

namespace Tests\Fixtures\Transaction;

abstract class TransactionBackground
{
    public static function getTransactionBackground(): array
    {
        return [
            'gatewayUrl' => 'https://pay-accept.bm.pl',
            'transaction' => [
                'orderID' => '12345',
                'amount' => '5.12',
                'description' => 'Test transaction 12345',
                'gatewayID' => '21',
                'currency' => 'PLN',
                'customerEmail' => 'test@test.test',
                'customerIP' => '127.0.0.1',
                'title' => 'Test',
                'validityTime' => date('Y-m-d H:i:s', strtotime('now +5 hour')),
                'linkValidityTime' => date('Y-m-d H:i:s', strtotime('now +5 hour'))
            ]
        ];
    }

    public static function getTransactionBackgroundResponse(): string
    {
        return file_get_contents(__DIR__ . '/TransactionBackgroundResponse.xml');
    }

    public static function getTransactionBackgroundResponseData(): array
    {
        return json_decode(json_encode(simplexml_load_string(self::getTransactionBackgroundResponse())), true);
    }

    public static function getPaywayFormResponse(): string
    {
        return file_get_contents(__DIR__ . '/PaywayFormResponse.txt');
    }
}
