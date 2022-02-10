<?php

namespace Mollie\Api\Types;

class PaymentStatus
{
    /**
     * The payment has just been created, no action has happened on it yet.
     */
    const STATUS_OPEN = "open";

    /**
     * The payment has just been started, no final confirmation yet.
     */
    const STATUS_PENDING = "pending";

    /**
     * The payment is authorized, but captures still need to be created in order to receive the money.
     *
     * This is currently only possible for Klarna Pay later and Klarna Slice it. Payments with these payment methods can
     * only be created with the Orders API. You should create a Shipment to trigger the capture to receive the money.
     *
     * @see https://docs.mollie.com/reference/v2/shipments-api/create-shipment
     */
    const STATUS_AUTHORIZED = "authorized";

    /**
     * The customer has canceled the payment.
     */
    const STATUS_CANCELED = "canceled";

    /**
     * The payment has expired due to inaction of the customer.
     */
    const STATUS_EXPIRED = "expired";

    /**
     * The payment has been paid.
     */
    const STATUS_PAID = "paid";

    /**
     * The payment has failed.
     */
    const STATUS_FAILED = "failed";
}
