<?php

namespace Mollie\Api\Types;

class OrderStatus
{
    /**
     * The order has just been created.
     */
    const STATUS_CREATED = "created";

    /**
     * The order has been paid.
     */
    const STATUS_PAID = "paid";

    /**
     * The order has been authorized.
     */
    const STATUS_AUTHORIZED = "authorized";

    /**
     * The order has been canceled.
     */
    const STATUS_CANCELED = "canceled";

    /**
     * The order is shipping.
     */
    const STATUS_SHIPPING = "shipping";

    /**
     * The order is completed.
     */
    const STATUS_COMPLETED = "completed";

    /**
     * The order is expired.
     */
    const STATUS_EXPIRED = "expired";

    /**
     * The order is pending.
     */
    const STATUS_PENDING = "pending";

    /**
     * (Deprecated) The order has been refunded.
     * @deprecated 2018-11-27
     */
    const STATUS_REFUNDED = "refunded";
}
