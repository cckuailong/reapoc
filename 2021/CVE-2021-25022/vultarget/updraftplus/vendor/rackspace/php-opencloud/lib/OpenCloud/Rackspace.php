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

namespace OpenCloud;

use OpenCloud\Common\Exceptions\CredentialError;
use OpenCloud\Common\Service\ServiceBuilder;

/**
 * Rackspace extends the OpenStack class with support for Rackspace's
 * API key and tenant requirements.
 *
 * The only difference between Rackspace and OpenStack is that the
 * Rackspace class generates credentials using the username
 * and API key, as required by the Rackspace authentication
 * service.
 *
 * Example:
 * <pre><code>
 * $client = new Rackspace(
 *      'https://identity.api.rackspacecloud.com/v2.0/',
 *      array(
 *          'username' => 'FRED',
 *          'apiKey'   => '0900af093093788912388fc09dde090ffee09'
 *      )
 * );
 * </code></pre>
 */
class Rackspace extends OpenStack
{
    const US_IDENTITY_ENDPOINT = 'https://identity.api.rackspacecloud.com/v2.0/';
    const UK_IDENTITY_ENDPOINT = 'https://lon.identity.api.rackspacecloud.com/v2.0/';

    /**
     * Generates Rackspace API key credentials
     * {@inheritDoc}
     */
    public function getCredentials()
    {
        $secret = $this->getSecret();

        if (!empty($secret['username']) && !empty($secret['apiKey'])) {
            $credentials = array('auth' => array(
                'RAX-KSKEY:apiKeyCredentials' => array(
                    'username' => $secret['username'],
                    'apiKey'   => $secret['apiKey']
                )
            ));

            if (!empty($secret['tenantName'])) {
                $credentials['auth']['tenantName'] = $secret['tenantName'];
            } elseif (!empty($secret['tenantId'])) {
                $credentials['auth']['tenantId'] = $secret['tenantId'];
            }

            return json_encode($credentials);
        } else {
            throw new CredentialError('Unrecognized credential secret');
        }
    }

    /**
     * Creates a new Database service. Note: this is a Rackspace-only feature.
     *
     * @param string $name    The name of the service as it appears in the Catalog
     * @param string $region  The region (DFW, IAD, ORD, LON, SYD)
     * @param string $urltype The URL type ("publicURL" or "internalURL")
     * @return \OpenCloud\Database\Service
     */
    public function databaseService($name = null, $region = null, $urltype = null)
    {
        return ServiceBuilder::factory($this, 'OpenCloud\Database\Service', array(
            'name'    => $name,
            'region'  => $region,
            'urlType' => $urltype
        ));
    }

    /**
     * Creates a new Load Balancer service. Note: this is a Rackspace-only feature.
     *
     * @param string $name    The name of the service as it appears in the Catalog
     * @param string $region  The region (DFW, IAD, ORD, LON, SYD)
     * @param string $urltype The URL type ("publicURL" or "internalURL")
     * @return \OpenCloud\LoadBalancer\Service
     */
    public function loadBalancerService($name = null, $region = null, $urltype = null)
    {
        return ServiceBuilder::factory($this, 'OpenCloud\LoadBalancer\Service', array(
            'name'    => $name,
            'region'  => $region,
            'urlType' => $urltype
        ));
    }

    /**
     * Creates a new DNS service. Note: this is a Rackspace-only feature.
     *
     * @param string $name    The name of the service as it appears in the Catalog
     * @param string $region  The region (DFW, IAD, ORD, LON, SYD)
     * @param string $urltype The URL type ("publicURL" or "internalURL")
     * @return OpenCloud\DNS\Service
     */
    public function dnsService($name = null, $region = null, $urltype = null)
    {
        return ServiceBuilder::factory($this, 'OpenCloud\DNS\Service', array(
            'name'    => $name,
            'region'  => $region,
            'urlType' => $urltype
        ));
    }

    /**
     * Creates a new CloudMonitoring service. Note: this is a Rackspace-only feature.
     *
     * @param string $name    The name of the service as it appears in the Catalog
     * @param string $region  The region (DFW, IAD, ORD, LON, SYD)
     * @param string $urltype The URL type ("publicURL" or "internalURL")
     * @return \OpenCloud\CloudMonitoring\Service
     */
    public function cloudMonitoringService($name = null, $region = null, $urltype = null)
    {
        return ServiceBuilder::factory($this, 'OpenCloud\CloudMonitoring\Service', array(
            'name'    => $name,
            'region'  => $region,
            'urlType' => $urltype
        ));
    }

    /**
     * Creates a new CloudQueues service. Note: this is a Rackspace-only feature.
     *
     * @param string $name    The name of the service as it appears in the Catalog
     * @param string $region  The region (DFW, IAD, ORD, LON, SYD)
     * @param string $urltype The URL type ("publicURL" or "internalURL")
     * @return \OpenCloud\Autoscale\Service
     */
    public function autoscaleService($name = null, $region = null, $urltype = null)
    {
        return ServiceBuilder::factory($this, 'OpenCloud\Autoscale\Service', array(
            'name'    => $name,
            'region'  => $region,
            'urlType' => $urltype
        ));
    }

    /**
     * Creates a new CloudQueues service. Note: this is a Rackspace-only feature.
     *
     * @param string $name    The name of the service as it appears in the Catalog
     * @param string $region  The region (DFW, IAD, ORD, LON, SYD)
     * @param string $urltype The URL type ("publicURL" or "internalURL")
     * @return \OpenCloud\Queues\Service
     */
    public function queuesService($name = null, $region = null, $urltype = null)
    {
        return ServiceBuilder::factory($this, 'OpenCloud\Queues\Service', array(
            'name'    => $name,
            'region'  => $region,
            'urlType' => $urltype
        ));
    }

    /**
     * Creates a new CDN (Rackspace CDN) service object
     *
     * @param string $name    The name of the service as it appears in the Catalog
     * @param string $region  The region (DFW, IAD, ORD, LON, SYD)
     * @param string $urltype The URL type ("publicURL" or "internalURL")
     * @return \OpenCloud\Cdn\Service
     * @codeCoverageIgnore
     */
    public function cdnService($name = null, $region = null, $urltype = null)
    {
        return ServiceBuilder::factory($this, 'OpenCloud\CDN\Service', array(
            'name'    => $name,
            'type'    => 'rax:cdn',
            'region'  => $region,
            'urlType' => $urltype
        ));
    }
}
