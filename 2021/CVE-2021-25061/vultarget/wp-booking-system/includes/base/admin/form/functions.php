<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Forms admin area
 *
 */
function wpbs_include_files_admin_form()
{

    // Get legend admin dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include submenu page
    if (file_exists($dir_path . 'class-submenu-page-form.php')) {
        include $dir_path . 'class-submenu-page-form.php';
    }

    // Include forms list table
    if (file_exists($dir_path . 'class-list-table-forms.php')) {
        include $dir_path . 'class-list-table-forms.php';
    }

    // Include admin actions
    if (file_exists($dir_path . 'functions-actions-form.php')) {
        include $dir_path . 'functions-actions-form.php';
    }

}
add_action('wpbs_include_files', 'wpbs_include_files_admin_form');

/**
 * Register the Forms admin submenu page
 *
 */
function wpbs_register_submenu_page_forms($submenu_pages)
{

    if (!is_array($submenu_pages)) {
        return $submenu_pages;
    }

    $submenu_pages['forms'] = array(
        'class_name' => 'WPBS_Submenu_Page_Forms',
        'data' => array(
            'page_title' => __('Forms', 'wp-booking-system'),
            'menu_title' => __('Forms', 'wp-booking-system'),
            'capability' => apply_filters('wpbs_submenu_page_capability_forms', 'manage_options'),
            'menu_slug' => 'wpbs-forms',
        ),
    );

    return $submenu_pages;

}
add_filter('wpbs_register_submenu_page', 'wpbs_register_submenu_page_forms', 20);

/**
 * Declare all the field types available when building forms
 *
 * @return array
 *
 */
function wpbs_form_available_field_types()
{
    $fields = array();

    $fields['text'] = array(
        'type' => 'text',
        'supports' => array(
            'primary' => array('label', 'required'),
            'secondary' => array('placeholder', 'value', 'description', 'class', 'hide_label'),
        ),
        'values' => array(),
    );

    $fields['email'] = array(
        'type' => 'email',
        'supports' => array(
            'primary' => array('label', 'required'),
            'secondary' => array('placeholder', 'value', 'description', 'class', 'hide_label'),
        ),
        'values' => array(),
    );

    $fields['textarea'] = array(
        'type' => 'textarea',
        'supports' => array(
            'primary' => array('label', 'required'),
            'secondary' => array('placeholder', 'value_textarea', 'description', 'class', 'hide_label'),
        ),
        'values' => array(),
    );

    $fields['dropdown'] = array(
        'type' => 'dropdown',
        'supports' => array(
            'primary' => array('label', 'required', 'options'),
            'secondary' => array('placeholder', 'class', 'hide_label'),
        ),
        'values' => array(),
    );

    $fields['checkbox'] = array(
        'type' => 'checkbox',
        'supports' => array(
            'primary' => array('label', 'required', 'options'),
            'secondary' => array('description', 'class', 'hide_label'),
        ),
        'values' => array(),
    );

    $fields['radio'] = array(
        'type' => 'radio',
        'supports' => array(
            'primary' => array('label', 'required', 'options'),
            'secondary' => array('description', 'class', 'hide_label'),
        ),
        'values' => array(),
    );

    $fields['html'] = array(
        'type' => 'html',
        'supports' => array(
            'primary' => array('value_textarea'),
        ),
        'values' => array(),
    );

    $fields['hidden'] = array(
        'type' => 'hidden',
        'supports' => array(
            'primary' => array('label', 'value'),
            'secondary' => array('class'),
        ),
        'values' => array(),
    );

    $fields['captcha'] = array(
        'type' => 'captcha',
        'supports' => array(
            'primary' => array('notice_captcha', 'label'),
            'secondary' => array('hide_label'),
        ),
        'values' => array(),
    );

    $fields = apply_filters('wpbs_form_available_field_types', $fields, 1);

    return $fields;

}

/**
 * Declare all the options for field types
 *
 * @return array
 *
 */
function wpbs_form_available_field_types_options()
{
    $options = array();

    $options['label'] = array('key' => 'label', 'label' => __('Label', 'wp-booking-system'), 'translatable' => true);
    $options['required'] = array('key' => 'required', 'label' => __('Required', 'wp-booking-system'), 'translatable' => false);
    $options['options'] = array('key' => 'options', 'label' => __('Options', 'wp-booking-system'), 'translatable' => true);

    $options['placeholder'] = array('key' => 'placeholder', 'label' => __('Placeholder', 'wp-booking-system'), 'translatable' => true);
    $options['value'] = array('key' => 'value', 'label' => __('Default Value', 'wp-booking-system'), 'translatable' => true);
    $options['value_textarea'] = array('key' => 'value', 'label' => __('Default Value', 'wp-booking-system'), 'input' => 'textarea', 'translatable' => true);
    $options['class'] = array('key' => 'class', 'label' => __('Custom Class', 'wp-booking-system'), 'translatable' => false);
    $options['description'] = array('key' => 'description', 'label' => __('Description', 'wp-booking-system'), 'translatable' => true);
    $options['hide_label'] = array('key' => 'hide_label', 'label' => __('Hide Label', 'wp-booking-system'), 'translatable' => false);

    $options['notice_captcha'] = array('key' => 'notice_captcha', 'label' => __('To use reCAPTCHA you must add your API Keys in the', 'wp-booking-system') . ' <a target="_blank" href="' . add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'form'), admin_url('admin.php')) . '">' . __('Settings Page', 'wp-booking-system') . '</a>.', 'translatable' => false);

    $options = apply_filters('wpbs_form_available_field_types_options', $options);

    return $options;
}

