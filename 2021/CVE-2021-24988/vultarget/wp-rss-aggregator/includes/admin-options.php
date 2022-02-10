<?php

/**
 * Returns the given general setting option value form the database, or the default value if it is not found.
 *
 * @since 3.7.1
 *
 * @param bool $not_empty If true, the default value will be returned if the option exists but is empty.
 * @param string $option_name The name of the option to get
 *
 * @return mixed
 */
function wprss_get_general_setting($option_name, $not_empty = false)
{
    $options = get_option('wprss_settings_general', []);
    $defaults = wprss_get_default_settings_general();

    $value = isset($options[$option_name])
        ? $options[$option_name]
        : $defaults[$option_name];

    return ($not_empty && empty($value))
        ? $defaults[$option_name]
        : $value;
}

function wprss_get_settings_tabs()
{
    $tabs = [
        [
            'label' => __('General', 'wprss'),
            'slug' => 'general_settings',
        ],
        [
            'label' => __('Custom Feed', 'wprss'),
            'slug' => 'custom_feed_settings',
        ],
    ];

    $tabs = apply_filters('wprss_options_tabs', $tabs);

    $tabs[] = [
        'label' => __('Advanced', 'wprss'),
        'slug' => 'advanced_settings',
    ];

    if (count(wprss_get_addons()) > 0 && is_main_site()) {
        $tabs[] = [
            'label' => __('Licenses', 'wprss'),
            'slug' => 'licenses_settings',
        ];
    }

    return $tabs;
}

/**
 * Build the plugin settings page, used to save general settings like whether a link should be follow or no follow
 *
 * @since 1.1
 */
function wprss_settings_page_display()
{
    echo '<div class="wrap">';
    echo '<div id="wpra-settings-app"></div>';
    printf('<h2>%s</h2>', __('WP RSS Aggregator Settings', 'wprss'));

    // Any errors that happened during saving
    settings_errors();

    $active_tab = isset($_GET['tab'])
        ? $_GET['tab']
        : 'general_settings';

    $tabs = wprss_get_settings_tabs();

    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab_property) {
        $tabSlug = $tab_property['slug'];
        $tabLabel = $tab_property['label'];
        $tabUrl = '?post_type=wprss_feed&page=wprss-aggregator-settings&tab=' . urlencode($tabSlug);
        $activeClass = $active_tab == $tabSlug ? 'nav-tab-active' : '';

        printf(
            '<a href="%s" class="nav-tab %s">%s</a>',
            $tabUrl,
            $activeClass,
            $tabLabel
        );
    }
    echo '</h2>';

    // Begin form
    echo '<form action="options.php" method="post">';

    switch ($active_tab) {
        case 'general_settings':
        {
            settings_fields('wprss_settings_general');
            do_settings_sections('wprss_settings_general');
            break;
        }
        case 'custom_feed_settings':
        {
            settings_fields('wprss_settings_custom_feed');
            do_settings_sections('wprss_settings_custom_feed');
            break;
        }
        case 'advanced_settings':
        {
            settings_fields('wprss_settings_advanced');
            do_settings_sections('wprss_settings_advanced');
            break;
        }
        case 'licenses_settings':
        {
            if (!is_main_site()) {
                printf(
                    '<p><strong>%s</strong></p>',
                    __('You do not have access to this page', 'wprss')
                );

                return;
            }

            settings_fields('wprss_settings_license_keys');
            do_settings_sections('wprss_settings_license_keys');
            break;
        }
        default:
        {
            do_action('wprss_add_settings_fields_sections', $active_tab);
            break;
        }
    }

    submit_button(__('Save Settings', 'wprss'));

    echo '</form>';
    echo '</div>';
}

