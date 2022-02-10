<?php

/**
 * Functions relating to feed importing
 *
 * @package WPRSSAggregator
 */

// Warning: Order may be important
add_filter('wprss_normalize_permalink', 'wprss_google_news_url_fix', 8);
add_filter('wprss_normalize_permalink', 'wprss_bing_news_url_fix', 9);
add_filter('wprss_normalize_permalink', 'wprss_google_alerts_url_fix', 10);
add_filter('wprss_normalize_permalink', 'wprss_convert_video_permalink', 100);

// Adds comparators for item sorting
add_filter('wprss_item_comparators', 'wprss_sort_comparators_default');

add_action( 'wprss_fetch_single_feed_hook', 'wprss_fetch_insert_single_feed_items' );
/**
 * The main feed fetching function.
 * Fetches the feed items from the source provided and inserts them into the DB.
 *
 * Called on hook 'wprss_fetch_single_feed_hook'.
 *
 * @since 3.2
 */
function wprss_fetch_insert_single_feed_items( $feed_ID ) {
    set_transient('wpra/feeds/importing/' . $feed_ID, true, 0);

    global $wprss_importing_feed;
    $wprss_importing_feed = $feed_ID;

    register_shutdown_function('wprss_detect_exec_timeout');

    $importFn = function ($feed_ID) {
        $logger = wpra_get_logger($feed_ID);
        $logger->info('Starting import of feed {id}', ['id' => $feed_ID]);

        $t = microtime(true);

        // Check if the feed source is active.
        if ( ! wprss_is_feed_source_active( $feed_ID ) && ! wprss_feed_source_force_next_fetch( $feed_ID ) ) {
            $logger->info('Feed is not active. Finished');
            return;
        }

        // If the feed source is forced for next fetch, remove the force next fetch data
        if ( wprss_feed_source_force_next_fetch( $feed_ID ) ) {
            delete_post_meta( $feed_ID, 'wprss_force_next_fetch' );
        }

        // Truncate old items first
        wprss_truncate_items_for_source( $feed_ID );

        // Get the feed source URL from post meta, and filter it
        $feed_url = get_post_meta( $feed_ID, 'wprss_url', true );
        $feed_url = apply_filters( 'wprss_feed_source_url', $feed_url, $feed_ID );
        $logger->debug('Feed source URL: {0}', [$feed_url]);

        // Get the feed limit from post meta
        $feed_limit = get_post_meta( $feed_ID, 'wprss_limit', true );

        // If the feed has no individual limit
        if ( $feed_limit === '' || intval( $feed_limit ) <= 0 ) {
            // Get the global limit
            $global_limit = wprss_get_general_setting('limit_feed_items_imported');
            // If no global limit is set, mark as NULL
            if ( $global_limit === '' || intval($global_limit) <= 0 ) {
                $feed_limit = NULL;
            }
            else $feed_limit = $global_limit;
        }

        // Filter the URL for validity
        if ( ! wprss_validate_url( $feed_url ) ) {
            $logger->error('Feed URL is not valid!');
        } else {
            // Get the feed items from the source
            $items = wprss_get_feed_items( $feed_url, $feed_ID );

            // If got NULL, convert to an empty array
            if ( $items === NULL ) {
                $items_to_insert = array();
            } else {
                // See `wprss_item_comparators` filter
                wprss_sort_items($items);

                // If using a limit ...
                if ( $feed_limit === NULL ) {
                    $items_to_insert = $items;
                } else {
                    $items_to_insert = array_slice( $items, 0, $feed_limit );
                    $logger->debug('{0} items in the feed, {1} items after applying limit', [
                        count($items),
                        count($items_to_insert)
                    ]);
                }
            }

            $unique_titles_only = get_post_meta($feed_ID, 'wprss_unique_titles', true);
            $unique_titles_only = ($unique_titles_only === '')
                ? wprss_get_general_setting('unique_titles')
                : $unique_titles_only;
            $unique_titles_only = filter_var($unique_titles_only, FILTER_VALIDATE_BOOLEAN);
            // Gather the titles of the items that are imported
            // The import process will check not only the titles in the DB but the titles currently in the feed
            $existing_titles = [];

            // Gather the permalinks of existing feed item's related to this feed source
            $existing_permalinks = wprss_get_existing_permalinks( $feed_ID );

            // Generate a list of items fetched, that are not already in the DB
            $new_items = array();
            foreach ( $items_to_insert as $item ) {
                $item_title = $item->get_title();
                $permalink = $item->get_permalink();
                $permalink = wprss_normalize_permalink( $permalink, $item, $feed_ID );

                // Check if blacklisted
                if (wprss_is_blacklisted($permalink)) {
                    $logger->debug('Item "{0}" is blacklisted', [$item_title]);

                    continue;
                }

                // Check if already imported
                if (array_key_exists($permalink, $existing_permalinks)) {
                    $logger->debug('Item "{0}" already exists in the database', [$item_title]);

                    continue;
                }

                // Check if title exists (if the option is enabled)
                if ($unique_titles_only) {
                    $title_exists_db = wprss_item_title_exists($item->get_title());
                    $title_exists_feed = array_key_exists($item_title, $existing_titles);
                    $title_exists = $title_exists_db || $title_exists_feed;
                    // Add this item's title to the list to check against
                    $existing_titles[$item_title] = 1;

                    if ($title_exists) {
                        $logger->debug('An item with the title "{0}" already exists', [$item_title]);

                        continue;
                    }
                }

                $new_items[] = $item;
            }

            $original_count = count( $items_to_insert );
            $new_count = count( $new_items );

            if ( $new_count !== $original_count ) {
                $logger->debug('{0} will be skipped', [$original_count - $new_count]);
            }

            $items_to_insert = $new_items;
            $per_import = wprss_get_general_setting('limit_feed_items_per_import');
            if (!empty($per_import)) {
                $items_to_insert = array_slice( $items_to_insert, 0, $per_import );
            }

            // If using a limit - delete any excess items to make room for the new items
            if ( $feed_limit !== NULL ) {
                // Get the number of feed items in DB, and their count
                $db_feed_items = wprss_get_feed_items_for_source( $feed_ID );
                $num_db_feed_items = $db_feed_items->post_count;

                // Get the number of feed items we can store until we reach the limit
                $num_can_insert = $feed_limit - $num_db_feed_items;
                // Calculate how many feed items we must delete before importing, to keep to the limit
                $num_new_items = count( $new_items );
                $num_feed_items_to_delete = $num_can_insert > $num_new_items
                        ? 0
                        : $num_new_items - $num_can_insert;

                // Get an array with the DB feed items in reverse order (oldest first)
                $db_feed_items_reversed = array_reverse( $db_feed_items->posts );
                // Cut the array to get only the first few that are to be deleted ( equal to $num_feed_items_to_delete )
                $feed_items_to_delete = array_slice( $db_feed_items_reversed, 0, $num_feed_items_to_delete );

                // Iterate the feed items and delete them
                $num_items_deleted = 0;
                foreach ( $feed_items_to_delete as $key => $post ) {
                    wp_delete_post( $post->ID, TRUE );
                    $num_items_deleted++;
                }

                if ($num_items_deleted > 0) {
                    $logger->info('Deleted the oldest {0} items', [$num_items_deleted]);
                }
            }

            update_post_meta( $feed_ID, 'wprss_last_update', $last_update_time = time() );
            update_post_meta( $feed_ID, 'wprss_last_update_items', 0 );

            // Insert the items into the db
            if ( !empty( $items_to_insert ) ) {
                wprss_items_insert_post( $items_to_insert, $feed_ID );
            }
        }

        $next_scheduled = get_post_meta( $feed_ID, 'wprss_reschedule_event', TRUE );

        if ( $next_scheduled !== '' ) {
            wprss_feed_source_update_start_schedule( $feed_ID );
            delete_post_meta( $feed_ID, 'wprss_reschedule_event' );
            $logger->info('Scheduled next update');
        }

        $t = microtime(true) - $t;

        $logger->info(sprintf('Import completed in %.2f seconds!', $t));
    };

    try {
        $importFn($feed_ID);
    } catch (Exception $e) {
        wpra_get_logger($feed_ID)->error($e->getMessage());
        throw $e;
    }

    delete_transient('wpra/feeds/importing/' . $feed_ID);
    wprss_flag_feed_as_idle($feed_ID);
    $wprss_importing_feed = null;
}

