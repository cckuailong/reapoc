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

namespace OpenCloud\CDN;

use OpenCloud\Common\Service\CatalogService;
use OpenCloud\Common\Http\Message\Formatter;
use OpenCloud\CDN\Resource\Service as ServiceResource;
use OpenCloud\CDN\Resource\Flavor;

/**
 * The CDN class represents the OpenStack Poppy service.
 *
 * Poppy is a service that abstracts various CDN providers APIs
 */
class Service extends CatalogService
{
    const SUPPORTED_VERSION = 'v1.0';
    const DEFAULT_TYPE = 'cdn';
    const DEFAULT_NAME = 'rackCDN';
    const MAX_LIMIT = 20;

    protected $regionless = true;

    /**
     * Returns a Service object associated with this CDN service
     *
     * @param string $id ID of service to retrieve
     * @return \OpenCloud\CDN\Resource\Service object
     */
    public function service($id = null)
    {
        return $this->resource('Service', $id);
    }

    /**
     * Creates a new Service and returns it.
     *
     * @see https://github.com/rackspace/php-opencloud/blob/master/docs/userguide/CDN/USERGUIDE.md#create-a-service
     * @param array $params Service creation parameters.
     * @return \OpenCloud\CDN\Resource\Service Object representing created service
     */
    public function createService(array $params = array())
    {
        $service = $this->service();
        $service->create($params);
        return $service;
    }

    /**
     * Returns a Service object associated with this CDN service
     *
     * @param string $id ID of service to retrieve
     * @return \OpenCloud\CDN\Resource\Service object
     */
    public function getService($id)
    {
        return $this->service($id);
    }

    /**
     * Returns a list of services you created
     *
     * @param array $params
     * @return \OpenCloud\Common\Collection\PaginatedIterator
     */
    public function listServices(array $params = array())
    {
        $params['limit'] = isset($params['limit']) && $params['limit'] <= self::MAX_LIMIT ?: self::MAX_LIMIT;

        $url = clone $this->getUrl();
        $url->addPath(ServiceResource::resourceName())->setQuery($params);

        return $this->resourceList('Service', $url);
    }

    /**
     * Returns a Flavor object associated with this CDN service
     *
     * @param string $id ID of flavor to retrieve
     * @return \OpenCloud\CDN\Resource\Flavor object
     */
    public function flavor($id = null)
    {
        return $this->resource('Flavor', $id);
    }

    /**
     * Creates a new Flavor and returns it.
     *
     * @see https://github.com/rackspace/php-opencloud/blob/master/docs/userguide/CDN/USERGUIDE.md#create-a-flavor
     * @param array $params Flavor creation parameters.
     * @return \OpenCloud\CDN\Resource\Flavor Object representing created flavor
     */
    public function createFlavor(array $params = array())
    {
        $flavor = $this->flavor();
        $flavor->create($params);
        return $flavor;
    }

    /**
     * Returns a Flavor object associated with this CDN service
     *
     * @param string $id ID of flavor to retrieve
     * @return \OpenCloud\CDN\Resource\Flavor object
     */
    public function getFlavor($id)
    {
        return $this->flavor($id);
    }

    /**
     * Returns a list of flavors you created
     *
     * @param array $params
     * @return \OpenCloud\Common\Collection\PaginatedIterator
     */
    public function listFlavors(array $params = array())
    {
        $url = clone $this->getUrl();
        $url->addPath(Flavor::resourceName())->setQuery($params);

        return $this->resourceList('Flavor', $url);
    }

    /**
     * Returns the home document for the CDN service
     *
     * @return \stdClass home document response
     */
    public function getHomeDocument()
    {
        $url = clone $this->getUrl();

        // This hack is necessary otherwise Guzzle will remove the trailing
        // slash from the URL and the request will fail because the service
        // expects the trailing slash :(
        $url->setPath($url->getPath() . '/');

        $response = $this->getClient()->get($url)->send();
        return Formatter::decode($response);
    }

    /**
     * Returns the ping (status) response for the CDN service
     *
     * @return Guzzle\Http\Message\Response
     */
    public function getPing()
    {
        $url = clone $this->getUrl();
        $url->addPath('ping');

        $request = $this->getClient()->get($url);

        // This is necessary because the response does not include a body
        // and fails with a 406 Not Acceptable if the default
        // 'Accept: application/json' header is used in the request.
        $request->removeHeader('Accept');

        return $request->send();
    }

    /**
     * Return namespaces.
     *
     * @return array
     */
    public function namespaces()
    {
        return array();
    }
}
