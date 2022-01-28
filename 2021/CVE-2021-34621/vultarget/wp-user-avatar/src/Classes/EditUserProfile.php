<?php

namespace ProfilePress\Core\Classes;

class EditUserProfile
{
    public static function is_ajax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    public static function get_success_message($form_id = 0, $is_melange = false)
    {
        $success_message = FormRepository::get_form_meta($form_id, FormRepository::EDIT_PROFILE_TYPE, FormRepository::SUCCESS_MESSAGE);

        if ($is_melange) {
            $success_message = FormRepository::get_form_meta($form_id, FormRepository::EDIT_PROFILE_TYPE, FormRepository::MELANGE_EDIT_PROFILE_SUCCESS_MESSAGE);
        }

        if (FormRepository::is_drag_drop($form_id, FormRepository::EDIT_PROFILE_TYPE)) {
            // Drag and drop do not allow the use of div wrapper. only the message to be shown is entered.
            // so here, we are wrapping it in edit profile status div.
            if ( ! empty($success_message)) {
                $success_message = '<div class="profilepress-edit-profile-status success">' . $success_message . '</div>';
            }
        }

        $success_message = ! empty($success_message) ? $success_message : '<div class="profilepress-edit-profile-status success">' . esc_html__('Account was updated successfully.', 'wp-user-avatar') . '</div>';

        return apply_filters('ppress_edit_profile_success_message', $success_message);
    }

    /**
     * @param $form_id
     * @param $redirect
     * @param bool $is_melange
     *
     * @return mixed|void the edit profile response be it error or success message
     */
    public static function process_func($form_id, $redirect, $is_melange = false)
    {
        $success_message = self::get_success_message($form_id, $is_melange);

        $edit_profile_response = self::update_user_profile($form_id, $redirect);

        if ( ! empty($edit_profile_response) && self::is_ajax()) {
            $ajax_response = [];

            if (is_string($edit_profile_response)) {
                $ajax_response['message'] = '<div class="profilepress-edit-profile-status">' . $edit_profile_response . '</div>';
            }

            if (is_array($edit_profile_response) && $edit_profile_response['status'] == 'success') {
                $ajax_response['message'] = html_entity_decode($success_message);

                if ( ! empty($edit_profile_response['avatar_url'])) {
                    $ajax_response['avatar_url'] = $edit_profile_response['avatar_url'];
                }

                if ( ! empty($edit_profile_response['cover_image_url'])) {
                    $ajax_response['cover_image_url'] = $edit_profile_response['cover_image_url'];
                }
            }

            if ( ! empty($redirect)) {
                $ajax_response['redirect'] = esc_url_raw($redirect);
            }

            return $ajax_response;
        }

        if ( ! empty($edit_profile_response)) {
            return '<div class="profilepress-edit-profile-status">' . $edit_profile_response . '</div>';
        }
    }

    public static function get_current_user_id()
    {
        return get_current_user_id();
    }