/**
 * Fetches the feed items from a feed at the given URL.
 *
 * Called from 'wprss_fetch_insert_single_feed_items'
 *
 * @since 3.0
 *
 * @return SimplePie_Item[]|null
 */
function wprss_get_feed_items( $feed_url, $source, $force_feed = FALSE ) {
    // Add filters and actions prior to fetching the feed items
    add_filter( 'wp_feed_cache_transient_lifetime' , 'wprss_feed_cache_lifetime' );

    /* Fetch the feed from the soure URL specified */
    $feed = wprss_fetch_feed( $feed_url, $source, $force_feed );

    if (is_wp_error($feed)) {
        wpra_get_logger($source)->error('Failed to fetch the feed from {0}. Error: {1}', [
            $feed_url,
            $feed->get_error_message()
        ]);

        return NULL;
    }

    update_post_meta( $source, 'wprss_site_url', $feed->get_permalink() );
    update_post_meta( $source, 'wprss_feed_image', $feed->get_image_url() );

    // Remove previously added filters and actions
    remove_filter( 'wp_feed_cache_transient_lifetime' , 'wprss_feed_cache_lifetime' );

    return @$feed->get_items();
}

if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('cron_request', 'wprss_cron_add_xdebug_cookie', 10);
}

/**
 * Allow debugging of wp_cron jobs using xDebug.
 *
 * This is done by taking the XDEBUG cookie received from the browser (which enables an xDebug session) and passing it
 * to WP Cron. That way, code initiated from a cron job will be debuggable.
 *
 * @param array $cronRequest
 *
 * @return array $cron_request_array with the current XDEBUG_SESSION cookie added if set
 */
