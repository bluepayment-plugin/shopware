<?php
declare(strict_types=1);

namespace BlueMedia\Transaction\ValueObject;

use BlueMedia\Serializer\SerializableInterface;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\AccessorOrder;

/**
 * @AccessorOrder("custom",
 *     custom = {
 *      "status",
 *      "redirecturl",
 *      "orderID",
 *      "remoteID",
 *      "hash"
 * })
 */
final class TransactionContinue extends Transaction
{
    /**
     * @var string
     * @Type("string")
     */
    private $status;

    /**
     * @var string
     * @Type("string")
     */
    private $redirecturl;

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirecturl;
    }
}
