<?php

namespace Mollie\Api\Types;

class PaymentMethod
{
    /**
     * @link https://www.mollie.com/en/payments/applepay
     */
    const APPLEPAY = "applepay";

    /**
     * @link https://www.mollie.com/en/payments/bancontact
     */
    const BANCONTACT = "bancontact";

    /**
     * @link https://www.mollie.com/en/payments/bank-transfer
     */
    const BANKTRANSFER = "banktransfer";

    /**
     * @link https://www.mollie.com/en/payments/belfius
     */
    const BELFIUS = "belfius";

    /**
     * @deprecated 2019-05-01
     */
    const BITCOIN = "bitcoin";

    /**
     * @link https://www.mollie.com/en/payments/credit-card
     */
    const CREDITCARD = "creditcard";

    /**
     * @link https://www.mollie.com/en/payments/direct-debit
     */
    const DIRECTDEBIT = "directdebit";

    /**
     * @link https://www.mollie.com/en/payments/eps
     */
    const EPS = "eps";

    /**
     * @link https://www.mollie.com/en/payments/gift-cards
     */
    const GIFTCARD = "giftcard";

    /**
     * @link https://www.mollie.com/en/payments/giropay
     */
    const GIROPAY = "giropay";

    /**
     * @link https://www.mollie.com/en/payments/ideal
     */
    const IDEAL = "ideal";

    /**
     * Support for inghomepay will be discontinued February 1st, 2021.
     * Make sure to remove this payment method from your checkout if needed.
     *
     * @deprecated
     * @link https://docs.mollie.com/changelog/v2/changelog
     *
     */
    const INGHOMEPAY = "inghomepay";

    /**
     * @link https://www.mollie.com/en/payments/kbc-cbc
     */
    const KBC = "kbc";

    /**
     * @link https://www.mollie.com/en/payments/klarna-pay-later
     */
    const KLARNA_PAY_LATER = "klarnapaylater";

    /**
     * @link https://www.mollie.com/en/payments/klarna-slice-it
     */
    const KLARNA_SLICE_IT = "klarnasliceit";

    /**
     * @link https://www.mollie.com/en/payments/mybank
     */
    const MYBANK = "mybank";

    /**
     * @link https://www.mollie.com/en/payments/paypal
     */
    const PAYPAL = "paypal";

    /**
     * @link https://www.mollie.com/en/payments/paysafecard
     */
    const PAYSAFECARD = "paysafecard";

    /**
     * @link https://www.mollie.com/en/payments/przelewy24
     */
    const PRZELEWY24 = 'przelewy24';

    /**
     * @deprecated
     * @link https://www.mollie.com/en/payments/gift-cards
     */
    const PODIUMCADEAUKAART = "podiumcadeaukaart";

    /**
     * @link https://www.mollie.com/en/payments/sofort
     */
    const SOFORT = "sofort";
}
