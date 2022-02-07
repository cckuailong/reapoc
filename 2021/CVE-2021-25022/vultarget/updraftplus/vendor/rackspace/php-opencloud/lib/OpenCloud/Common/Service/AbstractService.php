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
use OpenCloud\Common\Base;
use OpenCloud\Common\Collection\PaginatedIterator;
use OpenCloud\Common\Exceptions;

/**
 * This class defines a cloud service; a relationship between a specific OpenStack
 * and a provided service, represented by a URL in the service catalog.
 *
 * Because Service is an abstract class, it cannot be called directly. Provider
 * services such as Rackspace Cloud Servers or OpenStack Swift are each
 * subclassed from Service.
 */
abstract class AbstractService extends Base implements ServiceInterface
{
    /**
     * @var \OpenCloud\Common\Http\Client The client which interacts with the API.
     */
    protected $client;

    /**
     * @var \OpenCloud\Common\Service\Endpoint The endpoint for this service.
     */
    protected $endpoint;

    /**
     * @var array A collection of resource models that this service has control over.
     */
    protected $resources = array();

    /**
     * @var array Namespaces for this service.
     */
    protected $namespaces = array();

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return \OpenCloud\Common\Http\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Endpoint $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return \OpenCloud\Common\Service\Endpoint
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get all associated resources for this service.
     *
     * @access public
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Internal method for accessing child namespace from parent scope.
     *
     * @return type
     */
    protected function getCurrentNamespace()
    {
        $namespace = get_class($this);

        return substr($namespace, 0, strrpos($namespace, '\\'));
    }

    /**
     * Resolves FQCN for local resource.
     *
     * @param  $resourceName
     * @return string
     * @throws \OpenCloud\Common\Exceptions\UnrecognizedServiceError
     */
    protected function resolveResourceClass($resourceName)
    {
        $className = substr_count($resourceName, '\\')
            ? $resourceName
            : $this->getCurrentNamespace() . '\\Resource\\' . ucfirst($resourceName);

        if (!class_exists($className)) {
            throw new Exceptions\UnrecognizedServiceError(sprintf(
                '%s resource does not exist, please try one of the following: %s',
                $resourceName,
                implode(', ', $this->getResources())
            ));
        }

        return $className;
    }

    /**
     * Factory method for instantiating resource objects.
     *
     * @param  string $resourceName
     * @param  mixed  $info   (default: null)
     * @param  mixed  $parent The parent object
     * @return object
     */
    public function resource($resourceName, $info = null, $parent = null)
    {
        $className = $this->resolveResourceClass($resourceName);

        $resource = new $className($this);

        if ($parent) {
            $resource->setParent($parent);
        }

        $resource->populate($info);

        return $resource;
    }

    /**
     * Factory method for instantiating a resource collection.
     *
     * @param string      $resourceName
     * @param string|null $url
     * @param string|null $parent
     * @return PaginatedIterator
     */
    public function resourceList($resourceName, $url = null, $parent = null)
    {
        $className = $this->resolveResourceClass($resourceName);

        return $this->collection($className, $url, $parent);
    }

    /**
     * @codeCoverageIgnore
     */
    public function collection($class, $url = null, $parent = null, $data = null)
    {
        if (!$parent) {
            $parent = $this;
        }

        if (!$url) {
            $resource = $this->resolveResourceClass($class);
            $url = $parent->getUrl($resource::resourceName());
        }

        $options = $this->makeResourceIteratorOptions($this->resolveResourceClass($class));
        $options['baseUrl'] = $url;

        return PaginatedIterator::factory($parent, $options, $data);
    }

    /**
     * @deprecated
     */
    public function namespaces()
    {
        return $this->getNamespaces();
    }

    /**
     * Returns a list of supported namespaces
     *
     * @return array
     */
    public function getNamespaces()
    {
        return (isset($this->namespaces) && is_array($this->namespaces))
            ? $this->namespaces
            : array();
    }
}