function wprss_cron_add_xdebug_cookie($cronRequest)
{
    if (empty($_COOKIE['XDEBUG_SESSION'])) {
        return ($cronRequest);
    }

    $cookie = filter_var($_COOKIE['XDEBUG_SESSION'], FILTER_SANITIZE_STRING);

    if (empty($cronRequest['args']['cookies'])) {
        $cronRequest['args']['cookies'] = [];
    }

    $cronRequest['args']['cookies']['XDEBUG_SESSION'] = $cookie;

    return $cronRequest;
}

/**
 * Checks if feed caching is enabled.
 *
 * This function will only return true if the cache option is enabled in the settings AND the cache directory exists
 * or can be successfully created and is writable.
 *
 * @since 4.15
 *
 * @return bool
 */
function wprss_is_feed_cache_enabled()
{
    $cacheOption = wprss_get_general_setting('feed_cache_enabled');
    $cacheOption = filter_var($cacheOption, FILTER_VALIDATE_BOOLEAN);

    if (!$cacheOption) {
        return false;
    }

    $cacheDir = wprss_get_feed_cache_dir();

    if (!file_exists($cacheDir)) {
        @mkdir($cacheDir);
    }

    return is_writable($cacheDir);
}

/**
 * Retrieves the feed cache directory.
 *
 * @since 4.15
 *
 * @return string
 */
function wprss_get_feed_cache_dir()
{
    return wpra_container()->get('wpra/importer/cache/dir');
}


/**
 * A clone of the function 'fetch_feed' in wp-includes/feed.php [line #529]
 *
 * Called from 'wprss_get_feed_items'
 *
 * @since 3.5
 */
function wprss_fetch_feed($url, $source = null, $param_force_feed = false)
{
    // Trim the URL
    $url = trim($url);
    // Parse the URL
    $parsed = wpra_parse_url($url);
    // Filter the URL
    $url = apply_filters('wpra/importer/feed/url', $url, $parsed);

    // Initialize the Feed
    $feed = new SimplePie();
    $feed->set_feed_url($url);
    $feed->set_autodiscovery_level(SIMPLEPIE_LOCATOR_ALL);

    // If a feed source was passed
    if ($source !== null || $param_force_feed) {
        // Get the force feed option for the feed source
        $force_feed = get_post_meta($source, 'wprss_force_feed', true);
        // If turned on, force the feed
        if ($force_feed == 'true' || $param_force_feed) {
            $feed->force_feed(true);
            $feed->set_autodiscovery_level(SIMPLEPIE_LOCATOR_NONE);

            global $wpraNoSslVerification;
            $wpraNoSslVerification = true;
        }
    }

    // Set timeout limit
    $fetch_time_limit = wprss_get_feed_fetch_time_limit();
    $feed->set_timeout($fetch_time_limit);

    $cacheEnabled = wprss_is_feed_cache_enabled();
    $feed->enable_cache($cacheEnabled);

    if ($cacheEnabled) {
        $feed->set_cache_location(wprss_get_feed_cache_dir());
    }

    // Reference array action hook, for the feed object and the URL
    do_action_ref_array('wp_feed_options', array(&$feed, $url));

    // Prepare the tags to strip from the feed
    $tags_to_strip = apply_filters('wprss_feed_tags_to_strip', $feed->strip_htmltags, $source);
    // Strip them
    $feed->strip_htmltags($tags_to_strip);

    do_action('wprss_fetch_feed_before', $feed, $source);

    // Fetch the feed
    $feed->init();
    $feed->handle_content_type();

    do_action('wprss_fetch_feed_after', $feed);

    // Convert the feed error into a WP_Error, if applicable
    if ($feed->error()) {
        if ($source !== null) {
            $msg = sprintf(
                __('Failed to fetch the RSS feed. Error: %s', 'wprss'),
                $feed->error()
            );
            update_post_meta($source, 'wprss_error_last_import', $msg);
        }
        return new WP_Error('simplepie-error', $feed->error(), array('feed' => $feed));
    }
    // If no error, return the feed and remove any error meta
    delete_post_meta($source, 'wprss_error_last_import');
    return $feed;
}


/**
 * Normalizes the given permalink.
 *
 * @param string $permalink The permalink to normalize
 * @return string The normalized permalink
 * @since 4.2.3
 */
function wprss_normalize_permalink( $permalink, $item, $feed_ID) {
    // Apply normalization functions on the permalink
    $permalink = trim( $permalink );
    $permalink = htmlspecialchars_decode($permalink);
    $permalink = apply_filters( 'wprss_normalize_permalink', $permalink, $item, $feed_ID);
    // Return the normalized permalink
    return $permalink;
}


/**
 * Extracts the actual URL from a Google News permalink
 *
 * @param string $permalink The permalink to normalize.
 * @since 4.2.3
 */
function wprss_google_news_url_fix($permalink) {
    return wprss_tracking_url_fix($permalink, '!^(https?:\/\/)?' . preg_quote('news.google.com', '!') . '.*!');
}


    /**
 * Extracts the actual URL from a Google Alerts permalink
 *
 * @param string $permalink The permalink to normalize.
     * @since 4.7.3
 */
