<?php

use ProfilePress\Core\Classes\UserAvatar;

/**
 * Core user functions.
 *
 *
 */
class WP_User_Avatar_Functions
{
    function wpua_get_default_avatar_url($size = false)
    {
        global $mustache_admin, $mustache_avatar, $mustache_medium, $mustache_original, $mustache_thumbnail, $wpua_avatar_default, $wpua_functions;

        $size = ! empty($size) ? $size : 96;
        // Show custom Default Avatar
        if ( ! empty($wpua_avatar_default) && wp_attachment_is_image($wpua_avatar_default)) {
            // Get image
            $wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src($wpua_avatar_default, array($size, $size));
            // Image src
            $url = $wpua_avatar_default_image[0];
            // Add dimensions if numeric size
        } else {
            // Get mustache image based on numeric size comparison
            if ($size > get_option('medium_size_w')) {
                $url = $mustache_original;
            } elseif ($size <= get_option('medium_size_w') && $size > get_option('thumbnail_size_w')) {
                $url = $mustache_medium;
            } elseif ($size <= get_option('thumbnail_size_w') && $size > 96) {
                $url = $mustache_thumbnail;
            } elseif ($size <= 96 && $size > 32) {
                $url = $mustache_avatar;
            } elseif ($size <= 32) {
                $url = $mustache_admin;
            }
            // Add dimensions if numeric size
        }

        return $url;
    }

    /**
     * Returns true if user has Gravatar-hosted image
     *
     * @param int|string $id_or_email
     * @param bool $has_gravatar
     * @param int|string $user
     * @param string $email
     *
     * @return bool $has_gravatar
     */
    public function wpua_has_gravatar($id_or_email)
    {
        global $avatar_default;

        // User has WPUA
        // Decide if check gravatar required or not.
        if (trim($avatar_default) != 'wp_user_avatar') return true;

        $email = get_userdata(ppress_var_obj(UserAvatar::get_avatar_user_id($id_or_email), 'user_email'));

        if ( ! empty($email)) {

            $hash = md5(strtolower(trim($email)));

            $gravatar = 'https://www.gravatar.com/avatar/' . $hash . '?d=404';

            $data = wp_cache_get($hash);

            if (false === $data) {
                $response = wp_remote_head($gravatar);
                $data     = is_wp_error($response) ? 'not200' : $response['response']['code'];

                wp_cache_set($hash, $data, "", MINUTE_IN_SECONDS);
            }

            return $data == '200';
        }

        return false;
    }

    /**
     * Get local image tag
     *
     * @param int $attachment_id
     * @param int|string $size
     * @param bool $icon
     * @param string $attr
     *
     * @return string
     */
    public function wpua_get_attachment_image($attachment_id, $size = 'thumbnail', $icon = 0, $attr = '')
    {
        $image = wp_get_attachment_image($attachment_id, $size, $icon, $attr);

        /**
         * Filter local image tag
         *
         * @param string $image
         * @param int $attachment_id
         * @param int|string $size
         * @param bool $icon
         * @param string $attr
         */
        return apply_filters('wpua_get_attachment_image', $image, $attachment_id, $size, $icon, $attr);
    }

    /**
     * Get local image src
     *
     * @param int $attachment_id
     * @param int|string $size
     * @param bool $icon
     *
     * @return array
     */
    public function wpua_get_attachment_image_src($attachment_id, $size = 'thumbnail', $icon = 0)
    {
        $image_src_array = wp_get_attachment_image_src($attachment_id, $size, $icon);

        /**
         * Filter local image src
         *
         * @param array $image_src_array
         * @param int $attachment_id
         * @param int|string $size
         * @param bool $icon
         */
        return apply_filters('wpua_get_attachment_image_src', $image_src_array, $attachment_id, $size, $icon);
    }

    /**
     * Returns true if user has wp_user_avatar
     *
     * @param int|string $id_or_email
     * @param bool $has_wpua
     * @param object $user
     * @param int $user_id
     *
     * @return bool
     */
    public function has_wp_user_avatar($id_or_email)
    {
        return UserAvatar::user_has_pp_avatar($id_or_email);
    }

    /**
     * Retrive default image url set by admin.
     */
    public function wpua_default_image($size)
    {
        global $mustache_admin, $mustache_avatar, $mustache_medium, $mustache_original, $mustache_thumbnail, $wpua_avatar_default, $wpua_functions;

        $default_image_details = array();
        // Show custom Default Avatar
        if ( ! empty($wpua_avatar_default) && wp_attachment_is_image($wpua_avatar_default)) {
            // Get image
            $wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src($wpua_avatar_default, array($size, $size));
            // Image src
            $default = $wpua_avatar_default_image[0];
            // Add dimensions if numeric size
            $default_image_details['dimensions'] = ' width="' . $wpua_avatar_default_image[1] . '" height="' . $wpua_avatar_default_image[2] . '"';

        } else {
            // Get mustache image based on numeric size comparison
            if ($size > get_option('medium_size_w')) {
                $default = $mustache_original;
            } elseif ($size <= get_option('medium_size_w') && $size > get_option('thumbnail_size_w')) {
                $default = $mustache_medium;
            } elseif ($size <= get_option('thumbnail_size_w') && $size > 96) {
                $default = $mustache_thumbnail;
            } elseif ($size <= 96 && $size > 32) {
                $default = $mustache_avatar;
            } elseif ($size <= 32) {
                $default = $mustache_admin;
            }
            $size = esc_attr($size);
            // Add dimensions if numeric size
            $default_image_details['dimensions'] = ' width="' . $size . '" height="' . $size . '"';
        }
        // Construct the img tag
        $default_image_details['size'] = $size;
        $default_image_details['src']  = $default;

        return $default_image_details;
    }

