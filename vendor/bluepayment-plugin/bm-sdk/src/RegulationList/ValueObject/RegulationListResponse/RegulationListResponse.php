<?php
declare(strict_types=1);

namespace BlueMedia\RegulationList\ValueObject\RegulationListResponse;

use JMS\Serializer\Annotation\Type;
use BlueMedia\RegulationList\ValueObject\RegulationList;

final class RegulationListResponse extends RegulationList
{
    /**
     * @var Regulations
     * @Type("BlueMedia\RegulationList\ValueObject\RegulationListResponse\Regulations")
     */
    private $regulations;

    /**
     * @return Regulations
     */
    public function getRegulations(): Regulations
    {
        return $this->regulations;
    }
}
