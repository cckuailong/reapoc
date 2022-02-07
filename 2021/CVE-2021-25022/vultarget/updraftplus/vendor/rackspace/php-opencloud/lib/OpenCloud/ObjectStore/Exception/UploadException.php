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

namespace OpenCloud\ObjectStore\Exception;

class UploadException extends \Exception
{
    protected $state;

    public function __construct($state, \Exception $exception = null)
    {
        parent::__construct(
            'An error was encountered while performing an upload: ' . $exception->getMessage(),
            0,
            $exception
        );

        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}