    /**
     * Get original avatar, for when user removes wp_user_avatar
     *
     * @param int|string $id_or_email
     * @param int|string $size
     *
     * @return string $default
     */
    public function wpua_get_avatar_original($id_or_email = "", $size = "")
    {
        global $avatar_default, $mustache_avatar, $wpua_avatar_default, $wpua_disable_gravatar, $wpua_functions;
        if ((bool)$wpua_disable_gravatar != 1) {
            // User doesn't have Gravatar and Default Avatar is wp_user_avatar, show custom Default Avatar
            if ( ! $wpua_functions->wpua_has_gravatar($id_or_email) && $avatar_default == 'wp_user_avatar') {
                // Show custom Default Avatar
                if ( ! empty($wpua_avatar_default) && wp_attachment_is_image($wpua_avatar_default)) {
                    $size_numeric_w_x_h        = array(get_option($size . '_size_w'), get_option($size . '_size_h'));
                    $wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src($wpua_avatar_default, $size_numeric_w_x_h);

                    $default = $wpua_avatar_default_image[0];
                } else {
                    $default = $mustache_avatar;
                }
            } else {
                // Get image from Gravatar, whether it's the user's image or default image
                $wpua_image = get_avatar($id_or_email, $size, '', '', array('ppress_skip' => true));
                // Takes the img tag, extracts the src
                preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $wpua_image, $matches, PREG_SET_ORDER);
                $default = ! empty($matches) ? $matches [0][1] : "";
            }
        } else {
            if ( ! empty($wpua_avatar_default) && wp_attachment_is_image($wpua_avatar_default)) {
                $size_numeric_w_x_h        = array(get_option($size . '_size_w'), get_option($size . '_size_h'));
                $wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src($wpua_avatar_default, $size_numeric_w_x_h);

                $default = $wpua_avatar_default_image[0];
            } else {
                $default = $mustache_avatar;
            }
        }

        /**
         * Filter original avatar src
         *
         * @param string $default
         */
        return apply_filters('wpua_get_avatar_original', $default);
    }


    /**
     * Find WPUA, show get_avatar if empty
     *
     * @param int|string $id_or_email
     * @param int|string $size
     * @param string $align
     * @param string $alt
     *
     * @return string $avatar
     */
    public function get_wp_user_avatar($id_or_email, $size = '96')
    {
        global $all_sizes, $_wp_additional_image_sizes;

        // Check for custom image sizes
        if (in_array($size, $all_sizes)) {
            if (in_array($size, array('original', 'large', 'medium', 'thumbnail'))) {
                $get_size = ($size == 'original') ? get_option('large_size_w') : get_option($size . '_size_w');
            } else {
                $get_size = $_wp_additional_image_sizes[$size]['width'];
            }
        } else {
            // Numeric sizes leave as-is
            $get_size = $size;
        }


        // User with no WPUA uses get_avatar
        $avatar = get_avatar($id_or_email, $get_size);
        // Remove width and height for non-numeric sizes
        if (in_array($size, array('original', 'large', 'medium', 'thumbnail'))) {
            $avatar = preg_replace('/(width|height)=\"\d*\"\s/', "", $avatar);
            $avatar = preg_replace("/(width|height)=\'\d*\'\s/", "", $avatar);
        }
        $replace      = array('wp-user-avatar ', 'wp-user-avatar-' . $get_size . ' ', 'wp-user-avatar-' . $size . ' ', 'avatar-' . $get_size, ' photo');
        $replacements = array("", "", "", 'avatar-' . $size, 'wp-user-avatar wp-user-avatar-' . $size . ' photo');
        $avatar       = str_replace($replace, $replacements, $avatar);

        /**
         * Filter get_wp_user_avatar
         *
         * @param string $avatar
         * @param int|string $id_or_email
         * @param int|string $size
         * @param string $align
         * @param string $alt
         */
        return apply_filters('get_wp_user_avatar', $avatar, $id_or_email, $size);
    }

    /**
     * Return just the image src
     *
     * @param int|string $id_or_email
     * @param int|string $size
     *
     * @return string
     */
    public function get_wp_user_avatar_src($id_or_email = "", $size = "")
    {
        $wpua_image_src = "";
        // Gets the avatar img tag
        $wpua_image = $this->get_wp_user_avatar($id_or_email, $size);
        // Takes the img tag, extracts the src
        if ( ! empty($wpua_image)) {
            $output         = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $wpua_image, $matches, PREG_SET_ORDER);
            $wpua_image_src = ! empty($matches) ? $matches [0] [1] : "";
        }

        return $wpua_image_src;
    }
}

/**
 * Initialize
 */
function wpua_functions_init()
{
    global $wpua_functions;
    $wpua_functions = new WP_User_Avatar_Functions();
}

add_action('plugins_loaded', 'wpua_functions_init');
