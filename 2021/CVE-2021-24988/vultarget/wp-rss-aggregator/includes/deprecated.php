<?php

/**
 * This file contains all legacy deprecated constants and functionality for WP RSS Aggregator.
 *
 * @since 4.13
 */

// The feed source CPT name
define('WPRSS_POST_TYPE_FEED_SOURCE', 'wprss_feed');

add_filter('wp_link_query_args', 'wprss_modify_link_builder_query');
/**
 * Filter the link query arguments to exclude the feed and feed item post types.
 * This filter will only work for WordPress versions 3.7 or higher.
 *
 * @since 3.4.3
 *
 * @deprecated Needs to be refactored/moved into an appropriate module
 *
 * @param array $query An array of WP_Query arguments.
 *
 * @return array $query
 */
function wprss_modify_link_builder_query($query)
{
    // custom post type slugs to be removed
    $toRemove = ['wprss_feed', 'wprss_feed_item', 'wprss_feed_template'];

    // find and remove the array keys
    foreach ($toRemove as $postType) {
        if ($key = array_search($postType, $query['post_type'])) {
            unset($query['post_type'][$key]);
        }
    }

    return $query;
}

/**
 * @deprecated Replaced by PSR Logging since 4.13
 */
function wprss_log_get_level()
{
    return 'Error';
}
