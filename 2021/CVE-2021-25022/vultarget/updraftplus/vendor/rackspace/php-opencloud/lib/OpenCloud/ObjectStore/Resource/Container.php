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

use Guzzle\Http\EntityBody;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use OpenCloud\Common\Constants\Size;
use OpenCloud\Common\Exceptions;
use OpenCloud\Common\Service\ServiceInterface;
use OpenCloud\ObjectStore\Constants\Header as HeaderConst;
use OpenCloud\ObjectStore\Exception\ContainerException;
use OpenCloud\ObjectStore\Exception\ObjectNotFoundException;
use OpenCloud\ObjectStore\Upload\DirectorySync;
use OpenCloud\ObjectStore\Upload\TransferBuilder;
use OpenCloud\ObjectStore\Enum\ReturnType;

/**
 * A container is a storage compartment for your data and provides a way for you
 * to organize your data. You can think of a container as a folder in Windows
 * or a directory in Unix. The primary difference between a container and these
 * other file system concepts is that containers cannot be nested.
 *
 * A container can also be CDN-enabled (for public access), in which case you
 * will need to interact with a CDNContainer object instead of this one.
 */
class Container extends AbstractContainer
{
    const METADATA_LABEL = 'Container';

    /**
     * This is the object that holds all the CDN functionality. This Container therefore acts as a simple wrapper and is
     * interested in storage concerns only.
     *
     * @var CDNContainer|null
     */
    private $cdn;

    public function __construct(ServiceInterface $service, $data = null)
    {
        parent::__construct($service, $data);

        // Set metadata items for collection listings
        if (isset($data->count)) {
            $this->metadata->setProperty('Object-Count', $data->count);
        }
        if (isset($data->bytes)) {
            $this->metadata->setProperty('Bytes-Used', $data->bytes);
        }
    }

    /**
     * Factory method that instantiates an object from a Response object.
     *
     * @param Response         $response
     * @param ServiceInterface $service
     * @return static
     */
    public static function fromResponse(Response $response, ServiceInterface $service)
    {
        $self = parent::fromResponse($response, $service);

        $segments = Url::factory($response->getEffectiveUrl())->getPathSegments();
        $self->name = end($segments);

        return $self;
    }

    /**
     * Get the CDN object.
     *
     * @return null|CDNContainer
     * @throws \OpenCloud\Common\Exceptions\CdnNotAvailableError
     */
    public function getCdn()
    {
        if (!$this->isCdnEnabled()) {
            throw new Exceptions\CdnNotAvailableError(
                'Either this container is not CDN-enabled or the CDN is not available'
            );
        }

        return $this->cdn;
    }

    /**
     * It would be awesome to put these convenience methods (which are identical to the ones in the Account object) in
     * a trait, but we have to wait for v5.3 EOL first...
     *
     * @return null|string|int
     */
    public function getObjectCount()
    {
        return $this->metadata->getProperty('Object-Count');
    }