function wprss_google_alerts_url_fix($permalink) {
    return wprss_tracking_url_fix($permalink, '!^(https?:\/\/)?(www\.)?' . preg_quote('google.com/url', '!') . '.*!');
}


/**
 * Extracts the actual URL from a Bing permalink
 *
 * @param string $permalink The permalink to normalize.
 * @since 4.2.3
 */
function wprss_bing_news_url_fix($permalink) {
    return wprss_tracking_url_fix($permalink, '!^(https?:\/\/)?(www\.)?' . preg_quote('bing.com/news', '!') . '.*!');
}


/**
 * Checks if the permalink is a tracking permalink based on host, and if
 * it is, returns the normalized URL of the proper feed item article,
 * determined by the named query argument.
 *
 * Fixes the issue with equivalent Google News etc. items having
 * different URLs, that contain randomly generated GET parameters.
 * Example:
 *
 * http://news.google.com/news/url?sa=t&fd=R&ct2=us&ei=V3e9U6izMMnm1QaB1YHoDA&url=http://abcd...
 * http://news.google.com/news/url?sa=t&fd=R&ct2=us&ei=One9U-HQLsTp1Aal-oDQBQ&url=http://abcd...
 *
 * @param string $permalink The permalink URL to check and/or normalize.
 * @param string|array $patterns One or an array of host names, for which the URL should be fixed.
 * @param string Name of the query argument that specifies the actual URL.
 * @return string The normalized URL of the original article, as indicated by the `url`
 *					parameter in the URL query string.
 * @since 4.2.3
 */
function wprss_tracking_url_fix( $permalink, $patterns, $argName = 'url' ) {
    // Parse the url
    $parsed = parse_url( urldecode( html_entity_decode( $permalink ) ) );
    $patterns = is_array($patterns) ? $patterns :array($patterns);

    // If parsing failed, return the permalink
    if ( $parsed === FALSE || $parsed === NULL ) return $permalink;

    // Determine if it's a tracking item
    $isMatch = false;
    foreach( $patterns as $_idx => $_pattern ) {
        if( preg_match($_pattern, $permalink) ) {
        $isMatch = true;
        break;
        }
    }

    if( !$isMatch ) return $permalink;

    // Check if the url GET query string is present
    if ( !isset( $parsed['query'] ) ) return $permalink;

    // Parse the query string
    $query = array();
    parse_str( $parsed['query'], $query );

    // Check if the url GET parameter is present in the query string
    if ( !is_array($query) || !isset( $query[$argName] ) ) return $permalink;

    return urldecode( $query[$argName] );
}


/**
 * Converts YouTube, Vimeo and DailyMotion video urls
 * into embedded video player urls.
 * If the permalink is not a video url, the permalink is returned as is.
 *
 * @param	$permalink The string permalink url to convert.
 * @return	A string, with the convert permalink, or the same permalink passed as parameter if
 *			not a video url.
 * @since 4.0
 */
function wprss_convert_video_permalink( $permalink ) {
    // CHECK PERMALINK FOR VIDEO HOSTS : YOUTUBE, VIMEO AND DAILYMOTION
    $found_video_host = preg_match( '/http[s]?:\/\/(www\.)?(youtube|dailymotion|vimeo)\.com\/(.*)/i', $permalink, $matches );

    // If video host was found
    if ( $found_video_host !== 0 && $found_video_host !== FALSE ) {

        // Get general options
        $options = get_option( 'wprss_settings_general' );
        // Get the video link option entry, or false if it does not exist
        $video_link = ( isset($options['video_link']) )? $options['video_link'] : 'false';

        // If the video link option is true, change the video URL to its repective host's embedded
        // video player URL. Otherwise, leave the permalink as is.
        if ( strtolower( $video_link ) === 'true' ) {
            $host = $matches[2];
            switch( $host ) {
                case 'youtube':
                    preg_match( '/(&|\?)v=([^&]+)/', $permalink, $yt_matches );
                    $permalink = 'https://www.youtube.com/embed/' . $yt_matches[2];
                    break;
                case 'vimeo':
                    preg_match( '/(\d*)$/i', $permalink, $vim_matches );
                    $permalink = 'https://player.vimeo.com/video/' . $vim_matches[0];
                    break;
                case 'dailymotion':
                    preg_match( '/(\.com\/)(video\/)(.*)/i', $permalink, $dm_matches );
                    $permalink = 'https://www.dailymotion.com/embed/video/' . $dm_matches[3];
                    break;
            }
        }
    }

    return $permalink;
}


/**
 * Insert wprss_feed_item posts into the DB
 *
 * @since 3.0
 *
 * @param SimplePie_Item[] $items
 * @param int|string $feed_ID
 */
