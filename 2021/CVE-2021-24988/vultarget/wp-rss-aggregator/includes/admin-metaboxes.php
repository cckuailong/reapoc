<?php

add_action('add_meta_boxes', function () {
    // Remove some plugin's meta boxes because they're not relevant to the wprss_feed post type.
    $post_type = 'wprss_feed';
    remove_meta_box('wpseo_meta', $post_type, 'normal');                 // WP SEO Yoast
    remove_meta_box('ta-reviews-post-meta-box', $post_type, 'normal');   // Author hReview
    remove_meta_box('wpdf_editor_section', $post_type, 'advanced');      // ImageInject

    // Remove the default WordPress Publish box, because we will be using custom ones
    remove_meta_box('submitdiv', 'wprss_feed', 'side');
    // Custom Publish box
    add_meta_box(
        'submitdiv',
        __('Save Feed Source', 'wprss'),
        'post_submit_meta_box',
        'wprss_feed',
        'side',
        'high'
    );
});

/**
 * Set up the input boxes for the wprss_feed post type
 *
 * @since 2.0
 */
add_action('add_meta_boxes', function () {
    global $wprss_meta_fields;

    add_meta_box(
        'preview_meta_box',
        __('Feed Preview', 'wprss'),
        'wprss_preview_meta_box_callback',
        'wprss_feed',
        'side',
        'high'
    );

    add_meta_box(
        'wprss-feed-processing-meta',
        __('Feed Processing', 'wprss'),
        'wprss_feed_processing_meta_box_callback',
        'wprss_feed',
        'side',
        'high'
    );

    if (!defined('WPRSS_FTP_VERSION') && !defined('WPRSS_ET_VERSION') && !defined('WPRSS_C_VERSION')) {
        add_meta_box(
            'wprss-like-meta',
            __('Share The Love', 'wprss'),
            'wprss_like_meta_box_callback',
            'wprss_feed',
            'side',
            'low'
        );
    }

    add_meta_box(
        'custom_meta_box',
        __('Feed Source Details', 'wprss'),
        'wprss_show_meta_box_callback',
        'wprss_feed',
        'normal',
        'high'
    );
}, 99);

/**
 * Set up fields for the meta box for the wprss_feed post type
 *
 * @since 2.0
 */
function wprss_get_custom_fields()
{
    $prefix = 'wprss_';

    // Field Array
    $wprss_meta_fields['url'] = [
        'label' => __('URL', 'wprss'),
        'id' => $prefix . 'url',
        'type' => 'url',
        'after' => 'wprss_after_url',
        'placeholder' => 'https://',
    ];

    $wprss_meta_fields['limit'] = [
        'label' => __('Limit', 'wprss'),
        'id' => $prefix . 'limit',
        'type' => 'number',
    ];

    $wprss_meta_fields['unique_titles'] = [
        'label' => __('Unique titles only', 'wprss'),
        'id' => $prefix . 'unique_titles',
        'type' => 'select',
        'options' => [
            ['value' => '', 'label' => __('Default', 'wprss')],
            ['value' => '1', 'label' => __('Yes', 'wprss')],
            ['value' => '0', 'label' => __('No', 'wprss')],
        ],
    ];

    $wprss_meta_fields['enclosure'] = [
        'label' => __('Link to enclosure', 'wprss'),
        'id' => $prefix . 'enclosure',
        'type' => 'checkbox',
    ];

    if (wprss_is_et_active()) {
        $wprss_meta_fields['source_link'] = [
            'label' => __('Link source', 'wprss'),
            'id' => $prefix . 'source_link',
            'type' => 'boolean_fallback',
        ];
    }

    $wprss_meta_fields['import_source'] = [
        'label' => __('Use source info', 'wprss'),
        'id' => $prefix . 'import_source',
        'type' => 'checkbox',
    ];

    // for extensibility, allows more meta fields to be added
    return apply_filters('wprss_fields', $wprss_meta_fields);
}

