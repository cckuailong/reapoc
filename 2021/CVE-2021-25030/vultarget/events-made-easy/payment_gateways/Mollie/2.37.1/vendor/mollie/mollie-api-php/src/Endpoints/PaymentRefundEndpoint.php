<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class PaymentRefundEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "payments_refunds";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Refund
     */
    protected function getResourceObject()
    {
        return new Refund($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return RefundCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new RefundCollection($this->client, $count, $_links);
    }

    /**
     * @param Payment $payment
     * @param string $refundId
     * @param array $parameters
     *
     * @return Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Payment $payment, $refundId, array $parameters = [])
    {
        return $this->getForId($payment->id, $refundId, $parameters);
    }

    /**
     * @param string $paymentId
     * @param string $refundId
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId($paymentId, $refundId, array $parameters = [])
    {
        $this->parentId = $paymentId;

        return parent::rest_read($refundId, $parameters);
    }

    /**
     * @param Payment $payment
     * @param array $parameters
     *
     * @return Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Payment $payment, array $parameters = [])
    {
        return $this->listForId($payment->id, $parameters);
    }

    /**
     * @param string $paymentId
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseCollection|\Mollie\Api\Resources\Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId($paymentId, array $parameters = [])
    {
        $this->parentId = $paymentId;

        return parent::rest_list(null, null, $parameters);
    }
}
