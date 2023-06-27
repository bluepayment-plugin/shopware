<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Icons;

class ApplePayPaymentIcon extends AbstractPaymentIcon
{
    // phpcs:disable
    /**
     * Contains only encoded value without prefix "data:image/png;base64,"
     */
    protected string $blob = "iVBORw0KGgoAAAANSUhEUgAAAHsAAAB7CAMAAABjGQ9NAAAAbFBMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD///+fn58gICCAgIDf399AQEDv7+8QEBC/v79gYGCQkJBwcHBQUFCPj4+vr6+goKDPz88wMDCwsLB/f38fHx++D7MbAAAADnRSTlMAgO8gv2BwMJ+Pz5Cg3yViiwUAAAGeSURBVHja7djJbtswFIXhxBmaNM25nCfJQ9r3f8eCpKFoW0m2gOJ8KwFa/AQvF5QeiIiIiIiIiIiIiDb29HzAMj9f1qYPblTLJLyva79pWUzhx6r2IctyYd2u4yTL/Xlkm222/63tjdutnZCk+tKNO92vbaGtVApX5j5tH8WONp57W9elFCDeoe0dEJIDoG1vVxeUtqrob9keMFGtPc3fOgBIXlyfQIHbth0x+S3f7QFJNC6laDjJPXqB2rZtMLHfbQuoCO3b2rxv7yyCv1lbWjsopUqAlqvadcgiGWnjeWdMYmt3zor4nAana/sIJ+IQ5Wbz7uc8GGPGWrEa4eJSbfsAb6E3P+dhFp/mXSUM/rrnUnBSSJu3DSZp3u5DlmNrR7gBcfO217M9n7cNdLQ5tLaEAC2bt+UcgBAAHKW354sKqrcNUG7QFmuiyNnE9qyOMlFm9KJUnfoXYPe6OyQ42adtEqB2agNhlJ3a8ex5T2V7p/aO3/6vq9rPThbLeFr5r0dntUzBx9ofTb8OWObz9YGIiIiIiIiIiIj+S38BHMOZzpoUWDgAAAAASUVORK5CYII=";

    // phpcs:enable

    protected string $extension = 'png';

    protected string $mime = 'image/png';

    public function getBlob(): string
    {
        return base64_decode($this->blob);
    }
}