function wprss_settings_fields_array()
{
    // Define the settings per section
    $settings = apply_filters('wprss_settings_array', [
        'import' => [
            'cron-interval' => [
                'label' => __('Update interval', 'wprss'),
                'callback' => 'wprss_setting_cron_interval_callback',
            ],
            'unique-titles' => [
                'label' => __('Unique titles only', 'wprss'),
                'callback' => 'wprss_setting_unique_titles',
            ],
            'feed_items_import_order' => [
                'label' => __('Import order', 'wprss'),
                'callback' => 'wprss_setting_feed_items_import_order_callback',
            ],
            'limit-feed-items-by-age' => [
                'label' => __('Limit items by age', 'wprss'),
                'callback' => 'wprss_setting_limit_feed_items_age_callback',
            ],
            'limit-feed-items-imported' => [
                'label' => __('Limit feed items stored per feed', 'wprss'),
                'callback' => 'wprss_setting_limit_feed_items_imported_callback',
            ],
            'limit-feed-items-db' => [
                'label' => __('Limit feed items stored', 'wprss'),
                'callback' => 'wprss_setting_limit_feed_items_callback',
            ],
            'limit_feed_items_per_import' => [
                'label' => __('Limit feed items per import', 'wprss'),
                'callback' => 'wprss_setting_limit_feed_items_per_import_callback',
            ],
            'schedule_future_items' => [
                'label' => __('Schedule future items', 'wprss'),
                'callback' => 'wprss_setting_schedule_future_items_callback',
            ],
        ],

        'custom_feed' => [
            'custom-feed-url' => [
                'label' => __('Custom feed URL', 'wprss'),
                'callback' => 'wprss_settings_custom_feed_url_callback',
            ],
            'custom-feed-title' => [
                'label' => __('Custom feed title', 'wprss'),
                'callback' => 'wprss_settings_custom_feed_title_callback',
            ],
            'custom-feed-limit' => [
                'label' => __('Custom feed limit', 'wprss'),
                'callback' => 'wprss_settings_custom_feed_limit_callback',
            ],
        ],
    ]);

    if (apply_filters('wprss_use_fixed_feed_limit', false) === false) {
        unset($settings['import']['limit-feed-items-db']);
    }

    $settings['styles'] = [
        'styles-disable' => [
            'label' => __('Disable styles', 'wprss'),
            'callback' => 'wprss_setting_styles_disable_callback',
        ],
    ];

    if (apply_filters('wprss_use_fixed_feed_limit', false) === false) {
        unset($settings['general']['limit-feed-items-db']);
    }

    return $settings;
}

add_action('admin_init', 'wprss_admin_init');
/**
 * Register and define options and settings
 *
 * @since 2.0
 *
 * Note: In the future might change to
 * the way EDD builds the settings pages, cleaner method.
 */
function wprss_admin_init()
{
    $fields = wprss_settings_fields_array();

    // page => sections -> fields
    $settings = [
        'general' => [
            'sections' => apply_filters(
                'wprss_settings_sections_array',
                [
                    'import' => [
                        'title' => __('Import Settings', 'wprss'),
                        'fields' => $fields['import'],
                    ],
                ]
            ),
            'option' => 'wprss_settings_general',
            'callback' => 'wprss_settings_general_validate',
        ],
        'custom_feed' => [
            'sections' => [
                'custom_feed' => [
                    'title' => __('Custom RSS Feed', 'wprss'),
                    'fields' => $fields['custom_feed'],
                ],
            ],
            'option' => 'wprss_settings_general',
            'callback' => 'wprss_settings_general_validate',
        ],
        'advanced' => [
            'sections' => [
                'advanced' => [
                    'title' => __('Advanced Settings', 'wprss'),
                    'fields' => $fields['advanced'],
                ],
                'styles' => [
                    'title' => __('Styles', 'wprss'),
                    'fields' => $fields['styles'],
                ],
            ],
            'option' => 'wprss_settings_general',
            'callback' => 'wprss_settings_general_validate',
        ],
        'license_keys' => [
            'sections' => [],
            'option' => 'wprss_settings_license_keys',
            'callback' => 'wprss_settings_license_keys_validate',
        ],
    ];

    $setting_field_id_prefix = 'wprss-settings-';

    foreach ($settings as $pageKey => $page) {
        $groupId = "wprss_settings_${pageKey}";

        register_setting(
            $groupId,
            $page['option'],
            $page['callback']
        );

        foreach ($page['sections'] as $sectionKey => $section) {
            $sectionId = "wprss_settings_${sectionKey}_section";

            add_settings_section(
                $sectionId,
                $section['title'],
                "wprss_settings_${sectionKey}_callback",
                $groupId
            );

            foreach ($section['fields'] as $fieldId => $field) {
                /**
                 * This will be passed to the field callback as the only argument
                 *
                 * @see http://codex.wordpress.org/Function_Reference/add_settings_field#Parameters
                 */
                $callback_args = [
                    'field_id' => $fieldId,
                    'field_id_prefix' => $setting_field_id_prefix,
                    'section_id' => $sectionKey,
                    'field_label' => isset($field['label']) ? $field['label'] : null,
                    'tooltip' => isset($field['tooltip']) ? $field['tooltip'] : null,
                ];

                add_settings_field(
                    $setting_field_id_prefix . $fieldId,
                    $field['label'],
                    $field['callback'],
                    $groupId,
                    $sectionId,
                    $callback_args
                );
            }
        }
    }

    do_action('wprss_admin_init');
}

