<?php

/**
 * Contains all roles and capabilities functions
 *
 * @package WPRSSAggregator
 */

/**
 * Remove core post type capabilities (called on uninstall)
 *
 * @since 3.3
 * @return void
 */
function wprss_remove_caps()
{
    global $wp_roles;

    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    if ($wp_roles instanceof WP_Roles) {
        /** Site Administrator Capabilities */
        $wp_roles->remove_cap('administrator', 'manage_feed_settings');
        /** Editor Capabilities */
        $wp_roles->remove_cap('editor', 'manage_feed_settings');

        /** Remove the Main Post Type Capabilities */
        $capabilities = wprss_get_core_caps();

        foreach ($capabilities as $cap_group) {
            foreach ($cap_group as $cap) {
                $wp_roles->remove_cap('administrator', $cap);
                $wp_roles->remove_cap('editor', $cap);
            }
        }
    }
}

/**
 * Gets the core post type capabilities.
 *
 * @since 4.18
 */
function wprss_get_core_caps()
{
    $capabilities = [];

    $capability_types = ['feed', 'feed_source'];

    foreach ($capability_types as $capability_type) {
        $capabilities[$capability_type] = [
            // Post type
            "edit_{$capability_type}",
            "read_{$capability_type}",
            "delete_{$capability_type}",
            "edit_{$capability_type}s",
            "edit_others_{$capability_type}s",
            "publish_{$capability_type}s",
            "read_private_{$capability_type}s",
            "delete_{$capability_type}s",
            "delete_private_{$capability_type}s",
            "delete_published_{$capability_type}s",
            "delete_others_{$capability_type}s",
            "edit_private_{$capability_type}s",
            "edit_published_{$capability_type}s",

            // Terms
            "manage_{$capability_type}_terms",
            "edit_{$capability_type}_terms",
            "delete_{$capability_type}_terms",
            "assign_{$capability_type}_terms",
        ];
    }

    return $capabilities;
}
