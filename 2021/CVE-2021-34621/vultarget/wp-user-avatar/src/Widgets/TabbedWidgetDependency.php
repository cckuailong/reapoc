<?php

namespace ProfilePress\Core\Widgets;

use ProfilePress\Core\Classes\LoginAuth;
use ProfilePress\Core\Classes\PasswordReset;
use ProfilePress\Core\Classes\RegistrationAuth;

class TabbedWidgetDependency
{
    /**
     * Wrapper function for login authentication
     *
     * @param $username         string      login username
     * @param $password         string      login password
     * @param $remember_login   bool        remember login
     *
     * @return string
     */
    static function login($username, $password, $remember_login)
    {
        $login_status = LoginAuth::login_auth($username, $password, $remember_login);

        if (isset($login_status)) return $login_status->get_error_message();

        return esc_html__('Unable to log in. Please try again', 'wp-user-avatar');

    }

    /**
     * Process password reset
     *
     * @param $user_login
     *
     * @return bool|string
     */
    static function retrieve_password_process($user_login)
    {
        if (isset($_POST['tabbed_reset_passkey'])) {
            /** @var \WP_Error|string $password_reset */
            $password_reset = PasswordReset::retrieve_password_func($user_login);

            if ( ! is_wp_error($password_reset)) {
                return apply_filters('ppress_password_reset_success_text', esc_html__('Check your email for further instructions.', 'wp-user-avatar'));
            }

            if (is_wp_error($password_reset)) {
                return $password_reset->get_error_message();
            }

            return esc_html__('Unexpected error, please try again', 'wp-user-avatar');
        }
    }

    /**
     * Register the user - tabbed widget
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $auto_login_after_reg
     *
     * @return \WP_Error|string
     */
    public static function registration($username, $password, $email)
    {
        add_filter('ppress_after_registration',function ($form_id, $user_data, $user_id) {
            // update is being used because RegistrationAuth::register_new_user will set it to 0.
            update_user_meta($user_id, '_pp_signup_via', 'tab_widget');
        }, 10, 3);

        $reg_errors = self::validate_tab_registration($username, $password, $email);

        //if there is an error, return the error message
        if (is_wp_error($reg_errors) && ($reg_errors->get_error_code())) {
            return $reg_errors->get_error_message();
        }

        $data = array(
            'reg_username' => sanitize_user($username),
            'reg_email'    => sanitize_email($email),
            'reg_password' => $password
        );

        $notice = RegistrationAuth::register_new_user($data);

        // remove and return the div wrapped success or error message.
        preg_match('/<div class=".+">(.+)<\/div>/', $notice, $matches);

        return isset($matches[1]) ? $matches[1] : $notice;

    }

    /**
     * @param $username
     * @param $password
     * @param $email
     *
     * @return mixed
     */
    public static function validate_tab_registration($username, $password, $email)
    {
        $reg_errors = new \WP_Error;

        if (empty($username) || empty($password) || empty($email)) {
            $reg_errors->add('field', esc_html__('Required form field is missing', 'wp-user-avatar'));
        }

        if ( ! is_email($email)) {
            $reg_errors->add('email_invalid', 'Email is not valid');
        }

        return apply_filters('validate_profilepress_tab_widget', $reg_errors, $username, $password, $email);
    }
}
