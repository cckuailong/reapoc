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

namespace OpenCloud\Common\Resource;

use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use OpenCloud\Common\Base;
use OpenCloud\Common\Exceptions\DocumentError;
use OpenCloud\Common\Exceptions\ServiceException;
use OpenCloud\Common\Exceptions\UrlError;
use OpenCloud\Common\Metadata;
use OpenCloud\Common\Service\ServiceInterface;
use OpenCloud\Common\Http\Message\Formatter;

abstract class BaseResource extends Base
{
    /** @var \OpenCloud\Common\Service\ServiceInterface */
    protected $service;

    /** @var BaseResource */
    protected $parent;

    /** @var \OpenCloud\Common\Metadata */
    protected $metadata;

    /**
     * @param ServiceInterface $service The service that this resource belongs to
     * @param $data $data
     */
    public function __construct(ServiceInterface $service, $data = null)
    {
        $this->setService($service);
        $this->metadata = new Metadata();
        $this->populate($data);
    }

    /**
     * @param \OpenCloud\Common\Service\ServiceInterface $service
     * @return \OpenCloud\Common\PersistentObject
     */
    public function setService(ServiceInterface $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return \OpenCloud\Common\Service\ServiceInterface
     * @throws \OpenCloud\Common\Exceptions\ServiceException
     */
    public function getService()
    {
        if (null === $this->service) {
            throw new ServiceException('No service defined');
        }

        return $this->service;
    }

    /**
     * @param BaseResource $parent
     * @return self
     */
    public function setParent(BaseResource $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        if (null === $this->parent) {
            $this->parent = $this->getService();
        }

        return $this->parent;
    }

    /**
     * Convenience method to return the service's client
     *
     * @return \Guzzle\Http\ClientInterface
     */
    public function getClient()
    {
        return $this->getService()->getClient();
    }

    /**
     * @param mixed $metadata
     * @return $this
     */
    public function setMetadata($data)
    {
        if ($data instanceof Metadata) {
            $metadata = $data;
        } elseif (is_array($data) || is_object($data)) {
            $metadata = new Metadata();
            $metadata->setArray($data);
        } else {
            throw new \InvalidArgumentException(sprintf(
                'You must specify either an array/object of parameters, or an '
                . 'instance of Metadata. You provided: %s',
                print_r($data, true)
            ));
        }

        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Get this resource's URL
     *
     * @param null  $path   URI path to add on
     * @param array $query  Query to add on
     * @return mixed
     */
    public function getUrl($path = null, array $query = array())
    {
        if (!$url = $this->findLink('self')) {
            // ...otherwise construct a URL from parent and this resource's
            // "URL name". If no name is set, resourceName() throws an error.
            $url = $this->getParent()->getUrl($this->resourceName());

            // Does it have a primary key?
            if (null !== ($primaryKey = $this->getProperty($this->primaryKeyField()))) {
                $url->addPath((string) $primaryKey);
            }
        }

        if (!$url instanceof Url) {
            $url = Url::factory($url);
        }

        return $url->addPath((string) $path)->setQuery($query);
    }

    /**
     * @deprecated
     */
    public function url($path = null, array $query = array())
    {
        return $this->getUrl($path, $query);
    }


    /**
     * Find a resource link based on a type
     *
     * @param string $type
     * @return bool
     */
    public function findLink($type = 'self')
    {
        if (empty($this->links)) {
            return false;
        }

        foreach ($this->links as $link) {
            if ($link->rel == $type) {
                return $link->href;
            }
        }

        return false;
    }

    /**
     * Returns the primary key field for the object
     *
     * @return string
     */
    protected function primaryKeyField()
    {
        return 'id';
    }

    /**
     * Returns the top-level key for the returned response JSON document
     *
     * @throws DocumentError
     */
    public static function jsonName()
    {
        if (isset(static::$json_name)) {
            return static::$json_name;
        }

        throw new DocumentError('A top-level JSON document key has not been defined for this resource');
    }

    /**
     * Returns the top-level key for collection responses
     *
     * @return string
     */
    public static function jsonCollectionName()
    {
        return isset(static::$json_collection_name) ? static::$json_collection_name : static::$json_name . 's';
    }

    /**
     * Returns the nested keys that could (rarely) prefix collection items. For example:
     *
     * {
     *    "keypairs": [
     *       {
     *          "keypair": {
     *              "fingerprint": "...",
     *              "name": "key1",
     *              "public_key": "..."
     *          }
     *       },
     *       {
     *          "keypair": {
     *              "fingerprint": "...",
     *              "name": "key2",
     *              "public_key": "..."
     *          }
     *       }
     *    ]
     * }
     *
     * In the above example, "keypairs" would be the $json_collection_name and "keypair" would be the
     * $json_collection_element
     *
     * @return string
     */
    public static function jsonCollectionElement()
    {
        if (isset(static::$json_collection_element)) {
            return static::$json_collection_element;
        }
    }

    /**
     * Returns the URI path for this resource
     *
     * @throws UrlError
     */
    public static function resourceName()
    {
        if (isset(static::$url_resource)) {
            return static::$url_resource;
        }

        throw new UrlError('No URL path defined for this resource');
    }

    /**
     * Parse a HTTP response for the required content
     *
     * @param Response $response
     * @return mixed
     */
    public function parseResponse(Response $response)
    {
        $document = Formatter::decode($response);

        $topLevelKey = $this->jsonName();

        return ($topLevelKey && isset($document->$topLevelKey)) ? $document->$topLevelKey : $document;
    }
}
