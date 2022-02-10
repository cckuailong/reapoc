<?php

// FEED BURNER
// Adds the "format=xml" query parameter if not present
add_filter('wpra/importer/feed/url', function ($url, $parsed) {
    // Check if it's a Youtube URL
    if (stripos($parsed['host'], 'feedburner.com') === false) {
        return $url;
    }

    if (stripos($url, 'format=xml') === false) {
        $url .= empty($parsed['query']) ? '?' : '&';
        $url .= 'format=xml';
    }

    return $url;
}, 10, 2);
