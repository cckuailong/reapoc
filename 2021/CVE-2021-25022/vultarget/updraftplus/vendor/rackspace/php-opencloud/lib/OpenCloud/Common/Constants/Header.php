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

namespace OpenCloud\Common\Constants;

/**
 * Standard Header Field names as defined in RFC2616.
 *
 * @link    http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
 * @package OpenCloud\Common\Constants
 */
class Header
{
    const ACCEPT = 'Accept';
    const ACCEPT_CHARSET = 'Accept-Charset';
    const ACCEPT_ENCODING = 'Accept-Encoding';
    const ACCEPT_LANGUAGE = 'Accept-Language';
    const ACCEPT_RANGES = 'Accept-Ranges';
    const AGE = 'Age';
    const ALLOW = 'Allow';
    const AUTHORIZATION = 'Authorization';
    const CACHE_CONTROL = 'Cache-Control';
    const CONNECTION = 'Connection';
    const CONTENT_ENCODING = 'Content-Encoding';
    const CONTENT_LANGUAGE = 'Content-Language';
    const CONTENT_LENGTH = 'Content-Length';
    const CONTENT_LOCATION = 'Content-Location';
    const CONTENT_MD5 = 'Content-MD5';
    const CONTENT_RANGE = 'Content-Range';
    const CONTENT_TYPE = 'Content-Type';
    const DATE = 'Date';
    const ETAG = 'ETag';
    const EXPECT = 'Expect';
    const EXPIRES = 'Expires';
    const FROM = 'From';
    const HOST = 'Host';
    const IF_MATCH = 'If-Match';
    const IF_MODIFIED_SINCE = 'If-Modified-Since';
    const IF_NONE_MATCH = 'If-None-Match';
    const IF_RANGE = 'If-Range';
    const IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
    const LAST_MODIFIED = 'Last-Modified';
    const LOCATION = 'Location';
    const MAX_FORWARDS = 'Max-Forwards';
    const PRAGMA = 'Pragma';
    const PROXY_AUTHENTICATION = 'Proxy-Authenticate';
    const PROXY_AUTHORIZATION = 'Proxy-Authorization';
    const RANGE = 'Range';
    const REFERER = 'Referer';
    const RETRY_AFTER = 'Retry-After';
    const SERVER = 'Server';
    const TE = 'TE';
    const TRAILER = 'Trailer';
    const TRANSFER_ENCODING = 'Transfer-Encoding';
    const UPGRADE = 'Upgrade';
    const USER_AGENT = 'User-Agent';
    const VARY = 'Vary';
    const VIA = 'Via';
    const X_OBJECT_MANIFEST = 'X-Object-Manifest';
}
