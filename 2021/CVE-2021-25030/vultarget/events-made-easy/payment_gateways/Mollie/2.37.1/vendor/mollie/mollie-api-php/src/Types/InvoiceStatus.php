<?php

namespace Mollie\Api\Types;

class InvoiceStatus
{
    /**
     * The invoice is not paid yet.
     */
    const STATUS_OPEN = "open";

    /**
     * The invoice is paid.
     */
    const STATUS_PAID = "paid";

    /**
     * Payment of the invoice is overdue.
     */
    const STATUS_OVERDUE = "overdue";
}
