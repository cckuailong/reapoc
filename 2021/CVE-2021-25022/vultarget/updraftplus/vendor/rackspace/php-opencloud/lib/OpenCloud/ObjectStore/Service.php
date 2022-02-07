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

namespace OpenCloud\ObjectStore;

use Guzzle\Http\EntityBody;
use OpenCloud\Common\Constants\Header;
use OpenCloud\Common\Constants\Mime;
use OpenCloud\Common\Exceptions;
use OpenCloud\Common\Exceptions\InvalidArgumentError;
use OpenCloud\Common\Http\Client;
use OpenCloud\Common\Http\Message\Formatter;
use OpenCloud\Common\Log\Logger;
use OpenCloud\Common\Service\ServiceBuilder;
use OpenCloud\ObjectStore\Constants\UrlType;
use OpenCloud\ObjectStore\Resource\Container;
use OpenCloud\ObjectStore\Upload\ContainerMigration;

/**
 * The ObjectStore (Cloud Files) service.
 */
class Service extends AbstractService
{
    const DEFAULT_NAME = 'cloudFiles';
    const DEFAULT_TYPE = 'object-store';
    const BATCH_DELETE_MAX = 10000;

    /**
     * This holds the associated CDN service (for Rackspace public cloud)
     * or is NULL otherwise. The existence of an object here is
     * indicative that the CDN service is available.
     */
    private $cdnService;

    public function __construct(Client $client, $type = null, $name = null, $region = null, $urlType = null)
    {
        parent::__construct($client, $type, $name, $region, $urlType);

        try {
            $this->cdnService = ServiceBuilder::factory($client, 'OpenCloud\ObjectStore\CDNService', array(
                'region' => $region
            ));
        } catch (Exceptions\EndpointError $e) {
        }
    }

    /**
     * Return the CDN version of the ObjectStore service.
     *
     * @return CDNService CDN version of the ObjectStore service
     */
    public function getCdnService()
    {
        return $this->cdnService;
    }

    /**
     * List all available containers.
     *
     * @param array $filter Array of filter options such as:
     *
     * * `limit`: number of results to limit the list to. Optional.
     * * `marker`: name of container after which to start the list. Optional.
     * * `end_marker`: name of container before which to end the list. Optional.
     * @return \OpenCloud\Common\Collection\PaginatedIterator Iterator to list of containers
     */
    public function listContainers(array $filter = array())
    {
        $filter['format'] = 'json';
        return $this->resourceList('Container', $this->getUrl(null, $filter), $this);
    }

    /**
     * Return a new or existing (if name is specified) container.
     *
     * @param \stdClass $data Data to initialize container. Optional.
     * @return Container Container
     */
    public function getContainer($data = null)
    {
        if (is_string($data) || is_numeric($data)) {
            $this->checkContainerName($data);
        }

        return new Container($this, $data);
    }

    /**
     * Create a container for this service.
     *
     * @param string $name    The name of the container
     * @param array $metadata Additional (optional) metadata to associate with the container
     * @return bool|Container Newly-created Container upon success; false, otherwise
     */
    public function createContainer($name, array $metadata = array())
    {
        $this->checkContainerName($name);

        $containerHeaders = Container::stockHeaders($metadata);

        $response = $this->getClient()
            ->put($this->getUrl($name), $containerHeaders)
            ->send();

        if ($response->getStatusCode() == 201) {
            return Container::fromResponse($response, $this);
        }

        return false;
    }

    /**
     * Check the validity of a potential container name.
     *
     * @param string $name Name of container
     * @return bool True if container name is valid
     * @throws \OpenCloud\Common\Exceptions\InvalidArgumentError if container name is invalid
     */
    public function checkContainerName($name)
    {
        if (strlen($name) == 0) {
            $error = 'Container name cannot be blank';
        }

        if (strpos($name, '/') !== false) {
            $error = 'Container name cannot contain "/"';
        }

        if (strlen($name) > self::MAX_CONTAINER_NAME_LENGTH) {
            $error = 'Container name is too long';
        }

        if (isset($error)) {
            throw new InvalidArgumentError($error);
        }

        return true;
    }

