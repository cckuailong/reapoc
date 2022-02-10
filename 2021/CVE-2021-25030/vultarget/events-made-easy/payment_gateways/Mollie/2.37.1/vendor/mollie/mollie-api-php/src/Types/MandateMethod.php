<?php

namespace Mollie\Api\Types;

class MandateMethod
{
    const DIRECTDEBIT = "directdebit";
    const CREDITCARD = "creditcard";
    const PAYPAL = "paypal";

    public static function getForFirstPaymentMethod($firstPaymentMethod)
    {
        if ($firstPaymentMethod === PaymentMethod::PAYPAL) {
            return static::PAYPAL;
        }

        if (in_array($firstPaymentMethod, [
            PaymentMethod::APPLEPAY,
            PaymentMethod::CREDITCARD,
        ])) {
            return static::CREDITCARD;
        }

        return static::DIRECTDEBIT;
    }
}
