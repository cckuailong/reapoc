<?php

add_action('wp_head', 'wprss_cpt_feeds');
/**
 * Adds Link tags to the head of the page, for CPTs' feeds.
 */
function wprss_cpt_feeds()
{
    // Get all post types
    $post_types = get_post_types([
        'public' => true,
        '_builtin' => false,
    ]);

    // If current page is archive page for a particular post type
    if (is_post_type_archive()) {
        // Remove post type from the post types list
        unset($post_types[get_post_type()]);
    }

    // Filter which post types to use
    // False: none
    // True: all
    // Array: particular post types
    // String: Single post type
    $post_type_feeds = apply_filters('wprss_cpt_feeds', false);
    switch (gettype($post_type_feeds)) {
        // If it's a boolean ...
        case 'boolean':
            // If it is FALSE, exit function. Do nothing. Simply.
            if ($post_type_feeds === false) return;
            // Otherwise, if TRUE, no further action is needed.
            break;
        // If it's a string ...
        case 'string':
            // If the post type does not exist, stop
            if (!isset($post_types[$post_type_feeds])) return;
            // Otherwise, only use this post type
            $single = $post_types[$post_type_feeds];
            $post_types = [$single => $single];
            break;
        // If it's an array ...
        case 'array':
            $post_types = array_intersect($post_types, $post_type_feeds);
            break;
        // If any other type, stop.
        default:
            return;
    }

    // Get only the values of the post types
    $post_types = array_values($post_types);

    // Get the site name and RSS feed URL, parsed as an array
    $siteName = get_bloginfo("name");
    $feedURL = parse_url(get_bloginfo('rss2_url'));

    // Foreach post type
    foreach ($post_types as $i => $post_type) {
        // Get its RSS feed URL
        $feed = get_post_type_archive_feed_link($post_type);

        // If it doesnt have one, use the internal WP feed URL using the post_type query arg
        if ($feed === '' || !is_string($feed)) {
            // Start with the feed URL of the site
            $feed = $feedURL;
            // If there are no query args, set to an empty string
            if (!isset($feed['query'])) {
                $feed['query'] = '';
            }
            // If the query is not empty, we need to add an ampersand
            if (strlen($feed['query']) > 0) {
                $feed['query'] .= '&';
            }
            // Add the post_type query arg
            $feed['query'] .= "post_type=$post_type";
            // Unparse the URL array into a string
            $feed = wprss_unparse_url($feed);
        }

        // Get the Post Type Pretty Name
        $obj = get_post_type_object($post_type);
        $name = $obj->labels->name;

        // Print the <link> tag
        $feedName = sprintf(__('%1$s &raquo; %2$s Feed', 'wprss'), $siteName, $name);

        printf(
            '<link rel="%1$s" type="%2$s" title="%3$s" href="%4$s" />',
            "alternate",
            "application/rss+xml",
            $feedName,
            $feed
        );

        echo "\n";
    }
}

if (!function_exists('wprss_unparse_url')) {
    function wprss_unparse_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return implode('', [
            $scheme,
            $user,
            $pass,
            $host,
            $port,
            $path,
            $query,
            $fragment,
        ]);
    }
}
