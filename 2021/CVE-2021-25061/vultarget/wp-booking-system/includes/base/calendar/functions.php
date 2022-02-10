<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Calendars
 *
 */
function wpbs_include_files_calendar()
{

    // Get calendar dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include other functions files
    if (file_exists($dir_path . 'functions-ajax.php')) {
        include $dir_path . 'functions-ajax.php';
    }

    // Include main Calendar class
    if (file_exists($dir_path . 'class-calendar.php')) {
        include $dir_path . 'class-calendar.php';
    }

    // Include the db layer classes
    if (file_exists($dir_path . 'class-object-db-calendars.php')) {
        include $dir_path . 'class-object-db-calendars.php';
    }

    if (file_exists($dir_path . 'class-object-meta-db-calendars.php')) {
        include $dir_path . 'class-object-meta-db-calendars.php';
    }

    // Include calendar outputters
    if (file_exists($dir_path . 'class-calendar-outputter.php')) {
        include $dir_path . 'class-calendar-outputter.php';
    }


}
add_action('wpbs_include_files', 'wpbs_include_files_calendar');

/**
 * Register the class that handles database queries for the Calendars
 *
 * @param array $classes
 *
 * @return array
 *
 */
function wpbs_register_database_classes_calendars($classes)
{

    $classes['calendars'] = 'WPBS_Object_DB_Calendars';
    $classes['calendarmeta'] = 'WPBS_Object_Meta_DB_Calendars';

    return $classes;

}
add_filter('wpbs_register_database_classes', 'wpbs_register_database_classes_calendars');

/**
 * Returns an array with WPBS_Calendar objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function wpbs_get_calendars($args = array(), $count = false)
{

    $calendars = wp_booking_system()->db['calendars']->get_calendars($args, $count);

    /**
     * Add a filter hook just before returning
     *
     * @param array $calendars
     * @param array $args
     * @param bool  $count
     *
     */
    return apply_filters('wpbs_get_calendars', $calendars, $args, $count);

}

/**
 * Gets a calendar from the database
 *
 * @param mixed int|object      - calendar id or object representing the calendar
 *
 * @return WPBS_Calendar|false
 *
 */
function wpbs_get_calendar($calendar)
{

    return wp_booking_system()->db['calendars']->get_object($calendar);

}

/**
 * Inserts a new calendar into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function wpbs_insert_calendar($data)
{

    return wp_booking_system()->db['calendars']->insert($data);

}

/**
 * Updates a calendar from the database
 *
 * @param int     $calendar_id
 * @param array $data
 *
 * @return bool
 *
 */
function wpbs_update_calendar($calendar_id, $data)
{

    return wp_booking_system()->db['calendars']->update($calendar_id, $data);

}

/**
 * Deletes a calendar from the database
 *
 * @param int $calendar_id
 *
 * @return bool
 *
 */
function wpbs_delete_calendar($calendar_id)
{

    return wp_booking_system()->db['calendars']->delete($calendar_id);

}

/**
 * Inserts a new meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function wpbs_add_calendar_meta($calendar_id, $meta_key, $meta_value, $unique = false)
{

    return wp_booking_system()->db['calendarmeta']->add($calendar_id, $meta_key, $meta_value, $unique);

}

/**
 * Updates a meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function wpbs_update_calendar_meta($calendar_id, $meta_key, $meta_value, $prev_value = '')
{

    return wp_booking_system()->db['calendarmeta']->update($calendar_id, $meta_key, $meta_value, $prev_value);

}

/**
 * Returns a meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function wpbs_get_calendar_meta($calendar_id, $meta_key = '', $single = false)
{

    return wp_booking_system()->db['calendarmeta']->get($calendar_id, $meta_key, $single);

}

/**
 * Removes a meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function wpbs_delete_calendar_meta($calendar_id, $meta_key, $meta_value = '', $delete_all = '')
{

    return wp_booking_system()->db['calendarmeta']->delete($calendar_id, $meta_key, $meta_value, $delete_all);

}

/**
 * Returns the default arguments for the calendar outputter
 *
 * @return array
 *
 */
function wpbs_get_calendar_output_default_args()
{

    $args = array(
        'show_title' => 1,
        'show_legend' => 1,
        'current_year' => date('Y'),
        'current_month' => date('n'),
        'language' => wpbs_get_locale(),
        'min_width' => '200',
        'max_width' => '380',
        'start_date' => 0,
        'end_date' => 0,
    );

    /**
     * Filter the args before returning
     *
     * @param array $args
     *
     */
    $args = apply_filters('wpbs_get_calendar_output_default_args', $args);

    return $args;

}


/**
 * Generates and returns a random 32 character long string
 *
 * @return string
 *
 */
function wpbs_generate_ical_hash()
{

    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars_length = strlen($chars);
    $ical_hash = '';

    for ($i = 0; $i < 19; $i++) {

        $ical_hash .= $chars[rand(0, $chars_length - 1)];

    }

    return $ical_hash . uniqid();

}

/**
 * Returns an array with all iCal feeds saved in the database
 *
 * @param int $calendar_id
 *
 * @return array
 *
 */
function wpbs_get_calendar_meta_ical_feeds($calendar_id)
{

    global $wpdb;

    $calendar_id = absint($calendar_id);
    $table_name = wp_booking_system()->db['calendarmeta']->table_name;

    $results = $wpdb->get_results("SELECT meta_value FROM {$table_name} WHERE calendar_id = '{$calendar_id}' AND meta_key LIKE '%ical_feed_%'", ARRAY_A);

    if (!is_array($results)) {
        return array();
    }

    foreach ($results as $key => $result) {

        $meta_value = $results[$key]['meta_value'];

        unset($results[$key]);

        $results[$key] = maybe_unserialize($meta_value);

    }

    return $results;

}

/**
 * Returns the last added ical_feed id
 *
 * @param int $calendar_id
 *
 * @return int
 *
 */
function wpbs_get_ical_feeds_last_id($calendar_id)
{

    $ical_feeds = wpbs_get_calendar_meta_ical_feeds($calendar_id);
    $last_id = 0;

    foreach ($ical_feeds as $ical_feed) {

        if ($ical_feed['id'] > $last_id) {
            $last_id = $ical_feed['id'];
        }

    }

    return $last_id;

}


/**
 * Gets all the bookings, loops through the interval and returns WPBS_Event objects
 *
 * @param int $calendar_id
 *
 * @return array
 *
 */
function wpbs_get_bookings_as_events($calendar_id)
{

    $booking_events = array();

    $bookings = wpbs_get_bookings(array('calendar_id' => $calendar_id, 'orderby' => 'id', 'order' => 'asc', 'status' => array('pending', 'accepted')));

    foreach ($bookings as $booking) {
        $events_begin = new DateTime($booking->get('start_date'));

        $events_end = new DateTime($booking->get('end_date'));
        $events_end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($events_begin, $interval, $events_end);

        foreach ($period as $event_date) {

            $event_data = array(
                'id' => null,
                'calendar_id' => $calendar_id,
                'booking_id' => $booking->get('id'),
                'date_year' => $event_date->format('Y'),
                'date_month' => $event_date->format('m'),
                'date_day' => $event_date->format('d'),
            );
            $booking_events[] = wpbs_get_event((object) $event_data);
        }
    }

    return $booking_events;

}
