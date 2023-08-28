<?php
declare(strict_types=1);

namespace BlueMedia\Transaction\ValueObject;

use BlueMedia\Common\ValueObject\AbstractValueObject;
use BlueMedia\Hash\HashableInterface;
use BlueMedia\Serializer\SerializableInterface;
use DateTime;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\Type;

/**
 * @AccessorOrder("custom",
 *     custom = {
 *      "serviceID",
 *      "orderID",
 *      "amount",
 *      "description",
 *      "gatewayID",
 *      "currency",
 *      "customerEmail",
 *      "customerNRB",
 *      "taxCountry",
 *      "customerIP",
 *      "title",
 *      "receiverName",
 *      "validityTime",
 *      "linkValidityTime",
 *      "authorizationCode",
 *      "screenType",
 *      "blikUIDKey",
 *      "blikUIDLabel",
 *      "blikAMKey",
 *      "returnURL",
 *      "paymentToken",
 *      "defaultRegulationAcceptanceState",
 *      "defaultRegulationAcceptanceID",
 *      "defaultRegulationAcceptanceTime",
 *
 *      "receiverNRB",
 *      "receiverAddress",
 *      "remoteID",
 *      "bankHref",
 *
 *      "platformName",
 *      "platformVersion",
 *      "platformPluginVersion",
 *
 *      "hash"
 * })
 */
class Transaction extends AbstractValueObject implements SerializableInterface, HashableInterface
{
    /**
     * Transaction service id.
     *
     * @var string
     * @Type("string")
     */
    protected $serviceID;

    /**
     * Transaction order id.
     *
     * @var string
     * @Type("string")
     */
    protected $orderID;

    /**
     * Transaction amount.
     *
     * @var string
     * @Type("string")
     */
    protected $amount;

    /**
     * Transaction description.
     *
     * @var string
     * @Type("string")
     */
    protected $description;

    /**
     * Transaction gateway id.
     *
     * @var int
     * @Type("int")
     */
    protected $gatewayID;

    /**
     * Transaction currency.
     *
     * @var string
     * @Type("string")
     */
    protected $currency;

    /**
     * Transaction customer e-mail address.
     *
     * @var string
     * @Type("string")
     */
    protected $customerEmail;

    /**
     * Transaction customer bank account number.
     *
     * @var string
     * @Type("string")
     */
    protected $customerNRB;

    /**
     * Transaction tax country.
     *
     * @var string
     * @Type("string")
     */
    protected $taxCountry;

    /**
     * Customer IP address.
     *
     * @var string
     * @Type("string")
     */
    protected $customerIP;

    /**
     * Transaction title.
     *
     * @var string
     * @Type("string")
     */
    protected $title;

    /**
     * Transaction receiver name.
     *
     * @var string
     * @Type("string")
     */
    protected $receiverName;

    /**
     * Transaction validity time.
     *
     * @var DateTime
     * @Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected $validityTime;

    /**
     * Transaction link validity time.
     *
     * @var DateTime
     * @Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected $linkValidityTime;

    /**
     * Transaction authorization code.
     *
     * @var string
     * @Type("string")
     */
    protected $authorizationCode;

    /**
     * Screen tpe for payment authorization (only for card payment).
     *
     * @var string
     * @Type("string")
     */
    protected $screenType;

    /**
     * BLIK Alias UID key.
     *
     * @var string
     * @Type("string")
     */
    protected $blikUIDKey;

    /**
     * BLIK Alias UID label.
     *
     * @var string
     * @Type("string")
     */
    protected $blikUIDLabel;

    /**
     * BLIK banks mobile application key.
     *
     * @var string
     * @Type("string")
     */
    protected $blikAMKey;

    /**
     * return address.
     *
     * @var string
     * @Type("string")
     */
    protected $returnURL;

    /**
     * Token Visa or Google Pay.
     *
     * @var string
     * @Type("string")
     */
    protected $paymentToken;

    /**
     * @var string
     * @Type("string")
     */
    protected $defaultRegulationAcceptanceState;

    /**
     * @var string
     * @Type("string")
     */
    protected $defaultRegulationAcceptanceID;

