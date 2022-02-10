<?php
/**
 * Feed display related functions
 *
 * @package WPRSSAggregator
 */

/**
 * Display template for a feed source. Simulates a shortcode call.
 *
 * @since 4.6.6
 * @deprecated 4.13 This function was left here because the ET addon references it.
 */
function wprss_render_feed_view($content)
{
    return $content;
}

/**
 * Display template for a feed source. Simulates a shortcode call.
 *
 * @since 4.6.6
 * @deprecated 4.13 This function was left here because the ET addon references it.
 */
function wprss_render_feed_item_view($content)
{
    return $content;
}

/**
 * Redirects to wprss_display_feed_items
 * It is used for backwards compatibility to versions < 2.0
 *
 * @since 2.1
 */
function wp_rss_aggregator($args = [])
{
    $template = wpra_get('feeds/templates/master_template');
    $fullArgs = $args;

    // Use legacy mode if arg was not explicitly given
    if (!isset($fullArgs['legacy'])) {
        $fullArgs['legacy'] = true;
    }

    return $template->render($args);
}

/**
 * Handles the display for a single feed item.
 *
 * @since 4.6.6
 */
function wprss_display_single_feed_item($atts = [])
{
    if (empty($atts)) {
        return '';
    }

    $id = empty($atts['id']) ? false : $atts['id'];
    if ($id === false || get_post_type($id) !== 'wprss_feed_item' || ($item = get_post($id)) === false) {
        return '';
    }

    //Enqueue scripts / styles
    wp_enqueue_script('jquery.colorbox-min', WPRSS_JS . 'jquery.colorbox-min.js', ['jquery']);
    wp_enqueue_script('wprss_custom', WPRSS_JS . 'custom.js', ['jquery', 'jquery.colorbox-min']);

    setup_postdata($item);
    $output = wprss_render_feed_item($id);
    $output = apply_filters('wprss_shortcode_single_output', $output);
    wp_reset_postdata();

    return $output;
}

/**
 * Renders a single feed item.
 *
 * @since 4.6.6
 *
 * @param string $default The default text to return if something fails.
 * @param int $ID The ID of the feed item to render
 *
 * @return string The output
 */
