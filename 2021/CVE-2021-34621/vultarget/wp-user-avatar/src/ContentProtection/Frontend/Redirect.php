<?php

namespace ProfilePress\Core\ContentProtection\Frontend;

use ProfilePress\Core\ContentProtection\SettingsPage;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;

class Redirect
{
    public function __construct()
    {
        add_action('template_redirect', [$this, 'handler']);
    }

    public function handler()
    {
        $metas = PROFILEPRESS_sql::get_meta_data_by_key(SettingsPage::META_DATA_KEY);

        if (is_array($metas)) {

            foreach ($metas as $meta) {

                $meta = ppress_var($meta, 'meta_value', []);

                if ( ! in_array(ppress_var($meta, 'is_active', true), ['true', true], true)) continue;

                $access_condition = ppress_var($meta, 'access_condition', []);

                $noaccess_action = ppress_var($access_condition, 'noaccess_action');

                if ('redirect' != $noaccess_action) continue;

                $redirect_url = '';

                $current_url = ppress_get_current_url_query_string();

                if ('custom_url' == ppress_var($access_condition, 'noaccess_action_redirect_url')) {
                    $redirect_url = ppress_var($access_condition, 'noaccess_action_redirect_custom_url');
                }

                if (empty($redirect_url)) {
                    $redirect_url = ppress_login_url($current_url);
                }

                // ----- Exclude the redirect url, login, registration, password reset and edit profile pages ------//
                /** strtok() remove all query strings and trailing slash. @see https://stackoverflow.com/a/6975045/2648410 */
                $comp_redirect_url = untrailingslashit(strtok($redirect_url, '?'));

                $login_url          = untrailingslashit(strtok(ppress_login_url(), '?'));
                $registration_url   = untrailingslashit(strtok(ppress_registration_url(), '?'));
                $password_reset_url = untrailingslashit(strtok(ppress_password_reset_url(), '?'));
                $edit_profile_url   = untrailingslashit(strtok(ppress_edit_profile_url(), '?'));

                if (strpos($current_url, $comp_redirect_url) !== false) continue;
                if (strpos($current_url, $login_url) !== false) continue;
                if (strpos($current_url, $registration_url) !== false) continue;
                if (strpos($current_url, $password_reset_url) !== false) continue;
                if (strpos($current_url, $edit_profile_url) !== false) continue;

                $who_can_access = ppress_var($access_condition, 'who_can_access', 'everyone');

                $access_roles = ppress_var($access_condition, 'access_roles', []);

                if (Checker::content_match($meta['content'], true)) {

                    if (Checker::is_blocked($who_can_access, $access_roles)) {
                        wp_safe_redirect($redirect_url);
                        exit;
                    }

                    break;
                };

            }
        }
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