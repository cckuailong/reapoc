<?php

namespace Mollie\Api\Types;

class RefundStatus
{
    /**
     * The refund is queued until there is enough balance to process te refund. You can still cancel the refund.
     */
    const STATUS_QUEUED = 'queued';

    /**
     * The refund will be sent to the bank on the next business day. You can still cancel the refund.
     */
    const STATUS_PENDING = 'pending';

    /**
     * The refund has been sent to the bank. The refund amount will be transferred to the consumer account as soon as possible.
     */
    const STATUS_PROCESSING = 'processing';

    /**
     * The refund amount has been transferred to the consumer.
     */
    const STATUS_REFUNDED = 'refunded';

    /**
     * The refund has failed after processing. For example, the customer has closed his / her bank account. The funds will be returned to your account.
     */
    const STATUS_FAILED = 'failed';
}