    /**
     * Perform a bulk extraction, expanding an archive file. If the $path is an empty string, containers will be
     * auto-created accordingly, and files in the archive that do not map to any container (files in the base directory)
     * will be ignored. You can create up to 1,000 new containers per extraction request. Also note that only regular
     * files will be uploaded. Empty directories, symlinks, and so on, will not be uploaded.
     *
     * @param string $path The path to the archive being extracted
     * @param string|stream $archive The contents of the archive (either string or stream)
     * @param string $archiveType The type of archive you're using {@see \OpenCloud\ObjectStore\Constants\UrlType}
     * @return \Guzzle\Http\Message\Response HTTP response from API
     * @throws \OpenCloud\Common\Exceptions\InvalidArgumentError if specifed `$archiveType` is invalid
     * @throws Exception\BulkOperationException if there are errors with the bulk extract
     */
    public function bulkExtract($path = '', $archive, $archiveType = UrlType::TAR_GZ)
    {
        $entity = EntityBody::factory($archive);

        $acceptableTypes = array(
            UrlType::TAR,
            UrlType::TAR_GZ,
            UrlType::TAR_BZ2
        );

        if (!in_array($archiveType, $acceptableTypes)) {
            throw new InvalidArgumentError(sprintf(
                'The archive type must be one of the following: [%s]. You provided [%s].',
                implode($acceptableTypes, ','),
                print_r($archiveType, true)
            ));
        }

        $url = $this->getUrl()->addPath($path)->setQuery(array('extract-archive' => $archiveType));
        $response = $this->getClient()->put($url, array(Header::CONTENT_TYPE => ''), $entity)->send();

        $body = Formatter::decode($response);

        if (!empty($body->Errors)) {
            throw new Exception\BulkOperationException((array) $body->Errors);
        }

        return $response;
    }

    /**
     * @deprecated Please use {@see batchDelete()} instead.
     */
    public function bulkDelete(array $paths)
    {
        $this->getLogger()->warning(Logger::deprecated(__METHOD__, '::batchDelete()'));

        return $this->executeBatchDeleteRequest($paths);
    }

    /**
     * Batch delete will delete an array of object paths. By default,
     * the API will only accept a maximum of 10,000 object deletions
     * per request - so for arrays that exceed this size, it is chunked
     * and sent as individual requests.
     *
     * @param array $paths The objects you want to delete. Each path needs
     *                     be formatted as `/{containerName}/{objectName}`. If
     *                     you are deleting `object_1` and `object_2` from the
     *                     `photos_container`, the array will be:
     *
     *                     array(
     *                        '/photos_container/object_1',
     *                        '/photos_container/object_2'
     *                     )
     *
     * @return array[Guzzle\Http\Message\Response] HTTP responses from the API
     * @throws Exception\BulkOperationException if the bulk delete operation fails
     */
    public function batchDelete(array $paths)
    {
        $chunks = array_chunk($paths, self::BATCH_DELETE_MAX);

        $responses = array();

        foreach ($chunks as $chunk) {
            $responses[] = $this->executeBatchDeleteRequest($chunk);
        }

        return $responses;
    }

    /**
     * Internal method for dispatching single batch delete requests.
     *
     * @param array $paths
     * @return \Guzzle\Http\Message\Response
     * @throws Exception\BulkOperationException
     */
    private function executeBatchDeleteRequest(array $paths)
    {
        $entity = EntityBody::factory(implode(PHP_EOL, $paths));

        $url = $this->getUrl()->setQuery(array('bulk-delete' => true));

        $response = $this->getClient()
            ->delete($url, array(Header::CONTENT_TYPE => Mime::TEXT), $entity)
            ->send();

        try {
            $body = Formatter::decode($response);
            if (!empty($body->Errors)) {
                throw new Exception\BulkOperationException((array) $body->Errors);
            }
        } catch (Exceptions\JsonError $e) {
        }

        return $response;
    }

    /**
     * Allows files to be transferred from one container to another.
     *
     * @param Container $old Where you're moving files from
     * @param Container $new Where you're moving files to
     * @param array $options Options to configure the migration. Optional. Available options are:
     *
     * * `read.batchLimit`: Number of files to read at a time from `$old` container. Optional; default = 1000.
     * * `write.batchLimit`: Number of files to write at a time to `$new` container. Optional; default = 1000.
     * * `read.pageLimit`: Number of filenames to read at a time from `$old` container. Optional; default = 10000.
     * @return array[Guzzle\Http\Message\Response] HTTP responses from the API
     */
    public function migrateContainer(Container $old, Container $new, array $options = array())
    {
        $migration = ContainerMigration::factory($old, $new, $options);

        return $migration->transfer();
    }
}
