<?php

namespace ProfilePress\Core\Classes;

use WP_Error;
use WP_User;

class UsernameEmailRestrictLogin
{
    public function __construct()
    {
        add_filter('authenticate', [$this, 'do_action'], 999999999, 3);
    }

    /**
     * @param WP_User|WP_Error|null $user
     * @param string $username [description]
     * @param string $password [description]
     *
     * @return WP_Error|WP_User
     */
    public function do_action($user, $username, $password)
    {
        $default_error_email    = new WP_Error('pp_login_error', __('<strong>ERROR</strong>: Invalid email address or incorrect password.', 'wp-user-avatar'));
        $default_error_username = new WP_Error('pp_login_error', __('<strong>ERROR</strong>: Invalid username or incorrect password.', 'wp-user-avatar'));

        $type = ppress_get_setting('login_username_email_restrict');

        if ('username' == $type) {
            return wp_authenticate_username_password($default_error_username, $username, $password);
        }

        if ('email' == $type) {
            return wp_authenticate_email_password($default_error_email, $username, $password);
        }

        return $user;
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}