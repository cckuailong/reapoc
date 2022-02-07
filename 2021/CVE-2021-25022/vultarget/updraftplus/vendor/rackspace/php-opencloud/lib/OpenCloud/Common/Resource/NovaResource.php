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

namespace OpenCloud\Common\Resource;

abstract class NovaResource extends PersistentResource
{
    /**
     * This method is used for many purposes, such as rebooting server, etc.
     *
     * @param $object
     * @return \Guzzle\Http\Message\Response
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function action($object)
    {
        if (!$this->getProperty($this->primaryKeyField())) {
            throw new \RuntimeException('A primary key is required');
        }

        if (!is_object($object)) {
            throw new \InvalidArgumentException(sprintf('This method expects an object as its parameter'));
        }

        // convert the object to json
        $json = json_encode($object);
        $this->checkJsonError();

        // get the URL for the POST message
        $url = clone $this->getUrl();
        $url->addPath('action');

        // POST the message
        return $this->getClient()->post($url, self::getJsonHeader(), $json)->send();
    }
}
