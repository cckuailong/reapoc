<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Bookings
 *
 */
function wpbs_include_files_booking()
{

    // Get booking dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include main Booking class
    if (file_exists($dir_path . 'class-booking.php')) {
        include $dir_path . 'class-booking.php';
    }

    // Include the db layer classes
    if (file_exists($dir_path . 'class-object-db-bookings.php')) {
        include $dir_path . 'class-object-db-bookings.php';
    }

    if (file_exists($dir_path . 'class-object-meta-db-bookings.php')) {
        include $dir_path . 'class-object-meta-db-bookings.php';
    }

}
add_action('wpbs_include_files', 'wpbs_include_files_booking');

/**
 * Register the class that handles database queries for the Bookings
 *
 * @param array $classes
 *
 * @return array
 *
 */
function wpbs_register_database_classes_bookings($classes)
{

    $classes['bookings'] = 'WPBS_Object_DB_Bookings';
    $classes['bookingmeta'] = 'WPBS_Object_Meta_DB_Bookings';

    return $classes;

}
add_filter('wpbs_register_database_classes', 'wpbs_register_database_classes_bookings');

/**
 * Returns an array with WPBS_Booking objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function wpbs_get_bookings($args = array(), $count = false)
{

    $bookings = wp_booking_system()->db['bookings']->get_bookings($args, $count);

    /**
     * Add a filter hook just before returning
     *
     * @param array $bookings
     * @param array $args
     * @param bool  $count
     *
     */
    return apply_filters('wpbs_get_bookings', $bookings, $args, $count);

}

/**
 * Gets a booking from the database
 *
 * @param mixed int|object      - booking id or object representing the booking
 *
 * @return WPBS_Booking|false
 *
 */
function wpbs_get_booking($booking)
{

    return wp_booking_system()->db['bookings']->get_object($booking);

}

/**
 * Inserts a new booking into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function wpbs_insert_booking($data)
{

    return wp_booking_system()->db['bookings']->insert($data);

}

/**
 * Updates a booking from the database
 *
 * @param int     $booking_id
 * @param array $data
 *
 * @return bool
 *
 */
function wpbs_update_booking($booking_id, $data)
{

    return wp_booking_system()->db['bookings']->update($booking_id, $data);

}

/**
 * Deletes a booking from the database
 *
 * @param int $booking_id
 *
 * @return bool
 *
 */
function wpbs_delete_booking($booking_id)
{

    return wp_booking_system()->db['bookings']->delete($booking_id);

}

/**
 * Inserts a new meta entry for the booking
 *
 * @param int    $booking_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function wpbs_add_booking_meta($booking_id, $meta_key, $meta_value, $unique = false)
{

    return wp_booking_system()->db['bookingmeta']->add($booking_id, $meta_key, $meta_value, $unique);

}

/**
 * Updates a meta entry for the booking
 *
 * @param int    $booking_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function wpbs_update_booking_meta($booking_id, $meta_key, $meta_value, $prev_value = '')
{

    return wp_booking_system()->db['bookingmeta']->update($booking_id, $meta_key, $meta_value, $prev_value);

}

/**
 * Returns a meta entry for the booking
 *
 * @param int    $booking_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function wpbs_get_booking_meta($booking_id, $meta_key = '', $single = false)
{

    return wp_booking_system()->db['bookingmeta']->get($booking_id, $meta_key, $single);

}

/**
 * Returns the translated meta entry for the booking
 *
 * @param int    $booking_id
 * @param string $meta_key
 * @param string $language_code
 *
 * @return mixed
 *
 */
function wpbs_get_translated_booking_meta($booking_id, $meta_key, $language_code)
{
    $translated_meta = wpbs_get_booking_meta($booking_id, $meta_key . '_translation_' . $language_code, true);

    if (!empty($translated_meta)) {
        return $translated_meta;
    }

    return wpbs_get_booking_meta($booking_id, $meta_key, true);
}

/**
 * Removes a meta entry for the booking
 *
 * @param int    $booking_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function wpbs_delete_booking_meta($booking_id, $meta_key, $meta_value = '', $delete_all = '')
{

    return wp_booking_system()->db['bookingmeta']->delete($booking_id, $meta_key, $meta_value, $delete_all);

}
