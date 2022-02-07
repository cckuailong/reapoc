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

namespace OpenCloud\Common\Http;

use Guzzle\Http\Client as GuzzleClient;
use OpenCloud\Version;

/**
 * Base client object which handles HTTP transactions. Each service is based off of a Client which acts as a
 * centralized parent.
 */
class Client extends GuzzleClient
{
    public function getDefaultUserAgent()
    {
        return 'OpenCloud/' . Version::getVersion()
          . ' Guzzle/' . Version::getGuzzleVersion()
          . ' cURL/' . Version::getCurlVersion()
          . ' PHP/' . PHP_VERSION;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }
}
