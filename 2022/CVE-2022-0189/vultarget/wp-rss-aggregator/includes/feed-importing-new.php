<?php

namespace RebelCode\Wpra\Core\Importer;

use SimplePie_Item;
use WP_Post;

/**
 * Fetches new items from a feed.
 *
 * @since [*next-version*]
 *
 * @param int|string $feedId The ID of the feed source.
 *
 * @return SimplePie_Item[]
 */
function fetchNewItems($feedId)
{
    $feed = get_post($feedId);
    $logger = wpra_get_logger($feedId);

    if ($feed instanceof WP_Post) {
        $logger->info('Fetching items');
    } else {
        $logger->error('Feed source #{0} does not exist', [$feedId]);
    }

    // Filter the URL
    $feedUrl = apply_filters('wprss_feed_source_url', $feed->wprss_url, $feedId);
    if ($feedUrl !== $feed->wprss_url) {
        $logger->debug('Filtered RSS URL: {0}', [$feedUrl]);
    }

    // Validate the URL
    if (!wprss_validate_url($feedUrl)) {
        $logger->error('Feed URL is not valid!');

        return [];
    }

    // Get the feed items from the source
    $items = wprss_get_feed_items($feedUrl, $feedId);
    $items = (empty($items) || !is_array($items)) ? [] : $items;

    // See `wprss_item_comparators` filter
    wprss_sort_items($items);

    // Apply fixed limit
    {
        $feedLimit = $feed->wprss_limit;
        $globalLimit = wprss_get_general_setting('limit_feed_items_imported');
        $filteredLimits = array_filter([$feedLimit, $globalLimit], function ($limit) {
            return !empty($limit) && intval($limit) > 0;
        });
        $limit = (int) reset($filteredLimits);

        if ($limit > 0) {
            $preLimitCount = count($items);
            $items = array_slice($items, 0, $limit);

            $logger->debug('{0} items in the feed, {1} items after applying limit', [
                $preLimitCount,
                $limit,
            ]);
        }
    }

    // Process "unique titles only" option
    {
        $feedUto = $feed->wprss_unique_titles;
        $globalUto = wprss_get_general_setting('unique_titles');
        $uto = empty($feedUto) ? $globalUto : $feedUto;
        $uto = filter_var($uto, FILTER_VALIDATE_BOOLEAN);
    }

    $items = apply_filters('wpra/importer/items/before_filters', $items, $feed, $logger);

    // Gather the titles and permalinks of the items that are being fetched
    $existingTitles = [];
    $existingPermalinks = wprss_get_existing_permalinks($feedId);

    $newItems = [];
    foreach ($items as $item) {
        // Filter the item
        $item = apply_filters('wpra/importer/item', $item, $feed, $logger);

        $title = $item->get_title();
        $permalink = wprss_normalize_permalink($item->get_permalink(), $item, $feedId);

        // Check if already imported
        if (array_key_exists($permalink, $existingPermalinks)) {
            $logger->debug('Skipping item "{0}": already imported', [$title]);

            continue;
        }

        // Check if blacklisted
        if (wprss_is_blacklisted($permalink)) {
            $logger->debug('Skipping item "{0}": blacklisted', [$title]);

            continue;
        }

        // Check if title exists
        if ($uto) {
            $existingTitles[$title] = 1;

            if (wprss_item_title_exists($title) || array_key_exists($title, $existingTitles)) {
                $logger->debug('Skipping item "{0}": title is not unique', [$title]);

                continue;
            }
        }

        if (apply_filters('wpra/importer/filter_item', true, $item, $feed, $logger)) {
            $newItems[] = $item;
        }
    }

    $items = apply_filters('wpra/importer/items/after_filters', $items, $feed, $logger);

    $numOgItems = count($items);
    $numNewItems = count($newItems);
    if ($numOgItems !== $numNewItems) {
        $logger->debug('{0} items were skipped', [$numOgItems - $numNewItems]);
    }

    // Apply the "per-import" limit
    $importLimit = (int) wprss_get_general_setting('limit_feed_items_per_import');
    if ($importLimit > 0) {
        $items = array_slice($items, 0, $importLimit);
    }

    return $items;
}

