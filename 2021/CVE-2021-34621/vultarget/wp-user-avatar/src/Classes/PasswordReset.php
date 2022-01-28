<?php

namespace ProfilePress\Core\Classes;

use WP_Error;

class PasswordReset
{
    protected static function is_ajax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    /**
     * Change the password reset title.
     *
     * @param \WP_User $user_data
     * @param string $key
     *
     * @return string
     */
    public static function reset_email_title($user_data, $key)
    {
        $default_title = sprintf(__('[%s] Password Reset'), ppress_site_title());

        return apply_filters(
            'ppress_retrieve_password_title',
            self::parse_placeholders(
                ppress_get_setting('password_reset_email_subject', $default_title, true),
                $user_data, $key
            )
        );
    }

    /**
     * Return the formatted password reset message.
     *
     * @param string $content
     * @param \WP_User $user_data username
     * @param string $key activation key
     *
     * @return string
     */
    protected static function parse_placeholders($content, $user_data, $key)
    {
        $user_email = $user_data->user_email;

        $user_login = $user_data->user_login;

        $search = apply_filters('ppress_password_reset_placeholder_search', [
            '{{username}}',
            '{{password_reset_link}}',
            '{{email}}',
            '{{site_title}}'
        ],
            $user_data, $key
        );

        $replace = apply_filters('ppress_password_reset_placeholder_replace', [
            $user_login,
            ppress_get_do_password_reset_url($user_login, $key),
            $user_email,
            ppress_site_title()
        ],
            $user_data, $key
        );

        return str_replace($search, $replace, $content);
    }

    /**
     * Callback function for filter
     *
     * @param string $user_data
     * @param $key
     *
     * @return string formatted message for use by the password reset form
     */
    protected static function reset_email_message($user_data, $key)
    {
        $message = apply_filters('ppress_retrieve_password_message',
            ppress_get_setting('password_reset_email_content', ppress_password_reset_content_default(), true)
        );

        return wp_specialchars_decode(self::parse_placeholders($message, $user_data, $key));
    }

    /**
     * Does the heavy lifting of resetting password
     *
     * @param $user_login string username or email
     *
     * @return bool|WP_Error
     */
    public static function retrieve_password_func($user_login)
    {
        $errors = new WP_Error();

        if (ppress_get_setting('password_reset_email_enabled', 'on') !== 'on') {
            $errors->add('password_reset_email_disabled', esc_html__('<strong>ERROR</strong>: Password reset email is disabled.', 'wp-user-avatar'));
        } else {

            $_POST['user_login'] = $user_login;

            if (empty($_POST['user_login'])) {
                $errors->add('empty_username', esc_html__('<strong>ERROR</strong>: Enter a username or e-mail address.', 'wp-user-avatar'));
            } else {

                if (strpos($_POST['user_login'], '@')) {
                    $user_data = get_user_by('email', trim($_POST['user_login']));
                    if ( ! $user_data) {
                        $errors->add('invalid_email', esc_html__('<strong>ERROR</strong>: There is no user registered with that email address.', 'wp-user-avatar'));
                    }
                } else {
                    $login     = trim($_POST['user_login']);
                    $user_data = get_user_by('login', $login);
                }

                if ( ! $user_data) {
                    $errors->add('invalidcombo', esc_html__('<strong>ERROR</strong>: Invalid username or e-mail.', 'wp-user-avatar'));
                }
            }
        }

        do_action('lostpassword_post', $errors, $user_data);

        if ($errors->get_error_code()) {
            return $errors;
        }

        $user_email = $user_data->user_email;

        $key = get_password_reset_key($user_data);

        if (is_wp_error($key)) return $key;

        $title = self::reset_email_title($user_data, $key);

        $message = self::reset_email_message($user_data, $key);

        return ppress_send_email($user_email, wp_specialchars_decode($title), $message);
    }

