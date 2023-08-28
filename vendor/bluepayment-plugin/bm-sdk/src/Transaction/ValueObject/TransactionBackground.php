<?php
declare(strict_types=1);

namespace BlueMedia\Transaction\ValueObject;

use JMS\Serializer\Annotation\AccessorOrder;

/**
 * @AccessorOrder("custom",
 *     custom = {
 *      "receiverNRB",
 *      "receiverName",
 *      "receiverAddress",
 *      "orderID",
 *      "amount",
 *      "currency",
 *      "title",
 *      "remoteID",
 *      "bankHref",
 *     "returnURL",
 *      "platformName",
 *      "platformVersion",
 *      "platformPluginVersion",
 *      "hash"
 * })
 */
final class TransactionBackground extends Transaction
{
}