/**
 * Returns the HTML of a tooltip handle.
 *
 * Filters used:
 * - `wprss_settings_inline_help_default_options` - The default options for "Settings" page's tooltips
 * - `wprss_settings_inline_help_id_prefix` - The prefix for all tooltip IDs for the "Settings" page.
 *
 * @param string $id The ID of the tooltip
 * @param string|null $text Text for this tooltip, if any.
 * @param array $options Any options for this setting.
 *
 * @return string Tooltip handle HTML. See {@link WPRSS_Help::tooltip()}.
 */
function wprss_settings_inline_help($id, $text = null, $options = [])
{
    $help = WPRSS_Help::get_instance();

    // Default options, entry point
    $defaults = apply_filters('wprss_settings_inline_help_default_options', [
        'tooltip_handle_class_extra' => $help->get_options('tooltip_handle_class_extra') . ' ' . $help->get_options('tooltip_handle_class') . '-setting',
    ]);

    $options = $help->array_merge_recursive_distinct($defaults, $options);

    // ID Prefix
    $id = apply_filters('wprss_settings_inline_help_id_prefix', 'setting-') . $id;

    return $help->tooltip($id, $text, $options);
}

function wprss_settings_field_name_prefix($string = '')
{
    $string = (string) $string;
    $prefix = apply_filters('wprss_settings_field_name_prefix', 'wprss_settings_', $string);

    return $prefix . $string;
}

/**
 * Generates a uniform setting field name for use in HTML.
 * The parts used are the ID of the field, the section it is in, and an optional prefix.
 * All parts are optional, but, if they appear, they shall appear in this order: $prefix, $section, $id.
 *
 * If only the section is not specified, the $id will be simply prefixed by $prefix.
 * If either the $id or the $section are empty (but not both), $prefix will be stripped of known separators.
 * Empty parts will be excluded.
 *
 * @param string $id ID of the field.
 * @param string|null $section Name of the section, to which this field belongs.
 * @param string|null $prefix The string to prefix the name with; appears first. If boolean false, no prefix will be
 *     applied. Default: return value of {@link wprss_settings_field_name_prefix()}.
 *
 * @return string Name of the settings field, namespaced and optionally prefixed.
 */
function wprss_settings_field_name($id = null, $section = null, $prefix = null)
{
    if ($prefix !== false) {
        $prefix = $prefix !== null
            ? $prefix
            : wprss_settings_field_name_prefix();
    } else {
        $prefix = '';
    }

    $section = (string) $section;

    $format = '';
    if (!strlen($section) xor !strlen($id)) {
        $prefix = trim($prefix, "\t\n\r _-:");
    }
    if (strlen($prefix)) {
        $format .= '%3$s';
    }
    if (strlen($section)) {
        $format .= '%2$s';
    }
    if (strlen($id)) {
        $format .= (!strlen($section) ? '%1$s' : '[%1$s]');
    }

    return apply_filters('wprss_settings_field_name', sprintf($format, $id, $section, $prefix), $id, $section, $prefix);
}

/**
 * General settings section header
 *
 * @since 3.0
 */
function wprss_settings_import_callback()
{
    echo wpautop(__('Configure how WP RSS Aggregator imports RSS feed items.', 'wprss'));
}

/**
 * Custom feed settings section header
 *
 * @since 4.13
 */
