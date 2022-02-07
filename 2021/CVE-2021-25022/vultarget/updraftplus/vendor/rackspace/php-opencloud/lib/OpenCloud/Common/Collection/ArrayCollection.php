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

namespace OpenCloud\Common\Collection;

use Countable;
use OpenCloud\Common\ArrayAccess;

/**
 * A generic, abstract collection class that allows collections to exhibit array functionality.
 *
 * @package OpenCloud\Common\Collection
 */
abstract class ArrayCollection extends ArrayAccess implements Countable
{
    /**
     * @var array The elements being held by this iterator.
     */
    protected $elements;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->setElements($data);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setElements(array $data = array())
    {
        $this->elements = $data;

        return $this;
    }

    /**
     * Appends a value to the container.
     *
     * @param $value
     */
    public function append($value)
    {
        $this->elements[] = $value;
    }

    /**
     * Checks to see whether a particular value exists.
     *
     * @param $value
     * @return bool
     */
    public function valueExists($value)
    {
        return array_search($value, $this->elements) !== false;
    }
}
