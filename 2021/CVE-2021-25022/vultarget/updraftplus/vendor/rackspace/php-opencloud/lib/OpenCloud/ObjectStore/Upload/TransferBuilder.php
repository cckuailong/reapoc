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

use Guzzle\Http\EntityBody;
use OpenCloud\Common\Exceptions\InvalidArgumentError;
use OpenCloud\ObjectStore\Resource\Container;

/**
 * Factory which creates Transfer objects, either ConcurrentTransfer or ConsecutiveTransfer.
 */
class TransferBuilder
{
    /**
     * @var Container The container being uploaded to
     */
    protected $container;

    /**
     * @var EntityBody The data payload.
     */
    protected $entityBody;

    /**
     * @var array A key/value pair of options.
     */
    protected $options = array();

    /**
     * @return TransferBuilder
     */
    public static function newInstance()
    {
        return new self();
    }

    /**
     * @param type $options Available configuration options:
     *
     * * `concurrency'    <bool>   The number of concurrent workers.
     * * `partSize'       <int>    The size, in bytes, for the chunk
     * * `doPartChecksum' <bool>   Enable or disable MD5 checksum in request (ETag)
     *
     * If you are uploading FooBar, its chunks will have the following naming structure:
     *
     * FooBar/1
     * FooBar/2
     * FooBar/3
     *
     * @return \OpenCloud\ObjectStore\Upload\UploadBuilder
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param $key   The option name
     * @param $value The option value
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param EntityBody $entityBody
     * @return $this
     */
    public function setEntityBody(EntityBody $entityBody)
    {
        $this->entityBody = $entityBody;

        return $this;
    }

    /**
     * Build the transfer.
     *
     * @return mixed
     * @throws \OpenCloud\Common\Exceptions\InvalidArgumentError
     */
    public function build()
    {
        // Validate properties
        if (!$this->container || !$this->entityBody || !$this->options['objectName']) {
            throw new InvalidArgumentError('A container, entity body and object name must be set');
        }

        // Create TransferState object for later use
        $transferState = TransferState::factory();

        // Instantiate Concurrent-/ConsecutiveTransfer
        $transferClass = isset($this->options['concurrency']) && $this->options['concurrency'] > 1
            ? __NAMESPACE__ . '\\ConcurrentTransfer'
            : __NAMESPACE__ . '\\ConsecutiveTransfer';

        return $transferClass::newInstance()
            ->setClient($this->container->getClient())
            ->setEntityBody($this->entityBody)
            ->setTransferState($transferState)
            ->setOptions($this->options)
            ->setOption('containerName', $this->container->getName())
            ->setOption('containerUrl', $this->container->getUrl())
            ->setup();
    }
}
