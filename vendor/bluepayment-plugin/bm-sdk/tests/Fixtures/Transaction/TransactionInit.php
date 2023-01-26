<?php
declare(strict_types=1);

namespace Tests\Fixtures\Transaction;

abstract class TransactionInit
{
    public static function getTransactionInitContinue(): array
    {
        return [
            'gatewayUrl' => 'https://pay-accept.bm.pl',
            'transaction' => [
                'orderID' => '123',
                'amount' => '1.20',
                'description' => 'Transakcja 123-123',
                'gatewayID' => '0',
                'currency' => 'PLN',
                'customerEmail' => 'test@hostname.domain'
            ]
        ];
    }

    public static function getTransactionInit(): array
    {
        return [
            'gatewayUrl' => 'https://pay-accept.bm.pl',
            'transaction' => [
                'orderID' => '186-1590996507',
                'amount' => '22.94',
                'description' => 'Transakcja testowa',
                'gatewayID' => '1500',
                'currency' => 'PLN',
                'customerEmail' => 'test@test.test',
                'customerIP' => '127.0.0.1',
                'title' => 'Test',
            ]
        ];
    }

    public static function getTransactionInitContinueResponse(): string
    {
        return file_get_contents(__DIR__ . '/TransactionInitContinueResponse.xml');
    }

    public static function getTransactionInitResponse(): string
    {
        return file_get_contents(__DIR__ . '/TransactionInitResponse.xml');
    }
}
