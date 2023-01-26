<?php
declare(strict_types=1);

namespace BlueMedia\PaywayList\ValueObject\PaywayListResponse;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlList;
use BlueMedia\PaywayList\ValueObject\PaywayList;

final class PaywayListResponse extends PaywayList
{
    /**
     * @XmlList(inline = true, entry = "gateway")
     * @Type("array<BlueMedia\PaywayList\ValueObject\PaywayListResponse\Gateway>")
     */
    private $gateways;

    /**
     * @return array
     */
    public function getGateways(): array
    {
        return $this->gateways;
    }
}
