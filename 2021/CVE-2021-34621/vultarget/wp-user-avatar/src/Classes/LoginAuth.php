<?php

namespace ProfilePress\Core\Classes;

use WP_Error;

/**
 * Authorise login and redirect to the appropriate page
 *
 * currently used only by the tabbed widget.
 */
class LoginAuth
{
    /**
     * Called to validate login credentials
     * @return string
     */
    public static function is_ajax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    /**
     * Authenticate login
     *
     * @param string $username
     * @param string $password
     * @param bool $remember_login
     * @param null|int $login_form_id
     * @param string $redirect
     *
     * @return null|string
     */
    public static function login_auth($username, $password, $remember_login = 'true', $login_form_id = 0, $redirect = '')
    {
        do_action('ppress_before_login_validation', $username, $password, $login_form_id);

        /* start filter Hook */
        $login_errors = new WP_Error();

        // call validate reg from function
        $login_form_errors = apply_filters('ppress_login_validation', $login_errors, $login_form_id);

        if (is_wp_error($login_form_errors) && $login_form_errors->get_error_code() != '') {
            return $login_form_errors;
        }

        /* End Filter Hook */

        $creds                  = array();
        $creds['user_login']    = $username;
        $creds['user_password'] = $password;

        if ($remember_login == 'true') {
            $creds['remember'] = true;
        }

        $secure_cookie = '';
        // If the user wants ssl but the session is not ssl, force a secure cookie.
        if ( ! force_ssl_admin()) {
            if ($user = get_user_by('login', $username)) {
                if (get_user_option('use_ssl', $user->ID)) {
                    $secure_cookie = true;
                    force_ssl_admin(true);
                }
            }
        }

        if (defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN === true) {
            $secure_cookie = true;
        }

        $user = wp_signon($creds, $secure_cookie);

        if (is_wp_error($user) && ($user->get_error_code())) {
            return $user;
        }

        do_action('ppress_before_login_redirect', $username, $password, $login_form_id);

        // culled from wp-login.php file.
        if ( ! empty($redirect)) {
            // Redirect to https if user wants ssl
            if ($secure_cookie && false !== strpos($redirect, 'wp-admin')) {
                $redirect = preg_replace('|^http://|', 'https://', $redirect);
            }
        } else {
            $redirect = ppress_login_redirect();
        }

        $login_redirect = esc_url_raw($redirect);

        /** Setup a custom location of the builder */
        /**@since > 1.8.2  $user was added */
        $login_redirection = apply_filters('ppress_login_redirect', $login_redirect, $login_form_id, $user);

        // if ajax, return the url to redirect to
        if (self::is_ajax()) return $login_redirection;

        wp_safe_redirect($login_redirection);
        exit;
    }
}
