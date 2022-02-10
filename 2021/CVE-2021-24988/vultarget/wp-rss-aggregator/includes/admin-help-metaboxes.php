<?php

add_action('plugins_loaded', function () {
    if (!class_exists('WPRSS_Help')) {
        return;
    }

    $help = WPRSS_Help::get_instance();;

    // Feed source setting fields
    $prefix = 'field_';
    $tooltips = [
        /* -----------------------------
         *  Feed Source Details Metabox
         * -----------------------------
         */
        // Feed Source URL
        'wprss_url' => __(
            "The URL of the feed source. In most cases, the URL of the site will also work, but for best results we recommend trying to find the URL of the RSS feed." .
            "\n\n" .
            "Also include the <code>http://</code> or <code>https://</code> prefix in the URL.",
            'wprss'
        ),
        // Feed limit
        'wprss_limit' => __(
            'The maximum number of imported items from this feed to keep stored.' .
            "\n\n" .
            'When new items are imported and the limit is exceeded, the oldest feed items will be deleted to make room for new ones.' .
            "\n\n" .
            'If you already have items imported from this feed source, setting this option now may delete some of your items, in order to comply with the limit.',
            'wprss'
        ),
        // Link to Enclosure
        'wprss_enclosure' => __(
            'Tick this box to make imported items link to their enclosure, rather than to the original article.' .
            "\n\n" .
            'Enclosures are typically links to attachments, such as images, audio, videos, documents or flash content.' .
            "\n\n" .
            'If you are not sure, leave this option unticked.',
            'wprss'
        ),

        'wprss_unique_titles' => __(
            'Whether to allow multiple feed items to have the same title. When checked, if a feed item has the same title as a previously-imported feed item, it will not be imported.' .
            "\n\n" .
            'This can be useful in cases where permalinks change, or where multiple permalinks refer to the same item.',
            'wprss'
        ),

        'wprss_source_link' => __(
            'Enable this option to link the feed source name to the RSS feed\'s source site.' .
            "\n\n" .
            'Selecting "Default" will cause the value chosen in the general Source Display Settings to be used.' .
            "\n\n" .
            'This option only applies when using the shortcode to output feed items.',
            'wprss'
        ),

        'wprss_import_source' => __(
            'Tick this box to get the site name and URL from the RSS feed, for each item individually.' .
            "\n\n" .
            'This option is useful when importing from aggregated RSS feeds that have items from different sources.' .
            "\n\n" .
            'If the RSS feed does not provide source information for its items, the name and URL that you have given for the feed source will be used instead.',
            'wprss'
        ),

        /* -------------------------
         *  Feed Processing Metabox
         * -------------------------
         */
        // Feed State
        'wprss_state' => __(
            'State of the feed, active or paused.' .
            "\n\n" .
            'If active, the feed source will fetch items periodically, according to the settings below.' .
            "\n\n" .
            'If paused, the feed source will not fetch feed items periodically.',
            'wprss'
        ),

        // Activate Feed: [date]
        'wprss_activate_feed' => __(
            'You can set a time, in UTC, in the future when the feed source will become active, if it is paused.' .
            "\n\n" .
            'Leave blank to activate immediately.',
            'wprss'
        ),

        // Pause Feed: [date]
        'wprss_pause_feed' => __(
            'You can set a time, in UTC, in the future when the feed source will become paused, if it is active.' .
            "\n\n" .
            'Leave blank to never pause.',
            'wprss'
        ),

        // Update Interval
        'wprss_update_interval' => __(
            'How frequently the feed source should check for new items and fetch if needed.' .
            "\n\n" .
            'If left as <em>Default</em>, the interval in the global settings is used.',
            'wprss'
        ),

        // Delete items older than: [date]
        'wprss_age_limit' => __(
            'The maximum age allowed for feed items. Very useful if you are only concerned with, say, last week\'s news.' .
            "\n\n" .
            'Items already imported will be deleted if they eventually exceed this age limit.' .
            "\n\n" .
            'Also, items in the RSS feed that are already older than this age will not be imported at all.' .
            "\n\n" .
            'Leave empty to use the <em>Limit feed items by age</em> option in the general settings.',
            'wprss'
        ),

        /* ----------------------
         *  Feed Preview Metabox
         * ----------------------
         */
        // Force Feed
        'wprss_force_feed' => __(
            'Use this option if you are seeing an <q>Invalid feed URL</q> error in the Feed Preview above, but you are sure that the URL is correct.' .
            "\n\n" .
            'Note, however, that this will disable auto-discovery, meaning that the given URL must be an RSS feed URL. Using the site\'s URL will not work.',
            'wprss'
        ),
    ];

    $help->add_tooltips($tooltips, $prefix);
}, 11);
