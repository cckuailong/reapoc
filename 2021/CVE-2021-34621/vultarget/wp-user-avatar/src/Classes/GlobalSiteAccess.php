<?php

namespace ProfilePress\Core\Classes;

class GlobalSiteAccess
{
    public static function init()
    {
        // using wp hook because it precedes template_redirect which is used by Content protection redirect rules.
        add_action('wp', array(__CLASS__, 'global_redirect'), -1);
    }

    public static function global_redirect()
    {
        if (is_admin()) return;

        $access = ppress_get_setting('global_site_access', 'everyone', true);

        if ('login' != $access) return;

        $redirect_url        = ppress_get_setting('global_site_access_redirect_page');
        $custom_redirect_url = ppress_get_setting('global_site_access_custom_redirect_page');

        $excluded_pages = ppress_get_setting('global_site_access_exclude_pages', [], true);

        $excluded_pages = array_map('absint', array_filter(
                array_merge($excluded_pages, [
                    ppress_get_setting('set_login_url', false, true),
                    ppress_get_setting('set_registration_url', false, true),
                    ppress_get_setting('set_lost_password_url', false, true),
                ])
            )
        );

        $allow_homepage = ppress_get_setting('global_site_access_allow_homepage');

        if ( ! empty($redirect_url)) {
            $redirect_url = get_permalink(absint($redirect_url));
        }

        if ( ! empty($custom_redirect_url)) {
            $redirect_url = $custom_redirect_url;
        }

        if (empty($redirect_url)) return;

        $current_url = ppress_get_current_url_query_string();

        // skip all wp-login urls.
        if (strpos($current_url, 'wp-login.php') !== false) return;

        /**
         * Exclude the redirect URL.
         * strtok() remove all query strings and trailing slash. @see https://stackoverflow.com/a/6975045/2648410
         */
        if (strpos($current_url, untrailingslashit(strtok($redirect_url, '?'))) !== false) return;

        if ($allow_homepage == 'yes' && is_front_page()) return;

        if ( ! empty($excluded_pages) && is_page($excluded_pages)) return;

        if ( ! is_user_logged_in()) {
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
}