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
 * A role object represents a role that a User has.
 *
 * A role is a personality that a user assumes when performing a
 * specific set of operations. A role includes a set of rights and privileges. A user assuming a role inherits the
 * rights and privileges associated with the role. A token that is issued to a user includes the list of roles the user
 * can assume. When a user calls a service, that service determines how to interpret a user's roles. A role that grants
 * access to a list of operations or resources within one service may grant access to a completely different list when
 * interpreted by a different service.
 *
 * @package OpenCloud\Identity\Resource
 */
class Role extends PersistentObject
{
    /** @var string The role ID */
    private $id;

    /** @var string The role name */
    private $name;

    /** @var string The role description */
    private $description;

    protected static $url_resource = 'OS-KSADM/roles';
    protected static $json_name = 'role';

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
}
