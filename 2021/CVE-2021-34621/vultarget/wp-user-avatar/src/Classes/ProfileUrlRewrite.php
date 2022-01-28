<?php

namespace ProfilePress\Core\Classes;

/**
 * Rewrite the profile page URL
 *
 * Rewrite the page URL to contain the "/profile" slug
 */
class ProfileUrlRewrite
{
    /** @type object instance */
    private static $instance;

    public function __construct()
    {
        add_action('init', array($this, 'rewrite_function'), 10, 0);
    }

    public function rewrite_function()
    {
        // set $page_id to the WordPress page with the profile shortcode
        $page_id = apply_filters('ppress_profile_page_id', ppress_get_setting('set_user_profile_shortcode'));

        $profile_slug = ppress_get_profile_slug();

        add_rewrite_tag('%who%', '([^&]+)');

        $regex_1 = apply_filters('ppress_profile_rewrite_regex_1', "^{$profile_slug}/([^/]*)/?", $profile_slug);
        $regex_2 = apply_filters('ppress_profile_rewrite_regex_2', "^{$profile_slug}/?$", $profile_slug);

        $query_1 = apply_filters('ppress_profile_rewrite_query_1', 'index.php?page_id=' . $page_id . '&who=$matches[1]', $page_id);
        $query_2 = apply_filters('ppress_profile_rewrite_query_2', 'index.php?page_id=' . $page_id, $page_id);

        add_rewrite_rule($regex_1, $query_1, 'top');
        add_rewrite_rule($regex_2, $query_2, 'top');

        do_action('ppress_after_rewrite_hook_added', $profile_slug, $page_id);
    }

    public static function get_instance()
    {
        if ( ! self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}