/**
 * Set up the meta box for the wprss_feed post type
 *
 * @since 2.0
 */
function wprss_show_meta_box_callback()
{
    global $post;
    $meta_fields = wprss_get_custom_fields();
    $field_tooltip_id_prefix = 'field_';
    $help = WPRSS_Help::get_instance();

    // Use nonce for verification
    wp_nonce_field('wpra_feed_source', 'wprss_meta_box_nonce');

    // Fix for WordPress SEO JS issue
    echo '<input type="hidden" id="content" value="" />';

    // Begin form table
    echo '<table class="form-table wprss-form-table">';

    foreach ($meta_fields as $field) {
        $meta = get_post_meta($post->ID, $field['id'], true);

        // Add default placeholder value
        $field = wp_parse_args($field, [
            'desc' => '',
            'placeholder' => '',
            'type' => 'text',
        ]);

        $fieldId = $field['id'];
        $fieldLabel = $field['label'];
        $fieldType = $field['type'];
        $fieldDesc = $field['desc'];
        $placeholder = isset($field['placeholder']) ? trim($field['placeholder']) : '';

        $tooltip = isset($field['tooltip']) ? trim($field['tooltip']) : null;
        $tooltip_id = isset($field['id']) ? $field_tooltip_id_prefix . $field['id'] : uniqid($field_tooltip_id_prefix);

        // Begin row
        echo '<tr>';

        // Label
        printf('<th><label for="%s">%s</label></th>', esc_attr($fieldId), esc_html($fieldLabel));

        // Begin field
        echo '<td>';

        if (isset($field['before']) && !empty($field['before'])) {
            call_user_func($field['before']);
        }

        switch ($fieldType) {
            // text/url
            case 'url':
            case 'text':
            {
                printf(
                    '<input id="%1$s" type="%2$s" name="%1$s" value="%3$s" placeholder="%4$s" class="wprss-text-input />"',
                    esc_attr($fieldId),
                    esc_attr($fieldType),
                    esc_attr($meta),
                    esc_attr($placeholder)
                );

                echo $help->tooltip($tooltip_id, $tooltip);
                echo wprss_render_option_desc($fieldDesc, $fieldId);
                break;
            }

            // textarea
            case 'textarea':
            {
                printf(
                    '<textarea id="%1$s" name="%1$s" cols="60" rows="4">%2$s</textarea>',
                    esc_attr($fieldId),
                    esc_textarea($meta)
                );

                echo $help->tooltip($tooltip_id, $tooltip);
                echo wprss_render_option_desc($fieldDesc, $fieldId);
                break;
            }

            // checkbox
            case 'checkbox2':
            case 'checkbox':
            {
                $trueValue = $fieldType === 'checkbox' ? 'true' : '1';
                $falseValue = $fieldType === 'checkbox' ? 'false' : '0';

                printf('<input type="hidden" name="%s" value="%s" />', esc_attr($fieldId), esc_attr($falseValue));
                printf(
                    '<input type="checkbox" name="%1$s" id="%1$s" value="%2$s" %3$s />',
                    esc_attr($fieldId),
                    esc_attr($trueValue),
                    checked($meta, $trueValue, false)
                );

                echo $help->tooltip($tooltip_id, $tooltip);
                echo wprss_render_option_desc($fieldDesc, $fieldId);
                break;
            }

            // select
            case 'select':
                printf('<select name="%1$s" id="%1$s">', esc_attr($fieldId));

                foreach ($field['options'] as $option) {
                    printf(
                        '<option %1$s value="%2$s">%3$s</option>',
                        selected($option['value'], $meta, false),
                        esc_attr($option['value']),
                        esc_html($option['label'])
                    );
                }

                echo '</select>';

                echo $help->tooltip($tooltip_id, $tooltip);
                echo wprss_render_option_desc($fieldDesc, $fieldId);
                break;

            // A select with "On" and "Off" values, and a special option to fall back to General setting
            case 'boolean_fallback':
            {
                $options = wprss_settings_get_feed_source_boolean_options();
                if ($meta === '') {
                    $meta = -1;
                }
                echo wprss_settings_render_select($field['id'], $field['id'], $options, $meta);
                echo $help->tooltip($tooltip_id, $tooltip);
                break;
            }

            // number
            case 'number':
            {
                printf(
                    '<input id="%1$s" name="%1$s" class="wprss-number-roller" type="number" min="0" value="%2$s" placeholder="%3$s" />',
                    esc_attr($fieldId),
                    esc_attr($meta),
                    __('Default', 'wprss')
                );

                echo $help->tooltip($tooltip_id, $tooltip);
                echo wprss_render_option_desc($fieldDesc, $fieldId);
                break;
            }
        }

        if (isset($field['after']) && !empty($field['after'])) {
            call_user_func($field['after']);
        }

        // End field
        echo '</td>';

        // End row
        echo '</tr>';
    }

    echo '</table>';
}