function wprss_settings_custom_feed_callback()
{
    echo wpautop(__('WP RSS Aggregator creates a custom RSS feed on your site that includes all of your imported items. Use the below options to set it up.',
        'wprss'));
}

/**
 * Advanced settings section header
 *
 * @since 4.13
 */
function wprss_settings_advanced_callback()
{
    echo wpautop(__('Only change these options if you know what you are doing!', 'wprss'));
}

/**
 * General settings styles section header
 *
 * @since 3.0
 */
function wprss_settings_styles_callback()
{
    echo wpautop(__('If you would like to disable all styles used in this plugin, tick the checkbox.', 'wprss'));
}

/**
 * Limit number of feed items stored by their age
 *
 * @since 3.0
 */
function wprss_setting_limit_feed_items_age_callback($field)
{
    $limit_feed_items_age = wprss_get_general_setting('limit_feed_items_age');
    $limit_feed_items_age_unit = wprss_get_general_setting('limit_feed_items_age_unit');
    $units = wprss_age_limit_units();

    printf(
        '<input id="%s" name="wprss_settings_general[limit_feed_items_age]" type="number" min="0" class="wprss-number-roller" placeholder="%s" value="%s" />',
        esc_attr($field['field_id']),
        __('No limit', 'wprss'),
        esc_attr($limit_feed_items_age)
    );

    echo '<select id="limit-feed-items-age-unit" name="wprss_settings_general[limit_feed_items_age_unit]">';
    foreach ($units as $unit) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($unit),
            selected($limit_feed_items_age_unit, $unit, false),
            esc_html($unit)
        );
    }
    echo '</select>';

    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Limit number of feed items stored
 *
 * @since 3.0
 */
