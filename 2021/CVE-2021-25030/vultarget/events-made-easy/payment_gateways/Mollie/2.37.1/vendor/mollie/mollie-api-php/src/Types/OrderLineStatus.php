<?php

namespace Mollie\Api\Types;

class OrderLineStatus
{
    /**
     * The order line has just been created.
     */
    const STATUS_CREATED = "created";

    /**
     * The order line has been paid.
     */
    const STATUS_PAID = "paid";

    /**
     * The order line has been authorized.
     */
    const STATUS_AUTHORIZED = "authorized";

    /**
     * The order line has been canceled.
     */
    const STATUS_CANCELED = "canceled";

    /**
     * (Deprecated) The order line has been refunded.
     * @deprecated
     */
    const STATUS_REFUNDED = "refunded";

    /**
     * The order line is shipping.
     */
    const STATUS_SHIPPING = "shipping";

    /**
     * The order line is completed.
     */
    const STATUS_COMPLETED = "completed";
}
