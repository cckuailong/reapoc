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

use Guzzle\Http\Url;
use OpenCloud\Common\Constants\State;
use OpenCloud\Common\Exceptions\CreateError;
use OpenCloud\Common\Exceptions\DeleteError;
use OpenCloud\Common\Exceptions\IdRequiredError;
use OpenCloud\Common\Exceptions\NameError;
use OpenCloud\Common\Exceptions\UnsupportedExtensionError;
use OpenCloud\Common\Exceptions\UpdateError;
use mikemccabe\JsonPatch\JsonPatch;

abstract class PersistentResource extends BaseResource
{
    /**
     * Create a new resource
     *
     * @param array $params
     * @return \Guzzle\Http\Message\Response
     */
    public function create($params = array())
    {
        // set parameters
        if (!empty($params)) {
            $this->populate($params, false);
        }

        // construct the JSON
        $json = json_encode($this->createJson());
        $this->checkJsonError();

        $createUrl = $this->createUrl();

        $response = $this->getClient()->post($createUrl, self::getJsonHeader(), $json)->send();

        // We have to try to parse the response body first because it should have precedence over a Location refresh.
        // I'd like to reverse the order, but Nova instances return ephemeral properties on creation which are not
        // available when you follow the Location link...
        if (null !== ($decoded = $this->parseResponse($response))) {
            $this->populate($decoded);
        } elseif ($location = $response->getHeader('Location')) {
            $this->refreshFromLocationUrl($location);
        }

        return $response;
    }

    /**
     * Update a resource
     *
     * @param array $params
     * @return \Guzzle\Http\Message\Response
     */
    public function update($params = array())
    {
        // set parameters
        if (!empty($params)) {
            $this->populate($params);
        }

        // construct the JSON
        $json = json_encode($this->updateJson($params));
        $this->checkJsonError();

        // send the request
        return $this->getClient()->put($this->getUrl(), self::getJsonHeader(), $json)->send();
    }

    /**
     * Delete this resource
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function delete()
    {
        return $this->getClient()->delete($this->getUrl())->send();
    }

    /**
     * Refresh the state of a resource
     *
     * @param null $id
     * @param null $url
     * @return \Guzzle\Http\Message\Response
     * @throws IdRequiredError
     */
    public function refresh($id = null, $url = null)
    {
        $primaryKey = $this->primaryKeyField();
        $primaryKeyVal = $this->getProperty($primaryKey);

        if (!$url) {
            if (!$id = $id ?: $primaryKeyVal) {
                $message = sprintf("This resource cannot be refreshed because it has no %s", $primaryKey);
                throw new IdRequiredError($message);
            }

            if ($primaryKeyVal != $id) {
                $this->setProperty($primaryKey, $id);
            }

            $url = $this->getUrl();
        }

        // reset status, if available
        if ($this->getProperty('status')) {
            $this->setProperty('status', null);
        }

        $response = $this->getClient()->get($url)->send();

        if (null !== ($decoded = $this->parseResponse($response))) {
            $this->populate($decoded);
        }

        return $response;
    }


    /**
     * Causes resource to refresh based on parent's URL
     */
    protected function refreshFromParent()
    {
        $url = clone $this->getParent()->getUrl();
        $url->addPath($this->resourceName());

        $response = $this->getClient()->get($url)->send();

        if (null !== ($decoded = $this->parseResponse($response))) {
            $this->populate($decoded);
        }
    }

    /**
     * Given a `location` URL, refresh this resource
     *
     * @param $url
     */
    public function refreshFromLocationUrl($url)
    {
        $fullUrl = Url::factory($url);

        $response = $this->getClient()->get($fullUrl)->send();

        if (null !== ($decoded = $this->parseResponse($response))) {
            $this->populate($decoded);
        }
    }

    /**
     * A method to repeatedly poll the API resource, waiting for an eventual state change
     *
     * @param null $state    The expected state of the resource
     * @param null $timeout  The maximum timeout to wait
     * @param null $callback The callback to use to check the state
     * @param null $interval How long between each refresh request
     */
    public function waitFor($state = null, $timeout = null, $callback = null, $interval = null)
    {
        $state    = $state ?: State::ACTIVE;
        $timeout  = $timeout ?: State::DEFAULT_TIMEOUT;
        $interval = $interval ?: State::DEFAULT_INTERVAL;

        // save stats
        $startTime = time();

        $states = array('ERROR', $state);

        while (true) {
            $this->refresh($this->getProperty($this->primaryKeyField()));

            if ($callback) {
                call_user_func($callback, $this);
            }

            if (in_array($this->status(), $states) || (time() - $startTime) > $timeout) {
                return;
            }

            sleep($interval);
        }
    }

    /**
     * Provides JSON for create request body
     *
     * @return object
     * @throws \RuntimeException
     */
    protected function createJson()
    {
        if (!isset($this->createKeys)) {
            throw new \RuntimeException(sprintf(
                'This resource object [%s] must have a visible createKeys array',
                get_class($this)
            ));
        }

        $element = (object) array();

        foreach ($this->createKeys as $key) {
            if (null !== ($property = $this->getProperty($key))) {
                $element->{$this->getAlias($key)} = $this->recursivelyAliasPropertyValue($property);
            }
        }

        if (isset($this->metadata) && count($this->metadata)) {
            $element->metadata = (object) $this->metadata->toArray();
        }

        return (object) array($this->jsonName() => (object) $element);
    }

