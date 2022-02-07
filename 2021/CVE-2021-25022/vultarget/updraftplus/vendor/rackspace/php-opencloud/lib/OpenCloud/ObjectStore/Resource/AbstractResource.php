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

namespace OpenCloud\ObjectStore\Resource;

use Guzzle\Http\Message\Response;
use OpenCloud\Common\Base;
use OpenCloud\Common\Service\ServiceInterface;

/**
 * Abstract base class which implements shared functionality of ObjectStore
 * resources. Provides support, for example, for metadata-handling and other
 * features that are common to the ObjectStore components.
 */
abstract class AbstractResource extends Base
{
    const GLOBAL_METADATA_PREFIX = 'X';

    /** @var \OpenCloud\Common\Metadata */
    protected $metadata;

    /** @var string The FQCN of the metadata object used for the container. */
    protected $metadataClass = 'OpenCloud\\Common\\Metadata';

    /** @var \OpenCloud\Common\Service\ServiceInterface The service object. */
    protected $service;

    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
        $this->metadata = new $this->metadataClass;
    }

    /**
     * For internal use only.
     *
     * @return Service The ObjectStore service associated with this ObjectStore resource.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * For internal use only.
     *
     * @return Service The CDN version of the ObjectStore service associated with this ObjectStore resource.
     */
    public function getCdnService()
    {
        return $this->service->getCDNService();
    }

    /**
     * For internal use only.
     *
     * @return Client The HTTP client associated with the associated ObjectStore service.
     */
    public function getClient()
    {
        return $this->service->getClient();
    }

    /**
     * Factory method that allows for easy instantiation from a Response object.
     *
     * For internal use only.
     *
     * @param Response         $response HTTP response from an API operation.
     * @param ServiceInterface $service  The ObjectStore service to associate with this ObjectStore resource object.
     * @return AbstractResource A concrete sub-class of `AbstractResource`.
     */
    public static function fromResponse(Response $response, ServiceInterface $service)
    {
        $object = new static($service);

        if (null !== ($headers = $response->getHeaders())) {
            $object->setMetadata($headers, true);
        }

        return $object;
    }

    /**
     * Trim headers of their resource-specific prefixes.
     *
     * For internal use only.
     *
     * @param  array $headers Headers as returned from an HTTP response
     * @return array Trimmed headers
     */
    public static function trimHeaders($headers)
    {
        $output = array();

        foreach ($headers as $header => $value) {
            // Only allow allow X-<keyword>-* headers to pass through after stripping them
            if (static::headerIsValidMetadata($header) && ($key = self::stripPrefix($header))) {
                $output[$key] = (string) $value;
            }
        }

        return $output;
    }

    protected static function headerIsValidMetadata($header)
    {
        $pattern = sprintf('#^%s\-#i', self::GLOBAL_METADATA_PREFIX);

        return preg_match($pattern, $header);
    }

    /**
     * Strip an individual header name of its resource-specific prefix.
     *
     * @param $header
     * @return mixed
     */
    protected static function stripPrefix($header)
    {
        $pattern = '#^' . self::GLOBAL_METADATA_PREFIX . '\-(' . static::METADATA_LABEL . '-)?(Meta-)?#i';

        return preg_replace($pattern, '', $header);
    }

    /**
     * Prepend/stock the header names with a resource-specific prefix.
     *
     * @param array $headers Headers to use on ObjectStore resource.
     * @return array Headers returned with appropriate prefix as expected by ObjectStore service.
     */
    public static function stockHeaders(array $headers)
    {
        $output = array();
        $prefix = null;
        $corsHeaders = array(
            'Access-Control-Allow-Origin',
            'Access-Control-Expose-Headers',
            'Access-Control-Max-Age',
            'Access-Control-Allow-Credentials',
            'Access-Control-Allow-Methods',
            'Access-Control-Allow-Headers'
        );
        foreach ($headers as $header => $value) {
            if (!in_array($header, $corsHeaders)) {
                $prefix = self::GLOBAL_METADATA_PREFIX . '-' . static::METADATA_LABEL . '-Meta-';
            }
            $output[$prefix . $header] = $value;
        }

        return $output;
    }

    /**
     * Set the metadata (local-only) for this object. You must call saveMetadata
     * to actually persist the metadata using the ObjectStore service.
     *
     * @param array $data Object/container metadata key/value pair array.
     * @param bool $constructFromResponse Whether the metadata key/value pairs were obtiained from an HTTP response of an ObjectStore API operation.
     * @return AbstractResource This object, with metadata set.
     */
    public function setMetadata($data, $constructFromResponse = false)
    {
        if ($constructFromResponse) {
            $metadata = new $this->metadataClass;
            $metadata->setArray(self::trimHeaders($data));
            $data = $metadata;
        }

        $this->metadata = $data;

        return $this;
    }

    /**
     * Returns metadata for this object.
     *
     * @return \OpenCloud\Common\Metadata Metadata set on this object.
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Push local metadata to the API, thereby executing a permanent save.
     *
     * @param array $metadata    The array of values you want to set as metadata
     * @param bool  $stockPrefix Whether to prepend each array key with the metadata-specific prefix. For objects, this
     *                           would be X-Object-Meta-Foo => Bar
     * @return Response HTTP response from API operation.
     */
    public function saveMetadata(array $metadata, $stockPrefix = true)
    {
        $headers = ($stockPrefix === true) ? self::stockHeaders($metadata) : $metadata;

        return $this->getClient()->post($this->getUrl(), $headers)->send();
    }

    /**
     * Retrieve metadata from the API. This method will then set and return this value.
     *
     * @return \OpenCloud\Common\Metadata Metadata returned from the ObjectStore service for this object/container.
     */
    public function retrieveMetadata()
    {
        $response = $this->getClient()
            ->head($this->getUrl())
            ->send();

        $this->setMetadata($response->getHeaders(), true);

        return $this->metadata;
    }

    /**
     * To delete or unset a particular metadata item.
     *
     * @param $key Metadata key to unset
     * @return Response HTTP response returned from API operation to unset metadata item.
     */
    public function unsetMetadataItem($key)
    {
        $header = sprintf('%s-Remove-%s-Meta-%s', self::GLOBAL_METADATA_PREFIX,
            static::METADATA_LABEL, $key);

        $headers = array($header => 'True');

        return $this->getClient()
            ->post($this->getUrl(), $headers)
            ->send();
    }

    /**
     * Append a particular array of values to the existing metadata. Analogous
     * to a merge. You must call to actually persist the metadata using the
     * ObjectStore service.
     *
     * @param array $values The array of values you want to append to metadata.
     * @return array Metadata, after `$values` are appended.
     */
    public function appendToMetadata(array $values)
    {
        return (!empty($this->metadata) && is_array($this->metadata))
            ? array_merge($this->metadata, $values)
            : $values;
    }
}
