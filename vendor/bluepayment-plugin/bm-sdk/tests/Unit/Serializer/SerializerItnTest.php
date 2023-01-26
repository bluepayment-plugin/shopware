<?php
declare(strict_types=1);

namespace Tests\Unit\Serializer;

use BlueMedia\Itn\ValueObject\Itn;
use BlueMedia\Serializer\Serializer;
use Tests\Unit\BaseTestCase;

class SerializerItnTest extends BaseTestCase
{
    private static $serializer;

    public static function setUpBeforeClass(): void
    {
        self::$serializer = new Serializer();
    }

    /**
     * @dataProvider getItns
     */
    public function testSerializeItnDataReturnsItnDto(array $itnData)
    {
        /** @var Itn $itn */
        $itn = self::$serializer->fromArray($itnData, Itn::class);

        $this->assertSame($itnData['orderID'], $itn->getOrderID());
        $this->assertSame($itnData['remoteID'], $itn->getRemoteID());
        $this->assertSame($itnData['amount'], $itn->getAmount());
        $this->assertSame($itnData['currency'], $itn->getCurrency());
        $this->assertSame($itnData['paymentDate'], $itn->getPaymentDate());
        $this->assertSame($itnData['paymentStatus'], $itn->getPaymentStatus());
        $this->assertSame($itnData['paymentStatusDetails'] ?? null, $itn->getPaymentStatusDetails());
        if (isset($itnData['customerData'])) {
            $this->assertSame($itnData['customerData']['fName'], $itn->getCustomerData()->getFName());
            $this->assertSame($itnData['customerData']['lName'], $itn->getCustomerData()->getLName());
            $this->assertSame($itnData['customerData']['streetName'], $itn->getCustomerData()->getStreetName());
            $this->assertSame($itnData['customerData']['streetHouseNo'], $itn->getCustomerData()->getStreetHouseNo());
            $this->assertSame($itnData['customerData']['streetStaircaseNo'], $itn->getCustomerData()->getStreetStaircaseNo());
            $this->assertSame($itnData['customerData']['streetPremiseNo'], $itn->getCustomerData()->getStreetPremiseNo());
            $this->assertSame($itnData['customerData']['postalCode'], $itn->getCustomerData()->getPostalCode());
            $this->assertSame($itnData['customerData']['city'], $itn->getCustomerData()->getCity());
            $this->assertSame($itnData['customerData']['nrb'], $itn->getCustomerData()->getNrb());
            $this->assertSame($itnData['customerData']['senderData'], $itn->getCustomerData()->getSenderData());
        } else {
            $this->assertNull($itn->getCustomerData());
        }
        $this->assertSame($itnData['hash'], $itn->getHash());
    }

    public function getItns(): array
    {
        return [
            [
                [
                    'serviceID' => 'service_1',
                    'orderID' => 'order_1',
                    'remoteID' => 'remote_1',
                    'amount' => '123',
                    'currency' => 'PLN',
                    'gatewayID' => 1,
                    'paymentDate' => '09.09.2020',
                    'paymentStatus' => 'OK',
                    'paymentStatusDetails' => 'details_1',
                    'customerData' => [
                        'fName' => 'fname_1',
                        'lName' => 'lname_1',
                        'streetName' => 'street_name_1',
                        'streetHouseNo' => 'street_house_no_1',
                        'streetStaircaseNo' => 'street_staircase_no_1',
                        'streetPremiseNo' => 'street_premise_no_1',
                        'postalCode' => 'postal_code_1',
                        'city' => 'city_1',
                        'nrb' => 'nrb_1',
                        'senderData' => 'sender_data_1',
                    ],
                    'hash' => '123asd123asd',
                ],
                [
                    'serviceID' => 'service_2',
                    'orderID' => 'order_2',
                    'remoteID' => 'remote_2',
                    'amount' => '321',
                    'currency' => 'EUR',
                    'gatewayID' => 15,
                    'paymentDate' => '12.09.2020',
                    'paymentStatus' => 'START',
                    'paymentStatusDetails' => 'details_2',
                    'customerData' => [
                        'fName' => 'fname_2',
                        'lName' => 'lname_2',
                        'streetName' => 'street_name_2',
                        'streetHouseNo' => 'street_house_no_2',
                        'postalCode' => 'postal_code_2',
                        'city' => 'city_2',
                        'nrb' => 'nrb_2',
                    ],
                    'hash' => '56465as4d65465',
                ],
                [
                    'serviceID' => 'service_3',
                    'orderID' => 'order_3',
                    'remoteID' => 'remote_3',
                    'amount' => '13',
                    'currency' => 'USD',
                    'gatewayID' => 12,
                    'paymentDate' => '09.09.2022',
                    'paymentStatus' => 'PENDING',
                    'hash' => '123asd234565sddsgh',
                ],
            ],
        ];
    }
}