    /**
     * @var DateTime
     * @Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected $defaultRegulationAcceptanceTime;

    /**
     * Receiver bank account number.
     *
     * @var string
     * @Type("string")
     */
    protected $receiverNRB;

    /**
     * Receiver address.
     *
     * @var string
     * @Type("string")
     */
    protected $receiverAddress;

    /**
     * Remote order id.
     *
     * @var string
     * @Type("string")
     */
    protected $remoteID;

    /**
     * Banks system URL.
     *
     * @var string
     * @Type("string")
     */
    protected $bankHref;

    /**
     * @var string
     * @Type("string")
     */
    protected $platformName;

    /**
     * @var string
     * @Type("string")
     */
    protected $platformVersion;

    /**
     * @var string
     * @Type("string")
     */
    protected $platformPluginVersion;

    /**
     * Transaction hash.
     *
     * @var string
     * @Type("string")
     */
    protected $hash;

    public function isHashPresent(): bool
    {
        return $this->hash !== null;
    }

    /**
     * @param string $serviceID
     * @return Transaction
     */
    public function setServiceId(string $serviceID): Transaction
    {
        $this->serviceID = $serviceID;
        return $this;
    }

    /**
     * @param string $hash
     * @return Transaction
     */
    public function setHash(string $hash): Transaction
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceID(): string
    {
        return $this->serviceID;
    }

    /**
     * @return string
     */
    public function getOrderID(): string
    {
        return $this->orderID;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getGatewayID(): int
    {
        return $this->gatewayID;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function getCustomerNRB(): string
    {
        return $this->customerNRB;
    }

    /**
     * @return string
     */
    public function getTaxCountry(): string
    {
        return $this->taxCountry;
    }

    /**
     * @return string
     */
    public function getCustomerIP(): string
    {
        return $this->customerIP;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    /**
     * @return DateTime
     */
    public function getValidityTime(): DateTime
    {
        return $this->validityTime;
    }

    /**
     * @return DateTime
     */
    public function getLinkValidityTime(): DateTime
    {
        return $this->linkValidityTime;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode(): string
    {
        return $this->authorizationCode;
    }

    /**
     * @return string
     */
    public function getScreenType(): string
    {
        return $this->screenType;
    }

    /**
     * @return string
     */
    public function getBlikUIDKey(): string
    {
        return $this->blikUIDKey;
    }

    /**
     * @return string
     */
    public function getBlikUIDLabel(): string
    {
        return $this->blikUIDLabel;
    }

    /**
     * @return string
     */
    public function getBlikAMKey(): string
    {
        return $this->blikAMKey;
    }

    /**
     * @return string
     */
    public function getReturnURL(): string
    {
        return $this->returnURL;
    }

    /**
     * @return string
     */
    public function getPaymentToken(): string
    {
        return $this->paymentToken;
    }

    /**
     * @return string
     */
    public function getDefaultRegulationAcceptanceState(): string
    {
        return $this->defaultRegulationAcceptanceState;
    }

    /**
     * @return string
     */
    public function getDefaultRegulationAcceptanceID(): string
    {
        return $this->defaultRegulationAcceptanceID;
    }

    /**
     * @return DateTime
     */
    public function getDefaultRegulationAcceptanceTime(): DateTime
    {
        return $this->defaultRegulationAcceptanceTime;
    }

    /**
     * @return string
     */
    public function getReceiverNRB(): string
    {
        return $this->receiverNRB;
    }

    /**
     * @return string
     */
    public function getReceiverAddress(): string
    {
        return $this->receiverAddress;
    }

    /**
     * @return string
     */
    public function getRemoteID(): string
    {
        return $this->remoteID;
    }

    /**
     * @return string
     */
    public function getBankHref(): string
    {
        return $this->bankHref;
    }

    /**
     * @return string
     */
    public function getPlatformName(): string
    {
        return trim($this->platformName);
    }

    /**
     * @return string
     */
    public function getPlatformVersion(): string
    {
        return trim($this->platformVersion);
    }

    /**
     * @return string
     */
    public function getPlatformPluginVersion(): string
    {
        return trim($this->platformPluginVersion);
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return trim($this->hash);
    }
}