    /**
     * @return null|string|int
     */
    public function getBytesUsed()
    {
        return $this->metadata->getProperty('Bytes-Used');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setCountQuota($value)
    {
        $this->metadata->setProperty('Quota-Count', $value);

        return $this->saveMetadata($this->metadata->toArray());
    }

    /**
     * @return null|string|int
     */
    public function getCountQuota()
    {
        return $this->metadata->getProperty('Quota-Count');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setBytesQuota($value)
    {
        $this->metadata->setProperty('Quota-Bytes', $value);

        return $this->saveMetadata($this->metadata->toArray());
    }

    /**
     * @return null|string|int
     */
    public function getBytesQuota()
    {
        return $this->metadata->getProperty('Quota-Bytes');
    }

    public function delete($deleteObjects = false)
    {
        if ($deleteObjects === true) {
            // Delegate to auxiliary method
            return $this->deleteWithObjects();
        }

        try {
            return $this->getClient()->delete($this->getUrl())->send();
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() == 409) {
                throw new ContainerException(sprintf(
                    'The API returned this error: %s. You might have to delete all existing objects before continuing.',
                    (string) $e->getResponse()->getBody()
                ));
            } else {
                throw $e;
            }
        }
    }

    public function deleteWithObjects($secondsToWait = null)
    {
        // If container is empty, just delete it
        $numObjects = (int) $this->retrieveMetadata()->getProperty('Object-Count');
        if (0 === $numObjects) {
            return $this->delete();
        }

        // If timeout ($secondsToWait) is not specified by caller,
        // try to estimate it based on number of objects in container
        if (null === $secondsToWait) {
            $secondsToWait = round($numObjects / 2);
        }

        // Attempt to delete all objects and container
        $endTime = time() + $secondsToWait;
        $containerDeleted = false;
        while ((time() < $endTime) && !$containerDeleted) {
            $this->deleteAllObjects();
            try {
                $response = $this->delete();
                $containerDeleted = true;
            } catch (ContainerException $e) {
                // Ignore exception and try again
            } catch (ClientErrorResponseException $e) {
                if ($e->getResponse()->getStatusCode() == 404) {
                    // Container has been deleted
                    $containerDeleted = true;
                } else {
                    throw $e;
                }
            }
        }

        if (!$containerDeleted) {
            throw new ContainerException('Container and all its objects could not be deleted.');
        }

        return $response;
    }

    /**
     * Deletes all objects that this container currently contains. Useful when doing operations (like a delete) that
     * require an empty container first.
     *
     * @return mixed
     */
    public function deleteAllObjects()
    {
        $paths = array();
        $objects = $this->objectList();
        foreach ($objects as $object) {
            $paths[] = sprintf('/%s/%s', $this->getName(), $object->getName());
        }
        return $this->getService()->batchDelete($paths);
    }

    /**
     * Delete an object from the API.
     *
     * @param string $name The name of object you want to delete
     * @throws \Guzzle\Http\Exception\BadResponseException When an error occurred
     */
    public function deleteObject($name)
    {
        $this->getClient()
            ->delete($this->getUrl($name))
            ->send();
    }

    /**
     * Creates a Collection of objects in the container
     *
     * @param array $params associative array of parameter values.
     *                      * account/tenant - The unique identifier of the account/tenant.
     *                      * container- The unique identifier of the container.
     *                      * limit (Optional) - The number limit of results.
     *                      * marker (Optional) - Value of the marker, that the object names
     *                      greater in value than are returned.
     *                      * end_marker (Optional) - Value of the marker, that the object names
     *                      less in value than are returned.
     *                      * prefix (Optional) - Value of the prefix, which the returned object
     *                      names begin with.
     *                      * format (Optional) - Value of the serialized response format, either
     *                      json or xml.
     *                      * delimiter (Optional) - Value of the delimiter, that all the object
     *                      names nested in the container are returned.
     * @link   http://api.openstack.org for a list of possible parameter
     *                      names and values
     * @return \OpenCloud\Common\Collection
     * @throws ObjFetchError
     */
    public function objectList(array $params = array())
    {
        $params['format'] = 'json';

        return $this->getService()->resourceList('DataObject', $this->getUrl(null, $params), $this);
    }

    /**
     * Turn on access logs, which track all the web traffic that your data objects accrue.
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function enableLogging()
    {
        return $this->saveMetadata($this->appendToMetadata(array(
            HeaderConst::ACCESS_LOGS => 'True'
        )));
    }

    /**
     * Disable access logs.
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function disableLogging()
    {
        return $this->saveMetadata($this->appendToMetadata(array(
            HeaderConst::ACCESS_LOGS => 'False'
        )));
    }

    /**
     * Enable this container for public CDN access.
     *
     * @param null $ttl
     */
    public function enableCdn($ttl = null)
    {
        $headers = array('X-CDN-Enabled' => 'True');
        if ($ttl) {
            $headers['X-TTL'] = (int) $ttl;
        }

        $this->getClient()->put($this->getCdnService()->getUrl($this->name), $headers)->send();
        $this->refresh();
    }

    /**
     * Disables the containers CDN function. Note that the container will still
     * be available on the CDN until its TTL expires.
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function disableCdn()
    {
        $headers = array('X-CDN-Enabled' => 'False');

        return $this->getClient()
            ->put($this->getCdnService()->getUrl($this->name), $headers)
            ->send();
    }

    public function refresh($id = null, $url = null)
    {
        $headers = $this->createRefreshRequest()->send()->getHeaders();
        $this->setMetadata($headers, true);
    }

    /**
     * Get either a fresh data object (no $info), or get an existing one by passing in data for population.
     *
     * @param  mixed $info
     * @return DataObject
     */
    public function dataObject($info = null)
    {
        return new DataObject($this, $info);
    }

    /**
     * Retrieve an object from the API. Apart from using the name as an
     * identifier, you can also specify additional headers that will be used
     * fpr a conditional GET request. These are
     *
     * * `If-Match'
     * * `If-None-Match'
     * * `If-Modified-Since'
     * * `If-Unmodified-Since'
     * * `Range'  For example:
     *      bytes=-5    would mean the last 5 bytes of the object
     *      bytes=10-15 would mean 5 bytes after a 10 byte offset
     *      bytes=32-   would mean all dat after first 32 bytes
     *
     * These are also documented in RFC 2616.
     *
     * @param string $name
     * @param array  $headers
     * @return DataObject
     */
    public function getObject($name, array $headers = array())
    {
        try {
            $response = $this->getClient()
                ->get($this->getUrl($name), $headers)
                ->send();
        } catch (BadResponseException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                throw ObjectNotFoundException::factory($name, $e);
            }
            throw $e;
        }

        return $this->dataObject()
            ->populateFromResponse($response)
            ->setName($name);
    }