    /**
     * The error or success message received from the retrieve_password_func
     *
     * @param string $user_login username/email
     * @param int|null $form_id password_reset id
     * @param string $is_melange is this melange
     *
     * @return string|array
     */
    public static function password_reset_status($user_login, $form_id = null, $is_melange = '')
    {
        /**
         * Fires before password reset is processed
         *
         * @param string $user_login username/email
         * @param int $form_id password reset builder ID
         */
        do_action('ppress_before_password_reset', $user_login, $form_id);

        /** filter to validate additional password field */
        $password_reset_validation = apply_filters('ppress_password_reset_validation', '', $form_id);

        // if the action is contain WP_Error message, set the password response to the object
        // for reuse further down to return its WP_Error message
        if (is_wp_error($password_reset_validation) && $password_reset_validation->get_error_code() != '') {
            $password_reset_response = $password_reset_validation;
        } else {
            $password_reset_response = self::retrieve_password_func($user_login);
        }

        /**
         * Fires after password reset is processed
         *
         * @param string $user_login username/email
         * @param string $password_reset_response password reset response message
         */
        do_action('ppress_after_password_reset', $form_id, $user_login, $password_reset_response);

        // return the response of the password reset process
        if (is_wp_error($password_reset_response)) {
            return '<div class="profilepress-reset-status">' . $password_reset_response->get_error_message() . '</div>';
        }

        do_action('ppress_password_reset_success', $form_id, $user_login, $password_reset_response);

        if ($is_melange) {
            $success_message = FormRepository::get_form_meta($form_id, FormRepository::MELANGE_TYPE, FormRepository::MELANGE_PASSWORD_RESET_SUCCESS_MESSAGE);
        } else {
            $success_message = FormRepository::get_form_meta($form_id, FormRepository::PASSWORD_RESET_TYPE, FormRepository::SUCCESS_MESSAGE);
        }

        if (FormRepository::is_drag_drop($form_id, FormRepository::PASSWORD_RESET_TYPE)) {
            // Drag and drop do not allow the use of div wrapper. only the message to be shown is entered.
            // so here, we are wrapping it in edit profile status div.
            if ( ! empty($success_message)) {
                $success_message = "<div class=\"profilepress-reset-status success\">$success_message</div>";
            }
        }

        // remove the username/email address after password reset ish is successful.
        unset($_POST['user_login']);

        $success_msg = ! empty($success_message) ? $success_message : apply_filters('ppress_default_password_reset_text', '<h4>' . esc_html__('Check your email for further instruction.', 'wp-user-avatar') . '</h4>');

        if (self::is_ajax()) return [$success_msg];

        return $success_msg;
    }

    /**
     * Resets the user's password if the password reset form was submitted.
     */
    public static function do_password_reset()
    {
        $reset_key   = $_REQUEST['reset_key'];
        $reset_login = $_REQUEST['reset_login'];

        $user = check_password_reset_key($reset_key, $reset_login);

        if (is_wp_error($user)) {
            if ($user->get_error_code() === 'expired_key') {

                if (self::is_ajax()) return self::do_password_reset_status_messages('expired_key');

                wp_safe_redirect(ppress_password_reset_url() . '?login=expiredkey');
                exit;
            }

            if (self::is_ajax()) return self::do_password_reset_status_messages('expired_key');

            wp_safe_redirect(ppress_password_reset_url() . '?login=invalidkey');
            exit;
        }

        if (isset($_POST['password1']) && isset($_POST['password2'])) {

            if ($_POST['password1'] != $_POST['password2']) {

                if (self::is_ajax()) {
                    return self::do_password_reset_status_messages('password_mismatch');
                }

                // Passwords don't match
                $redirect_url = add_query_arg(
                    [
                        'key'   => $reset_key,
                        'login' => $reset_login,
                        'error' => 'password_mismatch'
                    ],
                    ppress_password_reset_url()
                );

                wp_safe_redirect($redirect_url);
                exit;
            }

            if (empty($_POST['password1'])) {

                if (self::is_ajax()) return self::do_password_reset_status_messages('password_empty');

                // Empty password
                $redirect_url = add_query_arg(
                    [
                        'key'   => $reset_key,
                        'login' => $reset_login,
                        'error' => 'password_empty'
                    ],
                    ppress_password_reset_url()
                );

                wp_safe_redirect($redirect_url);
                exit;
            }

            if (isset($_POST['pp_enforce_password_meter']) && ($_POST['pp_enforce_password_meter'] != '1')) {

                if (self::is_ajax()) return self::do_password_reset_status_messages('weak_password');

                // Empty password
                $redirect_url = add_query_arg(
                    [
                        'key'   => $reset_key,
                        'login' => $reset_login,
                        'error' => 'weak_password'
                    ],
                    ppress_password_reset_url()
                );

                wp_safe_redirect($redirect_url);
                exit;
            }

            // Everything is cool now.
            reset_password($user, $_POST['password1']);

            if (self::is_ajax()) {
                return [self::do_password_reset_status_messages('changed')];
            }

            wp_safe_redirect(ppress_password_reset_redirect());
            exit;
        }

        if (self::is_ajax()) {
            return self::do_password_reset_status_messages('invalid');
        }

        $redirect_url = add_query_arg(
            [
                'key'   => $reset_key,
                'login' => $reset_login,
                'error' => 'invalid'
            ],
            ppress_password_reset_url()
        );

        wp_safe_redirect($redirect_url);
        exit;
    }

