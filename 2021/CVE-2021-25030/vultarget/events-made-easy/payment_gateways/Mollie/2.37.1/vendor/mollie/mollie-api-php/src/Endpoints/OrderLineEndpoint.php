<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderLine;
use Mollie\Api\Resources\OrderLineCollection;

class OrderLineEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "orders_lines";

    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'odl_';

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one
     * type of object.
     *
     * @return OrderLine
     */
    protected function getResourceObject()
    {
        return new OrderLine($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API
     * endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return OrderLineCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new OrderLineCollection($count, $_links);
    }

    /**
     * Update a specific OrderLine resource.
     *
     * Will throw an ApiException if the order line id is invalid or the resource cannot be found.
     *
     * @param $orderId
     * @param string $orderlineId
     *
     * @param array $data
     *
     * @return \Mollie\Api\Resources\BaseResource|null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update($orderId, $orderlineId, array $data = [])
    {
        $this->parentId = $orderId;

        if (empty($orderlineId) || strpos($orderlineId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid order line ID: '{$orderlineId}'. An order line ID should start with '".self::RESOURCE_ID_PREFIX."'.");
        }

        return parent::rest_update($orderlineId, $data);
    }

    /**
     * Cancel lines for the provided order.
     * The data array must contain a lines array.
     * You can pass an empty lines array if you want to cancel all eligible lines.
     * Returns null if successful.
     *
     * @param Order $order
     * @param array $data
     *
     * @return null
     * @throws ApiException
     */
    public function cancelFor(Order $order, array $data)
    {
        return $this->cancelForId($order->id, $data);
    }

    /**
     * Cancel lines for the provided order id.
     * The data array must contain a lines array.
     * You can pass an empty lines array if you want to cancel all eligible lines.
     * Returns null if successful.
     *
     * @param string $orderId
     * @param array $data
     *
     * @return null
     * @throws ApiException
     */
    public function cancelForId($orderId, array $data)
    {
        if (! isset($data['lines']) || ! is_array($data['lines'])) {
            throw new ApiException("A lines array is required.");
        }
        $this->parentId = $orderId;

        $this->client->performHttpCall(
            self::REST_DELETE,
            "{$this->getResourcePath()}",
            $this->parseRequestBody($data)
        );

        return null;
    }
}