function wprss_render_feed_item($ID = null, $default = '', $args = [])
{
    $ID = ($ID === null)
        ? get_the_ID()
        : $ID;

    if (is_feed()) {
        return $default;
    }

    // Prepare the options
    $generalSettings = get_option('wprss_settings_general');
    $displaySettings = wprss_get_display_settings($generalSettings);
    $excerptsSettings = get_option('wprss_settings_excerpts');
    $thumbnailsSettings = get_option('wprss_settings_thumbnails');

    $args = wp_parse_args($args, [
        'link_before' => '',
        'link_after' => '',
    ]);
    $extraOptions = apply_filters('wprss_template_extra_options', [], $args);

    // Declare each item in $args as its own variable
    $beforeLink = $args['link_before'];
    $afterLink = $args['link_after'];
    extract($args, EXTR_SKIP);

    // Get the item meta
    $permalink = get_post_meta($ID, 'wprss_item_permalink', true);
    $enclosure = get_post_meta($ID, 'wprss_item_enclosure', true);
    $feedId = get_post_meta($ID, 'wprss_feed_id', true);
    $linkToEnclosure = get_post_meta($feedId, 'wprss_enclosure', true);
    $feedName = get_the_title($feedId);
    $siteUrl = get_post_meta($feedId, 'wprss_site_url', true);
    $timestamp = get_the_time('U', $ID);

    $linkTitleSetting = wprss_get_general_setting('title_link');

    $linkSourceSetting = isset($generalSettings['source_link'])
        ? $generalSettings['source_link']
        : 0;
    $linkSourceMeta = get_post_meta($feedId, 'wprss_source_link', true);
    $linkSource = empty($linkSourceMeta)
        ? $linkSourceSetting
        : $linkSourceMeta;
    $linkSource = intval(trim($linkSource));

    // Fallback for feeds created with older versions of the plugin
    if ($siteUrl === '') {
        $siteUrl = get_post_meta($feedId, 'wprss_url', true);
    }
    // convert from Unix timestamp
    $date = wprss_date_i18n($timestamp);

    // Prepare the title
    $itemTitle = get_the_title();
    $itemTitle = wprss_shorten_title($itemTitle, $ID);
    $itemTitleUrl = ($linkToEnclosure === 'true' && $enclosure !== '') ? $enclosure : $permalink;

    // Prepare the text that precedes the source
    $sourcePrefix = wprss_get_general_setting('text_preceding_source');
    $sourcePrefix = ltrim(__($sourcePrefix, 'wprss') . ' ');

    $datePrefix = wprss_get_general_setting('text_preceding_date');
    $datePrefix = ltrim(__($datePrefix, 'wprss') . ' ');

    do_action('wprss_get_post_data');

    $meta = $extraOptions;
    $extraMeta = apply_filters('wprss_template_extra_meta', $meta, $args, $ID);

    ///////////////////////////////////////////////////////////////
    // BEGIN TEMPLATE

    // Prepare the output
    $output = '';
    // Begin output buffering
    ob_start();
    // Print the links before
    echo $beforeLink;

    // The Title
    $titleHtml = wprss_link_display($itemTitleUrl, $itemTitle, $linkTitleSetting);
    $titleHtml = apply_filters('wprss_item_title', $titleHtml, $itemTitleUrl, $itemTitle, $linkTitleSetting);
    echo $titleHtml;

    do_action('wprss_after_feed_item_title', $extraMeta, $displaySettings, $ID);

    // FEED ITEM META
    echo '<div class="wprss-feed-meta">';

    // SOURCE
    if (wprss_get_general_setting('source_enable') == 1) {
        echo '<span class="feed-source">';
        $sourceLinkText = apply_filters('wprss_item_source_link', wprss_link_display($siteUrl, $feedName, $linkSource));
        $sourceLinkText = $sourcePrefix . $sourceLinkText;
        echo $sourceLinkText;
        echo '</span>';
    }

    // DATE
    if (wprss_get_general_setting('date_enable') == 1) {
        echo '<span class="feed-date">';
        $dateText = apply_filters('wprss_item_date', $date);
        $dateText = $datePrefix . $dateText;
        echo esc_html($dateText);
        echo '</span>';
    }

    // AUTHOR
    $author = get_post_meta($ID, 'wprss_item_author', true);
    if (wprss_get_general_setting('authors_enable') == 1 && $author !== null && is_string($author) && $author !== '') {
        echo '<span class="feed-author">';
        $authorText = apply_filters('wprss_item_author', $author);
        echo apply_filters(
            'wprss_author_prefix_text',
            _x('By', 'Text before author name. Example: "By John Smith" ', 'wprss')
        );
        echo ' ' . esc_html($authorText);
        echo '</span>';
    }

    echo '</div>';

    // TIME AGO
    if (wprss_get_general_setting('date_enable') == 1 && wprss_get_general_setting('time_ago_format_enable') == 1) {
        $timeAgo = human_time_diff($timestamp, time());
        echo '<div class="wprss-time-ago">';
        $timeAgoText = apply_filters('wprss_item_time_ago', $timeAgo);
        printf(_x('%1$s ago', 'Time ago', 'wprss'), $timeAgoText);
        echo '</div>';
    }

    // END TEMPLATE - Retrieve buffered output
    $output .= ob_get_clean();
    $output = apply_filters('wprss_single_feed_output', $output, $permalink);
    $output .= $afterLink;

    // Print the output
    return $output;
}

/**
 * Retrieve settings and prepare them for use in the display function
 *
 * @since 3.0
 */
function wprss_get_display_settings($settings = null)
{
    if ($settings === null) {
        $settings = get_option('wprss_settings_general');
    }
    // Parse the arguments together with their default values
    $args = wp_parse_args(
        $settings,
        [
            'open_dd' => 'blank',
            'follow_dd' => '',
        ]
    );

    // Prepare the 'open' setting - how to open links for feed items
    $open = '';
    switch ($args['open_dd']) {
        case 'lightbox' :
            $open = 'class="colorbox"';
            break;
        case 'blank' :
            $open = 'target="_blank"';
            break;
    }

    // Prepare the 'follow' setting - whether links marked as nofollow or not
    $follow = ($args['follow_dd'] == 'no_follow') ? 'rel="nofollow"' : '';

    // Prepare the final settings array
    $display_settings = [
        'open' => $open,
        'follow' => $follow,
    ];

    do_action('wprss_get_settings');

    return $display_settings;
}

/**
 * Merges the default arguments with the user set arguments
 *
 * @since 3.0
 */
