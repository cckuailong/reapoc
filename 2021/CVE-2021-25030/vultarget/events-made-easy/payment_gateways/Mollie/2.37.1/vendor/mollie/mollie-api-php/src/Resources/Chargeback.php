<?php

namespace Mollie\Api\Resources;

/**
 * @method Refund[]|RefundCollection all($from = null, $limit = 50, array $filters = [])
 * @method Refund get($refundId, array $filters = [])
 * @method Refund create(array $data = [], array $filters = [])
 * @method Refund delete($refundId)
 */
class Chargeback extends BaseResource
{
    /**
     * Id of the payment method.
     *
     * @var string
     */
    public $id;

    /**
     * The $amount that was refunded.
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * UTC datetime the payment was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string|null
     */
    public $createdAt;

    /**
     * The payment id that was refunded.
     *
     * @var string
     */
    public $paymentId;

    /**
     * The settlement amount
     *
     * @var \stdClass
     */
    public $settlementAmount;

    /**
     * @var \stdClass
     */
    public $_links;
}
