<?php

add_action('plugins_loaded', function () {
    if (defined('WPRSS_ET_VERSION')) {
        add_filter('wprss_settings_sections_array', 'wpra_add_legacy_display_settings_sections');
    }
});

/**
 * Adds the sections for the legacy display settings.
 *
 * @since 4.13
 *
 * @param array $sections The settings section.
 *
 * @return array
 */
function wpra_add_legacy_display_settings_sections($sections)
{
    $sections['display'] = [
        'title' => __('Display settings', 'wprss'),
        'fields' => [
            'link-enable' => [
                'label' => __('Link title', 'wprss'),
                'callback' => 'wprss_setting_title_link_callback',
            ],
            'title-limit' => [
                'label' => __('Title maximum length', 'wprss'),
                'callback' => 'wprss_setting_title_length_callback',
            ],
            'authors-enable' => [
                'label' => __('Show authors', 'wprss'),
                'callback' => 'wprss_setting_authors_enable_callback',
            ],
            'video-links' => [
                'label' => __('For video feed items use', 'wprss'),
                'callback' => 'wprss_setting_video_links_callback',
            ],
            'pagination' => [
                'label' => __('Pagination type', 'wprss'),
                'callback' => 'wprss_setting_pagination_type_callback',
            ],
            'feed-limit' => [
                'label' => __('Feed display limit', 'wprss'),
                'callback' => 'wprss_setting_feed_limit_callback',
            ],
            'open-dd' => [
                'label' => __('Open links behaviour', 'wprss'),
                'callback' => 'wprss_setting_open_dd_callback',
            ],
            'follow-dd' => [
                'label' => __('Set links as nofollow', 'wprss'),
                'callback' => 'wprss_setting_follow_dd_callback',
            ],
        ],
    ];

    $sections['source'] = [
        'title' => __('Source Display settings', 'wprss'),
        'fields' => [
            'source-enable' => [
                'label' => __('Show source', 'wprss'),
                'callback' => 'wprss_setting_source_enable_callback',
            ],
            'text-preceding-source' => [
                'label' => __('Text preceding source', 'wprss'),
                'callback' => 'wprss_setting_text_preceding_source_callback',
            ],
            'source-link' => [
                'label' => __('Link source', 'wprss'),
                'callback' => 'wprss_setting_source_link_callback',
            ],
        ],
    ];

    $sections['date'] = [
        'title' => __('Date Display settings', 'wprss'),
        'fields' => [
            'date-enable' => [
                'label' => __('Show date', 'wprss'),
                'callback' => 'wprss_setting_date_enable_callback',
            ],
            'text-preceding-date' => [
                'label' => __('Text preceding date', 'wprss'),
                'callback' => 'wprss_setting_text_preceding_date_callback',
            ],
            'date-format' => [
                'label' => __('Date format', 'wprss'),
                'callback' => 'wprss_setting_date_format_callback',
            ],
            'time-ago-format-enable' => [
                'label' => __('Time ago format', 'wprss'),
                'callback' => 'wprss_setting_time_ago_format_enable_callback',
            ],
        ],
    ];

    return $sections;
}

/**
 * General settings display section header
 *
 * @since 3.5
 */
function wprss_settings_display_callback()
{
    printf(
        '<p>%s</p>',
        __('In this section you can find some general options that control how the feed items are displayed.', 'wprss')
    );
}

/**
 * Display settings for source section header
 *
 * @since 4.2.4
 */
function wprss_settings_source_callback()
{
    printf(
        '<p>%s</p>',
        __("Options that control how the feed item's source is displayed.", 'wprss')
    );
}

/**
 * Display settings for date section header
 *
 * @since 4.2.4
 */
function wprss_settings_date_callback()
{
    printf(
        '<p>%s</p>',
        __("Options that control how the feed item's date is displayed.", 'wprss')
    );
}

/**
 * Enable linked title options.
 *
 * @since 3.0
 *
 * @param array $field The field information.
 */
