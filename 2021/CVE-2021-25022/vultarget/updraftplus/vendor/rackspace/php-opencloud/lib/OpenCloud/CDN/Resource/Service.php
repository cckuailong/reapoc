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
 * A service represents your application that has its content cached to the
 * edge nodes.
 *
 * @package OpenCloud\CDN\Resource
 */
class Service extends PersistentResource
{
    protected static $url_resource = 'services';
    protected static $json_name = 'service';

    protected $id;
    protected $name;
    protected $domains;
    protected $origins;
    protected $caching;
    protected $restrictions;
    protected $flavorId;
    protected $status;
    protected $links;
    protected $errors;

    protected $aliases = array(
        'flavor_id' => 'flavorId',
        'http_host' => 'httpHost',
        'request_url' => 'requestUrl'
    );

    protected $createKeys = array(
        'name',
        'domains',
        'origins',
        'caching',
        'restrictions',
        'flavorId'
    );

    protected $updateKeys = array(
        'name',
        'domains',
        'origins',
        'caching',
        'restrictions',
        'flavorId'
    );

    public function purgeAssets($assetUrl = null)
    {
        $assetsUrl = $this->assetsUrl();
        if (null === $assetUrl) {
            $assetsUrl->setQuery(array('all' => 'true'));
        } else {
            $assetsUrl->setQuery(array('url' => $assetUrl));
        }

        $request = $this->getClient()->delete($assetsUrl);

        // This is necessary because the response does not include a body
        // and fails with a 406 Not Acceptable if the default
        // 'Accept: application/json' header is used in the request.
        $request->removeHeader('Accept');

        return $request->send();
    }

    protected function assetsUrl()
    {
        $url = clone $this->getUrl();
        $url->addPath('assets');

        return $url;
    }

    protected function createJson()
    {
        $createJson = parent::createJson();
        return $createJson->{self::$json_name};
    }

    /**
     * Update this resource
     *
     * @param array $params
     * @return \Guzzle\Http\Message\Response
     */
    public function update($params = array())
    {
        $json = $this->generateJsonPatch($params);

        return $this->getClient()
            ->patch($this->getUrl(), $this->getPatchHeaders(), $json)
            ->send();
    }
}
