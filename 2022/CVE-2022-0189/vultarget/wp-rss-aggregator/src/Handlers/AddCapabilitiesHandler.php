<?php

namespace RebelCode\Wpra\Core\Handlers;

use stdClass;
use Traversable;
use WP_Roles;

/**
 * A handler for adding capabilities to user roles.
 *
 * @since 4.13
 */
class AddCapabilitiesHandler
{
    /**
     * The WordPress role manager instance.
     *
     * @since 4.13
     *
     * @var WP_Roles
     */
    protected $wpRoles;

    /**
     * The list of user roles to which the capabilities are added.
     *
     * @since 4.13
     *
     * @var array|stdClass|Traversable
     */
    protected $roles;

    /**
     * The list of capabilities to add.
     *
     * @since 4.13
     *
     * @var array|stdClass|Traversable
     */
    protected $capabilities;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param WP_Roles                   $wpRoles      The WordPress role manager instance.
     * @param array|stdClass|Traversable $roles        The list of user roles to which the capabilities are added.
     * @param array|stdClass|Traversable $capabilities The list of capabilities to add.
     */
    public function __construct(WP_Roles $wpRoles, $roles, $capabilities)
    {
        $this->wpRoles = $wpRoles;
        $this->roles = $roles;
        $this->capabilities = $capabilities;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        foreach ($this->roles as $role) {
            foreach ($this->capabilities as $capability) {
                $this->wpRoles->add_cap($role, $capability);
            }
        }
    }
}
