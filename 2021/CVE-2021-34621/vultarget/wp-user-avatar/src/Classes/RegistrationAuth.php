<?php

namespace ProfilePress\Core\Classes;

use ProfilePress\Libsodium\UserModeration\UserModeration;
use ProfilePress\Libsodium\UserModeration\UserModerationNotification;
use WP_Error;

class RegistrationAuth
{
    protected static $registration_form_status;

    public static function is_ajax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    /**
     * Wrapper function for call to the welcome email class
     *
     * @param int $user_id
     * @param string $password
     * @param string $form_id
     */
    public static function send_welcome_email($user_id, $password = '', $form_id = '')
    {
        $status = apply_filters('ppress_activate_send_welcome_email', ppress_get_setting('welcome_message_email_enabled', 'on'));

        if ($status == 'on') {

            do_action('ppress_before_send_welcome_mail', $user_id, $form_id);

            new WelcomeEmailAfterSignup($user_id, $password);

            do_action('ppress_after_send_welcome_mail', $user_id, $form_id);
        }
    }

    /**
     *
     * Wrapper function for call to the automatic login after reg function
     *
     * @param int $user_id
     * @param int $form_id
     * @param string $redirect redirect url after registration
     *
     * @return mixed
     */
    public static function auto_login_after_reg($user_id, $form_id, $redirect)
    {
        if ( ! empty($redirect)) {
            return Autologin::initialize($user_id, $form_id, $redirect);
        }

        $auto_login_option = apply_filters('ppress_activate_auto_login_after_signup', ppress_get_setting('set_auto_login_after_reg', ''), $form_id);

        if ($auto_login_option == 'on') {
            return Autologin::initialize($user_id, $form_id);
        }
    }

    /**
     * Perform redirect after registration without logging the user in.
     *
     * @param int $form_id
     * @param string $no_login_redirect URL to redirect to.
     *
     * @return array
     */
    public static function no_login_redirect_after_reg($form_id, $no_login_redirect)
    {
        esc_url_raw($no_login_redirect);

        do_action('ppress_before_no_login_redirect_after_reg', $no_login_redirect, $form_id);
        if (self::is_ajax()) {
            // we are returning array to uniquely identify redirect.
            return [$no_login_redirect];
        }

        wp_safe_redirect($no_login_redirect);
        exit;
    }

