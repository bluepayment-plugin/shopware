<?php
declare(strict_types=1);

namespace BlueMedia\Transaction;

use BlueMedia\Common\Dto\AbstractDto;
use BlueMedia\Common\Enum\ClientEnum;
use BlueMedia\Common\Util\Translations;

final class View
{
    public static function createRedirectHtml(AbstractDto $transactionDto): string
    {
        $translation = (new Translations())->getTranslation(
            $transactionDto->getHtmlFormLanguage()
        );

        $result = '<p>'.$translation[Translations::REDIRECT].'</p>'.PHP_EOL;
        $result .= sprintf(
            '<form action="%s" method="post" id="BlueMediaPaymentForm" name="BlueMediaPaymentForm">',
            $transactionDto->getGatewayUrl() . ClientEnum::PAYMENT_ROUTE
        ).PHP_EOL;

        foreach ($transactionDto->getTransaction()->capitalizedArray() as $fieldName => $fieldValue) {
            if (empty($fieldValue)) {
                continue;
            }
            $result .= sprintf('<input type="hidden" name="%s" value="%s" />', $fieldName, $fieldValue).PHP_EOL;
        }

        $result .= '<input type="submit" />'.PHP_EOL;
        $result .= '</form>'.PHP_EOL;
        $result .= '<script type="text/javascript">document.BlueMediaPaymentForm.submit();</script>';
        $result .= '<noscript><p>'.$translation[Translations::JAVASCRIPT_DISABLED].'<br>';

        return $result . $translation[Translations::JAVASCRIPT_REQUIRED] . '</p></noscript>'.PHP_EOL;
    }
}
