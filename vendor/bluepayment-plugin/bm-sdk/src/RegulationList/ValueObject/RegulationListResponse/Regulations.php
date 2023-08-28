<?php
declare(strict_types=1);

namespace BlueMedia\RegulationList\ValueObject\RegulationListResponse;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlList;

final class Regulations
{
    /**
     * @var Regulation
     * @XmlList(inline = true, entry = "regulation")
     * @Type("array<BlueMedia\RegulationList\ValueObject\RegulationListResponse\Regulation>")
     */
    private $regulation;

    /**
     * @return Regulation
     */
    public function getRegulation(): array
    {
        return $this->regulation;
    }
}
