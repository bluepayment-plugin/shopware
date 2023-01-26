<?php
declare(strict_types=1);

namespace BlueMedia\Common\Dto;

use BlueMedia\HttpClient\ValueObject\Request;
use BlueMedia\Serializer\SerializableInterface;
use JMS\Serializer\Annotation\Type;

abstract class AbstractDto
{
    /**
     * @var string
     * @Type("string");
     */
    protected $gatewayUrl;

    /**
     * @var Request
     * @Type("BlueMedia\HttpClient\ValueObject\Request");
     */
    protected $request;

    /**
     * @return string
     */
    public function getGatewayUrl(): string
    {
        return $this->gatewayUrl;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    abstract public function getRequestData(): SerializableInterface;
}