    /**
     * Status messages for do password reset.
     *
     * @param string $type
     *
     * @return string
     */
    public static function do_password_reset_status_messages($type)
    {
        switch ($type) {
            case 'expiredkey':
            case 'invalidkey':
                $error = apply_filters('ppress_password_reset_invalidkey',
                    esc_html__('Sorry, that key appears to be invalid.', 'wp-user-avatar'));
                break;
            case 'password_mismatch':
                $error = apply_filters('ppress_password_mismatch', esc_html__('Passwords do not match.', 'wp-user-avatar'));
                break;
            case 'password_empty':
                $error = apply_filters('ppress_password_empty', esc_html__('Please enter your password.', 'wp-user-avatar'));
                break;
            case 'invalid':
                $error = apply_filters('ppress_password_reset_invalid', esc_html__('Sorry, that key does not appear to be valid.', 'wp-user-avatar'));
                break;
            case 'weak_password':
                $error = apply_filters('ppress_weak_password', esc_html__('Password is not strong enough.', 'wp-user-avatar'));
                break;
            case 'changed':
                $changed_message = apply_filters('ppress_password_changed_message', esc_html__('You have successfully changed your password'));
                // we are keeping pp_password_changed to maintain backward compat
                $error = apply_filters('ppress_password_changed',
                    sprintf('%s <a href="%s">' . esc_html__('Log in', 'wp-user-avatar') . '</a>', $changed_message, ppress_login_url())
                );
                break;
            default:
                $error = esc_html__('Unexpected error. Please try again.', 'wp-user-avatar');
        }

        return '<div class="profilepress-reset-status">' . $error . '</div>';
    }

    /**
     * Do password reset.
     *
     * @return string
     */
    public static function parse_password_reset_error_codes()
    {
        $error = '';

        if (isset($_REQUEST['login']) && ! empty($_REQUEST['login'])) {

            switch ($_REQUEST['login']) {
                case 'expiredkey':
                case 'invalidkey':
                    $error = self::do_password_reset_status_messages('invalidkey');
                    break;
            }
        }

        if (isset($_REQUEST['error']) && ! empty($_REQUEST['error'])) {

            switch ($_REQUEST['error']) {
                case 'password_mismatch':
                    $error = self::do_password_reset_status_messages('password_mismatch');
                    break;
                case 'password_empty':
                    $error = self::do_password_reset_status_messages('password_empty');
                    break;
                case 'invalid':
                    $error = self::do_password_reset_status_messages('invalid');
                    break;
                case 'invalidkey':
                    $error = self::do_password_reset_status_messages('invalidkey');
                    break;
                case 'weak_password':
                    $error = self::do_password_reset_status_messages('weak_password');
                    break;
            }
        }

        if (isset($_REQUEST['password']) && 'changed' == $_REQUEST['password']) {
            $error = self::do_password_reset_status_messages('changed');
        }

        return $error;
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