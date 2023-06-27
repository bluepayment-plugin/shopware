<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Icons;

class CardPaymentIcon extends AbstractPaymentIcon
{
    // phpcs:disable
    /**
     * Contains only encoded value without prefix "data:image/png;base64,"
     */
    protected string $blob = "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";

    // phpcs:enable

    protected string $extension = 'png';

    protected string $mime = 'image/png';

    public function getBlob(): string
    {
        return base64_decode($this->blob);
    }
}
