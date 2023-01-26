<?php
declare(strict_types=1);

namespace BlueMedia\Transaction\Parser;

use BlueMedia\Common\Enum\ClientEnum;
use BlueMedia\Common\Exception\HashException;
use BlueMedia\Common\Parser\ResponseParser;
use BlueMedia\Common\Util\XMLParser;
use BlueMedia\Hash\HashChecker;
use BlueMedia\HttpClient\ValueObject\Response;
use BlueMedia\Serializer\SerializableInterface;
use BlueMedia\Serializer\Serializer;
use BlueMedia\Transaction\ValueObject\TransactionBackground;
use BlueMedia\Transaction\ValueObject\TransactionContinue;
use BlueMedia\Transaction\ValueObject\TransactionInit;

final class TransactionResponseParser extends ResponseParser
{
    public function parse(bool $transactionInit = false): Response
    {
        $this->isErrorResponse();
        $paywayForm = $this->getPaywayFormResponse();

        if (!empty($paywayForm)) {
            return new Response(htmlspecialchars_decode($paywayForm['1']['0']));
        }

        if ($transactionInit === true) {
            return new Response($this->parseTransactionInitResponse());
        }

        return new Response($this->parseTransactionBackgroundResponse());
    }

    private function getPaywayFormResponse(): array
    {
        $matchesCount = preg_match_all(ClientEnum::PATTERN_PAYWAY, $this->response, $data);

        return $matchesCount === 0 ? [] : $data;
    }

    private function parseTransactionBackgroundResponse(): SerializableInterface
    {
        /** @var TransactionBackground $transaction */
        $transaction = (new Serializer())->deserializeXml($this->response, TransactionBackground::class);

        if (HashChecker::checkHash($transaction, $this->configuration) === false) {
            throw HashException::wrongHashError();
        }

        return $transaction;
    }

    private function parseTransactionInitResponse(): SerializableInterface
    {
        $xmlTransaction = XMLParser::parse($this->response);

        if (isset($xmlTransaction->redirecturl)) {
            /** @var TransactionContinue $transaction */
            $transaction = (new Serializer())->deserializeXml($this->response, TransactionContinue::class);
        } else {
            /** @var TransactionInit $transaction */
            $transaction = (new Serializer())->deserializeXml($this->response, TransactionInit::class);
        }

        if (HashChecker::checkHash($transaction, $this->configuration) === false) {
            throw HashException::wrongHashError();
        }

        return $transaction;
    }
}
