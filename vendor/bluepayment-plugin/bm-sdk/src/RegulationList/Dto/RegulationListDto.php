<?php
declare(strict_types=1);

namespace BlueMedia\RegulationList\Dto;

use JMS\Serializer\Annotation\Type;
use BlueMedia\Common\Dto\AbstractDto;
use BlueMedia\Serializer\SerializableInterface;
use BlueMedia\RegulationList\ValueObject\RegulationList;

final class RegulationListDto extends AbstractDto
{
    /**
     * @var RegulationList
     * @Type("BlueMedia\RegulationList\ValueObject\RegulationList")
     */
    private $regulationList;

    /**
     * @return RegulationList
     */
    public function getRegulationList(): RegulationList
    {
        return $this->regulationList;
    }

    public function getRequestData(): SerializableInterface
    {
        return $this->getRegulationList();
    }
}