/** @deprecated There shouldn't be any options that still use a description. All help text was moved to tooltips. */
function wprss_render_option_desc($desc, $id)
{
    if (strlen($desc) === 0) {
        return '';
    }

    ob_start();
    ?>
    <br />
    <label for="<?= esc_attr($id) ?>">
            <span class="description">
                <?= esc_html($desc) ?>
            </span>
    </label>
    <?php
    return ob_get_clean();
}

/**
 * Renders content after the URL field
 *
 * @since 3.9.5
 */
function wprss_after_url()
{
    ?>
    <i
        id="wprss-url-spinner"
        class="fa fa-fw fa-refresh fa-spin wprss-updating-feed-icon"
        title="<?= __('Updating feed source', 'wprss') ?>">
    </i>

    <div id="wprss-url-error" style="color:red"></div>

    <a href="#" id="validate-feed-link" class="wprss-after-url-link">
        Validate feed
    </a>

    <span> | </span>

    <a
        href="https://kb.wprssaggregator.com/article/55-how-to-find-an-rss-feed"
        class="wprss-after-url-link"
        target="_blank"
    >
        <?= __('How to find an RSS feed', 'wprss') ?>
    </a>

    <script type="text/javascript">
        (function ($) {
            // When the DOM is ready
            $(document).ready(function () {
                // Move the link immediately after the url text field, and add the click event handler
                $('#validate-feed-link').on('click', function (e) {
                    // Get the url and proceed only if the url is not empty
                    var url = $('#wprss_url').val();
                    if (url.trim().length > 0) {
                        // Encode the url and generate the full url to the w3 feed validator
                        var encodedUrl = encodeURIComponent(url);
                        var fullURL = 'https://validator.w3.org/feed/check.cgi?url=' + encodedUrl;
                        // Open the window / tab
                        window.open(fullURL, 'wprss-feed-validator');
                    }
                    // Suppress the default link click behaviour
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });
            });
        })(jQuery);
    </script>
    <?php
}

/**
 * Save the custom fields
 *
 * @since 2.0
 */
