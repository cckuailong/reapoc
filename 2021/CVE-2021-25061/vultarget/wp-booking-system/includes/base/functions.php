<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the Base files
 *
 */
function wpbs_include_files_base()
{

    // Get legend dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include languages functions
    if (file_exists($dir_path . 'functions-languages.php')) {
        include $dir_path . 'functions-languages.php';
    }

    // Include utils functions
    if (file_exists($dir_path . 'functions-utils.php')) {
        include $dir_path . 'functions-utils.php';
    }

    // Include update functions
    if (file_exists($dir_path . 'functions-update.php')) {
        include $dir_path . 'functions-update.php';
    }

    // Include the shortcodes class
    if (file_exists($dir_path . 'class-shortcodes.php')) {
        include $dir_path . 'class-shortcodes.php';
    }

    // Include the widget class
    if (file_exists($dir_path . 'class-widget-calendar.php')) {
        include $dir_path . 'class-widget-calendar.php';
    }

    // Include the base mailer
    if (file_exists($dir_path . 'class-mailer.php')) {
        include $dir_path . 'class-mailer.php';
    }

}
add_action('wpbs_include_files', 'wpbs_include_files_base');

/**
 * Returns an array with the weekdays
 *
 * @return array
 *
 */
function wpbs_get_weekdays()
{

    $weekdays = array(
        __('Monday', 'wp-booking-system'),
        __('Tuesday', 'wp-booking-system'),
        __('Wednesday', 'wp-booking-system'),
        __('Thursday', 'wp-booking-system'),
        __('Friday', 'wp-booking-system'),
        __('Saturday', 'wp-booking-system'),
        __('Sunday', 'wp-booking-system'),
    );

    return $weekdays;

}

/**
 * Returns true if there are any active languages in settings, false if not.
 *
 * @return bool
 *
 */
function wpbs_translations_active()
{

    $settings = get_option('wpbs_settings', array());

    if (!isset($settings['active_languages'])) {
        return false;
    }

    if(empty($settings['active_languages'])) {
        return false;
    }

    return true;

}

