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
use OpenCloud\Common\Exceptions\ServiceException;

/**
 * This object is a factory for building Service objects.
 */
class ServiceBuilder
{
    /**
     * Simple factory method for creating services.
     *
     * @param Client $client  The HTTP client object
     * @param string $class   The class name of the service
     * @param array  $options The options.
     * @return \OpenCloud\Common\Service\ServiceInterface
     * @throws ServiceException
     */
    public static function factory(ClientInterface $client, $class, array $options = array())
    {
        $name = isset($options['name']) ? $options['name'] : null;
        $type = isset($options['type']) ? $options['type'] : null;
        $urlType = isset($options['urlType']) ? $options['urlType'] : null;

        if (isset($options['region'])) {
            $region = $options['region'];
        } elseif ($client->getUser() && ($defaultRegion = $client->getUser()->getDefaultRegion())) {
            $region = $defaultRegion;
        } else {
            $region = null;
        }

        return new $class($client, $type, $name, $region, $urlType);
    }
}