function wprss_items_insert_post( $items, $feed_ID ) {
    update_post_meta( $feed_ID, 'wprss_feed_is_updating', $update_started_at = time() );
    $logger = wpra_get_logger($feed_ID);

    // Gather the permalinks of existing feed item's related to this feed source
    $existing_permalinks = wprss_get_existing_permalinks( $feed_ID );

    // Count of items inserted
    $items_inserted = 0;

    foreach ( $items as $i => $item ) {

        // Normalize the URL
        $permalink = $item->get_permalink(); // Link or enclosure URL
        $permalink = wprss_normalize_permalink( $permalink, $item, $feed_ID );

        $logger->debug('Beginning import for item "{0}"', [$item->get_title()]);

        // Save the enclosure URL
        $enclosure_url = '';
        $enclosure = $item->get_enclosure(0);

        if ($enclosure && $enclosure->get_link()) {
            $enclosure_url = $enclosure->get_link();
        }

        // Check if newly fetched item already present in existing feed items,
        // if not insert it into wp_posts and insert post meta.
        if ( ! ( array_key_exists( $permalink, $existing_permalinks ) ) ) {
            // Extend the importing time and refresh the feed's updating flag to reflect that it is active
            $time_limit = wprss_get_item_import_time_limit();
            set_time_limit( $time_limit );

            global $wp_filter;
            if (isset($wp_filter['wprss_insert_post_item_conditionals'])) {
                $hook = $wp_filter['wprss_insert_post_item_conditionals'];

                if (count($hook->callbacks) > 0) {
                    $logger->debug('Hooks for `wprss_insert_post_item_conditionals`:');
                }

                foreach ($hook->callbacks as $list) {
                    foreach ($list as $callback) {
                        $logger->debug('-> {0}', [wprss_format_hook_callback($callback)]);
                    }
                }
            }

            $logger->debug('Checking conditionals ...');

            // Apply filters that determine if the feed item should be inserted into the DB or not.
            $ogItem = $item;
            $item = apply_filters( 'wprss_insert_post_item_conditionals', $item, $feed_ID, $permalink );
            /* @var $item SimplePie_Item */

            // Check if the imported count should still be updated, even if the item is NULL
            $still_update_count = apply_filters( 'wprss_still_update_import_count', FALSE );

            // If the item is not NULL, continue to inserting the feed item post into the DB
            if ( $item !== NULL && !is_bool($item) ) {
                $logger->debug('Resuming insertion into DB');

                $post_status = 'publish';

                // Get the date and GMT date and normalize if not valid or not given by the feed
                $format    = 'Y-m-d H:i:s';
                $timestamp = $item->get_date( 'U' );
                $has_date  = $timestamp ? true : false;

                if ($has_date) {
                    $logger->debug('Feed item "{0}" date: {1}', [$item->get_title(), $item->get_date($format)]);

                    if ($timestamp > time()) {
                        // Item has a future timestamp ...
                        $logger->debug('Item "{0}" has a future date', [$item->get_title()]);

                        $schedule_items_filter = apply_filters('wpra/importer/allow_scheduled_items', false);
                        $schedule_items_option = wprss_get_general_setting('schedule_future_items');

                        if ($schedule_items_filter || $schedule_items_option) {
                            // If can schedule future items, set the post status to "future" (aka scheduled)
                            $post_status = 'future';

                            $logger->debug('Setting future status');
                        } else {
                            // If cannot schedule future items, clamp the timestamp to the current time minus
                            // 1 second for each iteration done so far
                            $timestamp = min(time() - $i, $timestamp);

                            $logger->debug('Date was clamped to present time');
                        }
                    }
                } else {
                    // Item has no date ...
                    $logger->debug('Item "{0}" has no date. Using current time', [$item->get_title()]);
                    $timestamp = time();
                }

                $date     = date( $format, $timestamp );
                $date_gmt = gmdate( $format, $item->get_gmdate( 'U' ) );

                $logger->debug('Date for "{0}" will be {1}', [$item->get_title(), $date]);

                // Do not let WordPress sanitize the excerpt
                // WordPress sanitizes the excerpt because it's expected to be typed by a user and sent in a POST
                // request. However, our excerpt is being inserted as a raw string with custom sanitization.
                remove_all_filters( 'excerpt_save_pre' );

                $title = trim(html_entity_decode($item->get_title()));
                $title = empty($title) ? $item->get_id() : $title;

                // Prepare the item data
                $feed_item = apply_filters(
                    'wprss_populate_post_data',
                    array(
                        'post_title'     => $title,
                        'post_content'   => $item->get_content(),
                        'post_excerpt'   => wprss_sanitize_excerpt($item->get_description()),
                        'post_status'    => $post_status,
                        'post_type'      => 'wprss_feed_item',
                        'post_date'      => $date,
                        'post_date_gmt'  => $date_gmt
                    ),
                    $item
                );

                if ( defined('ICL_SITEPRESS_VERSION') )
                    @include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
                if ( defined('ICL_LANGUAGE_CODE') ) {
                    $_POST['icl_post_language'] = $language_code = ICL_LANGUAGE_CODE;
                }

                // Create and insert post object into the DB
                $inserted_ID = wp_insert_post( $feed_item );

                if ( !is_wp_error( $inserted_ID ) ) {

                    if ( is_object( $inserted_ID ) ) {
                        if ( isset( $inserted_ID['ID'] ) ) {
                            $inserted_ID = $inserted_ID['ID'];
                        }
                        elseif ( isset( $inserted_ID->ID ) ) {
                            $inserted_ID = $inserted_ID->ID;
                        }
                    }

                    $logger->debug('Item "{0}" was inserted into DB, ID: {1}', [
                        $ogItem->get_title(),
                        $inserted_ID,
                    ]);

                    // Create and insert post meta into the DB
                    wprss_items_insert_post_meta( $inserted_ID, $item, $feed_ID, $permalink );

                    $logger->debug('Inserted meta data for item #{0}', [
                        $inserted_ID,
                    ]);

                    // Remember newly added permalink
                    $existing_permalinks[$permalink] = 1;

                    // Increment the inserted items counter
                    $items_inserted++;

                    $logger->notice('Finished import for item {0}, ID {1}', [
                        $ogItem->get_title(),
                        $inserted_ID,
                    ]);
                }
                else {
                    update_post_meta( $feed_ID, 'wprss_error_last_import', 'An error occurred while inserting a feed item into the database.' );

                    $logger->error('Failed to save item "{0}" into the database', [$item->get_title()]);
                }
            }
            // If the item is TRUE, then a hook function in the filter inserted the item.
            // increment the inserted counter
            elseif ( ( is_bool($item) && $item === TRUE ) || ( $still_update_count === TRUE && $item !== FALSE ) ) {
                $logger->debug('Item "{0}" was imported by an add-on or filter', [
                    $ogItem->get_title(),
                ]);
                $items_inserted++;
            } elseif (has_filter('wprss_insert_post_item_conditionals', 'wprss_kf_check_post_item_keywords')) {
                $logger->info('Item "{0}" was rejected by your keyword or tag filtering.', [
                    $ogItem->get_title()
                ]);
            } else {
                $logger->notice('Item "{0}" was rejected by an add-on or filter.', [
                    $ogItem->get_title()
                ]);
            }
        }
    }

    update_post_meta( $feed_ID, 'wprss_last_update_items', $items_inserted );
}

