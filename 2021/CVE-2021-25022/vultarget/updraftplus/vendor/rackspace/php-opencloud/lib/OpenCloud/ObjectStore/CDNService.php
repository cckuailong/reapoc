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

use OpenCloud\ObjectStore\Resource\CDNContainer;
use OpenCloud\ObjectStore\Resource\ContainerMetadata;

/**
 * This is the CDN version of the ObjectStore service.
 */
class CDNService extends AbstractService
{
    const DEFAULT_NAME = 'cloudFilesCDN';
    const DEFAULT_TYPE = 'rax:object-cdn';

    /**
     * List CDN-enabled containers.
     *
     * @param array $filter Array of filter options such as:
     *
     * * `limit`: number of results to limit the list to. Optional.
     * * `marker`: name of container after which to start the list. Optional.
     * * `end_marker`: name of container before which to end the list. Optional.
     * @return \OpenCloud\Common\Collection\PaginatedIterator Iterator to list of CDN-enabled containers
     */
    public function listContainers(array $filter = array())
    {
        $filter['format'] = 'json';
        return $this->resourceList('CDNContainer', $this->getUrl(null, $filter), $this);
    }

    /**
     * Return an existing CDN-enabled container.
     *
     * @param \stdClass $data Data to initialize container.
     * @return CDNContainer CDN-enabled Container
     */
    public function cdnContainer($data)
    {
        $container = new CDNContainer($this, $data);

        if (is_object($data)) {
            $metadata = new ContainerMetadata();
            $metadata->setArray(array(
                'Streaming-Uri' => $data->cdn_streaming_uri,
                'Ios-Uri'       => $data->cdn_ios_uri,
                'Ssl-Uri'       => $data->cdn_ssl_uri,
                'Enabled'       => $data->cdn_enabled,
                'Ttl'           => $data->ttl,
                'Log-Retention' => $data->log_retention,
                'Uri'           => $data->cdn_uri,
            ));
            $container->setMetadata($metadata);
        }

        return $container;
    }
}
