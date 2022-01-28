<?php

namespace ProfilePress\Core\ShortcodeParser;

use ProfilePress\Core\Classes\EditUserProfile;
use ProfilePress\Core\Classes\LoginAuth;
use ProfilePress\Core\Classes\PasswordReset;
use ProfilePress\Core\Classes\RegistrationAuth;

class FormProcessor
{
    /**
     * When a password reset form is submitted to generate a reset key to be emailed,
     * it holds both success and error message.
     *
     * @var array
     */
    public $password_reset_form_error = [];

    public $login_form_error = [];

    public $edit_profile_form_error = [];

    public $registration_form_error = [];

    public $myac_change_password_error = '';

    public function set_global_state($key, $value, $form_id = false)
    {
        $GLOBALS['pp_form_processor_' . $key] = $value;

        if ($form_id) {
            $GLOBALS['pp_form_processor_form_id_' . $key] = $form_id;
        }
    }

    public function get_global_state_error($key)
    {
        return isset($GLOBALS['pp_form_processor_' . $key]) ? $GLOBALS['pp_form_processor_' . $key] : false;
    }

    public function restore_form_error($key)
    {
        $form_id = isset($GLOBALS['pp_form_processor_form_id_' . $key]) ? $GLOBALS['pp_form_processor_form_id_' . $key] : false;

        if ($form_id) {
            $this->$key = [];

            return $this->$key[$form_id] = $this->get_global_state_error($key);
        }

        $this->$key = $this->get_global_state_error($key);
    }

    /**
     * @return string|void
     */
    public function process_myaccount_change_password()
    {
        if ( ! isset($_POST['ppmyac_form_action']) || isset($_POST['ppmyac_form_action']) && $_POST['ppmyac_form_action'] !== 'changePassword') {
            return;
        }

        if ( ! ppress_verify_nonce()) return;

        $user = wp_get_current_user();

        $current_password     = $_POST['password_current'];
        $new_password         = $_POST['password_new'];
        $new_password_confirm = $_POST['password_confirm_new'];

        if ($new_password !== $new_password_confirm) {
            return $this->myac_change_password_error = esc_html__('Passwords do not match.', 'wp-user-avatar');
        }

        if ($user instanceof \WP_User && wp_check_password($current_password, $user->data->user_pass, $user->ID) && is_user_logged_in()) {

            $updated_user_id = wp_update_user([
                'ID'        => $user->ID,
                'user_pass' => $new_password,
            ]);

            do_action('ppress_myaccount_after_password_change');

            if (is_wp_error($updated_user_id)) {
                return $this->myac_change_password_error = $updated_user_id->get_error_message();
            }

            wp_safe_redirect(esc_url_raw(add_query_arg('edit', 'true')));
            exit;
        }

        $this->myac_change_password_error = __('The password you entered is incorrect.', 'wp-user-avatar');
    }

    public function process_edit_profile_form()
    {
        global $post;

        // check if the page being viewed contains the "edit profile" shortcode. if true, redirect to login page
        if (isset($post->post_content) && has_shortcode($post->post_content, 'profilepress-edit-profile')) {
            if ( ! is_user_logged_in()) {
                wp_safe_redirect(ppress_login_url());
                exit;
            }
        }

        if (isset($_POST['eup_remove_avatar']) && $_POST['eup_remove_avatar'] == 'removed') {
            EditUserProfile::remove_user_avatar();
        }

        if (isset($_POST['eup_remove_cover_image']) && $_POST['eup_remove_cover_image'] == 'removed') {
            EditUserProfile::remove_user_cover_image();
        }

        if (isset($_POST['eup_submit'])) {

            $state_key = 'edit_profile_form_error';

            if ($this->get_global_state_error($state_key)) {
                return $this->restore_form_error($state_key);
            }

            $form_id = absint(ppressPOST_var('pp_melange_id', @$_POST['editprofile_form_id'], true));

            $redirect = ppressPOST_var('editprofile_redirect', '', true);

            if ( ! empty($_POST['melange_redirect'])) {
                $redirect = esc_url_raw($_POST['melange_redirect']);
            }

            $is_melange = isset($_POST['is_melange']) && $_POST['is_melange'] == 'true';

            $response = EditUserProfile::process_func($form_id, $redirect, $is_melange);

            if ( ! empty($response)) {

                if ( ! $form_id) {
                    $this->set_global_state($state_key, $response);
                    $this->edit_profile_form_error = $response;
                } else {
                    $this->set_global_state($state_key, $response, $form_id);
                    $this->edit_profile_form_error[$form_id] = $response;
                }
            }
        }
    }

