<?php

add_action('plugins_loaded', function () {
    if (!class_exists('WPRSS_Help')) {
        return;
    }

    $help = WPRSS_Help::get_instance();

    // Feed source setting fields
    $prefix = 'setting-';
    $tooltips = [
        /* -----------------
         *  General Section
         * -----------------
         */
        // Limit feed items by age
        'limit-feed-items-by-age' => __(
            'The maximum age allowed for feed items.' .
            "\n<hr/>\n\n" .
            'Items already imported will be deleted if they eventually exceed this age limit.' .
            "\n\n" .
            'Also, items in the RSS feed that are already older than this age will not be imported at all.' .
            "\n<hr/>\n\n" .
            '<em>Leave empty for no limit.</em>',
            'wprss'
        ),
        // Limit feed items per feed
        'limit-feed-items-imported' => __(
            'The maximum number of imported items to keep stored, for feed sources that do not have their own limit.' .
            "\n<hr/>\n\n" .
            'When new items are imported and the limit for a feed source is exceeded, the oldest feed items for that feed source will be deleted to make room for the new ones.' .
            "\n\n" .
            'If you already have items imported from this feed source, setting this option now may delete some of your items, in order to comply with the limit.' .
            "\n<hr/>\n\n" .
            '<em>Use 0 or leave empty for no limit.</em>',
            'wprss'
        ),
        // Limit feed items per import
        'limit_feed_items_per_import' => __(
            'The maximum amount of items to process per import.' .
            "\n<hr/>\n\n" .
            'Will not process more than this amount of items every time the feed source updates, regardless of other settings.' .
            "\n\n" .
            'The frequency of updates is determined by the feed processing interval.' .
            "\n<hr/>\n\n" .
            '<em>Leave empty for no limit.</em>',
            'wprss'
        ),
        // Feed items import order
        'feed_items_import_order' => __(
            'The order in which feed items will be imported.' .
            "\n<hr />\n\n" .
            '<strong>Latest items first</strong> will import the most recent items in the feed first.' .
            "\n" .
            '<strong>Oldest items first</strong> will import the oldest items in the feed first.' .
            "\n" .
            '<strong>Original feed order</strong> only works well on PHP7 or later.' .
            "\n\n" .
            'This setting is very useful in combination with the per-import limit.' .
            "\n<hr />\n\n" .
            'Default: <em>Latest items first</em>',
            'wprss'
        ),
        // Schedule future items
        'schedule_future_items' => __(
            'If ticked, items with future dates will be scheduled to be published later. Leave unticked to always publish items immediately.' .
            "\n<hr/>\n\n" .
            'Default: <em>Off</em>',
            'wprss'
        ),
        // Feed processing interval
        'cron-interval' => __(
            'How frequently should the feed sources (that do not have their own update interval) check for updates and fetch items accordingly.' .
            "\n\n" .
            'It is recommended to not have more than 20 feed sources that use this global update interval. Having too many feed sources updating precisely at the same time can cause the WP Cron System to crash.',
            'wprss'),
        // Unique titles only
        'unique-titles' => __(
            'Whether to allow multiple feed items to have the same title. When checked, if a feed item has the same title as a previously-imported feed item from any feed source, it will not be imported.' .
            "\n\n" .
            'This can be useful in cases where permalinks change, or where multiple permalinks refer to the same item.' .
            "\n\n" .
            'Since this feature requires checking every post title, WordPress installs with a significant amount of posts may notice a slight slowdown of the post import process.',
            'wprss'
        ),
        // Custom Feed URL
        'custom-feed-url' => __(
            'The URL of the custom feed, located at <code>https://yoursite.com/[custom feed url]</code>.' .
            "\n<hr/>\n\n" .

            'WP RSS Aggregator allows you to create a custom RSS feed, that contains all of your imported feed items. This setting allows you to change the URL of this custom feed.' .
            "\n\n<hr/>\n\n" .
            '<strong>Note:</strong> You may be required to refresh you Permalinks after you change this setting, by going to <em>Settings <i class="fa fa-angle-right"></i> Permalinks</e> and clicking <em>Save</em>.',
            'wprss'
        ),
        // Custom Feed Title
        'custom-feed-title' => __(
            'The title of the custom feed.' .
            "\n\n" .
            'This title will be included in the RSS source of the custom feed, in a <code>&lt;title&gt;</code> tag.',
            'wprss'
        ),
        // Custom Feed Limit
        'custom-feed-limit' => __('The maximum number of feed items in the custom feed.', 'wprss'),

        /* --------------------------
         *  General Display Settings
         * --------------------------
         */
        // Link titles
        'link-enable' => __('Check this box to make the feed item titles link to the original article.', 'wprss'),
        // Title Maximum length
        'title-limit' => __(
            'Set the maximum number of characters to show for feed item titles.' .
            "\n<hr/>\n\n" .
            '<em>Leave empty for no limit.</em>',
            'wprss'
        ),
        // Show Authors
        'authors-enable' => __('Check this box to show the author for each feed item, if it is available.', 'wprss'),
        // Video Links
        'video-links' => __(
            'For feed items from YouTube, Vimeo or Dailymotion, you can choose whether you want to have the items link to the original page link, or a link to the embedded video player only.',
            'wprss'
        ),
        // Pagination Type
        'pagination' => __(
            'The type of pagination to use when showing feed items on multiple pages.' .
            "\n\n" .
            'The first shows two links, "Older" and "Newer", which allow you to navigate through the pages.' .
            "\n\n" .
            'The second shows links for all the pages, together with links for the next and previous pages.',
            'wprss'
        ),
        // Feed Limit
        'feed-limit' => __(
            'The maximum number of feed items to display when using the shortcode.' .
            "\n\n" .
            'This enables pagination if set to a number smaller than the number of items to be displayed.',
            'wprss'
        ),
        // Open Links Behaviour
        'open-dd' => __(
            'Choose how you want links to be opened. This applies to the feed item title and the source link.',
            'wprss'
        ),
        // Set links as no follow
        'follow-dd' => __(
            'Enable this option to set all links displayed as "NoFollow".' .
            "\n<hr/>\n\n" .
            '"Nofollow" provides a way to tell search engines to <em>not</em> follow certain links, such as links to feed items in this case.',
            'wprss'
        ),

        /* -------------------------
         *  Source Display Settings
         * -------------------------
         */ // Source Enabled
        'source-enable' => __('Enable this option to show the feed source name for each feed item.', 'wprss'),
        // Text preceding source
        'text-preceding-source' => __(
            'Enter the text that you want to show before the source name. A space is automatically added between this text and the feed source name.',
            'wprss'
        ),
        // Source Link
        'source-link' => __('Enable this option to link the feed source name to the RSS feed\'s source site.', 'wprss'),

        /* -------------------------
         *  Date Display Settings
         * -------------------------
         */ // Source Enabled
        'date-enable' => __('Enable this to show the feed item\'s date.', 'wprss'),
        // Text preceding date
        'text-preceding-date' => __(
            'Enter the text that you want to show before the feed item date. A space is automatically added between this text and the date.',
            'wprss'
        ),
        // Date Format
        'date-format' => __('The format to use for the feed item dates, as a PHP date format.', 'wprss'),
        // Time Ago Format Enable
        'time-ago-format-enable' => __(
            'Enable this option to show the elapsed time from the feed item\'s date and time to the present time.' .
            "\n" .
            '<em>Eg. 2 hours ago</em>',
            'wprss'
        ),

        /* --------
         *  Styles
         * --------
         */
        // Styles Disable
        'styles-disable' => __(
            'Check this box to disable all plugin styles used for displaying feed items.' .
            "\n\n" .
            'This will allow you to provide your own custom CSS styles for displaying the feed items.',
            'wprss'
        ),

        /*
         * -------
         *  Other
         * -------
         */
        // Certificate Path
        'certificate-path' => __(
            'Path to the file containing one or more certificates.' .
            "\n\n" .
            'These will be used to verify certificates over secure connection, such as when fetching a remote resource over HTTPS.' .
            "\n\n" .
            'Relative path will be relative to the WordPress root.' .
            "\n\n" .
            '<strong>Default:</strong> path to certificate file bundled with WordPress.',
            'wprss'
        ),

        /** @since 4.8.2 */
        'feed_request_useragent' => __(
            'The user agent string that WP RSS Aggregator uses for feed requests.' .
            "\n\n" .
            'You should leave this blank. Only change it if you know what you\'re doing.' .
            "\n<hr/>\n\n" .
            '<strong>Important:</strong> Do not use this option to circumvent any security mechanisms that an RSS feed server may have put in place. Servers reserve the right to block you or WP RSS Aggregator.' .
            "\n\n" .
            'Attempting to bypass such blocks may result in legal action being taken against you. WP RSS Aggregator will not be held liable for misuse of this setting.',
            'wprss'
        ),
    ];

    $help->add_tooltips($tooltips, $prefix);

    // Feed source specific
    $prefix = 'field_';
    $help->add_tooltips([
        WPRSS_Feed_Access::SETTING_KEY_FEED_REQUEST_USERAGENT => $tooltips['feed_request_useragent'],
    ], $prefix);
}, 11);