    /**
     * Update user profile.
     *
     * @param int $form_id ID of edit profile form
     * @param string $redirect URL to redirect to after edit profile.
     *
     * @return mixed
     */
    public static function update_user_profile($form_id, $redirect = '')
    {
        if (self::is_ajax()) {
            ppress_verify_ajax_nonce();
        } else {
            ppress_verify_nonce();
        }

        $post = $_POST;

        $old_user_data = get_userdata(self::get_current_user_id());

        /* Validate and add custom validation to edit profile */
        $validation_errors = apply_filters('ppress_edit_profile_validation', '', $form_id);

        if (is_wp_error($validation_errors)) {
            return $validation_errors->get_error_message();
        }

        // create an array of acceptable userdata for use by wp_update_user
        $valid_userdata = array(
            'eup_username',
            'eup_password',
            'eup_email',
            'eup_email2',
            'eup_website',
            'eup_nickname',
            'eup_display_name',
            'eup_first_name',
            'eup_last_name',
            'eup_bio'
        );

        if (isset($post['eup_email']) && ! is_email($post['eup_email'])) {
            return esc_html__('Email address is invalid. Please try again', 'wp-user-avatar');
        }

        if (isset($post['eup_email2']) && ! is_email($post['eup_email2'])) {
            return esc_html__('Email address confirmation is invalid. Please try again', 'wp-user-avatar');
        }

        if (isset($post['eup_email2']) && ($post['eup_email'] != $post['eup_email2'])) {
            return esc_html__('Email addresses do not match. Please try again', 'wp-user-avatar');
        }

        if (isset($post['eup_password2'])) {

            // if set to true, empty password and empty confirm password field will cause password not to be changed.
            if (apply_filters('ppress_allow_empty_password_unchanged', false)) {
                if ( ! empty($post['eup_password']) && ! empty($post['eup_password2'])) {
                    if (($post['eup_password'] != $post['eup_password2'])) {
                        return esc_html__('Password do not match. Please try again.', 'wp-user-avatar');
                    }
                }
            } else {
                if (empty($post['eup_password']) || empty($post['eup_password2'])) {
                    return esc_html__('Password is empty or do not match. Please try again.', 'wp-user-avatar');
                }

                if (($post['eup_password'] != $post['eup_password2'])) {
                    return esc_html__('Password do not match. Please try again.', 'wp-user-avatar');
                }
            }
        }

        // get the escaped data for userdata
        $escaped_post_data = self::escaped_post_data($post);

        // get the data for use by update_user_meta
        $custom_usermeta = apply_filters('ppress_edit_profile_custom_usermeta', self::custom_usermeta_data($escaped_post_data, $valid_userdata), $form_id);

        // convert the form post data to userdata for use by wp_update_users
        $real_userdata = array();

        $real_userdata['ID'] = self::get_current_user_id();

        // only process password change if it is specified.
        if ( ! empty($post['eup_password'])) {
            // never escape password.
            $real_userdata['user_pass'] = $post['eup_password'];
        }

        if (isset($post['eup_email'])) {
            $real_userdata['user_email'] = $escaped_post_data['eup_email'];
        }

        if (isset($post['eup_website'])) {
            $real_userdata['user_url'] = $escaped_post_data['eup_website'];
        }

        if (isset($post['eup_nickname'])) {
            $real_userdata['nickname'] = $escaped_post_data['eup_nickname'];
        }

        if (isset($post['eup_display_name'])) {
            $real_userdata['display_name'] = $escaped_post_data['eup_display_name'];
        }

        if (isset($post['eup_first_name'])) {
            $real_userdata['first_name'] = $escaped_post_data['eup_first_name'];
        }

        if (isset($post['eup_last_name'])) {
            $real_userdata['last_name'] = $escaped_post_data['eup_last_name'];
        }

        if (isset($post['eup_bio'])) {
            $real_userdata['description'] = $escaped_post_data['eup_bio'];
        }

        // merge real data(for use by wp_insert_user()) and custom fields data
        $user_data = apply_filters('ppress_edit_profile_user_data', array_merge($real_userdata, $custom_usermeta), $form_id);

        /**
         * Fires before profile is updated
         *
         * @param $user_data array user_data of user being updated
         * @param $form_id int builder ID
         */
        do_action('ppress_before_profile_update', $user_data, $form_id);

        $ajax_response = array();

        if (isset($_FILES['eup_avatar']['name']) && ! empty($_FILES['eup_avatar']['name'])) {
            $upload_avatar = ImageUploader::process($_FILES['eup_avatar']);

            if (is_wp_error($upload_avatar)) {
                return $upload_avatar->get_error_message();
            }

            // update custom field
            $custom_usermeta['pp_profile_avatar'] = $upload_avatar;

            /** WP User Avatar Adapter STARTS */
            self::delete_deprecated_wp_user_avatar_image();
            /** WP User Avatar Adapter ENDS */

            if (self::is_ajax()) {
                $ajax_response['avatar_url'] = PPRESS_AVATAR_UPLOAD_URL . $upload_avatar;
            }
        }

        if (isset($_FILES['eup_cover_image']['name']) && ! empty($_FILES['eup_cover_image']['name'])) {

            $upload_cover_image = ImageUploader::process($_FILES['eup_cover_image'], ImageUploader::COVER_IMAGE, PPRESS_COVER_IMAGE_UPLOAD_DIR);

            if (is_wp_error($upload_cover_image)) {
                return $upload_cover_image->get_error_message();
            }

            $custom_usermeta['pp_profile_cover_image'] = $upload_cover_image;

            if (self::is_ajax()) {
                $ajax_response['cover_image_url'] = PPRESS_COVER_IMAGE_UPLOAD_URL . $upload_cover_image;
            }
        }

        // update file uploads
        $uploads       = FileUploader::init();
        $upload_errors = '';
        foreach ($uploads as $field_key => $uploaded_filename_or_wp_error) {
            if (is_wp_error($uploads[$field_key])) {
                $upload_errors .= $uploads[$field_key]->get_error_message() . '<br/>';
            }
        }

        if ( ! empty($upload_errors)) return $upload_errors;

        // we get the old array of stored file for the user
        $old = get_user_meta(self::get_current_user_id(), 'pp_uploaded_files', true);
        $old = ! empty($old) ? $old : array();

        // we loop through the array of newly uploaded files and remove any file (un-setting the file array key)
        // that isn't be updated i.e if the field is left empty, un-setting it prevent update_user_meta
        // fom overriding it.
        // we then merge the old and new uploads before saving the data to user meta table.
        foreach ($uploads as $key => $value) {
            if (is_null($value) || empty($value)) {
                unset($uploads[$key]);
            }
        }

        update_user_meta(self::get_current_user_id(), 'pp_uploaded_files', array_merge($old, $uploads));

        if (is_array($custom_usermeta)) {

            $user_id = self::get_current_user_id();

            foreach ($custom_usermeta as $key => $value) {

                update_user_meta($user_id, $key, $value);

                // the 'edit_profile' parameter is used to distinguish it from same action hook in RegistrationAuth
                do_action('ppress_after_custom_field_update', $key, $value, $user_id, 'edit_profile');
            }
        }

        // proceed to profile edit using wp_update_user method which return the new user id
        $update_user = wp_update_user($real_userdata);

        if (is_wp_error($update_user)) {
            return $update_user->get_error_message();
        }

        if ( ! is_wp_error($update_user)) {

            /**
             * Fires after profile is updated
             *
             * @param array $user_data
             * @param int $form_id
             * @param \WP_User $old_user_data
             */
            do_action('ppress_after_profile_update', $user_data, $form_id, $old_user_data);

            // success flag is used by ajax mode. see self::process_func()
            if (self::is_ajax()) {
                $ajax_response['status'] = 'success';

                return $ajax_response;
            }

            $url = apply_filters('ppress_redirect_after_profile_edit', esc_url_raw(add_query_arg('edit', 'true')));

            if ( ! empty($redirect)) {
                $url = esc_url_raw($redirect);
            }

            wp_safe_redirect($url);
            exit;
        }

        return esc_html__('Something unexpected happened. Please try again', 'wp-user-avatar');
    }

