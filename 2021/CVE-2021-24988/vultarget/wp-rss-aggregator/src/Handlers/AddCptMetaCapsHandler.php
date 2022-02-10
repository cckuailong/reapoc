<?php

namespace RebelCode\Wpra\Core\Handlers;

use stdClass;
use Traversable;
use WP_Roles;

/**
 * Adds a CPT's meta-mapped capability type to a set of user roles.
 *
 * @since 4.13
 */
class AddCptMetaCapsHandler extends AddCapabilitiesHandler
{
    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param WP_Roles                   $wpRoles The WordPress role manager instance.
     * @param array|stdClass|Traversable $roles   The list of user roles to which the capabilities are added.
     * @param array|stdClass|Traversable $capType The capability type that the CPT was registered with.
     */
    public function __construct(WP_Roles $wpRoles, $roles, $capType)
    {
        $capabilities = $this->getCptMetaCaps($capType);

        parent::__construct($wpRoles, $roles, $capabilities);
    }

    /**
     * Retrieves the meta-capabilities for a CPT's capability type.
     *
     * @since 4.13
     *
     * @param string $type The capability type.
     *
     * @return string[] The capabilities.
     */
    protected function getCptMetaCaps($type)
    {
        return [
            // Post type
            "edit_{$type}",
            "read_{$type}",
            "delete_{$type}",
            "edit_{$type}s",
            "edit_others_{$type}s",
            "publish_{$type}s",
            "read_private_{$type}s",
            "delete_{$type}s",
            "delete_private_{$type}s",
            "delete_published_{$type}s",
            "delete_others_{$type}s",
            "edit_private_{$type}s",
            "edit_published_{$type}s",
            // Terms
            "manage_{$type}_terms",
            "edit_{$type}_terms",
            "delete_{$type}_terms",
            "assign_{$type}_terms",
        ];
    }
}
