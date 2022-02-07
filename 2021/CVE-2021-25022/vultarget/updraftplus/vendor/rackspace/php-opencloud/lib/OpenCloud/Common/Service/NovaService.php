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

namespace OpenCloud\Common\Service;

use OpenCloud\Compute\Resource\Flavor;

/**
 * NovaService serves as an additional abstraction for particular OpenStack services that exhibit shared functionality.
 */
abstract class NovaService extends CatalogService
{
    /**
     * Returns a flavor from the service
     *
     * @param string|null $id
     * @return Flavor
     */
    public function flavor($id = null)
    {
        return new Flavor($this, $id);
    }

    /**
     * Returns a list of Flavor objects
     *
     * @param boolean $details Returns full details or not.
     * @param array   $filter  Array for creating queries
     * @return Collection
     */
    public function flavorList($details = true, array $filter = array())
    {
        $path = Flavor::resourceName();

        if ($details === true) {
            $path .= '/detail';
        }

        return $this->collection('OpenCloud\Compute\Resource\Flavor', $this->getUrl($path, $filter));
    }

    /**
     * Loads the available namespaces from the /extensions resource
     */
    protected function loadNamespaces()
    {
        foreach ($this->getExtensions() as $object) {
            $this->namespaces[] = $object->alias;
        }

        if (!empty($this->additionalNamespaces)) {
            $this->namespaces += $this->additionalNamespaces;
        }
    }
}
