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

use OpenCloud\ObjectStore\Constants\Header as HeaderConst;

/**
 * A container that has been CDN-enabled. Each CDN-enabled container has a unique
 * Uniform Resource Locator (URL) that can be combined with its object names and
 * openly distributed in web pages, emails, or other applications.
 */
class CDNContainer extends AbstractContainer
{
    const METADATA_LABEL = 'Cdn';

    /**
     * @return null|string|int
     */
    public function getCdnSslUri()
    {
        return $this->metadata->getProperty('Ssl-Uri');
    }

    /**
     * @return null|string|int
     */
    public function getCdnUri()
    {
        return $this->metadata->getProperty('Uri');
    }

    /**
     * @return null|string|int
     */
    public function getTtl()
    {
        return $this->metadata->getProperty('Ttl');
    }

    /**
     * @return null|string|int
     */
    public function getCdnStreamingUri()
    {
        return $this->metadata->getProperty('Streaming-Uri');
    }

    /**
     * @return null|string|int
     */
    public function getIosStreamingUri()
    {
        return $this->metadata->getProperty('Ios-Uri');
    }

    public function refresh($name = null, $url = null)
    {
        $response = $this->createRefreshRequest()->send();

        $headers = $response->getHeaders();
        $this->setMetadata($headers, true);

        return $headers;
    }

    /**
     * Turn on access logs, which track all the web traffic that your data objects accrue.
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function enableCdnLogging()
    {
        $headers = array('X-Log-Retention' => 'True');

        return $this->getClient()->put($this->getUrl(), $headers)->send();
    }

    /**
     * Disable access logs.
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function disableCdnLogging()
    {
        $headers = array('X-Log-Retention' => 'False');

        return $this->getClient()->put($this->getUrl(), $headers)->send();
    }

    public function isCdnEnabled()
    {
        return $this->metadata->getProperty(HeaderConst::ENABLED) == 'True';
    }

    /**
     * Set the TTL.
     *
     * @param $ttl The time-to-live in seconds.
     * @return \Guzzle\Http\Message\Response
     */
    public function setTtl($ttl)
    {
        $headers = array('X-Ttl' => $ttl);

        return $this->getClient()->post($this->getUrl(), $headers)->send();
    }
}
