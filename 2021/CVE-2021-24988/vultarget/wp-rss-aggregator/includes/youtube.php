<?php

function wprss_is_url_youtube($url)
{
    $parsed = is_array($url) ? $url : wpra_parse_url($url);

    return isset($parsed['host']) && stripos($parsed['host'], 'youtube.com') !== false;
}

function wprss_is_feed_youtube($feed)
{
    $id = ($feed instanceof WP_Post) ? $feed->ID : $feed;
    $url = get_post_meta($id, 'wprss_url', true);

    return wprss_is_url_youtube($url);
}

// Filters URLs to allow WPRA to be able to use YouTube channel URLs as feed URLs
add_filter('wpra/importer/feed/url', function ($url, $parsed) {
    $pathArray = $parsed['path'];

    // Check if it's a Youtube URL
    if (stripos($parsed['host'], 'youtube.com') === false) {
        return $url;
    }

    // Check if the YouTube URL has "channel" in the path
    $channelPos = array_search('channel', $pathArray);
    if ($channelPos !== false && !empty($pathArray[$channelPos + 1])) {
        // Check if there's another part that follows the "channel" part in the URL path
        // And use it to construct the Youtube feed URL
        return sprintf(
            'https://www.youtube.com/feeds/videos.xml?channel_id=%s',
            $pathArray[$channelPos + 1]
        );
    }

    // Check if the YouTube URL has "user" in the path
    $userPos = array_search('user', $pathArray);
    if ($userPos !== false &&  !empty($pathArray[$userPos + 1])) {
        // Check if there's another part that follows the "user" part in the URL path
        // And use it to construct the Youtube feed URL
        return sprintf(
            'https://www.youtube.com/feeds/videos.xml?user=%s',
            $pathArray[$userPos + 1]
        );
    }

    return $url;
}, 10, 2);
