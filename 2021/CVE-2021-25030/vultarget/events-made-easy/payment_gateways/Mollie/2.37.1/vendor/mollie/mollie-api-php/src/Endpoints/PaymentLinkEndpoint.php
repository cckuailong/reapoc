<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\PaymentLinkCollection;

class PaymentLinkEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "payment-links";

    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'pl_';

    /**
     * @return PaymentLink
     */
    protected function getResourceObject()
    {
        return new PaymentLink($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return PaymentLinkCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new PaymentLinkCollection($this->client, $count, $_links);
    }

    /**
     * Creates a payment link in Mollie.
     *
     * @param array $data An array containing details on the payment link.
     * @param array $filters
     *
     * @return PaymentLink
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = [])
    {
        return $this->rest_create($data, $filters);
    }

    /**
     * Retrieve payment link from Mollie.
     *
     * Will throw a ApiException if the payment link id is invalid or the resource cannot be found.
     *
     * @param string $paymentLinkId
     * @param array $parameters
     * @return PaymentLink
     * @throws ApiException
     */
    public function get($paymentLinkId, array $parameters = [])
    {
        if (empty($paymentLinkId) || strpos($paymentLinkId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid payment link ID: '{$paymentLinkId}'. A payment link ID should start with '".self::RESOURCE_ID_PREFIX."'.");
        }

        return parent::rest_read($paymentLinkId, $parameters);
    }

    /**
     * Retrieves a collection of Payment Links from Mollie.
     *
     * @param string $from The first payment link ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return PaymentLinkCollection
     * @throws ApiException
     */
    public function page($from = null, $limit = null, array $parameters = [])
    {
        return $this->rest_list($from, $limit, $parameters);
    }
}