function wprss_setting_title_link_callback($field)
{
    $title_link = wprss_get_general_setting('title_link');

    echo wprss_options_render_checkbox(
        $field['field_id'],
        'title_link',
        $title_link,
        '1'
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Title length limit option.
 *
 * @since 3.0
 *
 * @param array $field The field information.
 */
function wprss_setting_title_length_callback($field)
{
    $title_limit = wprss_get_general_setting('title_limit');

    echo wprss_settings_render_input(
        $field['field_id'],
        'wprss_settings_general[title_limit]',
        $title_limit,
        'number',
        [
            'placeholder' => __('No limit', 'wprss'),
            'class' => 'wprss-number-roller',
        ]
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Shows the feed item authors option
 *
 * @since 4.2.4
 *
 * @param array $field The field information.
 */
function wprss_setting_authors_enable_callback($field)
{
    $authors_enable = wprss_get_general_setting('authors_enable');

    echo wprss_options_render_checkbox(
        $field['field_id'],
        'authors_enable',
        $authors_enable,
        '1'
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Use original video link, or embedded video links dropdown.
 *
 * @since 3.4
 *
 * @param array $field The field information.
 */
function wprss_setting_video_links_callback($field)
{
    $video_link = wprss_get_general_setting('video_link');
    $items = [
        'false' => __('Original page link', 'wprss'),
        'true' => __('Embedded video player link', 'wprss'),
    ];

    echo wprss_settings_render_select($field['field_id'], 'wprss_settings_general[video_link]', $items, $video_link);
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);

    printf(
        '<p><span class="description">%s</span></p>',
        esc_html(__('This will not affect already imported feed items.', 'wprss'))
    );
}

/**
 * Pagination Type
 *
 * @since 4.2.3
 *
 * @param array $field The field information.
 */
function wprss_setting_pagination_type_callback($field)
{
    $pagination = wprss_get_general_setting('pagination');
    $items = [
        'default' => __('"Older posts" and "Newer posts" links', 'wprss'),
        'numbered' => __('Page numbers with "Next" and "Previous" page links', 'wprss'),
    ];

    echo wprss_settings_render_select($field['field_id'], 'wprss_settings_general[pagination]', $items, $pagination);
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Set limit for feeds on frontend
 *
 * @since 2.0
 *
 * @param array $field The field information.
 */
function wprss_setting_feed_limit_callback($field)
{
    $feed_limit = wprss_get_general_setting('feed_limit');

    echo wprss_settings_render_input($field['field_id'], 'wprss_settings_general[feed_limit]', $feed_limit);
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Link open setting dropdown
 *
 * @since 1.1
 *
 * @param array $field The field information.
 */
function wprss_setting_open_dd_callback($field)
{
    $open_dd = wprss_get_general_setting('open_dd');
    $items = [
        'lightbox' => __('Lightbox', 'wprss'),
        'blank' => __('New window', 'wprss'),
        'self' => __('Self', 'wprss'),
    ];

    echo wprss_settings_render_select($field['field_id'], 'wprss_settings_general[open_dd]', $items, $open_dd);
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Follow or No Follow dropdown
 *
 * @since 1.1
 *
 * @param array $field The field information.
 */
function wprss_setting_follow_dd_callback($field)
{
    $follow_dd = wprss_get_general_setting('follow_dd');

    echo wprss_options_render_checkbox(
        $field['field_id'],
        'follow_dd',
        $follow_dd,
        'no_follow'
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Enable source
 *
 * @since 3.0
 *
 * @param array $field The field information.
 */
function wprss_setting_source_enable_callback($field)
{
    $source_enable = wprss_get_general_setting('source_enable');

    echo wprss_options_render_checkbox(
        $field['field_id'],
        'source_enable',
        $source_enable,
        '1'
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Set text preceding source
 *
 * @since 3.0
 *
 * @param array $field The field information.
 */
function wprss_setting_text_preceding_source_callback($field)
{
    $text_preceding_source = wprss_get_general_setting('text_preceding_source');

    echo wprss_settings_render_input(
        $field['field_id'],
        'wprss_settings_general[text_preceding_source]',
        $text_preceding_source
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Enable linked title
 *
 * @since 3.0
 *
 * @param array $field The field information.
 */
function wprss_setting_source_link_callback($field)
{
    $source_link = wprss_get_general_setting('source_link');

    echo wprss_options_render_checkbox(
        $field['field_id'],
        'source_link',
        $source_link,
        '1'
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Enable date
 *
 * @since 3.0
 *
 * @param array $field The field information.
 */
function wprss_setting_date_enable_callback($field)
{
    $date_enable = wprss_get_general_setting('date_enable');

    echo wprss_options_render_checkbox(
        $field['field_id'],
        'date_enable',
        $date_enable,
        '1'
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Set text preceding date
 *
 * @since 3.0
 *
 * @param array $field The field information.
 */
function wprss_setting_text_preceding_date_callback($field)
{
    $text_preceding_date = wprss_get_general_setting('text_preceding_date');

    echo wprss_settings_render_input(
        $field['field_id'],
        'wprss_settings_general[text_preceding_date]',
        $text_preceding_date
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Set date format
 *
 * @since 3.0
 *
 * @param array $field The field information.
 */
function wprss_setting_date_format_callback($field)
{
    $date_format = wprss_get_general_setting('date_format');

    echo wprss_settings_render_input(
        $field['field_id'],
        'wprss_settings_general[date_format]',
        $date_format
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);

    printf(
        '<p><a href="%s">%s</a></p>',
        esc_attr('https://codex.wordpress.org/Formatting_Date_and_Time'),
        esc_html(__('PHP Date Format Reference', 'wprss'))
    );
}

/**
 * Time ago format checkbox
 *
 * @since 4.2
 *
 * @param array $field The field information.
 */
function wprss_setting_time_ago_format_enable_callback($field)
{
    $time_ago_format = wprss_get_general_setting('time_ago_format_enable');

    echo wprss_options_render_checkbox(
        $field['field_id'],
        'time_ago_format_enable',
        $time_ago_format,
        '1'
    );
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}
