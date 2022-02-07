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

namespace OpenCloud\CDN\Resource;

use OpenCloud\Common\Resource\PersistentResource;

/**
 * A flavor is a configuration for the CDN service. A flavor enables you to
 * choose from a generic setting that is powered by one or more CDN providers.
 *
 * @package OpenCloud\CDN\Resource
 */
class Flavor extends PersistentResource
{
    protected static $url_resource = 'flavors';
    protected static $json_name = 'flavor';

    protected $id;
    protected $providers;

    protected $createKeys = array(
        'id',
        'providers'
    );

    public function update($params = array())
    {
        return $this->noUpdate();
    }

    protected function createJson()
    {
        $createJson = parent::createJson();
        return $createJson->{self::$json_name};
    }
}