    /**
     * Register new users
     *
     * @param array $post user form submitted data
     * @param int $form_id Registration builder ID
     * @param string $redirect URL to redirect to after registration.
     *
     * @param bool $is_melange
     * @param string $no_login_redirect
     *
     * @return string
     */
    public static function register_new_user($post, $form_id = 0, $redirect = '', $is_melange = false, $no_login_redirect = '')
    {
        $files = $_FILES;

        // create an array of acceptable userdata for use by wp_insert_user
        $valid_userdata = array(
            'reg_username',
            'reg_password',
            'reg_password2',
            'reg_email2',
            'reg_password_present',
            'reg_email',
            'reg_website',
            'reg_nickname',
            'reg_display_name',
            'reg_first_name',
            'reg_last_name',
            'reg_bio',
            'reg_select_role',
        );

        // get the data for userdata
        $segregated_userdata = array();

        // loop over the $_POST data and create an array of the wp_insert_user userdata
        foreach ($post as $key => $value) {
            if ($key == 'reg_submit') {
                continue;
            }

            if (in_array($key, $valid_userdata)) {
                $segregated_userdata[$key] = sanitize_text_field($value);
            }
        }

        $email = isset($segregated_userdata['reg_email']) ? $segregated_userdata['reg_email'] : '';

        $email2 = isset($segregated_userdata['reg_email2']) ? $segregated_userdata['reg_email2'] : null;

        // get convert the form post data to userdata for use by wp_insert_users
        $username = isset($segregated_userdata['reg_username']) ? $segregated_userdata['reg_username'] : '';

        // Handle username creation when username requirement is disabled.
        if (ppress_is_signup_form_username_disabled($form_id, $is_melange)) {
            $username = sanitize_user(current(explode('@', $email)), true);
            // Ensure username is unique.
            $append     = 1;
            $o_username = $username;
            while (username_exists($username)) {
                $username = $o_username . $append;
                $append++;
            }
        }

        $username = apply_filters('ppress_registration_username_value', $username);

        $password = apply_filters('ppress_registration_password_value', isset($segregated_userdata['reg_password']) ? $segregated_userdata['reg_password'] : '');

        $flag_to_send_password_reset = false;

        // if the reg_password field isn't present in registration, generate a password for the user and set a flag to send a password reset message
        if (empty($password) && (empty($segregated_userdata['reg_password_present']) || $segregated_userdata['reg_password_present'] != 'true')) {
            $password                    = wp_generate_password(24);
            $flag_to_send_password_reset = apply_filters('ppress_enable_auto_send_password_reset_flag', true);
        }

        $password2    = isset($segregated_userdata['reg_password2']) ? $segregated_userdata['reg_password2'] : null;
        $website      = isset($segregated_userdata['reg_website']) ? $segregated_userdata['reg_website'] : '';
        $nickname     = isset($segregated_userdata['reg_nickname']) ? $segregated_userdata['reg_nickname'] : '';
        $display_name = isset($segregated_userdata['reg_display_name']) ? $segregated_userdata['reg_display_name'] : '';
        $first_name   = isset($segregated_userdata['reg_first_name']) ? $segregated_userdata['reg_first_name'] : '';
        $last_name    = isset($segregated_userdata['reg_last_name']) ? $segregated_userdata['reg_last_name'] : '';
        $bio          = isset($segregated_userdata['reg_bio']) ? $segregated_userdata['reg_bio'] : '';
        $role         = isset($segregated_userdata['reg_select_role']) ? $segregated_userdata['reg_select_role'] : '';

        // real uer data
        $real_userdata = array(
            'user_login'   => $username,
            'user_pass'    => $password,
            'user_email'   => apply_filters('ppress_registration_email_value', $email),
            'user_url'     => apply_filters('ppress_registration_website_value', $website),
            'nickname'     => apply_filters('ppress_registration_nickname_value', $nickname),
            'display_name' => apply_filters('ppress_registration_display_name_value', $display_name),
            'first_name'   => apply_filters('ppress_registration_first_name_value', $first_name),
            'last_name'    => apply_filters('ppress_registration_last_name_value', $last_name),
            'description'  => apply_filters('ppress_registration_bio_value', $bio),
        );

        if ( ! empty($role)) {
            // acceptable defined roles in reg-select-role shortcode.
            $accepted_role = (array)self::acceptable_defined_roles($form_id);

            if ($role != 'administrator' && in_array($role, $accepted_role)) {
                $real_userdata['role'] = $role;
            }
        } else {
            if ( ! empty($builder_role)) {
                $builder_role = FormRepository::get_form_meta($form_id, FormRepository::REGISTRATION_TYPE, FormRepository::REGISTRATION_USER_ROLE);
                // only set user role if the registration form has one set
                // otherwise no role is set for the user thus wp_insert_user will use the default user role set in Settings > General
                $real_userdata['role'] = $builder_role;
            }
        }

        /* start filter Hook */
        $reg_errors = new WP_Error();

        // --------START ---------   validation for required fields ----------------------//
        // loop through required fields and throw error if any is empty
        if ( ! empty($_POST['required-fields']) && is_array($_POST['required-fields'])) {
            foreach ($_POST['required-fields'] as $key => $value) {

                if (empty($_POST[$key]) && empty($_FILES[$key])) {
                    $reg_errors->add('required_field_empty', sprintf(__('%s field is required', 'wp-user-avatar'), $value));
                    // stop looping if a required field is found empty.
                    break;
                }
            }
        }
        // --------END ---------   validation for required fields ----------------------//

        if ( ! validate_username($username)) {
            $reg_errors->add('invalid_username', esc_html__('<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'wp-user-avatar'));
        }

        if ( ! is_email($real_userdata['user_email'])) {
            $reg_errors->add('invalid_email', esc_html__('Email address is not valid', 'wp-user-avatar'));
        }

        if (isset($password2) && ($password != $password2)) {
            $reg_errors->add('password_mismatch', esc_html__('Passwords do not match', 'wp-user-avatar'));
        }

        if (isset($email2) && ($email != $email2)) {
            $reg_errors->add('email_mismatch', esc_html__('Email addresses do not match', 'wp-user-avatar'));
        }

        if (isset($post['pp_enforce_password_meter']) && ($post['pp_enforce_password_meter'] != '1')) {
            $reg_errors->add('password_weak', esc_html__('Password is not strong', 'wp-user-avatar'));
        }

        // get the data for use by update_meta
        $custom_usermeta = array();
        // loop over the $_POST data and create an array of the invalid userdata/ custom usermeta
        foreach ($post as $key => $value) {
            if ($key == 'reg_submit' || in_array($key, ppress_reserved_field_keys())) continue;

            if ( ! in_array($key, $valid_userdata)) {
                $custom_usermeta[$key] = is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
            }
        }

        // merge real data(for use by wp_insert_user()) and custom fields data
        // $real_userdata comes second so custom user meta won't override it.
        $user_data = array_merge($custom_usermeta, $real_userdata);

        /* Begin Filter Hook */
        // call validate reg from function
        $reg_form_errors = apply_filters('ppress_registration_validation', $reg_errors, $form_id, $user_data, $is_melange);
        if (is_wp_error($reg_form_errors) && $reg_form_errors->get_error_code() != '') {
            return '<div class="profilepress-reg-status">' . $reg_form_errors->get_error_message() . '</div>';
        }
        /* End Filter Hook */

        // --------START ---------   validation for file upload ----------------------//
        $uploads       = FileUploader::init();
        $upload_errors = '';
        if ( ! empty($uploads)) {
            foreach ($uploads as $field_key => $uploaded_filename_or_wp_error) {
                if (is_wp_error($uploads[$field_key])) {
                    $upload_errors .= $uploads[$field_key]->get_error_message() . '<br/>';
                }
            }

            if ( ! empty($upload_errors)) {
                return "<div class='profilepress-reg-status'>$upload_errors</div>";
            }
        }
        // --------END ---------   validation for file upload ----------------------//


        // --------START ---------   validation for avatar upload ----------------------//
        if (isset($files['reg_avatar']['name']) && ! empty($files['reg_avatar']['name'])) {
            $upload_avatar = ImageUploader::process($files['reg_avatar']);

            if (is_wp_error($upload_avatar)) {
                return "<div class='profilepress-reg-status'>" . $upload_avatar->get_error_message() . "</div>";
            }
        }
        // --------END ---------   validation for avatar upload ----------------------//


        // --------START ---------   validation for cover image upload ----------------------//
        if (isset($files['reg_cover_image']['name']) && ! empty($files['reg_cover_image']['name'])) {

            $upload_cover_image = ImageUploader::process($files['reg_cover_image'], ImageUploader::COVER_IMAGE, PPRESS_COVER_IMAGE_UPLOAD_DIR);

            if (is_wp_error($upload_cover_image)) {
                return "<div class='profilepress-reg-status'>" . $upload_cover_image->get_error_message() . "</div>";
            }
        }
        // --------END ---------   validation for cover image upload ----------------------//

        do_action('ppress_before_registration', $form_id, $user_data);

        // proceed to registration using wp_insert_user method which return the new user id
        $user_id = wp_insert_user($real_userdata);

        if (is_wp_error($user_id)) {
            return '<div class="profilepress-reg-status">' . $user_id->get_error_message() . '</div>';
        }

        // --------START ---------   register custom field ----------------------//

        $custom_usermeta['pp_profile_avatar']      = isset($upload_avatar) ? $upload_avatar : null;
        $custom_usermeta['pp_profile_cover_image'] = isset($upload_cover_image) ? $upload_cover_image : null;

        // if we get to this point, it means the files pass validation defined above.
        // array of files uploaded. Array key is the "custom field key" and the filename as the array value.
        $custom_usermeta['pp_uploaded_files'] = $uploads;

        // if @$user_id is no WP_Error, add the extra user profile field
        if (is_array($custom_usermeta)) {

            foreach ($custom_usermeta as $key => $value) {
                if ( ! empty($value)) {
                    update_user_meta($user_id, $key, $value);
                    // the 'edit_profile' parameter is used to distinguish it from same action hook in RegistrationAuth
                    do_action('ppress_after_custom_field_update', $key, $value, $user_id, 'registration');
                }
            }
        }
        // --------END ---------   register custom field ----------------------//

        // if moderation is active, set new registered users as pending
        if (class_exists('ProfilePress\Libsodium\UserModeration\UserModeration') && UserModeration::moderation_is_active()) {
            UserModeration::make_pending($user_id);
        }

        if ($flag_to_send_password_reset === true) {
            PasswordReset::retrieve_password_func($username);
        }

        // record signup via
        if ($is_melange) {
            add_user_meta($user_id, '_pp_signup_melange_via', $form_id);
        } else {
            add_user_meta($user_id, '_pp_signup_via', $form_id);
        }

        // if user moderation is active, send pending notification.
        if (class_exists('ProfilePress\Libsodium\UserModeration\UserModeration') && UserModeration::moderation_is_active()) {
            UserModerationNotification::pending($user_id);
            UserModerationNotification::pending_admin_notification($user_id);
        }

        self::send_welcome_email($user_id, $password, $form_id);

        if (is_int($user_id)) {

            ppress_wp_new_user_notification($user_id, null, 'admin');
        }

        /**
         * Fires after a user registration is completed.
         *
         * @param int $form_id ID of the registration form.
         * @param mixed $user_data array of registered user info.
         * @param int $user_id ID of the registered user.
         * @param bool $is_melange
         */
        do_action('ppress_after_registration', $form_id, $user_data, $user_id, $is_melange);
        /* End Action Hook */

        if ( ! empty($no_login_redirect)) {
            $response = self::no_login_redirect_after_reg($form_id, $no_login_redirect);
        } else {
            /**
             * call auto-login
             *
             * @param int $user_id registered user ID
             * @param int $form_id registration form ID
             * @param string $redirect redirect url after login
             */
            $response = self::auto_login_after_reg($user_id, $form_id, $redirect);
        }

        if (self::is_ajax() && isset($response) && ! empty($response) && is_array($response)) {
            // $response should be an array containing the url to redirect to.
            return $response;
        }

        $success_message = FormRepository::get_form_meta($form_id, FormRepository::REGISTRATION_TYPE, FormRepository::SUCCESS_MESSAGE);
        if ($is_melange) {
            $success_message = FormRepository::get_form_meta($form_id, FormRepository::MELANGE_TYPE, FormRepository::MELANGE_REGISTRATION_SUCCESS_MESSAGE);
        }

        $default_success_message = '<div class="profilepress-reg-status success">' . esc_html__('Registration successful.', 'wp-user-avatar') . '</div>';

        if (FormRepository::is_drag_drop($form_id, FormRepository::REGISTRATION_TYPE)) {
            // Drag and drop signup pages do not allow the use of div wrapper. only the message to be shown is entered.
            // so here, we are wrapping it in reg status div.
            if ( ! empty($success_message)) {
                $success_message = '<div class="profilepress-reg-status success">' . $success_message . '</div>';
            }
        }

        return apply_filters('ppress_registration_success_message', ! empty($success_message) ? $success_message : $default_success_message);
    }

    /**
     * Array list of acceptable defined roles.
     *
     * @param int $form_id ID of registration form
     *
     * @return array
     */
    public static function acceptable_defined_roles($form_id)
    {
        $registration_structure = FormRepository::get_form_meta($form_id, FormRepository::REGISTRATION_TYPE, FormRepository::FORM_STRUCTURE);

        // find the first occurrence of reg-select-role shortcode.
        preg_match('/\[reg-select-role.*\]/', $registration_structure, $matches);

        if (empty($matches) || ! isset($matches[0])) return;

        preg_match('/options="([,\s\w]+)"/', $matches[0], $matches2);

        $options = $matches2[1];

        //if no options attribute was found in the shortcode, default to all list of editable roles
        if (empty($options)) {
            $acceptable_user_role = array_keys(ppress_get_editable_roles());
        } else {
            $acceptable_user_role = array_map('trim', explode(',', $options));
        }

        return apply_filters('ppress_acceptable_user_role', $acceptable_user_role, $form_id);
    }
}