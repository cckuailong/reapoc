<?php

namespace RebelCode\Wpra\Core\Wp;

use WP_Roles;

/**
 * A proxy class for the {@link WP_Roles} class, that lazily fetches the global `$wp_roles` instance.
 *
 * This implementation is useful for declaring as a service, allowing other classes to be injected with it at load time,
 * since the original `$wp_roles` instance is `null` until the `admin_init` hook, which breaks parameter signatures
 * that are typed with `WP_Roles`.
 *
 * @since 4.13
 */
class WpRolesProxy extends WP_Roles
{
    /**
     * Constructor.
     *
     * @since 4.13
     */
    public function __construct()
    {
    }

    /**
     * Proxies a method call to the original {@link WP_Roles} instance.
     *
     * @since 4.13
     *
     * @param string $method    The name of the method.
     * @param array  $arguments The method call arguments.
     *
     * @return mixed
     */
    protected function proxy($method, $arguments)
    {
        global $wp_roles;

        if (is_object($wp_roles)) {
            return call_user_func_array([$wp_roles, $method], $arguments);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function add_role($role, $display_name, $capabilities = [])
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function remove_role($role)
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function add_cap($role, $cap, $grant = true)
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function remove_cap($role, $cap)
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function get_role($role)
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function get_names()
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function is_role($role)
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function init_roles()
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function for_site($site_id = null)
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function get_site_id()
    {
        return $this->proxy(__FUNCTION__, func_get_args());
    }
}
