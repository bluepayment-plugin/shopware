<?php
declare(strict_types=1);

namespace BlueMedia\Itn\ValueObject\ItnResponse;

use BlueMedia\Serializer\SerializableInterface;
use BlueMedia\Serializer\Serializer;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\Type;

/**
 * @XmlRoot("confirmationList")
 *
 * @AccessorOrder("custom",
 *     custom = {
 *      "serviceID",
 *      "transactionsConfirmations",
 *      "hash"
 * })
 */
class ItnResponse implements SerializableInterface
{
    /**
     * @var string
     * @Type("string")
     */
    private $serviceID;

    /**
     * @var TransactionsConfirmations
     * @Type("BlueMedia\Itn\ValueObject\ItnResponse\TransactionsConfirmations")
     */
    private $transactionsConfirmations;

    /**
     * @var string
     * @Type("string")
     */
    private $hash;

    /**
     * @return string
     */
    public function getServiceID(): string
    {
        return $this->serviceID;
    }

    /**
     * @return TransactionsConfirmations
     */
    public function getTransactionsConfirmations(): TransactionsConfirmations
    {
        return $this->transactionsConfirmations;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    public function toXml(): string
    {
        return (new Serializer())->toXml($this);
    }
}
