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

namespace OpenCloud\Identity\Resource;

use OpenCloud\Common\PersistentObject;

/**
 * Tenant class for tenant functionality.
 *
 * A tenant is a container used to group or isolate resources and/or identity objects. Depending on the service
 * operator, a tenant may map to a customer, account, organization, or project.
 *
 * @package OpenCloud\Identity\Resource
 */
class Tenant extends PersistentObject
{
    /** @var int The tenant ID */
    private $id;

    /** @var string The tenant name */
    private $name;

    /** @var string A description of the tenant */
    private $description;

    /** @var bool Whether this tenant is enabled or not (i.e. whether it can fulfil API operations) */
    private $enabled;

    protected static $url_resource = 'tenants';
    protected static $json_name = 'tenants';

    /**
     * @param $id Sets the ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string Returns the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $name Sets the name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string Returns the name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $description Sets the description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string Returns the description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $enabled Enables/disables the tenant
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return bool Checks whether this tenant is enabled or not
     */
    public function isEnabled()
    {
        return $this->enabled === true;
    }
}
