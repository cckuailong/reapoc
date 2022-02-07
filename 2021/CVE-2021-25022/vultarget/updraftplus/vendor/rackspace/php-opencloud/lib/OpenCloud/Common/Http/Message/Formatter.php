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

namespace OpenCloud\Common\Http\Message;

use Guzzle\Http\Message\Response;
use OpenCloud\Common\Constants\Header;
use OpenCloud\Common\Constants\Mime;
use OpenCloud\Common\Exceptions\JsonError;

class Formatter
{
    public static function decode(Response $response)
    {
        if (strpos($response->getHeader(Header::CONTENT_TYPE), Mime::JSON) !== false) {
            $string = (string) $response->getBody();
            $response = json_decode($string);
            self::checkJsonError($string);

            return $response;
        }
    }

    public static function encode($body)
    {
        return json_encode($body);
    }

    public static function checkJsonError($string = null)
    {
        if (json_last_error()) {
            $error = sprintf('%s', json_last_error_msg());
            $message = ($string) ? sprintf('%s trying to decode: %s', $error, $string) : $error;
            throw new JsonError($message);
        }
    }
}
