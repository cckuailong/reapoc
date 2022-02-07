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

use Guzzle\Http\Url;
use OpenCloud\OpenStack;
use OpenCloud\Common\Http\Message\Formatter;
use OpenCloud\Common\Exceptions\UnsupportedVersionError;

/**
 * An endpoint serves as a location which receives and emits API interactions. It will therefore also host
 * particular API resources. Each endpoint object has different access methods - one receives network connections over
 * the public Internet, another receives traffic through an internal network. You will be able to access the latter
 * from a Server, for example, in the same Region - which will incur no bandwidth charges, and be quicker.
 */
class Endpoint
{
    /**
     * @var \Guzzle\Http\Url
     */
    private $publicUrl;

    /**
     * @var \Guzzle\Http\Url
     */
    private $privateUrl;

    /**
     * @var string
     */
    private $region;

    /**
     * @param $object
     * @param string $supportedServiceVersion Service version supported by the SDK
     * @param OpenCloud\OpenStack $client OpenStack client
     * @return Endpoint
     */
    public static function factory($object, $supportedServiceVersion, OpenStack $client)
    {
        $endpoint = new self();

        if (isset($object->publicURL)) {
            $endpoint->setPublicUrl($endpoint->getVersionedUrl($object->publicURL, $supportedServiceVersion, $client));
        }
        if (isset($object->internalURL)) {
            $endpoint->setPrivateUrl($endpoint->getVersionedUrl($object->internalURL, $supportedServiceVersion, $client));
        }
        if (isset($object->region)) {
            $endpoint->setRegion($object->region);
        }

        return $endpoint;
    }

    /**
     * @param $publicUrl
     * @return $this
     */
    public function setPublicUrl(Url $publicUrl)
    {
        $this->publicUrl = $publicUrl;

        return $this;
    }

    /**
     * @return Url
     */
    public function getPublicUrl()
    {
        return $this->publicUrl;
    }

    /**
     * @param $privateUrl
     * @return $this
     */
    public function setPrivateUrl(Url $privateUrl)
    {
        $this->privateUrl = $privateUrl;

        return $this;
    }

    /**
     * @return Url
     */
    public function getPrivateUrl()
    {
        return $this->privateUrl;
    }

    /**
     * @param $region
     * @return $this
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Returns the endpoint URL with a version in it
     *
     * @param string $url Endpoint URL
     * @param string $supportedServiceVersion Service version supported by the SDK
     * @param OpenCloud\OpenStack $client OpenStack client
     * @return Guzzle/Http/Url Endpoint URL with version in it
     */
    private function getVersionedUrl($url, $supportedServiceVersion, OpenStack $client)
    {
        $versionRegex = '/\/[vV][0-9][0-9\.]*/';
        if (1 === preg_match($versionRegex, $url)) {
            // URL has version in it; use it as-is
            return Url::factory($url);
        }

        // If there is no version in $url but no $supportedServiceVersion
        // is specified, just return $url as-is but log a warning
        if (is_null($supportedServiceVersion)) {
            $client->getLogger()->warning('Service version supported by SDK not specified. Using versionless service URL as-is, without negotiating version.');
            return Url::factory($url);
        }

        // Make GET request to URL
        $response = Formatter::decode($client->get($url)->send());

        // Attempt to parse response and determine URL for given $version
        if (!isset($response->versions) || !is_array($response->versions)) {
            throw new UnsupportedVersionError('Could not negotiate version with service.');
        }

        foreach ($response->versions as $version) {
            if (($version->status == 'CURRENT' || $version->status == 'SUPPORTED')
                && $version->id == $supportedServiceVersion) {
                foreach ($version->links as $link) {
                    if ($link->rel == 'self') {
                        return Url::factory($link->href);
                    }
                }
            }
        }

        // If we've reached this point, we could not find a versioned
        // URL in the response; throw an error
        throw new UnsupportedVersionError(sprintf(
            'SDK supports version %s which is not currently provided by service.',
            $supportedServiceVersion
        ));
    }
}
