<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;

class ProfileEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "profiles";

    protected $resourceClass = Profile::class;

    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'pfl_';
    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Profile
     */
    protected function getResourceObject()
    {
        return new $this->resourceClass($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return ProfileCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new ProfileCollection($this->client, $count, $_links);
    }

    /**
     * Creates a Profile in Mollie.
     *
     * @param array $data An array containing details on the profile.
     * @param array $filters
     *
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Profile
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = [])
    {
        return $this->rest_create($data, $filters);
    }

    /**
     * Retrieve a Profile from Mollie.
     *
     * Will throw an ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param string $profileId
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Profile
     * @throws ApiException
     */
    public function get($profileId, array $parameters = [])
    {
        if ($profileId === 'me') {
            return $this->getCurrent($parameters);
        }

        return $this->rest_read($profileId, $parameters);
    }

    /**
     * Update a specific Profile resource.
     *
     * Will throw an ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param string $profileId
     *
     * @param array $data
     * @return Profile
     * @throws ApiException
     */
    public function update($profileId, array $data = [])
    {
        if (empty($profileId) || strpos($profileId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid profile id: '{$profileId}'. An profile id should start with '".self::RESOURCE_ID_PREFIX."'.");
        }

        return parent::rest_update($profileId, $data);
    }

    /**
     * Retrieve the current Profile from Mollie.
     *
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\CurrentProfile
     * @throws ApiException
     */
    public function getCurrent(array $parameters = [])
    {
        $this->resourceClass = CurrentProfile::class;

        return $this->rest_read('me', $parameters);
    }

    /**
     * Delete a Profile from Mollie.
     *
     * Will throw a ApiException if the profile id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param string $profileId
     *
     * @param array $data
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Profile
     * @throws ApiException
     */
    public function delete($profileId, array $data = [])
    {
        return $this->rest_delete($profileId, $data);
    }

    /**
     * Retrieves a collection of Profiles from Mollie.
     *
     * @param string $from The first profile ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseCollection|\Mollie\Api\Resources\ProfileCollection
     * @throws ApiException
     */
    public function page($from = null, $limit = null, array $parameters = [])
    {
        return $this->rest_list($from, $limit, $parameters);
    }
}