    public function process_registration_form()
    {
        if (isset($_POST['reg_submit'])) {

            $state_key = 'registration_form_error';

            if ($this->get_global_state_error($state_key)) {
                return $this->restore_form_error($state_key);
            }

            $form_id = absint(ppressPOST_var('pp_melange_id', @$_POST['signup_form_id'], true));

            $redirect = ppressPOST_var('signup_redirect', '', true);
            if ( ! empty($_POST['melange_redirect'])) {
                $redirect = esc_url_raw($_POST['melange_redirect']);
            }

            $no_login_redirect = ! empty($_POST['signup_no_login_redirect']) ? esc_url_raw($_POST['signup_no_login_redirect']) : '';

            $is_melange = isset($_POST['is_melange']) && $_POST['is_melange'] == 'true';

            $response = RegistrationAuth::register_new_user($_POST, $form_id, $redirect, $is_melange, $no_login_redirect);

            if ( ! empty($response)) {
                $response = html_entity_decode($response);

                $this->registration_form_error[$form_id] = $response;

                $this->set_global_state($state_key, $response, $form_id);
            }
        }
    }

    public function process_login_form()
    {
        if (isset($_GET['pp-sl-error']) && ! empty($_GET['pp-sl-error'])) {
            $error = esc_html__('Authentication failed. Please try again', 'wp-user-avatar');

            if ($_GET['pp-sl-error'] != 'true') {
                $error = sanitize_text_field(rawurldecode($_GET['pp-sl-error']));
            }

            $this->login_form_error = '<div class="profilepress-login-status">' . $error . '</div>';
        }

        if (isset($_POST['login_submit'])) {

            $state_key = 'login_form_error';

            if ($this->get_global_state_error($state_key)) {
                return $this->restore_form_error($state_key);
            }

            $username       = trim($_POST['login_username']);
            $password       = $_POST['login_password'];
            $remember_login = sanitize_text_field(@$_POST['login_remember']);

            $form_id = absint(! empty($_POST['pp_melange_id']) ? $_POST['pp_melange_id'] : @$_POST['login_form_id']);

            $redirect = ! empty($_POST['login_redirect']) ? esc_url_raw($_POST['login_redirect']) : '';
            if ( ! empty($_POST['melange_redirect'])) {
                $redirect = esc_url_raw($_POST['melange_redirect']);
            }

            $login_status = LoginAuth::login_auth($username, $password, $remember_login, $form_id, $redirect);

            $login_error = '';

            if (is_wp_error($login_status)) {
                $login_error = '<div class="profilepress-login-status">';
                $login_error .= $login_status->get_error_message();
                $login_error .= '</div>';
            }

            if ( ! empty($login_error)) {
                $this->login_form_error           = [];
                $this->login_form_error[$form_id] = $login_error;

                $this->set_global_state($state_key, $login_error, $form_id);
            }
        }
    }

    public function process_password_reset_form()
    {
        $parsed_error = PasswordReset::parse_password_reset_error_codes();

        if ( ! empty($parsed_error)) {
            $this->password_reset_form_error = $parsed_error;
        }

        if ( ! isset($_POST['password_reset_submit']) || empty($_POST['password_reset_submit'])) return;

        $state_key = 'password_reset_form_error';

        if ($this->get_global_state_error($state_key)) {
            return $this->restore_form_error($state_key);
        }

        $form_id = absint(! empty($_POST['pp_melange_id']) ? $_POST['pp_melange_id'] : @$_POST['passwordreset_form_id']);

        $is_melange = isset($_POST['is_melange']) && $_POST['is_melange'] == 'true';

        $response = PasswordReset::password_reset_status($_POST['user_login'], $form_id, $is_melange);

        if ( ! empty($response)) {
            $response = wp_specialchars_decode($response);

            $this->password_reset_form_error           = [];
            $this->password_reset_form_error[$form_id] = $response;

            $this->set_global_state($state_key, $response, $form_id);
        }
    }

    public function check_password_reset_key()
    {
        if ( ! isset($_REQUEST['key'], $_REQUEST['login'])) return;

        // Verify key / login combo
        $user = check_password_reset_key(sanitize_text_field($_REQUEST['key']), sanitize_text_field($_REQUEST['login']));

        if ($user && ! is_wp_error($user)) return;

        if ($user && $user->get_error_code() === 'expired_key') {
            wp_safe_redirect(ppress_password_reset_url() . '?error=expiredkey');
            exit;
        }

        wp_safe_redirect(ppress_password_reset_url() . '?error=invalidkey');
        exit;
    }

    public function process_password_reset_handler_form()
    {
        if (isset($_REQUEST['reset_password'], $_REQUEST['reset_key'], $_REQUEST['reset_login'])) {
            PasswordReset::get_instance()->do_password_reset();
        }
    }
}