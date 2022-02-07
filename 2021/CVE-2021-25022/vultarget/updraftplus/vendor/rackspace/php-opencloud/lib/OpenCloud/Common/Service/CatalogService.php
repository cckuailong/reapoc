<?php
/**
 * Copyright 2012-2014 Rackspace US, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OpenCloud\Common\Service;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Url;
use OpenCloud\Common\Base;
use OpenCloud\Common\Exceptions;
use OpenCloud\Common\Http\Message\Formatter;
use OpenCloud\OpenStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class CatalogService extends AbstractService
{
    const DEFAULT_URL_TYPE = 'publicURL';
    const SUPPORTED_VERSION = null;

    /**
     * @var string The type of this service, as set in Catalog.
     */
    private $type;

    /**
     * @var string The name of this service, as set in Catalog.
     */
    private $name;

    /**
     * @var string The chosen region(s) for this service.
     */
    private $region;

    /**
     * @var string Either 'publicURL' or 'internalURL'.
     */
    private $urlType;

    /**
     * @var bool Indicates whether a service is "regionless" or not. Defaults to FALSE because nearly all services
     *           are region-specific.
     */
    protected $regionless = false;

    /**
     * Creates a service object, based off the specified client.
     *
     * The service's URL is defined in the client's serviceCatalog; it
     * uses the $type, $name, $region, and $urlType to find the proper endpoint
     * and set it. If it cannot find a URL in the service catalog that matches
     * the criteria, then an exception is thrown.
     *
     * @param Client $client  Client object
     * @param string $type    Service type (e.g. 'compute')
     * @param string $name    Service name (e.g. 'cloudServersOpenStack')
     * @param string $region  Service region (e.g. 'DFW', 'ORD', 'IAD', 'LON', 'SYD' or 'HKG')
     * @param string $urlType Either 'publicURL' or 'internalURL'
     */
    public function __construct(ClientInterface $client, $type = null, $name = null, $region = null, $urlType = null)
    {
        if (($client instanceof Base || $client instanceof OpenStack) && $client->hasLogger()) {
            $this->setLogger($client->getLogger());
        }

        $this->setClient($client);

        $this->name = $name ? : static::DEFAULT_NAME;
        $this->region = $region;

        $this->region = $region;
        if ($this->regionless !== true && !$this->region) {
            throw new Exceptions\ServiceException(sprintf(
                'The %s service must have a region set. You can either pass in a region string as an argument param, or'
                . ' set a default region for your user account by executing User::setDefaultRegion and ::update().',
                $this->name
            ));
        }

        $this->type = $type ? : static::DEFAULT_TYPE;
        $this->urlType = $urlType ? : static::DEFAULT_URL_TYPE;
        $this->setEndpoint($this->findEndpoint());

        $this->client->setBaseUrl($this->getBaseUrl());

        if ($this instanceof EventSubscriberInterface) {
            $this->client->getEventDispatcher()->addSubscriber($this);
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrlType()
    {
        return $this->urlType;
    }

    /**
     * @deprecated
     */
    public function region()
    {
        return $this->getRegion();
    }

    /**
     * @deprecated
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Returns the URL for the Service
     *
     * @param  string $path  URL path segment
     * @param  array  $query Array of query pairs
     * @return Guzzle\Http\Url
     */
    public function getUrl($path = null, array $query = array())
    {
        return Url::factory($this->getBaseUrl())
            ->addPath($path)
            ->setQuery($query);
    }

    /**
     * @deprecated
     */
    public function url($path = null, array $query = array())
    {
        return $this->getUrl($path, $query);
    }

    /**
     * Returns the /extensions for the service
     *
     * @api
     * @return array of objects
     */
    public function getExtensions()
    {
        $ext = $this->getMetaUrl('extensions');

        return (is_object($ext) && isset($ext->extensions)) ? $ext->extensions : array();
    }

    /**
     * Returns the limits for the service
     *
     * @return array of limits
     */
    public function limits()
    {
        $limits = $this->getMetaUrl('limits');

        return (is_object($limits)) ? $limits->limits : array();
    }

    /**
     * Extracts the appropriate endpoint from the service catalog based on the
     * name and type of a service, and sets for further use.
     *
     * @return \OpenCloud\Common\Service\Endpoint
     * @throws \OpenCloud\Common\Exceptions\EndpointError
     */
    private function findEndpoint()
    {
        if (!$this->getClient()->getCatalog()) {
            $this->getClient()->authenticate();
        }

        $catalog = $this->getClient()->getCatalog();

        // Search each service to find The One
        foreach ($catalog->getItems() as $service) {
            if ($service->hasType($this->type) && $service->hasName($this->name)) {
                $endpoint = $service->getEndpointFromRegion($this->region, $this->regionless);
                return Endpoint::factory($endpoint, static::SUPPORTED_VERSION, $this->getClient());
            }
        }

        throw new Exceptions\EndpointError(sprintf(
            'No endpoints for service type [%s], name [%s], region [%s] and urlType [%s]',
            $this->type,
            $this->name,
            $this->region,
            $this->urlType
        ));
    }

    /**
     * Constructs a specified URL from the subresource
     *
     * Given a subresource (e.g., "extensions"), this constructs the proper
     * URL and retrieves the resource.
     *
     * @param string $resource The resource requested; should NOT have slashes
     *                         at the beginning or end
     * @return \stdClass object
     */
    private function getMetaUrl($resource)
    {
        $url = clone $this->getBaseUrl();
        $url->addPath($resource);
        try {
            $response = $this->getClient()->get($url)->send();

            return Formatter::decode($response);
        } catch (BadResponseException $e) {
            // @codeCoverageIgnoreStart
            return array();
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Get the base URL for this service, based on the set URL type.
     * @return \Guzzle\Http\Url
     * @throws \OpenCloud\Common\Exceptions\ServiceException
     */
    public function getBaseUrl()
    {
        $url = ($this->urlType == 'publicURL')
            ? $this->endpoint->getPublicUrl()
            : $this->endpoint->getPrivateUrl();

        if ($url === null) {
            throw new Exceptions\ServiceException(sprintf(
                'The base %s could not be found. Perhaps the service '
                . 'you are using requires a different URL type, or does '
                . 'not support this region.',
                $this->urlType
            ));
        }

        return $url;
    }
}
