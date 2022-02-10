<?php

namespace Mollie\Api\Types;

class SettlementStatus
{
    /**
     * The settlement has not been closed yet.
     */
    const STATUS_OPEN = 'open';

    /**
     * The settlement has been closed and is being processed.
     */
    const STATUS_PENDING = 'pending';

    /**
     * The settlement has been paid out.
     */
    const STATUS_PAIDOUT = 'paidout';

    /**
     * The settlement could not be paid out.
     */
    const STATUS_FAILED = 'failed';
}