add_action('save_post', function ($post_id, $post) {
    $meta_fields = wprss_get_custom_fields();

    /* Verify the nonce before proceeding. */
    if (!isset($_POST['wprss_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['wprss_meta_box_nonce'], 'wpra_feed_source')) {
        return;
    }

    /* Get the post type object. */
    $post_type = get_post_type_object($post->post_type);

    /* Check if the current user has permission to edit the post. */
    if (!current_user_can($post_type->cap->edit_post, $post_id)) {
        return;
    }

    /** Bail out if running an autosave, ajax or a cron */
    if (
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        (defined('DOING_AJAX') && DOING_AJAX) ||
        (defined('DOING_CRON') && DOING_CRON)
    ) {
        return;
    }

    $postType = class_exists('WPRSS_FTP_Meta')
        ? WPRSS_FTP_Meta::get_instance()->get($post_id, 'post_type')
        : 'wprss_feed_item';

    if ($postType === 'wprss_feed_item' && isset($_POST['wpra_feed_def_ft_image'])) {
        $def_ft_image_id = $_POST['wpra_feed_def_ft_image'];

        if (empty($def_ft_image_id)) {
            // Does not actually delete the image
            delete_post_thumbnail($post_id);
        } else {
            set_post_thumbnail($post_id, $def_ft_image_id);
        }
    }

    // Change the limit, if it is zero, to an empty string
    if (isset($_POST['wprss_limit']) && strval($_POST['wprss_limit']) == '0') {
        $_POST['wprss_limit'] = '';
    }

    // loop through fields and save the data
    foreach ($meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = trim($_POST[$field['id']]);
        if ($new !== $old || empty($old)) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif (empty($new) && !empty($old)) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach

    $force_feed = filter_input(INPUT_POST, 'wprss_force_feed', FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';

    $state = filter_input(INPUT_POST, 'wprss_state', FILTER_SANITIZE_STRING);
    $state = strtolower(trim($state)) === 'paused' ? 'paused' : 'active';

    $activate = filter_input(INPUT_POST, 'wprss_activate_feed', FILTER_SANITIZE_STRING);
    $activate = $activate ? : '';

    $pause = filter_input(INPUT_POST, 'wprss_pause_feed', FILTER_SANITIZE_STRING);
    $pause = $pause ? : '';

    $age_limit = filter_input(INPUT_POST, 'wprss_age_limit', FILTER_VALIDATE_INT);
    $age_limit = (is_int($age_limit) && $age_limit > 0) ? (string) $age_limit : '';

    $age_unit = filter_input(INPUT_POST, 'wprss_age_unit', FILTER_SANITIZE_STRING);
    $age_unit = $age_unit ? strtolower($age_unit) : '';
    $age_unit = in_array($age_unit, wprss_age_limit_units()) ? $age_unit : '';

    $update_interval = filter_input(INPUT_POST, 'wprss_update_interval', FILTER_SANITIZE_STRING);
    $update_interval = $update_interval ? $update_interval : wprss_get_default_feed_source_update_interval();
    $old_update_interval = get_post_meta($post_id, 'wprss_update_interval', true);

    // Update the feed source meta
    update_post_meta($post_id, 'wprss_force_feed', $force_feed);
    update_post_meta($post_id, 'wprss_activate_feed', $activate);
    update_post_meta($post_id, 'wprss_pause_feed', $pause);
    update_post_meta($post_id, 'wprss_age_limit', $age_limit);
    update_post_meta($post_id, 'wprss_age_unit', $age_unit);
    update_post_meta($post_id, 'wprss_update_interval', $update_interval);

    // Check if the state or the update interval has changed
    if (get_post_meta($post_id, 'wprss_state', true) !== $state || $old_update_interval !== $update_interval) {
        // Pause the feed source, and if it is active, re-activate it.
        // This should update the feed's scheduling
        wprss_pause_feed_source($post_id);
        if ($state === 'active') {
            wprss_activate_feed_source($post_id);
        }
    }

    // Update the schedules
    wprss_update_feed_processing_schedules($post_id);

    // If the feed source uses the global updating system, update the feed on publish
    if ($update_interval === wprss_get_default_feed_source_update_interval()) {
        wp_schedule_single_event(time(), 'wprss_fetch_single_feed_hook', [$post_id]);
    }
}, 10, 2);

/**
 * Generate a preview of the latest 5 posts from the feed source being added/edited
 *
 * @since 2.0
 */
function wprss_preview_meta_box_callback()
{
    global $post;
    $feed_url = get_post_meta($post->ID, 'wprss_url', true);

    echo '<div id="feed-preview-container">';

    if (empty($feed_url)) {
        echo '<p>' . __('No feed URL defined yet', 'wprss') . '</p>';
    } else {
        $feed = wprss_fetch_feed($feed_url, $post->ID);

        // Check if failed to fetch the feed
        if (is_wp_error($feed)) {
            // Log the error
            wprss_log_obj('Failed to preview feed.', $feed->get_error_message(), null, WPRSS_LOG_LEVEL_INFO);
            printf(
                '<span class="invalid-feed-url">%s</span>',
                __('<strong>Invalid feed URL</strong> - Double check the feed source URL setting above.', 'wprss')
            );

            echo wpautop(
                sprintf(
                    __(
                        'Not sure where to find the RSS feed on a website? <a target="_blank" href="%1$s">Click here</a> for a visual guide.',
                        'wprss'
                    ),
                    'https://kb.wprssaggregator.com/article/55-how-to-find-an-rss-feed'
                )
            );
        } else {
            ob_start();
            // Figure out how many total items there are
            $total = @$feed->get_item_quantity();
            // Get the number of items again, but limit it to 5.
            $maxItems = $feed->get_item_quantity(5);

            // Build an array of all the items, starting with element 0 (first element).
            $items = $feed->get_items(0, $maxItems);
            ob_clean();
            ?>
            <h4>
                <?php
                printf(
                    __('Latest %1$s feed items out of %2$s available from %3$s'),
                    $maxItems,
                    $total,
                    get_the_title()
                )
                ?>
            </h4>
            <ul>
                <?php
                foreach ($items as $item) {
                    $date = $item->get_date('U');
                    $has_date = !!$date;

                    // Get human readable date
                    $item_date = ($has_date)
                        ? human_time_diff($date, current_time('timestamp')) . ' ' . __('ago', 'wprss')
                        : sprintf('<em>[%s]</em>', esc_html(__('No Date', 'wprss')));

                    printf(
                        '<li>%s<div class="rss-date"><small>%s</small></div></li>',
                        esc_html($item->get_title()),
                        $item_date
                    );
                }
                ?>
            </ul>
            <?php
        }
    }

    echo '</div>';
    echo '<div id="force-feed-container">';

    wprss_render_force_feed_option($post->ID, true);

    echo '</div>';
}

/**
 * Renders the Force Feed option for the Feed Preview.
 *
 * @since 4.6.12
 *
 * @param bool $echo (Optional) If set to true, the function will immediately echo the option,
 *                                   rather than return a string of the option's markup. Default: False.
 * @param int|string $feed_source_id (Optional) The ID of the feed source for the option will be rendered. If not given
 *     or its value is null, the option will not be checked.
 *
 * @return string|null               A string containing the HTML for the rendered option if $echo is set to false,
 *                                   or null if $echo is set to true.
 */
function wprss_render_force_feed_option($feed_source_id = null, $echo = false)
{
    if (!$echo) {
        ob_start();
    }

    $force_feed = $feed_source_id !== null
        ? get_post_meta($feed_source_id, 'wprss_force_feed', true)
        : '';

    echo '<p>';
    echo '<label for="wprss-force-feed">' . __('Force the feed', 'wprss') . '</label>';
    echo '<input type="hidden" name="wprss_force_feed" value="false" />';

    printf(
        '<input type="checkbox" name="wprss_force_feed" id="wprss-force-feed" value="true" %s />',
        checked($force_feed, 'true', false)
    );

    echo WPRSS_Help::get_instance()->tooltip('field_wprss_force_feed');
    echo '</p>';

    return $echo ? null : ob_get_clean();
}

/**
 * Renders the Feed Processing metabox
 *
 * @since 3.7
 */
function wprss_feed_processing_meta_box_callback()
{
    global $post;

    // Get the post meta
    $state = get_post_meta($post->ID, 'wprss_state', true);
    $activate = get_post_meta($post->ID, 'wprss_activate_feed', true);
    $pause = get_post_meta($post->ID, 'wprss_pause_feed', true);
    $update_interval = get_post_meta($post->ID, 'wprss_update_interval', true);
    $update_time = get_post_meta($post->ID, 'wprss_update_time', true);

    $age_limit = get_post_meta($post->ID, 'wprss_age_limit', true);
    $age_unit = get_post_meta($post->ID, 'wprss_age_unit', true);

    // Set default strings for activate and pause times
    $default_activate = 'immediately';
    $default_pause = 'never';

    // Prepare the states
    $states = [
        'active' => __('Active', 'wprss'),
        'paused' => __('Paused', 'wprss'),
    ];

    // Prepare the schedules
    $default_interval = __('Default', 'wprss');
    $wprss_schedules = apply_filters('wprss_schedules', wprss_get_schedules());
    $default_interval_key = wprss_get_default_feed_source_update_interval();
    $schedules = array_merge(
        [
            $default_interval_key => [
                'display' => $default_interval,
                'interval' => $default_interval,
            ],
        ],
        $wprss_schedules
    );

    // Inline help
    $help = WPRSS_Help::get_instance();
    $help_options = [
        'tooltip_handle_class_extra' => $help->get_options('tooltip_handle_class_extra') . ' ' . $help->get_options('tooltip_handle_class') . '-side',
    ];

    ?>

    <div class="wprss-meta-side-setting">
        <label for="wprss_state">Feed state:</label>
        <select id="wprss_state" name="wprss_state">
            <?php foreach ($states as $value => $label) : ?>
                <option value="<?= esc_attr($value) ?>" <?php selected($state, $value) ?> >
                    <?= esc_html($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?= $help->tooltip('field_wprss_state', null, $help_options) ?>
    </div>

    <div class="wprss-meta-side-setting">
        <p>
            <label for="">Activate feed: </label>
            <strong id="wprss-activate-feed-viewer">
                <?= empty($activate) ? $default_activate : esc_attr($activate) ?>
            </strong>
            <a href="#">Edit</a>
            <?= $help->tooltip('field_wprss_activate_feed', null, $help_options) ?>
        </p>
        <div
            class="wprss-meta-slider"
            data-collapse-viewer="wprss-activate-feed-viewer"
            data-default-value="<?php echo $default_activate; ?>">
            <input
                id="wprss_activate_feed"
                class="wprss-datetimepicker-from-today"
                name="wprss_activate_feed"
                value="<?= esc_attr($activate) ?>"
            />
            <span class="description">
                Current UTC time is:
                <br />
                <code>
                    <?= date('d/m/Y H:i:s', current_time('timestamp', 1)) ?>
                </code>
            </span>
        </div>
    </div>

    <div class="wprss-meta-side-setting">
        <p>
            <label for="">Pause feed: </label>
            <strong id="wprss-pause-feed-viewer">
                <?= empty($pause) ? $default_pause : $pause ?>
            </strong>
            <a href="#">Edit</a>
            <?= $help->tooltip('field_wprss_pause_feed', null, $help_options) ?>
        </p>
        <div
            class="wprss-meta-slider"
            data-collapse-viewer="wprss-pause-feed-viewer"
            data-default-value="<?= esc_attr($default_pause) ?>">
            <input
                id="wprss_pause_feed"
                class="wprss-datetimepicker-from-today"
                name="wprss_pause_feed"
                value="<?= esc_attr($pause) ?>"
            />
            <span class="description">
                Current UTC time is:
                <br />
                <code>
                    <?= date('d/m/Y H:i:s', current_time('timestamp', 1)) ?>
                </code>
            </span>
        </div>
    </div>


    <div class="wprss-meta-side-setting">
        <p>
            <label for="">Update interval: </label>
            <strong id="wprss-feed-update-interval-viewer">
                <?php
                if ($update_interval === '' || $update_interval === wprss_get_default_feed_source_update_interval()) {
                    echo $default_interval;
                } else {
                    echo wprss_interval($schedules[$update_interval]['interval']);
                }
                ?>
            </strong>
            <a href="#">Edit</a>
            <?= $help->tooltip('field_wprss_update_interval', null, $help_options) ?>
        </p>
        <div
            class="wprss-meta-slider"
            data-collapse-viewer="wprss-feed-update-interval-viewer"
            data-default-value="<?= esc_attr($default_interval) ?>">
            <select id="feed-update-interval" name="wprss_update_interval">
                <?php foreach ($schedules as $value => $schedule) : ?>
                    <?php
                    $text = ($value === wprss_get_default_feed_source_update_interval())
                        ? $default_interval
                        : wprss_interval($schedule['interval']);
                    ?>
                    <option value="<?= esc_attr($value) ?>" <?php selected($update_interval, $value) ?>>
                        <?= esc_html($text) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>
                <input type="time" name="wpra_feed[update_time]" value="<?= esc_attr($update_time) ?>">
            </label>
        </div>
    </div>


    <div class="wprss-meta-side-setting">
        <p>
            <label id="wprss-age-limit-feed-label" for="" data-when-empty="Limit items by age:">
                <?= __('Limit items by age:', 'wprss'); ?>
            </label>
            <strong id="wprss-age-limit-feed-viewer">
                <?= __('Default', 'wprss'); ?>
            </strong>
            <a href="#">Edit</a>
            <?php echo $help->tooltip('field_wprss_age_limit', null, $help_options) ?>
        </p>
        <div
            class="wprss-meta-slider"
            data-collapse-viewer="wprss-age-limit-feed-viewer"
            data-label="#wprss-age-limit-feed-label"
            data-default-value=""
            data-empty-controller="#limit-feed-items-age"
            data-hybrid="#limit-feed-items-age, #limit-feed-items-age-unit">
            <input
                id="limit-feed-items-age"
                name="wprss_age_limit"
                type="number"
                min="0"
                class="wprss-number-roller"
                placeholder="No limit"
                value="<?= esc_attr($age_limit) ?>" />

            <select id="limit-feed-items-age-unit" name="wprss_age_unit">
                <?php foreach (wprss_age_limit_units() as $unit) : ?>
                    <option value="<?= esc_attr($unit) ?>" <?php selected($age_unit, $unit) ?> >
                        <?= esc_html($unit) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>


    <?php
}

/**
 * Generate Like this plugin meta box
 *
 * @since 2.0
 *
 */
function wprss_like_meta_box_callback()
{
    printf(
        '<ul><li><a href="%s" target="_blank">%s</a></li></ul>',
        'https://wordpress.org/support/view/plugin-reviews/wp-rss-aggregator?rate=5#postform',
        __('Give it a 5 star rating on WordPress.org', 'wprss')
    );

    do_action('wpra_share_the_love_metabox');
}

/**
 * Remove meta boxes from add feed source screen that tend to appear for all post types.
 *
 * @since 2.0
 */
add_action('add_meta_boxes', function () {
    if ('wprss_feed' !== get_current_screen()->id) {
        return;
    }

    //remove_meta_box( 'wpseo_meta', 'wprss_feed' ,'normal' );
    remove_meta_box('postpsp', 'wprss_feed', 'normal');
    remove_meta_box('su_postmeta', 'wprss_feed', 'normal');
    remove_meta_box('woothemes-settings', 'wprss_feed', 'normal');
    remove_meta_box('wpcf-post-relationship', 'wprss_feed', 'normal');
    remove_meta_box('wpar_plugin_meta_box ', 'wprss_feed', 'normal');
    remove_meta_box('sharing_meta', 'wprss_feed', 'advanced');
    remove_meta_box('content-permissions-meta-box', 'wprss_feed', 'advanced');
    remove_meta_box('theme-layouts-post-meta-box', 'wprss_feed', 'side');
    remove_meta_box('post-stylesheets', 'wprss_feed', 'side');
    remove_meta_box('hybrid-core-post-template', 'wprss_feed', 'side');
    remove_meta_box('wpcf-marketing', 'wprss_feed', 'side');
    remove_meta_box('trackbacksdiv22', 'wprss_feed', 'advanced');
    remove_meta_box('aiosp', 'wprss_feed', 'advanced');
    remove_action('post_submitbox_start', 'fpp_post_submitbox_start_action');
}, 100);
