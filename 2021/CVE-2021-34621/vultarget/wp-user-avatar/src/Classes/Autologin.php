<?php

namespace ProfilePress\Core\Classes;

class Autologin
{
    public static function is_ajax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    /**
     * Initialize class
     *
     * @param int $user_id
     * @param string $login_id
     * @param string $redirect
     *
     * @return  mixed
     */
    public static function initialize($user_id, $login_id = '', $redirect = '')
    {
        if ( ! ppress_user_id_exist($user_id)) return;

        if (apply_filters('ppress_auto_login_before_signup_redirection', true)) {
            do_action('ppress_before_auto_login', $login_id, $user_id);

            $set_login_cookie = apply_filters('ppress_auto_login_set_cookie', true);

            if ($set_login_cookie) {

                $secure_cookie = '';
                // If the user wants ssl but the session is not ssl, force a secure cookie.
                if ( ! force_ssl_admin()) {
                    if (get_user_option('use_ssl', $user_id)) {
                        $secure_cookie = true;
                        force_ssl_admin(true);
                    }
                }

                if (defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN === true) {
                    $secure_cookie = true;
                }

                wp_set_auth_cookie($user_id, true, $secure_cookie);
                wp_set_current_user($user_id);
            }

            do_action('ppress_before_auto_login_redirect', $login_id, $user_id);
        }

        $login_redirect = ! empty($redirect) ? $redirect : ppress_login_redirect();

        /** Setup a custom location for "auto login after registration" */
        $login_redirection = apply_filters('ppress_auto_login_redirection', $login_redirect, $login_id, $user_id);

        if (self::is_ajax()) {
            // we are returning array to uniquely identify redirect.
            return [$login_redirection];
        }

        wp_safe_redirect($login_redirection);
        exit;
    }

}