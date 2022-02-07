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

namespace OpenCloud\ObjectStore\Upload;

/**
 * Represents the current state of the Transfer.
 *
 * @codeCoverageIgnore
 */
class TransferState
{
    /**
     * @var array Holds all of the parts which have been transferred.
     */
    protected $completedParts = array();

    /**
     * @var bool
     */
    protected $running;

    /**
     * @return $this
     */
    public static function factory()
    {
        $self = new self();

        return $self->init();
    }

    /**
     * @param TransferPart $part
     */
    public function addPart(TransferPart $part)
    {
        $this->completedParts[] = $part;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->completedParts);
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->running;
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->running = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function cancel()
    {
        $this->running = false;

        return $this;
    }
}
