<?php

namespace ProfilePress\Core\Classes;

class BuddyPressBbPress
{
    public function __construct()
    {
        if (ppress_settings_by_key('override_bp_avatar') == 'yes') {
            add_filter('bp_core_fetch_avatar', array(__CLASS__, 'override_html_avatar'), 999999999, 3);
            add_filter('bp_core_fetch_avatar_url', array(__CLASS__, 'override_avatar_url'), 999999999, 2);
        }

        add_filter('bp_core_get_user_domain', array(__CLASS__, 'override_bp_profile_url'), 999999999, 4);

        add_filter('bbp_pre_get_user_profile_url', array(__CLASS__, 'override_bbp_profile_url'), 9999999);
    }

    public static function override_bp_profile_url($domain, $user_id, $user_nicename, $user_login)
    {
        if (ppress_settings_by_key('override_bp_profile_url') == 'yes') {
            if ( ! $user_login) {
                $user_login = ppress_get_username_by_id($user_id);
            }

            $domain = ppress_get_frontend_profile_url($user_login);
        }

        return $domain;
    }

    public static function override_bbp_profile_url($user_id)
    {
        if (ppress_settings_by_key('override_bbp_profile_url') == 'yes') {
            if (is_numeric($user_id)) {
                $user_id = ppress_get_frontend_profile_url(
                    ppress_get_username_by_id($user_id)
                );
            }
        }

        return $user_id;
    }

    /**
     * Override HTML BP avatar output.
     *
     * @param string $image_in_html
     * @param array $params
     * @param int $item_id
     *
     * @return mixed
     */
    public static function override_html_avatar($image_in_html, $params, $item_id)
    {
        if (isset($params['object']) && 'user' == $params['object']) {
            $user_id = $item_id;
            if (UserAvatar::user_has_pp_avatar($user_id)) {
                $avatar_url = UserAvatar::get_pp_avatar_url($user_id);

                return preg_replace('/src=".+?"/', 'src="' . $avatar_url . '"', $image_in_html);
            }
        }

        return $image_in_html;
    }

    /**
     * Override BP avatar url.
     *
     * @param string $image_url
     * @param array $params
     *
     * @return bool|mixed|string
     */
    public static function override_avatar_url($image_url, $params)
    {
        if (isset($params['object']) && 'user' == $params['object']) {
            $user_id = $params['item_id'];
            if (UserAvatar::user_has_pp_avatar($user_id)) {
                $image_url = UserAvatar::get_pp_avatar_url($user_id);
            }
        }

        return $image_url;
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