<?php

namespace ProfilePress\Core\Classes;

class UserAvatar
{
    public function __construct()
    {
        add_filter('pre_get_avatar_data', function ($args, $id_or_email) {

            if (ppress_var($args, 'force_default') === true || ppress_var($args, 'ppress_skip') === true) return $args;

            if (self::user_has_pp_avatar($id_or_email)) {

                $args['url'] = self::get_pp_avatar_url($id_or_email);

            } else {
                /** WP User Avatar Adapter STARTS */
                global $wpua_disable_gravatar, $wpua_functions;

                // First checking custom avatar.
                if ($wpua_disable_gravatar == '1' || ! $wpua_functions->wpua_has_gravatar($id_or_email)) {
                    $args['url'] = $wpua_functions->wpua_get_default_avatar_url(ppress_var($args, 'size'));
                }
                /** WP User Avatar Adapter ENDS */
            }

            if ( ! empty($args['url'])) {
                $args['found_avatar'] = true;
            }

            return $args;

        }, 999999999999, 2);

        add_filter('get_avatar', function ($avatar, $id_or_email, $size, $default, $alt, $args) {

            if (ppress_var($args, 'force_default') !== true && ppress_var($args, 'ppress_skip') !== true) {
                if (self::user_has_pp_avatar($id_or_email)) {
                    $class = [];
                    if (isset($args['class'])) {
                        if (is_array($args['class'])) {
                            $class = array_merge($class, $args['class']);
                        } else {
                            $class[] = $args['class'];
                        }
                    }

                    $class = esc_attr(implode(' ', $class));

                    $avatar = self::get_avatar_img($id_or_email, $size, $alt, $class);
                }
            }

            return $avatar;

        }, 999999999999, 6);
    }

    public static function user_has_pp_avatar($id_or_email)
    {
        $user_id = self::get_avatar_user_id($id_or_email);

        $avatar_url = get_user_meta($user_id, 'pp_profile_avatar', true);

        if ( ! empty($avatar_url) && is_string($avatar_url)) return true;

        /** WP User Avatar Adapter STARTS */
        global $wpdb, $blog_id, $avatar_default, $wpua_avatar_default;

        $attachment_id = get_user_meta($user_id, $wpdb->get_blog_prefix($blog_id) . 'user_avatar', true);
        // Check if avatar is same as default avatar or on excluded list
        if ( ! empty($attachment_id) && ($avatar_default != 'wp_user_avatar' || $attachment_id != $wpua_avatar_default) && wp_attachment_is_image($attachment_id)) {
            return true;
        }

        /** WP User Avatar Adapter ENDS */

        return false;
    }

    /**
     * @param $id_or_email
     *
     * @param bool $size
     *
     * @return string
     */
    public static function get_pp_avatar_url($id_or_email, $size = false)
    {
        $user_id = self::get_avatar_user_id($id_or_email);

        $avatar_url = get_user_meta($user_id, 'pp_profile_avatar', true);

        if ( ! empty($avatar_url) && is_string($avatar_url)) {

            return PPRESS_AVATAR_UPLOAD_URL . "$avatar_url";
        }

        /** WP User Avatar Adapter STARTS */
        global $wpdb, $blog_id;

        $attachment_id = get_the_author_meta($wpdb->get_blog_prefix($blog_id) . 'user_avatar', $user_id);

        if ( ! empty($attachment_id) && wp_attachment_is_image($attachment_id)) {

            if ( ! empty($size)) {

                $size = is_numeric($size) ? [$size, $size] : $size;

                return ppress_var(wp_get_attachment_image_src($attachment_id, $size), 0);
            }

            return wp_get_attachment_url($attachment_id);
        }

        /** WP User Avatar Adapter ENDS */

        return false;
    }

    /**
     * HTML image for the user profile
     *
     * @param $id_or_email
     * @param string $size
     * @param string $alt
     * @param string $class
     * @param string $css_id
     *
     * @return mixed
     */
    public static function get_avatar_img($id_or_email, $size = '96', $alt = '', $class = '', $css_id = '')
    {
        $alt = esc_attr($alt);

        if ( ! is_numeric($size)) $size = '96';

        if ( ! empty($css_id)) $css_id = " id='$css_id'";

        $size = absint($size);

        $avatar_url = get_avatar_url($id_or_email, ['size' => $size]);

        return "<img data-del=\"avatar\" alt='{$alt}' src='{$avatar_url}' class='avatar pp-user-avatar avatar-{$size} photo {$class}' height='{$size}' width='{$size}'$css_id/>";
    }

    /**
     * Culled from get_avatar_data()
     *
     * @param mixed $id_or_email
     *
     * @return bool|int
     */
    public static function get_avatar_user_id($id_or_email)
    {
        if (is_object($id_or_email) && isset($id_or_email->comment_ID)) {

            $email = get_comment($id_or_email)->comment_author_email;

            return ppress_var_obj(get_user_by('email', $email), 'ID');
        }

        if (is_numeric($id_or_email)) {
            return ppress_var_obj(get_user_by('id', absint($id_or_email)), 'ID');
        }

        if (is_string($id_or_email)) {

            if ( ! strpos($id_or_email, '@md5.gravatar.com')) {
                // email address
                return ppress_var_obj(get_user_by('email', $id_or_email), 'ID');
            }
        }

        if ($id_or_email instanceof \WP_User) {
            return $id_or_email->ID;
        }

        if ($id_or_email instanceof \WP_Post) {
            return get_user_by('id', (int)$id_or_email->post_author)->ID;
        }

        if ($id_or_email instanceof \WP_Comment) {

            if ( ! is_avatar_comment_type(get_comment_type($id_or_email))) return false;

            if ( ! empty($id_or_email->user_id)) return $id_or_email->user_id;

            if ( ! empty($id_or_email->comment_author_email)) {
                return get_user_by('email', $id_or_email->comment_author_email)->ID;
            }
        }

        return false;
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