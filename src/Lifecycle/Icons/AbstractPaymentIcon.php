<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Icons;

abstract class AbstractPaymentIcon
{
    public const BLUE_MEDIA_PAYMENTS_ICONS_FOLDER = 'Blue Media Payments - Icons';

    protected string $blob = '';

    protected string $extension = '';

    protected string $mime = '';

    public function getBlob(): string
    {
        return $this->blob;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getMime(): string
    {
        return $this->mime;
    }
}
