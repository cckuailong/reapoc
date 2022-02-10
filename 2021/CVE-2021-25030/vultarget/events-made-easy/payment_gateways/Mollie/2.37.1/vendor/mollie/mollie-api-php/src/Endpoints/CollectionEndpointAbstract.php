<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\BaseCollection;

abstract class CollectionEndpointAbstract extends EndpointAbstract
{
    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return BaseCollection
     */
    abstract protected function getResourceCollectionObject($count, $_links);
}