    /**
     * Essentially the same as {@see getObject()}, except only the metadata is fetched from the API.
     * This is useful for cases when the user does not want to fetch the full entity body of the
     * object, only its metadata.
     *
     * @param       $name
     * @param array $headers
     * @return $this
     */
    public function getPartialObject($name, array $headers = array())
    {
        $response = $this->getClient()
            ->head($this->getUrl($name), $headers)
            ->send();

        return $this->dataObject()
            ->populateFromResponse($response)
            ->setName($name);
    }

    /**
     * Check if an object exists inside a container. Uses {@see getPartialObject()}
     * to save on bandwidth and time.
     *
     * @param  $name    Object name
     * @return boolean  True, if object exists in this container; false otherwise.
     */
    public function objectExists($name)
    {
        try {
            // Send HEAD request to check resource existence
            $url = clone $this->getUrl();
            $url->addPath((string) $name);
            $this->getClient()->head($url)->send();
        } catch (ClientErrorResponseException $e) {
            // If a 404 was returned, then the object doesn't exist
            if ($e->getResponse()->getStatusCode() === 404) {
                return false;
            } else {
                throw $e;
            }
        }

        return true;
    }

    /**
     * Upload a single file to the API.
     *
     * @param       $name    Name that the file will be saved as in your container.
     * @param       $data    Either a string or stream representation of the file contents to be uploaded.
     * @param array $headers Optional headers that will be sent with the request (useful for object metadata).
     * @return DataObject
     */
    public function uploadObject($name, $data, array $headers = array())
    {
        $entityBody = EntityBody::factory($data);

        $url = clone $this->getUrl();
        $url->addPath($name);

        // @todo for new major release: Return response rather than populated DataObject

        $response = $this->getClient()->put($url, $headers, $entityBody)->send();

        return $this->dataObject()
            ->populateFromResponse($response)
            ->setName($name)
            ->setContent($entityBody);
    }