function wprss_get_shortcode_default_args($args)
{
    // Default shortcode/function arguments for displaying feed items
    $shortcode_args = apply_filters(
        'wprss_shortcode_args',
        [
            'links_before' => '<ul class="rss-aggregator">',
            'links_after' => '</ul>',
            'link_before' => '<li class="feed-item">',
            'link_after' => '</li>',
        ]
    );

    // Parse incoming $args into an array and merge it with $shortcode_args
    return wp_parse_args($args, $shortcode_args);
}

/**
 * Prepares and builds the query for fetching the feed items
 *
 * @since 3.0
 */
function wprss_get_feed_items_query($settings)
{
    if (isset($settings['feed_limit'])) {
        $posts_per_page = $settings['feed_limit'];
    } else {
        $posts_per_page = wprss_get_general_setting('feed_limit');
    }
    global $paged;
    if (get_query_var('paged')) {
        $paged = get_query_var('paged');
    } elseif (get_query_var('page')) {
        $paged = get_query_var('page');
    } else {
        $paged = 1;
    }

    $feed_items_args = [
        'post_type' => get_post_types(),
        'posts_per_page' => $posts_per_page,
        'orderby' => 'date',
        'order' => 'DESC',
        'paged' => $paged,
        'suppress_filters' => true,
        'ignore_sticky_posts' => true,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'wprss_feed_id',
                'compare' => 'EXISTS',
            ],
        ],
    ];

    if (isset($settings['pagination'])) {
        $pagination = strtolower($settings['pagination']);
        if (in_array($pagination, ['false', 'off', '0'])) {
            unset($feed_items_args['paged']);
        }
    }

    if (isset($settings['no-paged']) && $settings['no-paged'] === true) {
        unset($feed_items_args['no-paged']);
    }

    // If either the source or exclude arguments are set (but not both), prepare a meta query
    if (isset($settings['source']) xor isset($settings['exclude'])) {
        // Set the appropriate setting and operator
        $setting = 'source';
        $operator = 'IN';
        if (isset($settings['exclude'])) {
            $setting = 'exclude';
            $operator = 'NOT IN';
        }
        $feeds = array_filter(array_map('intval', explode(',', $settings[$setting])));
        foreach ($feeds as $feed)
            trim($feed);
        if (!empty($feeds)) {
            $feed_items_args['meta_query'] = [
                [
                    'key' => 'wprss_feed_id',
                    'value' => $feeds,
                    'type' => 'numeric',
                    'compare' => $operator,
                ],
            ];
        }
    }

    // Arguments for the next query to fetch all feed items
    $feed_items_args = apply_filters('wprss_display_feed_items_query', $feed_items_args, $settings);

    // Query to get all feed items for display
    $feed_items = new WP_Query($feed_items_args);

    if (isset($settings['get-args']) && $settings['get-args'] === true) {
        return $feed_items_args;
    } else return $feed_items;
}

add_action('wprss_display_template', 'wprss_default_display_template', 10, 3 );
/**
 * Default template for feed items display
 *
 * @since 3.0
 *
 * @param $args       array    The shortcode arguments
 * @param $feed_items WP_Query The feed items to display
 */
function wprss_default_display_template($args, $feed_items)
{
    global $wp_query;
    global $paged;

    // Swap the current WordPress Query with our own
    $old_wp_query = $wp_query;
    $wp_query = $feed_items;

    // Prepare the output
    $output = '';

    // Check if our current query returned any feed items
    if ($feed_items->have_posts()) {
        // PRINT LINKS BEFORE LIST OF FEED ITEMS
        $output .= $args['links_before'];

        // FOR EACH ITEM
        while ($feed_items->have_posts()) {
            // Get the item
            $feed_items->the_post();
            // Add the output
            $output .= wprss_render_feed_item(NULL, '', $args);
        }

        // OUTPUT LINKS AFTER LIST OF FEED ITEMS
        $output .= $args['links_after'];

        // Add pagination if needed
        if (!isset($args['pagination']) || !in_array($args['pagination'], array('off', 'false', '0', 0))) {
            $output = apply_filters('wprss_pagination', $output);
        }

        // Filter the final output, and print it
        echo apply_filters('feed_output', $output);
    } else {
        // No items found message
        echo apply_filters('no_feed_items_found', __('No feed items found.', 'wprss'));
    }

    // Reset the WordPress query
    $wp_query = $old_wp_query;
    wp_reset_postdata();
}

