<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Icons;

class DefaultPaymentIcon extends AbstractPaymentIcon
{
    // phpcs:disable
    protected string $blob = 'iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAMAAAC7IEhfAAAAkFBMVEX///8KlPqFhYU+Pj43NzclJSWbm5v+///8/P39/v57e3v3+PlWVlaPj49OTk5ra2vW1tbGxsbu7u7Ozs5jY2O2tranp6fn5+fd3d68vLyxsbEuLi5Qs/sfnvoUFBRGRkaWlpZYt/s+q/r///2fn5/y8vJSUlLg8v5rv/x0dHRgYGCPz/3V7f54xPyKy/p/x/yMS1VeAAABKElEQVQ4y+2S13qDMAyFFa/Iiw1mpEB2mq73f7saQpte0OYByvmw0MWPJHQMsGjRjNZy7aNEfMDJ8cgpA0D5G4ev6KMjBfiiQ1nEIUGUEqd05M671e68h7ylevhQq2S2q4TL6ml12QMJqEsF2pSHWlwrbUVYoiJ9baHyvTy49eBmD8Z2saCa16wXrG8qTk6ZaER74KUhdxA0S7O+CDTLWaJCYIIe6+A5hCbNSBD9AEseNOpwAg86D1JBk+I0ggU3t5/ZvOmPHdQ8VZ2lMc+binSlnyBXhnRRG7tWDbtYw4sv+w6HECITd5mJSFBmzDp2ZZWjmUl9E8C7M/K2QbgFDY5HkwUxT0DOmDTM7d9H60aPEGIBctbOcbvya8vT84e+LX94WRb9V30CaxUSWGe2QKAAAAAASUVORK5CYII=';

    // phpcs:enable

    protected string $extension = 'png';

    protected string $mime = 'image/png';

    public function getBlob(): string
    {
        return base64_decode($this->blob);
    }
}