function wpbs_form_available_field_types_languages($fields)
{

    $settings = get_option('wpbs_settings', array());
    $active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());

    foreach ($fields as &$field) {
        $field['values']['default'] = [];
    }

    if (!$active_languages) {
        return $fields;
    }

    foreach ($fields as &$field) {
        foreach ($active_languages as $language) {
            $field['languages'][] = $language;
            $field['values'][$language] = [];
        }
    }

    return $fields;
}
add_filter('wpbs_form_available_field_types', 'wpbs_form_available_field_types_languages', 10, 1);

/**
 * Return all the email fields from an existing form
 *
 * @param  array $form_data
 *
 * @return array
 *
 */
function wpbs_form_get_email_fields($form_data)
{

    $email_fields = array();
    foreach ($form_data as $field) {
        if ($field['type'] == 'email') {
            $email_fields[] = $field;
        }
    }
    if (empty($email_fields)) {
        return false;
    }

    return $email_fields;
}

/**
 * Checks for unused fields in the User and Admin Notification pages
 *
 * @param array $form_id
 * @param array $form_data
 * @param array $form_meta
 *
 * @return bool
 */
function wpbs_form_notifications_check_unused_fields($form_id, $form_data, $form_meta)
{

    $used_fields = array();

    $available_fields = array();

    // Go through both notification types
    foreach (array('admin_notification') as $notification_type) {

        // Skip if notification is not enabled
        if (wpbs_get_form_meta($form_id, $notification_type . '_enable', true) != 'on') {
            continue;
        }

        foreach ($form_meta as $meta_key => $meta_value) {
            // Skip if field is not notification related
            if (strpos($meta_key, $notification_type) === false) {
                continue;
            }

            if (!wpbs_form_get_email_tag_ids($meta_value[0])) {
                continue;
            }

            $found_tags = wpbs_form_get_email_tag_ids($meta_value[0]);

            foreach ($found_tags as $tag) {
                // If it's a general tag, continue
                if (!is_numeric($tag)) {
                    continue;
                }

                $used_fields[] = $tag;
            }

        }
    }

    // Set Available Fields
    foreach ($form_data as $field) {
        $available_fields[] = $field['id'];
    }
    // Check fields
    foreach ($used_fields as $used_field) {
        if (!in_array($used_field, $available_fields)) {
            return false;
        }
    }

    return true;

}

/**
 * Paste as Text on email builder TinyMCE
 *
 */
function wpbs_tinmyce_enable_paste_as_text()
{
    $screen = get_current_screen();

    if ($screen->id != 'wp-booking-system_page_wpbs-forms') {
        return false;
    }

    // always paste as plain text
    add_filter('teeny_mce_before_init', function ($mceInit) {
        $mceInit['paste_text_sticky'] = true;
        $mceInit['paste_text_sticky_default'] = true;
        return $mceInit;
    });

    // load 'paste' plugin in minimal/pressthis editor
    add_filter('teeny_mce_plugins', function ($plugins) {
        $plugins[] = 'paste';
        return $plugins;
    });
}
add_action('current_screen', 'wpbs_tinmyce_enable_paste_as_text');

function wpbs_output_email_tags($form_data)
{
    $output = '';

    $output .= '<div class="wpbs-email-tags">';
    $output .= '<div class="wpbs-email-tag"><div>{All Fields}</div></div>';
    $output .= '<div class="wpbs-email-tag"><div>{Start Date}</div></div>';
    $output .= '<div class="wpbs-email-tag"><div>{End Date}</div></div>';
    $output .= '<div class="wpbs-email-tag"><div>{Calendar Title}</div></div>';

    $output .= apply_filters('wpbs_output_email_tags', '');

    foreach ($form_data as $field):

        if (in_array($field['type'], array('html', 'captcha'))) {
            continue;
        }

        $label = (!empty($field['values']['default']['label'])) ? $field['values']['default']['label'] : __('no-label', 'wp-booking-system');
        $output .= ' <div class="wpbs-email-tag"><div>{' . $field['id'] . ':' . $label . '}</div></div>';
    endforeach;
    $output .= '</div>';

    echo $output;
}

/**
 * Default form error messages
 * 
 */
function wpbs_form_default_strings()
{
    return array(
        'required_field' => __('This field is required.', 'wp-booking-system'),
        'invalid_email' => __('Invalid email address.', 'wp-booking-system'),
        'select_date' => __('Please select a date.', 'wp-booking-system'),
        'validation_errors' => __('Please check the fields below for errors.', 'wp-booking-system')
    );
}