    /**
     * Escaped the POST data
     *
     * @param $post_data array raw post data
     *
     * @return array
     */
    public static function escaped_post_data($post_data)
    {
        $escaped_post_data = array();

        foreach ($post_data as $key => $value) {
            if ($key == 'eup_submit') {
                continue;
            }

            if ('eup_bio' == $key) {
                $escaped_post_data[$key] = wp_kses_post($value);
            } elseif (is_array($value)) {
                $escaped_post_data[$key] = array_map('sanitize_text_field', $value);
            } else {
                $escaped_post_data[$key] = sanitize_text_field($value);
            }
        }

        return $escaped_post_data;
    }

    /**
     * @param $post_data array escaped $_POST Data @see self::escaped_post_data
     *
     * @param $valid_userdata array userdata valid for wp_update_user
     *
     * @return array
     */
    public static function custom_usermeta_data($post_data, $valid_userdata)
    {
        $custom_usermeta = array();

        foreach ($post_data as $key => $value) {
            if ($key == 'eup_submit' || in_array($key, ppress_reserved_field_keys())) continue;

            if ( ! in_array($key, $valid_userdata)) {
                $custom_usermeta[$key] = $value;
            }
        }

        return $custom_usermeta;
    }

    /**
     * Remove user avatar and redirect. Triggered when JS is disabled.
     */
    public static function remove_user_avatar()
    {
        self::remove_avatar_core();

        wp_safe_redirect(esc_url(add_query_arg('edit', 'true')));
        exit;
    }

    /**
     * Remove user cover image and redirect. Triggered when JS is disabled.
     */
    public static function remove_user_cover_image()
    {
        self::remove_cover_image();
        wp_safe_redirect(esc_url(add_query_arg('edit', 'true')));
        exit;
    }

    /**
     * Core function that removes/delete the user's avatar
     */
    public static function remove_avatar_core()
    {
        $avatar_slug = get_user_meta(self::get_current_user_id(), 'pp_profile_avatar', true);

        do_action('ppress_before_avatar_removal', $avatar_slug);

        unlink(PPRESS_AVATAR_UPLOAD_DIR . $avatar_slug);

        $user_id = self::get_current_user_id();

        // delete the record from DB
        delete_user_meta($user_id, 'pp_profile_avatar');

        /** WP User Avatar Adapter STARTS */
        self::delete_deprecated_wp_user_avatar_image();
        /** WP User Avatar Adapter ENDS */

        do_action('ppress_after_avatar_removal');
    }

    private static function delete_deprecated_wp_user_avatar_image()
    {
        /** WP User Avatar Adapter STARTS */
        global $wpdb, $blog_id, $post, $wp_user_avatar;

        $user_id = self::get_current_user_id();

        if ( ! $wp_user_avatar->wpua_is_author_or_above()) {
            // Delete other uploads by user
            $q                = array(
                'author'         => $user_id,
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => '-1',
                'meta_query'     => array(
                    array(
                        'key'     => '_wp_attachment_wp_user_avatar',
                        'value'   => "",
                        'compare' => '!='
                    )
                )
            );
            $avatars_wp_query = new \WP_Query($q);
            while ($avatars_wp_query->have_posts()) : $avatars_wp_query->the_post();
                wp_delete_attachment($post->ID);
            endwhile;
        }

        delete_metadata('post', null, '_wp_attachment_wp_user_avatar', $user_id, true);
        delete_user_meta($user_id, $wpdb->get_blog_prefix($blog_id) . 'user_avatar');
        /** WP User Avatar Adapter ENDS */
    }

    /**
     * Core function that removes/delete the user's cover image
     */
    public static function remove_cover_image()
    {
        $slug = get_user_meta(self::get_current_user_id(), 'pp_profile_cover_image', true);

        do_action('ppress_before_cover_image_removal', $slug);

        unlink(PPRESS_COVER_IMAGE_UPLOAD_DIR . $slug);

        // delete the record from DB
        delete_user_meta(self::get_current_user_id(), 'pp_profile_cover_image');

        do_action('ppress_after_cover_image_removal');
    }
}