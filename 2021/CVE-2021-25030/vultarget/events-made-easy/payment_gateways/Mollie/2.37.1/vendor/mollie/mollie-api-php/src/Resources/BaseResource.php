<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;

abstract class BaseResource
{
    /**
     * @var MollieApiClient
     */
    protected $client;

    /**
     * @param $client
     */
    public function __construct(MollieApiClient $client)
    {
        $this->client = $client;
    }
}