/**
 * Inserts the appropriate post meta for feed items.
 *
 * Called from 'wprss_items_insert_post'
 *
 * @since 2.3
 *
 * @param int            $inserted_ID   The inserted post ID.
 * @param SimplePie_Item $item          The SimplePie item object.
 * @param string         $permalink     The item's permalink.
 */
function wprss_items_insert_post_meta( $inserted_ID, $item, $feed_ID, $permalink ) {
    update_post_meta( $inserted_ID, 'wprss_item_date', $item->get_date(DATE_ISO8601) );
    update_post_meta( $inserted_ID, 'wprss_item_permalink', $permalink );

    $enclosure = $item->get_enclosure(0);
    $enclosureUrl = $enclosure ? $enclosure->get_link() : null;

    if ($enclosureUrl) {
        $enclosureType = $enclosure->get_type();

        update_post_meta( $inserted_ID, 'wprss_item_enclosure', $enclosureUrl );
        update_post_meta( $inserted_ID, 'wprss_item_enclosure_type', $enclosureType );

        if (stripos($enclosureType, 'audio') !== false) {
            update_post_meta( $inserted_ID, 'wprss_item_audio', $enclosureUrl );
        }
    }

    /* @var $item SimplePie_Item */
    $feed = $item->get_feed();

    // Get the source from the RSS item
    $source = wprss_get_item_source_info($item);
    // Get the source name if available. If empty, default to the feed source CPT title
    $sourceName = empty($source->name) ? $feed->get_title() : $source->name;
    // Get the source URL if available. If empty, default to the RSS feed's URL
    $sourceUrl = empty($source->link) ? $feed->get_permalink() : $source->link;

    update_post_meta( $inserted_ID, 'wprss_item_source_name', $sourceName);
    update_post_meta( $inserted_ID, 'wprss_item_source_url', $sourceUrl);

    $author = $item->get_author();
    if ($author instanceof SimplePie_Author) {
        update_post_meta( $inserted_ID, 'wprss_item_author', $author->get_name() );
        update_post_meta( $inserted_ID, 'wprss_item_author_email', $author->get_email() );
        update_post_meta( $inserted_ID, 'wprss_item_author_link', $author->get_link() );
    }

    update_post_meta( $inserted_ID, 'wprss_feed_id', $feed_ID);
    do_action( 'wprss_items_create_post_meta', $inserted_ID, $item, $feed_ID );
}

/**
 * Gets the source info from a feed item.
 *
 * @param SimplePie_Item $item
 *
 * @return object An object with 2 properties: 'name' and 'link'.
 */
function wprss_get_item_source_info(SimplePie_Item $item)
{
    $source = $item->get_source();

    if ($source === null) {
        // Attempt to get RSS 2.0 <source>
        $rss2Source = $item->get_item_tags('', 'source');

        $rss2Source = is_array($rss2Source) ? $rss2Source : [];
        $name = isset($rss2Source[0]['data']) ? $rss2Source[0]['data'] : '';
        $link = isset($rss2Source[0]['attribs']['']['url']) ? $rss2Source[0]['attribs']['']['url'] : '';

        $source = (object) [
            'name' => $name,
            'link' => $link,
        ];
    } else {
        $source = (object) [
            'name' => $source->get_title(),
            'link' => $source->get_permalink(),
        ];
    }

    return $source;
}


