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

namespace OpenCloud\Common;

/**
 * The Metadata class represents either Server or Image metadata
 */
class Metadata extends Base implements \Countable
{
    /**
     * @var array Internal data store.
     */
    protected $metadata = array();

    /**
     * This setter overrides the base one, since the metadata key can be
     * anything
     *
     * @param string $property
     * @param string $value
     * @return void
     */
    public function __set($property, $value)
    {
        return $this->setProperty($property, $value);
    }

    public function __get($key)
    {
        return $this->getProperty($key);
    }

    public function propertyExists($property, $allowRetry = true)
    {
        return isset($this->metadata[strtolower($property)])
        || parent::propertyExists($property, $allowRetry);
    }

    public function getProperty($property)
    {
        return $this->propertyExists($property) ? $this->metadata[strtolower($property)] : null;
    }

    public function setProperty($property, $value)
    {
        $this->metadata[strtolower($property)] = $value;
    }

    public function __isset($property)
    {
        return $this->propertyExists($property);
    }

    /**
     * Returns the list of keys defined
     *
     * @return array
     */
    public function keylist()
    {
        return $this->metadata;
    }

    /**
     * Sets metadata values from an array, with optional prefix
     *
     * If $prefix is provided, then only array keys that match the prefix
     * are set as metadata values, and $prefix is stripped from the key name.
     *
     * @param array  $values an array of key/value pairs to set
     * @param string $prefix if provided, a prefix that is used to identify
     *                       metadata values. For example, you can set values from headers
     *                       for a Container by using $prefix='X-Container-Meta-'.
     * @return void
     */
    public function setArray($values, $prefix = null)
    {
        if (empty($values)) {
            return false;
        }

        foreach ($values as $key => $value) {
            if ($prefix && strpos($key, $prefix) === 0) {
                $key = substr($key, strlen($prefix));
            }
            $this->setProperty($key, $value);
        }
    }

    public function toArray()
    {
        return $this->metadata;
    }

    public function count()
    {
        return count($this->metadata);
    }
}
