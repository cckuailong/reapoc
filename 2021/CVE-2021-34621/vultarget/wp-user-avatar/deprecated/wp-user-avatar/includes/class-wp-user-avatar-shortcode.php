<?php

class WP_User_Avatar_Shortcode
{
    public function __construct()
    {
        global $wp_user_avatar;
        add_shortcode('avatar', array($this, 'wpua_shortcode'));
        add_shortcode('avatar_upload', array($this, 'wpua_edit_shortcode'));
        // Add avatar and scripts to avatar_upload
        add_action('wpua_show_profile', array($wp_user_avatar, 'wpua_action_show_user_profile'));
        add_action('wpua_show_profile', array($wp_user_avatar, 'wpua_media_upload_scripts'));
        add_action('wpua_update', array($wp_user_avatar, 'wpua_action_process_option_update'));
        // Add error messages to avatar_upload
        add_action('wpua_update_errors', array($wp_user_avatar, 'wpua_upload_errors'), 10, 3);
    }

    /**
     * Display shortcode
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function wpua_shortcode($atts, $content = null)
    {
        global $all_sizes, $blog_id, $wpdb;
        // Set shortcode attributes
        extract(shortcode_atts(array('user' => "", 'size' => '96', 'align' => "", 'link' => "", 'target' => ""), $atts));

        // Find user by ID, login, slug, or e-mail address
        if ( ! empty($user)) {
            if ($user == 'current') {
                $user = wp_get_current_user();
            } else {
                $user = is_numeric($user) ? get_user_by('id', $user) : get_user_by('login', $user);
                $user = empty($user) ? get_user_by('slug', $user) : $user;
                $user = empty($user) ? get_user_by('email', $user) : $user;
            }
        } else {
            // Find author's name if id_or_email is empty
            if (is_author()) {
                // On author page, get user by page slug
                $user = get_user_by('slug', get_query_var('author_name'));
            } else {
                // On post, get user by author meta
                $user_id = get_the_author_meta('ID');
                $user    = get_user_by('id', $user_id);
            }
        }
        $size = esc_attr($size);
        // Numeric sizes leave as-is
        $get_size = $size;
        // Check for custom image sizes if there are captions
        if ( ! empty($content)) {
            if (in_array($size, $all_sizes)) {
                if (in_array($size, array('original', 'large', 'medium', 'thumbnail'))) {
                    $get_size = ($size == 'original') ? get_option('large_size_w') : get_option($size . '_size_w');
                } else {
                    $get_size = $_wp_additional_image_sizes[$size]['width'];
                }
            }
        }

        // Get user ID
        $id_or_email = ! empty($user) ? $user->ID : 'unknown@gravatar.com';
        // Check if link is set
        if ( ! empty($link)) {
            // CSS class is same as link type, except for URL
            $link_class = $link;
            if ($link == 'file') {
                // Get image src
                $link = get_wp_user_avatar_src($id_or_email, 'original');
            } else {
                // URL
                $link_class = 'custom';
            }
            // Open in new window
            $target_link = ! empty($target) ? ' target="' . $target . '"' : "";
            // Wrap the avatar inside the link
            $html = '<a href="' . $link . '" class="wp-user-avatar-link wp-user-avatar-' . $link_class . '"' . $target_link . '>' . get_wp_user_avatar($id_or_email, $get_size) . '</a>';
        } else {
            $html = get_wp_user_avatar($id_or_email, $get_size);
        }

        $avatar = $html;

        // Check if caption is set
        if ( ! empty($content)) {
            // Get attachment ID
            $wpua = get_user_meta($id_or_email, $wpdb->get_blog_prefix($blog_id) . 'user_avatar', true);
            // Clean up caption
            $content = trim($content);
            $content = preg_replace('/\r|\n/', "", $content);
            $content = preg_replace('/<\/p><p>/', "", $content, 1);
            $content = preg_replace('/<\/p><p>$/', "", $content);
            $content = str_replace('</p><p>', "<br /><br />", $content);
            $avatar  = do_shortcode(image_add_caption($html, $wpua, $content, "", $align, $link, $get_size, ""));
        }

        return $avatar;
    }

    /**
     * Update user
     *
     * @param bool $user_id
     *
     * @return WP_Error
     */
    private function wpua_edit_user($user_id = 0)
    {
        $update = $user_id ? true : false;
        $user   = new stdClass;
        $errors = new WP_Error();
        do_action_ref_array('wpua_update_errors', array($errors, $update, $user));
        if ($errors->get_error_codes()) {
            // Return with errors
            return $errors;
        }
        if ($update) {
            // Redirect with updated variable
            $redirect_url = esc_url_raw(add_query_arg(array('updated' => '1'), wp_get_referer()));
            /**
             * Filter redirect URL
             *
             * @param string $redirect_url
             */
            $redirect_url = apply_filters('wpua_edit_user_redirect_url', $redirect_url);
            /**
             * Filter wp_safe_redirect or wp_redirect
             *
             * @param bool $safe_redirect
             */
            $safe_redirect = apply_filters('wpua_edit_user_safe_redirect', true);
            $safe_redirect ? wp_safe_redirect($redirect_url) : wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * Edit shortcode
     *
     * @param array $atts
     *
     * @return string
     */
    public function wpua_edit_shortcode($atts)
    {
        global $current_user, $errors;
        if (is_user_logged_in()) {
            extract(shortcode_atts(array('user' => ""), $atts));
            // Default user is current user
            $valid_user = $current_user;
            // Find user by ID, login, slug, or e-mail address
            if ( ! empty($user)) {
                $get_user = is_numeric($user) ? get_user_by('id', $user) : get_user_by('login', $user);
                $get_user = empty($get_user) ? get_user_by('slug', $user) : $get_user;
                $get_user = empty($get_user) ? get_user_by('email', $user) : $get_user;
                // Check if current user can edit this user
                $valid_user = current_user_can('edit_user', $get_user->ID) ? $get_user : null;
            }

            // Show form only for valid user
            if ($valid_user) {
                // Save
                if (isset($_POST['submit']) && $_POST['submit'] && $_POST['wpua_action'] == 'update') {
                    do_action('wpua_update', $valid_user->ID);
                    // Check for errors
                    $errors = $this->wpua_edit_user($valid_user->ID);
                }
                // Errors
                if (isset($errors) && is_wp_error($errors)) {
                    echo '<div class="error"><p>' . implode("</p>\n<p>", $errors->get_error_messages()) . '</p></div>';
                } elseif (isset($_GET['updated']) && $_GET['updated'] == '1') {
                    echo '<div class="updated"><p><strong>' . __('Profile updated.', 'wp-user-avatar') . '</strong></p></div>';
                }

                // Edit form
                return $this->wpua_edit_form($valid_user);
            }
        }
    }

    /**
     * Edit form
     *
     * @param object $user
     *
     * @return false|string
     */
    private function wpua_edit_form($user)
    {
        ob_start();
        ?>
        <form id="wpua-edit-<?php echo $user->ID; ?>" class="wpua-edit" action="" method="post" enctype="multipart/form-data">
            <?php do_action('wpua_show_profile', $user); ?>
            <input type="hidden" name="wpua_action" value="update"/>
            <input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($user->ID); ?>"/>
            <?php wp_nonce_field('update-user_' . $user->ID); ?>
            <?php submit_button(__('Update Profile', 'wp-user-avatar')); ?>
        </form>
        <?php
        return ob_get_clean();
    }
}

/**
 * Initialize
 */
function wpua_shortcode_init()
{
    global $wpua_shortcode;
    $wpua_shortcode = new WP_User_Avatar_Shortcode();
}

add_action('init', 'wpua_shortcode_init');