    /**
     * Upload an array of objects for upload. This method optimizes the upload procedure by batching requests for
     * faster execution. This is a very useful procedure when you just have a bunch of unremarkable files to be
     * uploaded quickly. Each file must be under 5GB.
     *
     * @param array $files   With the following array structure:
     *                       `name' Name that the file will be saved as in your container. Required.
     *                       `path' Path to an existing file, OR
     *                       `body' Either a string or stream representation of the file contents to be uploaded.
     * @param array $headers Optional headers that will be sent with the request (useful for object metadata).
     * @param string $returnType One of OpenCloud\ObjectStore\Enum\ReturnType::RESPONSE_ARRAY (to return an array of
     *                           Guzzle\Http\Message\Response objects) or OpenCloud\ObjectStore\Enum\ReturnType::DATA_OBJECT_ARRAY
     *                           (to return an array of OpenCloud\ObjectStore\Resource\DataObject objects).
     *
     * @throws \OpenCloud\Common\Exceptions\InvalidArgumentError
     * @return Guzzle\Http\Message\Response[] or OpenCloud\ObjectStore\Resource\DataObject[] depending on $returnType
     */
    public function uploadObjects(array $files, array $commonHeaders = array(), $returnType = ReturnType::RESPONSE_ARRAY)
    {
        $requests = $entities = array();

        foreach ($files as $entity) {
            if (empty($entity['name'])) {
                throw new Exceptions\InvalidArgumentError('You must provide a name.');
            }

            if (!empty($entity['path']) && file_exists($entity['path'])) {
                $body = fopen($entity['path'], 'r+');
            } elseif (!empty($entity['body'])) {
                $body = $entity['body'];
            } else {
                throw new Exceptions\InvalidArgumentError('You must provide either a readable path or a body');
            }

            $entityBody = $entities[] = EntityBody::factory($body);

            // @codeCoverageIgnoreStart
            if ($entityBody->getContentLength() >= 5 * Size::GB) {
                throw new Exceptions\InvalidArgumentError(
                    'For multiple uploads, you cannot upload more than 5GB per '
                    . ' file. Use the UploadBuilder for larger files.'
                );
            }
            // @codeCoverageIgnoreEnd

            // Allow custom headers and common
            $headers = (isset($entity['headers'])) ? $entity['headers'] : $commonHeaders;

            $url = clone $this->getUrl();
            $url->addPath($entity['name']);

            $requests[] = $this->getClient()->put($url, $headers, $entityBody);
        }

        $responses = $this->getClient()->send($requests);

        if (ReturnType::RESPONSE_ARRAY === $returnType) {
            foreach ($entities as $entity) {
                $entity->close();
            }
            return $responses;
        } else {
            // Convert responses to DataObjects before returning
            $dataObjects = array();
            foreach ($responses as $index => $response) {
                $dataObjects[] = $this->dataObject()
                               ->populateFromResponse($response)
                               ->setName($files[$index]['name'])
                               ->setContent($entities[$index]);
            }
            return $dataObjects;
        }
    }

    /**
     * When uploading large files (+5GB), you need to upload the file as chunks using multibyte transfer. This method
     * sets up the transfer, and in order to execute the transfer, you need to call upload() on the returned object.
     *
     * @param array Options
     * @see \OpenCloud\ObjectStore\Upload\UploadBuilder::setOptions for a list of accepted options.
     * @throws \OpenCloud\Common\Exceptions\InvalidArgumentError
     * @return mixed
     */
    public function setupObjectTransfer(array $options = array())
    {
        // Name is required
        if (empty($options['name'])) {
            throw new Exceptions\InvalidArgumentError('You must provide a name.');
        }

        // As is some form of entity body
        if (!empty($options['path']) && file_exists($options['path'])) {
            $body = fopen($options['path'], 'r+');
        } elseif (!empty($options['body'])) {
            $body = $options['body'];
        } else {
            throw new Exceptions\InvalidArgumentError('You must provide either a readable path or a body');
        }

        // Build upload
        $transfer = TransferBuilder::newInstance()
            ->setOption('objectName', $options['name'])
            ->setEntityBody(EntityBody::factory($body))
            ->setContainer($this);

        // Add extra options
        if (!empty($options['metadata'])) {
            $transfer->setOption('metadata', $options['metadata']);
        }
        if (!empty($options['partSize'])) {
            $transfer->setOption('partSize', $options['partSize']);
        }
        if (!empty($options['concurrency'])) {
            $transfer->setOption('concurrency', $options['concurrency']);
        }
        if (!empty($options['progress'])) {
            $transfer->setOption('progress', $options['progress']);
        }

        return $transfer->build();
    }

    /**
     * Upload the contents of a local directory to a remote container, effectively syncing them.
     *
     * @param string $path      The local path to the directory.
     * @param string $targetDir The path (or pseudo-directory) that all files will be nested in.
     */
    public function uploadDirectory($path, $targetDir = null)
    {
        $sync = DirectorySync::factory($path, $this, $targetDir);
        $sync->execute();
    }

    public function isCdnEnabled()
    {
        // If CDN object is not already populated, try to populate it.
        if (null === $this->cdn) {
            $this->refreshCdnObject();
        }
        return ($this->cdn instanceof CDNContainer) && $this->cdn->isCdnEnabled();
    }

    protected function refreshCdnObject()
    {
        try {
            if (null !== ($cdnService = $this->getService()->getCDNService())) {
                $cdn = new CDNContainer($cdnService);
                $cdn->setName($this->name);

                $response = $cdn->createRefreshRequest()->send();

                if ($response->isSuccessful()) {
                    $this->cdn = $cdn;
                    $this->cdn->setMetadata($response->getHeaders(), true);
                }
            } else {
                $this->cdn = null;
            }
        } catch (ClientErrorResponseException $e) {
        }
    }
}