/**
 * Returns the time limit for the importing of a single feed item.
 * The value if filtered through 'wprss_item_import_time_limit'. The default value is WPRSS_ITEM_IMPORT_TIME_LIMIT.
 *
 * @since 4.6.6
 * @return int The maximum amount of seconds allowed for a single feed item to import.
 */
function wprss_get_item_import_time_limit() {
    return apply_filters( 'wprss_item_import_time_limit', WPRSS_ITEM_IMPORT_TIME_LIMIT );
}

/**
 * Returns the time limit for a feed fetch operation.
 * The value if filtered through 'wprss_feed_fetch_time_limit'. The default value is WPRSS_FEED_FETCH_TIME_LIMIT.
 *
 * @since 4.6.6
 * @return int The maximum amount of seconds allowed for an RSS feed XML document to be fetched.
 */
function wprss_get_feed_fetch_time_limit() {
    return apply_filters( 'wprss_feed_fetch_time_limit', WPRSS_FEED_FETCH_TIME_LIMIT );
}


/**
 * Fetches all feed items from all feed sources.
 * Iteratively calls 'wprss_fetch_insert_single_feed_items' for all feed sources.
 *
 * This function is used by the cron job or the debugging functions to get all feeds from all feed sources
 *
 * @param $all  If set to TRUE, the function will pull from all feed sources, regardless of their individual
 *              update interval. If set to FALSE, only feed sources using the global update system will be updated.
 *              (Optional) Default: TRUE.
 * @since 3.0
 */
function wprss_fetch_insert_all_feed_items( $all = TRUE ) {
    wpra_get_logger()->info('Beginning import for all feed sources');
    // Get all feed sources
    $feed_sources = wprss_get_all_feed_sources();

    if( $feed_sources->have_posts() ) {
        // Start by getting one feed source, we will cycle through them one by one,
        // fetching feed items and adding them to the database in each pass
        while ( $feed_sources->have_posts() ) {
            $feed_sources->the_post();

            $interval = get_post_meta( get_the_ID(), 'wprss_update_interval', TRUE );
            $using_global_interval = ( $interval === wprss_get_default_feed_source_update_interval() || $interval === '' );

            // Check if fetching from all, or if feed source uses the global interval
            if ( $all === TRUE || $using_global_interval ) {
                wp_schedule_single_event( time(), 'wprss_fetch_single_feed_hook', array( get_the_ID() ) );
            }
        }
        wp_reset_postdata(); // Restore the $post global to the current post in the main query
    }
}


/**
 * Runs the above function with parameter FALSE
 *
 * @since 3.9
 */
function wprss_fetch_insert_all_feed_items_from_cron() {
    wprss_fetch_insert_all_feed_items( FALSE );
}


/**
 * Shutdown function for detecting if the PHP script reaches the maximum execution time limit
 * while importing a feed.
 *
 * @since 4.6.6
 */
function wprss_detect_exec_timeout() {
    global $wprss_importing_feed;
    $feed_ID = (isset($wprss_importing_feed) && !empty($wprss_importing_feed))
        ? $wprss_importing_feed
        : null;

    if ($feed_ID === null) {
        return;
    }

    // Remove the "importing" flag from the feed source
    wprss_flag_feed_as_idle($feed_ID);

    // If no error, stop
    $error = error_get_last();
    if (empty($error)) {
        return;
    }

    $errorDetails = $error['message'];
    if (!empty($error['file'])) {
        $errorDetails .= ' in ' . $error['file'];
    }
    if (!empty($error['line'])) {
        $errorDetails .= ' on line ' . $error['line'];
    }

    $msg = sprintf(
        __('The importing process failed after %d seconds with the message: "%s"', 'wprss'),
        wprss_get_item_import_time_limit(),
        $errorDetails
    );
    // Save the error in the feed source's meta and the plugin log
    update_post_meta($feed_ID, 'wprss_error_last_import', $msg);
    wpra_get_logger($feed_ID)->error($msg);
}

/**
 * Validates a feed item.
 *
 * @since 4.11.2
 *
 * @param \SimplePie_Item|mixed $item The item to validate.
 *
 * @return \SimplePie_Item|null The item, if it passes; otherwise, null.
 */
function wprss_item_filter_valid($item)
{
    return $item instanceof \SimplePie_Item
            ? $item
            : null;
}

/**
 * Sorts items according to settings.
 *
 * Use the `wprss_item_comparators` filter to change the list of comparators
 * used to determine the new order of items. See {@see wprss_items_sort_compare_items()}.
 *
 * @since 4.11.2
 *
 * @param \SimplePie_Item[] $items The items list.
 * @param \WP_Post $feedSource The feed source, for which to sort, if any.
 */
function wprss_sort_items(&$items, $feedSource = null)
{
    // Callbacks used to compare items
    $comparators = apply_filters('wprss_item_comparators', array());
    if (empty($comparators)) {
        return;
    }

    try {
        usort($items, function ($itemA, $itemB) use ($comparators, $feedSource) {
            return wprss_items_sort_compare_items($itemA, $itemB, $comparators, $feedSource);
        });
    } catch (\InvalidArgumentException $e) {
        wpra_get_logger($feedSource)->warning('Encountered an error while sorting the database items: {0}', [
            $e->getMessage()
        ]);
    }
}