function wprss_setting_limit_feed_items_callback($field)
{
    $limit_feed_items_db = wprss_get_general_setting('limit_feed_items_db');

    printf(
        '<input type="text" id="%s" name="%s" value="%s" />',
        esc_attr($field['field_id']),
        'wprss_settings_general[limit_feed_items_db]',
        esc_attr($limit_feed_items_db)
    );

    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Limit number of feed items imported per feed
 *
 * @since 3.1
 */
function wprss_setting_limit_feed_items_imported_callback($field)
{
    $limit_feed_items_imported = wprss_get_general_setting('limit_feed_items_imported');

    printf(
        '<input type="text" id="%s" name="%s" value="%s" placeholder="%s" />',
        esc_attr($field['field_id']),
        'wprss_settings_general[limit_feed_items_imported]',
        esc_attr($limit_feed_items_imported),
        __('No Limit', 'wprss')
    );

    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Gets a sorted (according to interval) list of the cron schedules
 *
 * @since 3.0
 */
function wprss_get_schedules()
{
    $schedules = wp_get_schedules();

    uasort($schedules, function ($a, $b) {
        return $a['interval'] - $b['interval'];
    });

    return $schedules;
}

/**
 * Cron interval dropdown callback
 *
 * @since 3.0
 */
function wprss_setting_cron_interval_callback($field)
{
    $current = wprss_get_general_setting('cron_interval');
    $schedules = wprss_get_schedules();

    // Set the allowed Cron schedules, we don't want any intervals that can lead to issues with server load
    $wprss_schedules = apply_filters(
        'wprss_schedules',
        ['fifteen_min', 'thirty_min', 'hourly', 'two_hours', 'twicedaily', 'daily']
    );

    printf(
        '<select id="%s" name="%s">',
        esc_attr($field['field_id']),
        'wprss_settings_general[cron_interval]'
    );

    foreach ($schedules as $schedule_name => $schedule_data) {
        if (!in_array($schedule_name, $wprss_schedules)) {
            continue;
        }

        printf(
            '<option value="%s" %s>%s (%s)</option>',
            esc_attr($schedule_name),
            selected($current, $schedule_name, false),
            esc_html($schedule_data['display']),
            wprss_interval($schedule_data['interval'])
        );
    }

    echo '</select>';
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Unique titles only checkbox callback
 *
 * @since 4.7
 */
function wprss_setting_unique_titles($field)
{
    $unique_titles = wprss_get_general_setting('unique_titles');

    echo wprss_options_render_checkbox($field['field_id'], 'unique_titles', $unique_titles);
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Sets the custom feed URL
 *
 * @since 3.3
 */
function wprss_settings_custom_feed_url_callback($field)
{
    $siteUrl = trailingslashit(get_site_url());
    $custom_feed_url = wprss_get_general_setting('custom_feed_url');
    $fullUrl = $siteUrl . $custom_feed_url;

    printf('<code>%s</code>', $siteUrl);
    printf(
        '<input type="text" id="%s" name="%s" value="%s" />',
        esc_attr($field['field_id']),
        'wprss_settings_general[custom_feed_url]',
        esc_attr($custom_feed_url)
    );

    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);

    echo '<p style="font-style: normal">';
    printf(
        '<a href="%s" target="_blank">%s</a>',
        esc_attr($fullUrl),
        __('Open custom feed', 'wprss')
    );
    echo '</p>';
}

/**
 * Sets the custom feed title
 *
 * @since 4.1.2
 */
function wprss_settings_custom_feed_title_callback($field)
{
    $custom_feed_title = wprss_get_general_setting('custom_feed_title');

    printf(
        '<input type="text" id="%s" name="%s" value="%s" />',
        esc_attr($field['field_id']),
        'wprss_settings_general[custom_feed_title]',
        esc_attr($custom_feed_title)
    );

    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Sets the custom feed limit
 *
 * @since 3.3
 */
function wprss_settings_custom_feed_limit_callback($field)
{
    $custom_feed_limit = wprss_get_general_setting('custom_feed_limit');

    printf(
        '<input type="number" id="%s" name="%s" value="%s" placeholder="%s" class="wprss-number-roller" min="0" />',
        esc_attr($field['field_id']),
        'wprss_settings_general[custom_feed_limit]',
        esc_attr($custom_feed_limit),
        __('Default', 'wprss')
    );

    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Disable styles
 *
 * @since 3.0
 */
function wprss_setting_styles_disable_callback($field)
{
    $styles_disable = wprss_get_general_setting('styles_disable');

    echo wprss_options_render_checkbox($field['field_id'], 'styles_disable', $styles_disable);
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Renders the `limit_feed_items_per_import` setting.
 *
 * @since 4.11.2
 *
 * @param array $field Field data.
 */
function wprss_setting_limit_feed_items_per_import_callback($field)
{
    $id = $field['field_id'];
    $mainOptionName = 'wprss_settings_general';
    $value = wprss_get_general_setting($id);

    echo \Aventura\Wprss\Core\Model\SettingsAbstract::getTextHtml($value, [
        'id' => $id,
        'name' => \Aventura\Wprss\Core\Model\SettingsAbstract::getNameHtml([$mainOptionName, $id]),
        'placeholder' => __('No Limit', 'wprss'),
    ]);

    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Renders the `limit_feed_items_per_import` setting.
 *
 * @since 4.17
 *
 * @param array $field Field data.
 */
function wprss_setting_schedule_future_items_callback($field)
{
    $id = $field['field_id'];
    $value = wprss_get_general_setting($id);

    echo wprss_options_render_checkbox($field['field_id'], 'schedule_future_items', $value);
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Renders the `feed_items_import_order` setting.
 *
 * @since 4.11.2
 *
 * @param array $field Field data.
 */
function wprss_setting_feed_items_import_order_callback($field)
{
    $id = $field['field_id'];
    $mainOptionName = 'wprss_settings_general';
    $value = wprss_get_general_setting($id);
    $items = [
        'latest' => __('Latest items first', 'wprss'),
        'oldest' => __('Oldest items first', 'wprss'),
        '' => __('Original feed order', 'wprss'),
    ];

    printf(
        '<select id="%s" name="%s">',
        esc_attr($id),
        esc_attr(\Aventura\Wprss\Core\Model\SettingsAbstract::getNameHtml([$mainOptionName, $id]))
    );

    foreach ($items as $_value => $_label) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($_value),
            selected($value, $_value, false),
            esc_html($_label)
        );
    }

    echo '</select>';
    echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
}

/**
 * Gets options that should go in a dropdown which represents a
 * feed-source-specific boolean setting.
 *
 * @since 4.10
 * @return array An array with options.
 */
function wprss_settings_get_feed_source_boolean_options()
{
    return [
        1 => __('On', 'wprss'),
        0 => __('Off', 'wprss'),
        -1 => __('Default', 'wprss'),
    ];
}

/**
 * Renders a <select> HTML tag from its parameters.
 *
 * @since 4.10
 * @return string The HTML of a <select> tag.
 */
function wprss_settings_render_select($id, $name, $items, $selected = null, $attributes = [])
{
    ob_start();
    $attributes = array_merge($attributes, [
        'id' => $id,
        'name' => $name,
    ]);

    array_walk($attributes, function (&$v, $k) {
        $v = sprintf('%1$s="%2$s"', $k, esc_attr($v));
    });
    $attrString = implode(' ', $attributes);

    $html = sprintf('<select %s>', $attrString);

    foreach ($items as $_key => $_item) {
        $_key = (string) $_key;
        $_item = (string) $_item;

        $html .= sprintf(
            '<option name="%s" %s>%s</option>',
            esc_attr($_key),
            selected($selected, $_key, false),
            esc_html($_item)
        );
    }

    $html .= '</select>';

    return $html;
}

/**
 * Renders an <input> HTML tag from its parameters.
 *
 * @since 4.13
 * @return string The HTML of an <input> tag.
 */
function wprss_settings_render_input($id, $name, $value, $type = 'text', $attributes = [])
{
    $attributes = array_merge($attributes, [
        'id' => $id,
        'name' => $name,
        'type' => $type,
        'value' => esc_attr($value),
    ]);

    $attributePairs = $attributes;

    array_walk($attributePairs, function (&$v, $k) {
        $v = sprintf('%1$s="%2$s"', $k, $v);
    });

    $attributesString = implode(' ', $attributePairs);

    return sprintf('<input %s />', $attributesString);
}

/**
 * Renders an <input> checkbox HTML tag from its parameters.
 *
 * @since 4.13
 * @return string The HTML of an <input> checkbox tag.
 */
function wprss_settings_render_checkbox($id, $name, $value, $checked = false)
{
    $attributes = [];

    if ($checked) {
        $attributes['checked'] = '';
    }

    return wprss_settings_render_input($id, $name, $value, 'checkbox', $attributes);
}

/**
 * Pretty-prints the difference in two times.
 *
 * @since 3.0
 *
 * @param time $older_date
 * @param time $newer_date
 *
 * @return string The pretty time_since value
 * @link http://wordpress.org/extend/plugins/wp-crontrol/
 */
function wprss_time_since($older_date, $newer_date)
{
    return wprss_interval($newer_date - $older_date);
}

/**
 * Calculates difference between times
 *
 * Taken from the WP-Crontrol plugin
 *
 * @link http://wordpress.org/extend/plugins/wp-crontrol/
 * @since 3.0
 *
 */
function wprss_interval($since)
{
    if ($since === wprss_get_default_feed_source_update_interval()) {
        return __('Default', 'wprss');
    }
    // array of time period chunks
    $chunks = [
        [60 * 60 * 24 * 365, _n_noop('%s year', '%s years', 'crontrol')],
        [60 * 60 * 24 * 30, _n_noop('%s month', '%s months', 'crontrol')],
        [60 * 60 * 24 * 7, _n_noop('%s week', '%s weeks', 'crontrol')],
        [60 * 60 * 24, _n_noop('%s day', '%s days', 'crontrol')],
        [60 * 60, _n_noop('%s hour', '%s hours', 'crontrol')],
        [60, _n_noop('%s minute', '%s minutes', 'crontrol')],
        [1, _n_noop('%s second', '%s seconds', 'crontrol')],
    ];

    if ($since <= 0) {
        return __('now', 'wprss');
    }

    // we only want to output two chunks of time here, eg:
    // x years, xx months
    // x days, xx hours
    // so there's only two bits of calculation below:

    // step one: the first chunk
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];

        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    // set output var
    $output = sprintf(_n($name[0], $name[1], $count, 'wprss'), $count);

    // step two: the second chunk
    if ($i + 1 < $j) {
        $seconds2 = $chunks[$i + 1][0];
        $name2 = $chunks[$i + 1][1];

        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
            // add to output var
            $output .= ' ' . sprintf(_n($name2[0], $name2[1], $count2, 'wprss'), $count2);
        }
    }

    return $output;
}

/**
 * Validate inputs from the general settings page
 *
 * @since 3.0
 */
function wprss_settings_general_validate($input)
{
    $current_cron_interval = wprss_get_general_setting('cron_interval');

    // Create our array for storing the validated options
    $output = get_option('wprss_settings_general', []);

    // Loop through each of the incoming options
    foreach ($input as $key => $value) {
        // Check to see if the current option has a value. If so, process it.
        if (!isset($value)) {
            continue;
        }

        // Strip all HTML and PHP tags and properly handle quoted strings
        $output[$key] = strip_tags(stripslashes($value));
    }

    if (isset($input['styles_disable'])) {
        $output['styles_disable'] = (int) $input['styles_disable'];
    }

    if (isset($input['unique_titles'])) {
        $output['unique_titles'] = $input['unique_titles'];
    }

    if (isset($input['cron_interval']) && $input['cron_interval'] != $current_cron_interval) {
        wp_clear_scheduled_hook('wprss_fetch_all_feeds_hook');
        wp_schedule_event(time(), $input['cron_interval'], 'wprss_fetch_all_feeds_hook');
    }

    // Return the array processing any additional functions filtered by this action
    return apply_filters('wprss_settings_general_validate', $output, $input);
}

/**
 * Validates the licenses settings
 *
 * @since 3.8
 */
function wprss_settings_license_keys_validate($input)
{
    // Get the current licenses option
    $licenses = get_option('wprss_settings_license_keys');
    // If no licenses have been defined yet, create an empty array
    if (!is_array($licenses)) {
        $licenses = [];
    }
    // For each entry in the received input
    foreach ($input as $addon => $license_code) {
        $addon_code = explode('_', $addon);
        $addon_code = isset($addon_code[0]) ? $addon_code[0] : null;
        // Only save if the entry does not exist OR the code is different
        if (array_key_exists($addon, $licenses) && $license_code === $licenses[$addon]) {
            continue;
        }

        $is_valid = apply_filters('wprss_settings_license_key_is_valid', true, $license_code);

        if ($addon_code) {
            $is_valid = apply_filters("wprss_settings_license_key_{$addon_code}_is_valid", $is_valid, $license_code);
        }

        if (!$is_valid) {
            continue;
        }

        // Save it to the licenses option
        $licenses[$addon] = $license_code;
    }
    wprss_check_license_statuses();
    // Return the new licenses
    return $licenses;
}

add_action('wprss_check_license_statuses', 'wprss_check_license_statuses');
/**
 * Checks the license statuses
 *
 * @since 3.8.1
 */
function wprss_check_license_statuses()
{
    $license_statuses = get_option('wprss_settings_license_statuses', []);

    if (count($license_statuses) === 0) return;

    $found_inactive = false;
    foreach ($license_statuses as $addon => $status) {
        if ($status !== 'active') {
            $found_inactive = true;
            break;
        }
    }

    if ($found_inactive) {
        set_transient('wprss_notify_inactive_licenses', 1, 0);
    }
}

/**
 * Returns the units used for the limit by age option.
 *
 * @since 3.8
 */
function wprss_age_limit_units()
{
    return apply_filters(
        'wprss_age_limit_units',
        [
            __('days', 'wprss'),
            __('weeks', 'wprss'),
            __('months', 'wprss'),
            __('years', 'wprss'),
        ]
    );
}

/**
 * Renders a checkbox with a hidden field for the default value (when unchecked).
 *
 * @param string $id
 * @param string $name
 * @param string $value
 * @param string $checked_value
 * @param string $default_value
 *
 * @return string
 */
function wprss_options_render_checkbox($id, $name, $value, $checked_value = '1', $default_value = '0')
{
    $name = sprintf('wprss_settings_general[%s]', $name);

    $result = sprintf(
        '<input type="hidden" name="%s" value="%s" />',
        esc_attr($name),
        esc_attr($default_value)
    );
    $result .= sprintf(
        '<input type="checkbox" id="%s" name="%s" value="%s" %s />',
        esc_attr($id),
        esc_attr($name),
        esc_attr($checked_value),
        checked($checked_value, $value, false)
    );

    return $result;
}
