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

namespace OpenCloud\Identity\Resource;

use OpenCloud\Common\PersistentObject;

/**
 * Token class for token functionality.
 *
 * A token is an opaque string that represents an authorization to access cloud resources. Tokens may be revoked at any
 * time and are valid for a finite duration.
 *
 * @package OpenCloud\Identity\Resource
 */
class Token extends PersistentObject
{
    /** @var string The token ID */
    private $id;

    /** @var string Timestamp of when this token will expire */
    private $expires;

    protected static $url_resource = 'tokens';

    /**
     * @param $id Sets the ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string Returns the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $expires Set the expiry timestamp
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * @return string Get the expiry timestamp
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @return bool Check whether this token has expired (i.e. still valid or not)
     */
    public function hasExpired()
    {
        return time() >= strtotime($this->expires);
    }
}
