<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api;

use BlueMedia\Hash\HashableInterface;
use BlueMedia\HttpClient\ValueObject\Response;
use BlueMedia\Itn\ValueObject\Itn;

interface ClientInterface
{
    /**
     * Perform standard transaction.
     */
    public function getTransactionRedirect(array $transactionData): Response;

    /**
     * Perform transaction in background.
     * Returns payway form or transaction data for user.
     */
    public function doTransactionBackground(array $transactionData): Response;

    /**
     * Initialize transaction.
     * Returns transaction continuation or transaction information.
     */
    public function doTransactionInit(array $transactionData): Response;

    /**
     * Process ITN requests.
     *
     * @param string $itn encoded with base64
     */
    public function doItnIn(string $itn): Response;

    /**
     * Returns response for ITN IN request.
     */
    public function doItnInResponse(Itn $itn, bool $transactionConfirmed = true): Response;

    /**
     * Returns payway list.
     */
    public function getPaywayList(): Response;

    /**
     * Returns payment regulations.
     */
    public function getRegulationList(): Response;

    /**
     * Checks id hash is valid.
     */
    public function checkHash(HashableInterface $data): bool;

    /**
     * Method allows to check if gateway returns with valid data.
     */
    public function doConfirmationCheck(array $data): bool;

    /**
     * Method allows to get Itn object from base64
     */
    public function getItnObject(string $itn): Itn;
}
