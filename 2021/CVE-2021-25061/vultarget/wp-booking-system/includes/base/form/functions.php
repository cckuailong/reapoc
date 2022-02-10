<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Forms
 *
 */
function wpbs_include_files_form()
{

    // Get form dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include main Form class
    if (file_exists($dir_path . 'class-form.php')) {
        include $dir_path . 'class-form.php';
    }

    // Include other functions files
    if (file_exists($dir_path . 'functions-ajax.php')) {
        include $dir_path . 'functions-ajax.php';
    }

    // Include the db layer classes
    if (file_exists($dir_path . 'class-object-db-forms.php')) {
        include $dir_path . 'class-object-db-forms.php';
    }

    if (file_exists($dir_path . 'class-object-meta-db-forms.php')) {
        include $dir_path . 'class-object-meta-db-forms.php';
    }

    // Include the Form Outputter class
    if (file_exists($dir_path . 'class-form-outputter.php')) {
        include $dir_path . 'class-form-outputter.php';
    }

    // Include the Form Validator class
    if (file_exists($dir_path . 'class-form-validator.php')) {
        include $dir_path . 'class-form-validator.php';
    }

    // Include the Form Mailer class
    if (file_exists($dir_path . 'class-form-mailer.php')) {
        include $dir_path . 'class-form-mailer.php';
    }

}
add_action('wpbs_include_files', 'wpbs_include_files_form');

/**
 * Register the class that handles database queries for the Forms
 *
 * @param array $classes
 *
 * @return array
 *
 */
function wpbs_register_database_classes_forms($classes)
{

    $classes['forms'] = 'WPBS_Object_DB_Forms';
    $classes['formmeta'] = 'WPBS_Object_Meta_DB_Forms';

    return $classes;

}
add_filter('wpbs_register_database_classes', 'wpbs_register_database_classes_forms');

/**
 * Returns an array with WPBS_Form objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function wpbs_get_forms($args = array(), $count = false)
{

    $forms = wp_booking_system()->db['forms']->get_forms($args, $count);

    /**
     * Add a filter hook just before returning
     *
     * @param array $forms
     * @param array $args
     * @param bool  $count
     *
     */
    return apply_filters('wpbs_get_forms', $forms, $args, $count);

}

/**
 * Gets a form from the database
 *
 * @param mixed int|object      - form id or object representing the form
 *
 * @return WPBS_Form|false
 *
 */
function wpbs_get_form($form)
{

    return wp_booking_system()->db['forms']->get_object($form);

}

/**
 * Inserts a new form into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function wpbs_insert_form($data)
{

    return wp_booking_system()->db['forms']->insert($data);

}

/**
 * Updates a form from the database
 *
 * @param int     $form_id
 * @param array $data
 *
 * @return bool
 *
 */
function wpbs_update_form($form_id, $data)
{

    return wp_booking_system()->db['forms']->update($form_id, $data);

}

/**
 * Deletes a form from the database
 *
 * @param int $form_id
 *
 * @return bool
 *
 */
function wpbs_delete_form($form_id)
{

    return wp_booking_system()->db['forms']->delete($form_id);

}

/**
 * Inserts a new meta entry for the form
 *
 * @param int    $form_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function wpbs_add_form_meta($form_id, $meta_key, $meta_value, $unique = false)
{

    return wp_booking_system()->db['formmeta']->add($form_id, $meta_key, $meta_value, $unique);

}

/**
 * Updates a meta entry for the form
 *
 * @param int    $form_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function wpbs_update_form_meta($form_id, $meta_key, $meta_value, $prev_value = '')
{

    return wp_booking_system()->db['formmeta']->update($form_id, $meta_key, $meta_value, $prev_value);

}

/**
 * Returns a meta entry for the form
 *
 * @param int    $form_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function wpbs_get_form_meta($form_id, $meta_key = '', $single = false)
{

    return wp_booking_system()->db['formmeta']->get($form_id, $meta_key, $single);

}

/**
 * Returns the translated meta entry for the form
 *
 * @param int    $form_id
 * @param string $meta_key
 * @param string $language_code
 *
 * @return mixed
 *
 */
function wpbs_get_translated_form_meta($form_id, $meta_key, $language_code)
{
    $translated_meta = wpbs_get_form_meta($form_id, $meta_key . '_translation_' . $language_code, true);

    if (!empty($translated_meta)) {
        return $translated_meta;
    }

    return wpbs_get_form_meta($form_id, $meta_key, true);
}

/**
 * Removes a meta entry for the form
 *
 * @param int    $form_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function wpbs_delete_form_meta($form_id, $meta_key, $meta_value = '', $delete_all = '')
{

    return wp_booking_system()->db['formmeta']->delete($form_id, $meta_key, $meta_value, $delete_all);

}

/**
 * Returns the default arguments for the calendar outputter
 *
 * @return array
 *
 */
function wpbs_get_form_output_default_args()
{

    $args = array(
        'language' => wpbs_get_locale(),
    );

    /**
     * Filter the args before returning
     *
     * @param array $args
     *
     */
    $args = apply_filters('wpbs_get_form_output_default_args', $args);

    return $args;

}

/**
 * Get all email tag fields IDs used in a text, matching the {ID:NAME} format
 *
 * @param string    $text
 *
 * @return mixed
 *
 */
function wpbs_form_get_email_tag_ids($text)
{
    $found_tags = array();
    preg_match_all('/{(.*?)}/', $text, $matches);
    foreach ($matches[1] as $match) {
        $found_tags[] = wpbs_form_get_email_tag_id($match);
    }

    if (empty($found_tags)) {
        return false;
    }

    return $found_tags;
}

/**
 * Gets the id of an email tag
 *
 * @param string    $tag
 *
 * @return mixed
 *
 */
function wpbs_form_get_email_tag_id($tag)
{
    $tag = trim($tag, '{}');
    $match_id = explode(':', $tag);
    return $match_id[0];
}

/**
 * Get all email tag fields used in a text, matching the {ID:NAME} format
 *
 * @param string    $text
 *
 * @return mixed
 *
 */
function wpbs_form_get_email_tags($text)
{
    $found_tags = array();
    preg_match_all('/{(.*?)}/', $text, $matches);
    foreach ($matches[0] as $match) {
        $found_tags[] = $match;
    }

    if (empty($found_tags)) {
        return false;
    }

    return $found_tags;
}

/**
 * Helper function to convert JS timestamp to a PHP Timestamp
 *
 * @param int $js_timestamp
 *
 * @return int
 *
 */
function wpbs_convert_js_to_php_timestamp($js_timestamp)
{
    $php_timestamp = ceil($js_timestamp / 1000);
    return mktime(0, 0, 0, date('n', $php_timestamp), date('j', $php_timestamp), date('Y', $php_timestamp));
}

/**
 * Helper function that returns the field value
 *
 * @param mixed $value
 *
 * @return string
 */

function wpbs_get_field_display_user_value($value)
{
    // Check if it's empty
    if (empty($value)) {
        return '-';
    }

    // Check if it's an array
    if (is_array($value)) {
        return trim(implode(', ', $value), ', ');
    }
    return $value;
}

/**
 * List of Form Fields to exclude from display templates or emails
 * 
 */
function wpbs_get_excluded_fields(){
    return array('html', 'hidden', 'captcha', 'total');
}