/**
 * Generates an HTML link, using the saved display settings.
 *
 * @param string $link The link URL
 * @param string $text The link text to display
 * @param string $bool Optional boolean. If FALSE, the text is returned unlinked. Default: TRUE.
 * @return string The generated link
 * @since 4.2.4
 */
function wprss_link_display( $link, $text, $bool = true ) {
    $settings = wprss_get_display_settings(get_option('wprss_settings_general'));

    return $bool
        ? sprintf('<a %s %s href="%s">%s</a>', $settings['open'], $settings['follow'], esc_attr($link), esc_html($text))
        : $text;
}


add_filter( 'wprss_pagination', 'wprss_pagination_links' );
/**
 * Display pagination links
 *
 * @since 3.5
 */
function wprss_pagination_links( $output ) {
    // Get the general setting
    $pagination = wprss_get_general_setting( 'pagination' );;

    // Check the pagination setting, if using page numbers
    if ( $pagination === 'numbered' ) {
        global $wp_query;
        $big = 999999999; // need an unlikely integer
        $output .= paginate_links( array(
            'base'		=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'	=> '?paged=%#%',
            'current'	=> max( 1, get_query_var('paged') ),
            'total'		=> $wp_query->max_num_pages
        ) );
        return $output;
    }
    // Otherwise, using default paginations
    else {
        sprintf(
            '<div class="nav-links">%s %s</div>',
            sprintf(
                '<div class="nav-previous alignleft">%s</div>',
                get_next_posts_link(__('Older posts', 'wprss'))
            ),
            sprintf(
                '<div class="nav-next alignright">%s</div>',
                get_previous_posts_link(__('Newer posts', 'wprss'))
            )
        );

        return $output;
    }
}

/**
 * Checks the title limit option and shortens the title when necessary.
 *
 * @since 1.0
 *
 * @param string $title The title of tge feed item.
 * @param int|string|null $id The ID of the feed item.
 *
 * @return string
 */
function wprss_shorten_title($title, $id = null)
{
    if (isset($id) && get_post_type($id) === 'wprss_feed_item') {
        $settings = get_option('wprss_settings_general');
        $limit = isset($settings['title_limit'])
            ? intval($settings['title_limit'])
            : 0;

        if ($limit > 0 && strlen($title) > $limit) {
            $suffix = apply_filters('wprss_shortened_title_ending', '...');
            $title = substr($title, 0, $limit) . $suffix;
        }
    }

    return $title;
}


/**
 * Display feed items on the front end (via shortcode or function)
 *
 * @since 2.0
 */
function wprss_display_feed_items($args = [])
{
    $settings = get_option('wprss_settings_general');
    $args = wprss_get_shortcode_default_args($args);

    $args = apply_filters('wprss_shortcode_args', $args);

    $query_args = $settings;
    if (isset($args['limit'])) {
        $query_args['feed_limit'] = filter_var($args['limit'], FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
                'default' => $query_args['feed_limit'],
            ],
        ]);
    }

    if (isset($args['pagination'])) {
        $query_args['pagination'] = $args['pagination'];
    }

    if (isset($args['source'])) {
        $query_args['source'] = $args['source'];
    } elseif (isset($args['exclude'])) {
        $query_args['exclude'] = $args['exclude'];
    }

    $query_args = apply_filters('wprss_process_shortcode_args', $query_args, $args);

    $feed_items = wprss_get_feed_items_query($query_args);

    do_action('wprss_display_template', $args, $feed_items);
}

/**
 * Limits a phrase/content to a defined number of words
 *
 * NOT BEING USED as we're using the native WP function, although the native one strips tags, so I'll
 * probably revisit this one again soon.
 *
 * @since  3.0
 * @param string $words
 * @param integer $limit
 * @param string $append
 * @return string
 */
function wprss_limit_words($words, $limit, $append = '')
{
    /* Add 1 to the specified limit becuase arrays start at 0 */
    $limit = $limit + 1;
    /* Store each individual word as an array element
       up to the limit */
    $words = explode(' ', $words, $limit);
    /* Shorten the array by 1 because that final element will be the sum of all the words after the limit */
    array_pop($words);
    /* Implode the array for output, and append an ellipse */
    $words = implode(' ', $words) . $append;
    /* Return the result */
    return rtrim($words);
}
