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

/**
 * Represents an account that interacts with the CloudFiles API.
 *
 * @link http://docs.rackspace.com/files/api/v1/cf-devguide/content/Accounts-d1e421.html
 */
class Account extends AbstractResource
{
    const METADATA_LABEL = 'Account';

    /**
     * @var string The temporary URL secret for this account
     */
    private $tempUrlSecret;

    public function getUrl($path = null, array $query = array())
    {
        return $this->getService()->getUrl();
    }

    /**
     * Convenience method.
     *
     * @return \OpenCloud\Common\Metadata
     */
    public function getDetails()
    {
        return $this->retrieveMetadata();
    }

    /**
     * @return null|string|int
     */
    public function getObjectCount()
    {
        return $this->metadata->getProperty('Object-Count');
    }

    /**
     * @return null|string|int
     */
    public function getContainerCount()
    {
        return $this->metadata->getProperty('Container-Count');
    }

    /**
     * @return null|string|int
     */
    public function getBytesUsed()
    {
        return $this->metadata->getProperty('Bytes-Used');
    }

    /**
     * Sets the secret value for the temporary URL.
     *
     * @link http://docs.rackspace.com/files/api/v1/cf-devguide/content/Set_Account_Metadata-d1a4460.html
     *
     * @param null $secret The value to set the secret to. If left blank, a random hash is generated.
     * @return $this
     */
    public function setTempUrlSecret($secret = null)
    {
        if (!$secret) {
            $secret = sha1(rand(1, 99999));
        }

        $this->tempUrlSecret = $secret;

        $this->saveMetadata($this->appendToMetadata(array('Temp-Url-Key' => $secret)));

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTempUrlSecret()
    {
        if (null === $this->tempUrlSecret) {
            $this->retrieveMetadata();
            $this->tempUrlSecret = $this->metadata->getProperty('Temp-Url-Key');
        }

        return $this->tempUrlSecret;
    }
}
