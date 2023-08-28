<?php
declare(strict_types=1);

namespace BlueMedia\Transaction\Dto;

use BlueMedia\Transaction\ValueObject\Transaction;

interface TransactionDtoInterface
{
    /**
     * @return Transaction
     */
    public function getTransaction(): Transaction;

    /**
     * @return string
     */
    public function getHtmlFormLanguage(): string;

    /**
     * @param string $htmlFormLanguage
     * @return TransactionDto
     */
    public function setHtmlFormLanguage(string $htmlFormLanguage): TransactionDto;
}
