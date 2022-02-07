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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Description of RequestSubscriber
 */
class RequestSubscriber implements EventSubscriberInterface
{
    public static function getInstance()
    {
        return new self();
    }

    public static function getSubscribedEvents()
    {
        return array(
            'curl.callback.progress' => 'doCurlProgress'
        );
    }

    /**
     * @param $options
     * @return mixed
     * @codeCoverageIgnore
     */
    public function doCurlProgress($options)
    {
        $curlOptions = $options['request']->getCurlOptions();

        if ($curlOptions->hasKey('progressCallback')) {
            return call_user_func($curlOptions->get('progressCallback'));
        }
    }
}
