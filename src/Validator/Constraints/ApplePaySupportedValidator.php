<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApplePaySupportedValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ApplePaySupported) {
            throw new UnexpectedTypeException($constraint, ApplePaySupported::class);
        }

        if (null === $value) { // not received from checkout order request
            return;
        }

        if (empty($value)) {
            $this->context->buildViolation($constraint->message)
                ->setCode(ApplePaySupported::IS_BLUE_MEDIA_APPLE_PAY_NOT_SUPPORTED)
                ->addViolation();
        }
    }
}