/**
 * Recursively compares two items using a list of comparators.
 *
 * If a comparator determines that two items are equal, then the items are
 * evaluated using the next comparator in list, recursively until one of
 * the comparators establishes a difference between items, or the list of
 * comparators is exhausted.
 *
 * @since 4.11.2
 *
 * @param \SimplePie_Item|mixed $itemA The item being compared;
 * @param \SimplePie_Item|mixed $itemB The item being compared to;
 * @param callable[] $comparators A list of functions for item comparison.
 *
 * @return int A result usable as a return value for {@see usort()}.
 *
 * @throws \InvalidArgumentException If the comparator is not callable.
 */
function wprss_items_sort_compare_items($itemA, $itemB, $comparators, $feedSource = null)
{
    if (empty($comparators)) {
        return 0;
    }

    $comparator = array_shift($comparators);
    if (!is_callable($comparator)) {
        throw new \InvalidArgumentException('Comparator must be callable');
    }

    $result = call_user_func_array($comparator, array($itemA, $itemB, $feedSource));
    if (!$result) {
        return wprss_items_sort_compare_items($itemA, $itemB, $comparators);
    }

    return $result;
}

/**
 * Retrieves a custom field of a feed source, or a general setting if the field doesn't exist.
 *
 * @since 4.11.2
 *
 * @param string $key The key of the field or setting.
 * @param \WP_Post|null $feedSource The feed source, if any.
 * @return type
 */
function wprss_get_source_meta_or_setting($key, $feedSource = null)
{
    $value = null;
    if ($feedSource instanceof \WP_Post) {
        $value = $feedSource->{$key};
    }

    return $value !== null && $value !== false
            ? $value
            : wprss_get_general_setting($key);
}

/**
 * Determines date order of two feed items.
 *
 * Which should come first is determined by `feed_items_import_order` setting.
 *
 * @since 4.11.2
 *
 * @param \SimplePie_Item|mixed $itemA The first item.
 * @param \SimplePie_Item|mixed $itemB The second item.
 * @param \WP_Post|null $feedSource The feed source for which the items are being compared, if any.
 * @return int A comparison result for {@see usort()}.
 */
function wprss_item_comparator_date($itemA, $itemB, $feedSource = null)
{
    $sortOrder = wprss_get_source_meta_or_setting('feed_items_import_order', $feedSource);
    if (empty($sortOrder)) {
        return 0;
    }

    if (!wprss_item_filter_valid($itemA) || !wprss_item_filter_valid($itemB)) {
        return 0;
    }

    $aDate = intval($itemA->get_gmdate('U'));
    $bDate = intval($itemB->get_gmdate('U'));

    switch ($sortOrder) {
        case 'latest':
            if ($aDate === $bDate) {
                return null;
            }
            return $aDate > $bDate ? -1 : 1;
            break;

        case 'oldest':
            return $aDate < $bDate ? -1 : 1;
            break;

        case '':
        default:
            return 0;
            break;
    }
}

/**
 * Retrieves default comparators for sorting.
 *
 * @since 4.11.2
 *
 * @param \WP_Post|null $feedSource The feed source, for which to get comparators, if any.
 *
 * @return callable[] The list of comparators.
 */
function wprss_sort_comparators_default($feedSource = null)
{
    $helper = wprss_wp_container()->get('wprss.admin_helper');
    $defaultArgs = array(2 => $feedSource);
    return array(
        $helper->createCommand('wprss_item_comparator_date', $defaultArgs),
    );
}

/**
 * Sanitizes a post excerpt, cleverly removing all HTML markup while preserving text content and whitespace.
 *
 * @since 4.14
 *
 * @param string $excerpt The excerpt to sanitize.
 *
 * @return string
 */
function wprss_sanitize_excerpt($excerpt) {
    // Decode HTML entities back to their respective characters
    $excerpt = html_entity_decode($excerpt);
    // Add a space between any HTML elements
    $excerpt = str_replace('>', ' >', $excerpt);
    // Strip all HTML tags
    $excerpt = strip_tags($excerpt);
    // Remove any redundant spaces
    $excerpt = str_replace('  ', ' ', trim($excerpt));

    return $excerpt;
}

/**
 * Parses a URL, it's query and its path.
 *
 * @since 4.14
 *
 * @param string $url The URL to parse.
 *
 * @return array
 */
function wpra_parse_url($url)
{
    // Parse the URL
    $parsed = parse_url($url);

    // Move the path to "path_str"
    $parsed['path_str'] = isset($parsed['path']) ? $parsed['path'] : '';
    // Explode the path
    $parsed['path'] = explode('/', $parsed['path_str']);

    // Move the query to "query_str"
    $parsed['query_str'] = isset($parsed['query']) ? $parsed['query'] : '';
    // Parse the query
    parse_str($parsed['query_str'], $parsed['query']);

    return $parsed;
}