    /**
     * Returns the alias configured for the given key. If no alias exists
     * it returns the original key.
     *
     * @param  string $key
     * @return string
     */
    protected function getAlias($key)
    {
        if (false !== ($alias = array_search($key, $this->aliases))) {
            return $alias;
        }

        return $key;
    }

    /**
     * Returns the given property value's alias, if configured; Else, the
     * unchanged property value is returned. If the given property value
     * is an array or an instance of \stdClass, it is aliases recursively.
     *
     * @param  mixed $propertyValue Array or \stdClass instance to alias
     * @return mixed Property value, aliased recursively
     */
    protected function recursivelyAliasPropertyValue($propertyValue)
    {
        if (is_array($propertyValue)) {
            foreach ($propertyValue as $key => $subValue) {
                $aliasedSubValue = $this->recursivelyAliasPropertyValue($subValue);
                if (is_numeric($key)) {
                    $propertyValue[$key] = $aliasedSubValue;
                } else {
                    unset($propertyValue[$key]);
                    $propertyValue[$this->getAlias($key)] = $aliasedSubValue;
                }
            }
        } elseif (is_object($propertyValue) && ($propertyValue instanceof \stdClass)) {
            foreach (get_object_vars($propertyValue) as $key => $subValue) {
                unset($propertyValue->$key);
                $propertyValue->{$this->getAlias($key)} = $this->recursivelyAliasPropertyValue($subValue);
            }
        }

        return $propertyValue;
    }

    /**
     * Provides JSON for update request body
     */
    protected function updateJson($params = array())
    {
        if (!isset($this->updateKeys)) {
            throw new \RuntimeException(sprintf(
                'This resource object [%s] must have a visible updateKeys array',
                get_class($this)
            ));
        }

        $element = (object) array();

        foreach ($this->updateKeys as $key) {
            if (null !== ($property = $this->getProperty($key))) {
                $element->{$this->getAlias($key)} = $this->recursivelyAliasPropertyValue($property);
            }
        }

        return (object) array($this->jsonName() => (object) $element);
    }

    /**
     * @throws CreateError
     */
    protected function noCreate()
    {
        throw new CreateError('This resource does not support the create operation');
    }

    /**
     * @throws DeleteError
     */
    protected function noDelete()
    {
        throw new DeleteError('This resource does not support the delete operation');
    }

    /**
     * @throws UpdateError
     */
    protected function noUpdate()
    {
        throw new UpdateError('This resource does not support the update operation');
    }

    /**
     * Check whether an extension is valid
     *
     * @param mixed $alias The extension name
     * @return bool
     * @throws UnsupportedExtensionError
     */
    public function checkExtension($alias)
    {
        if (!in_array($alias, $this->getService()->namespaces())) {
            throw new UnsupportedExtensionError(sprintf("%s extension is not installed", $alias));
        }

        return true;
    }

    /**
     * Returns the object's properties as an array
     */
    protected function getUpdateablePropertiesAsArray()
    {
        $properties = get_object_vars($this);

        $propertiesToKeep = array();
        foreach ($this->updateKeys as $key) {
            if (isset($properties[$key])) {
                $propertiesToKeep[$key] = $properties[$key];
            }
        }

        return $propertiesToKeep;
    }

    /**
     * Generates a JSON Patch representation and return its
     *
     * @param mixed $updatedProperties Properties of the resource to update
     * @return String JSON Patch representation for updates
     */
    protected function generateJsonPatch($updatedProperties)
    {
        // Normalize current and updated properties into nested arrays
        $currentProperties = json_decode(json_encode($this->getUpdateablePropertiesAsArray()), true);
        $updatedProperties = json_decode(json_encode($updatedProperties), true);

        // Add any properties that haven't changed to generate the correct patch
        // (otherwise unchanging properties are marked as removed in the patch)
        foreach ($currentProperties as $key => $value) {
            if (!array_key_exists($key, $updatedProperties)) {
                $updatedProperties[$key] = $value;
            }
        }

        // Recursively alias current and updated properties
        $currentProperties = $this->recursivelyAliasPropertyValue($currentProperties);
        $updatedProperties = $this->recursivelyAliasPropertyValue($updatedProperties);

        // Generate JSON Patch representation
        $json = json_encode(JsonPatch::diff($currentProperties, $updatedProperties));
        $this->checkJsonError();

        return $json;
    }

    /********  DEPRECATED METHODS ********/

    /**
     * @deprecated
     * @return string
     * @throws NameError
     */
    public function name()
    {
        if (null !== ($name = $this->getProperty('name'))) {
            return $name;
        } else {
            throw new NameError('Name attribute does not exist for this resource');
        }
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @deprecated
     * @return string
     */
    public function status()
    {
        return (isset($this->status)) ? $this->status : 'N/A';
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function region()
    {
        return $this->getService()->region();
    }

    /**
     * @deprecated
     * @return \Guzzle\Http\Url
     */
    public function createUrl()
    {
        return $this->getParent()->getUrl($this->resourceName());
    }
}
