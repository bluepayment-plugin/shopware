<?php
declare(strict_types=1);

namespace BlueMedia\Transaction\ValueObject;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\AccessorOrder;

/**
 * @AccessorOrder("custom",
 *     custom = {
 *      "orderID",
 *      "remoteID",
 *      "confirmation",
 *      "reason",
 *      "blikAMKey",
 *      "blikAMLabel",
 *      "paymentStatus",
 *      "hash"
 * })
 */
final class TransactionInit extends Transaction
{
    /**
     * @var string
     * @Type("string")
     */
    private $confirmation;

    /**
     * @var string
     * @Type("string")
     */
    private $reason;

    /**
     * @var string
     * @Type("string")
     */
    private $paymentStatus;

    /**
     * @return string
     */
    public function getConfirmation(): string
    {
        return $this->confirmation;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }
}