/**
 * Imports a feed item.
 *
 * @since [*next-version*]
 *
 * @param SimplePie_Item $item   The item to import.
 * @param int|string     $feedId The ID of the feed source.
 *
 * @return bool True if the item was imported successfully, false if importing failed.
 */
function importItem(SimplePie_Item $item, $feedId)
{
    $logger = wpra_get_logger($feedId);
    $logger->debug('Importing item "{0}"', [$item->get_title()]);

    set_time_limit(wprss_get_item_import_time_limit());

    $lcc = legacyConditionalCheck($item, $feedId);
    $item = $lcc[0];
    $import = $lcc[1];

    // If item should not be imported,
    // -> return true if the item was imported by other means (not null)
    // -> return false if the item was blocked (is null)
    if (!$import) {
        return $item !== null;
    }

    $title = trim(html_entity_decode($item->get_title()));
    $title = empty($title) ? $item->get_id() : $title;

    $enclosure = $item->get_enclosure();
    $enclosureUrl = $enclosure ? $enclosure->get_link() : null;

    $permalink = htmlspecialchars_decode($item->get_permalink());
    $permalink = wprss_normalize_permalink($permalink, $item, $feedId);

    $dates = getImportDates($item, $feedId);
    $date = $dates[0];
    $dateGmt = $dates[1];
    $isFuture = $dates[2];
    $status = $isFuture ? 'future' : 'publish';

    $excerpt = wprss_sanitize_excerpt($item->get_description());
    $content = $item->get_content();

    $source = getSourceInfo($item);
    $author = getAuthorInfo($item);

    // Do not let WordPress sanitize the excerpt
    // WordPress sanitizes the excerpt because it's expected to be typed by a user and sent in a POST
    // request. However, our excerpt is being inserted as a raw string with custom sanitization.
    remove_all_filters('excerpt_save_pre');

    $postData = apply_filters(
        'wprss_populate_post_data',
        [
            'post_title' => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_status' => $status,
            'post_type' => 'wprss_feed_item',
            'post_date' => $date,
            'post_date_gmt' => $dateGmt,
            'meta_input' => [
                'wprss_feed_id' => $feedId,
                'wprss_item_date' => $item->get_date(DATE_ISO8601),
                'wprss_item_permalink' => $permalink,
                'wprss_item_enclosure' => $enclosureUrl,
                'wprss_item_source_name' => $source[0],
                'wprss_item_source_url' => $source[1],
                'wprss_item_author' => $author[0],
                'wprss_item_author_email' => $author[1],
                'wprss_item_author_link' => $author[2],
            ],
        ],
        $item
    );

    $postData = apply_filters('wpra/importer/item/post_data', $postData, $item, $feedId);

    if (defined('ICL_SITEPRESS_VERSION')) {
        @include_once(WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php');
    }
    if (defined('ICL_LANGUAGE_CODE')) {
        $_POST['icl_post_language'] = $language_code = ICL_LANGUAGE_CODE;
    }

    // Create and insert post object into the DB
    $postId = wp_insert_post($postData);

    if (is_wp_error($postId)) {
        update_post_meta(
            $feedId,
            'wprss_error_last_import',
            'An error occurred while inserting a feed item into the database.'
        );

        $logger->error('Failed to insert item into the database');

        return false;
    }

    do_action('wprss_items_create_post_meta', $postId, $item, $feedId);
    do_action('wpra/importer/item/inserted', $item, $feedId);

    $logger->notice('Imported item {0}. ID: {1}', [$title, $postId]);

    return true;
}

/**
 * Runs the legacy "post item conditionals" filter for a feed item.
 *
 * @since [*next-version*]
 *
 * @param SimplePie_Item $item   The item.
 * @param int|string     $feedId The ID of the feed source.
 *
 * @return array An array with 2 elements:
 *               1. the filtered item, or null if the item was rejected.
 *               2. a boolean that signifies whether the item should be imported or not.
 *               A return value containing a non-null item and a false boolean signifies a non-rejected item, but that
 *               should still not be imported because it was imported using a non-Core mechanism (ex. Feed to Post).
 */
function legacyConditionalCheck(SimplePie_Item $item, $feedId)
{
    $logger = wpra_get_logger($feedId);

    // Log the callbacks that are hooked into the filter
    $condCallbacks = wpra_get_hook_callbacks('wprss_insert_post_item_conditionals');
    if (count($condCallbacks) > 0) {
        $logger->debug('Hooks for `wprss_insert_post_item_conditionals`:');

        foreach ($condCallbacks as $callback) {
            $logger->debug('-> {0}', [wprss_format_hook_callback($callback)]);
        }
    }

    $title = $item->get_title();
    $permalink = htmlspecialchars_decode($item->get_permalink());
    $permalink = wprss_normalize_permalink($permalink, $item, $feedId);

    $preItem = $item;
    $postItem = apply_filters('wprss_insert_post_item_conditionals', $item, $feedId, $permalink);
    $updateCount = apply_filters('wprss_still_update_import_count', false);

    if (is_bool($postItem) || $postItem === null) {
        // Item is TRUE, or it's FALSE/NULL but it still counts
        if ($postItem || $updateCount) {
            $logger->debug('Item "{0}" was imported by an add-on or filter', [$title]);

            return [$preItem, false];
        }

        // Item was filtered
        if (has_filter('wprss_insert_post_item_conditionals', 'wprss_kf_check_post_item_keywords')) {
            $logger->info('Item "{0}" was rejected by your keyword or tag filtering.', [$title]);
        } else {
            $logger->notice('Item "{0}" was rejected by an add-on or filter.', [$title]);
        }

        return [null, false];
    } else {
        return [$postItem, true];
    }
}

function getImportDates(SimplePie_Item $item, $feedId)
{
    $logger = wpra_get_logger($feedId);

    $dateFormat = 'Y-m-d H:i:s';
    $timestamp = $item->get_gmdate('U');
    $isFuture = false;

    if ($timestamp) {
        if ($timestamp > time()) {
            $scheduleItemsFilter = apply_filters('wpra/importer/allow_scheduled_items', false);
            $scheduleItemsOption = wprss_get_general_setting('schedule_future_items');

            if ($scheduleItemsFilter || $scheduleItemsOption) {
                // If can schedule future items, set the post status to "future" (aka scheduled)
                $isFuture = true;
                $logger->debug('Setting future status due to future date');
            } else {
                // If cannot schedule future items, clamp the timestamp to the current time
                $timestamp = min(time(), $timestamp);
                $logger->debug('Date clamped to present time');
            }
        }
    } else {
        // Item has no date ...
        $logger->debug('Item has no date. Using current time');
        $timestamp = time();
    }

    $date = $item->get_date($dateFormat);
    $dateGmt = gmdate($dateFormat, $timestamp);

    return [$date, $dateGmt, $isFuture];
}

function getSourceInfo(SimplePie_Item $item)
{
    /* @var $item SimplePie_Item */
    $feed = $item->get_feed();

    // Get the source from the RSS item
    $source = $item->get_source();

    // Get the source name if available. If empty, default to the feed source CPT title
    $name = ($source === null) ? '' : $source->get_title();
    $name = empty($name) ? $feed->get_title() : $name;

    // Get the source URL if available. If empty, default to the RSS feed's URL
    $url = ($source === null) ? '' : $source->get_permalink();
    $url = empty($url) ? $feed->get_permalink() : $url;

    return [$name, $url];
}

function getAuthorInfo(SimplePie_Item $item)
{
    $author = $item->get_author();

    if ($author) {
        $name = $author->get_name();
        $email = $author->get_email();
        $link = $author->get_link();
    } else {
        $name = '';
        $email = '';
        $link = '';
    }

    return [$name, $email, $link];
}

function getExcessItems($feedId, $add = 0)
{
    $limit = getLimit($feedId);

    if ($limit <= 0) {
        return 0;
    }

    // Get existing items
    $dbItems = wprss_get_feed_items_for_source($feedId);
    $numDbItems = $dbItems->post_count + $add;

    if ($numDbItems <= $limit) {
        return 0;
    }

    $numExcess = $numDbItems - $limit;

    return array_slice(array_reverse($dbItems->posts), 0, $numExcess);
}

function getLimit($feedId)
{
    $feedLimit = get_post_meta($feedId, 'wprss_limit', true);
    $globalLimit = wprss_get_general_setting('limit_feed_items_imported');

    $filteredLimits = array_filter([$feedLimit, $globalLimit], function ($limit) {
        return !empty($limit) && intval($limit) > 0;
    });

    return (int) reset($filteredLimits);
}
