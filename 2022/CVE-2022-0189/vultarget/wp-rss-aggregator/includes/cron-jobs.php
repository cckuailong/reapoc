<?php

define('WPRA_FETCH_ALL_FEEDS_HOOK', 'wprss_fetch_all_feeds_hook');
define('WPRA_FETCH_FEED_HOOK', 'wprss_fetch_single_feed_hook');
define('WPRA_TRUNCATE_ITEMS_HOOK', 'wprss_truncate_posts_hook');
define('WPRA_ACTIVATE_FEED_HOOK', 'wprss_activate_feed_schedule_hook');
define('WPRA_PAUSE_FEED_HOOK', 'wprss_pause_feed_schedule_hook');

define('WPRA_TRUNCATE_ITEMS_INTERVAL', 'daily');

/**
 * Alias for add_action, primarily used for readability to distinguish between cron-events and normal hooks.
 *
 * @since 4.17
 *
 * @param string   $cron     The cron hook event.
 * @param callable $callback The callback to invoke for the cron.
 */
function wpra_on_cron_do($cron, $callback)
{
    add_action($cron, $callback);
}

// Cron events
wpra_on_cron_do(WPRA_FETCH_ALL_FEEDS_HOOK, 'wprss_fetch_insert_all_feed_items_from_cron');
wpra_on_cron_do(WPRA_TRUNCATE_ITEMS_HOOK, 'wprss_truncate_posts');
wpra_on_cron_do(WPRA_ACTIVATE_FEED_HOOK, 'wprss_activate_feed_source');
wpra_on_cron_do(WPRA_PAUSE_FEED_HOOK, 'wprss_pause_feed_source');

// Initialize crons that must always be scheduled
add_action('init', 'wpra_init_crons');

// When a feed source is activated, schedule its fetch cron
add_action('wprss_on_feed_source_activated', 'wprss_feed_source_update_start_schedule');

// When a feed source is paused, cancel its fetch cron
add_action('wprss_on_feed_source_paused', 'wprss_feed_source_update_stop_schedule');

// Filter the possible cron intervals to add more options
add_filter('cron_schedules', 'wprss_filter_cron_schedules');

/**
 * Initializes the cron jobs.
 *
 * @since 4.17
 */
function wpra_init_crons()
{
    wprss_schedule_fetch_all_feeds_cron();
    wprss_schedule_truncate_posts_cron();
}

/**
 * Creates the cron to fetch feeds.
 *
 * @since 2.0
 */
function wprss_schedule_fetch_all_feeds_cron()
{
    // Check if the global fetch is scheduled
    if (wp_next_scheduled(WPRA_FETCH_ALL_FEEDS_HOOK)) {
        return;
    }

    // If the event is not scheduled, schedule it
    $interval = wprss_get_general_setting('cron_interval');
    wp_schedule_event(time(), $interval, WPRA_FETCH_ALL_FEEDS_HOOK);
}

/**
 * Gets the time of the global fetch cron.
 *
 * @since 4.17
 *
 * @return false|string A time string in the form `H:i`
 */
function wprss_get_global_update_time()
{
    // If the global fetch cron is not scheduled, schedule it
    wprss_schedule_fetch_all_feeds_cron();

    // Get the timestamp for the next run
    $next = wp_next_scheduled(WPRA_FETCH_ALL_FEEDS_HOOK);

    return date('H:i', $next);
}

/**
 * Creates the cron to truncate wprss_feed_item posts daily
 *
 * @since 2.0
 */
function wprss_schedule_truncate_posts_cron()
{
    // Check if the truncatation cron is scheduled
    if (wp_next_scheduled(WPRA_TRUNCATE_ITEMS_HOOK)) {
        return;
    }

    // If not, schedule it
    wp_schedule_event(time(), WPRA_TRUNCATE_ITEMS_INTERVAL, WPRA_TRUNCATE_ITEMS_HOOK);
}

/**
 * Updates the feed processing cron job schedules.
 * Removes the current schedules and adds the ones in the feed source's meta.
 *
 * @since 3.8
 *
 * @param int $feed_id The id of the wprss_feed
 */
function wprss_update_feed_processing_schedules($feed_id)
{
    // Get the feed's activate and pause times
    $activate = get_post_meta($feed_id, 'wprss_activate_feed', true);
    $pause = get_post_meta($feed_id, 'wprss_pause_feed', true);

    // Parse as time strings
    $activate = wprss_strtotime($activate);
    $pause = wprss_strtotime($pause);

    if (!empty($activate)) {
        wpra_reschedule($activate, WPRA_ACTIVATE_FEED_HOOK, null, [$feed_id]);
    }

    if ($pause !== '') {
        wpra_reschedule($pause, WPRA_PAUSE_FEED_HOOK, null, [$feed_id]);
    }
}

/**
 * Starts the looping schedule for a feed source. Runs on a schedule
 *
 * @since 3.9
 *
 * @param int $feed_id The ID of the feed source
 */
function wprss_feed_source_update_start_schedule($feed_id)
{
    // Stop any currently scheduled update operations
    wprss_feed_source_update_stop_schedule($feed_id);

    // Get the interval
    $interval = get_post_meta($feed_id, 'wprss_update_interval', true);
    // Do nothing if the feed source has no update interval (not sure if possible) or if the interval
    // is set to global
    if ($interval === '' || $interval === wprss_get_default_feed_source_update_interval()) {
        return;
    }

    wp_schedule_event(time(), $interval, WPRA_FETCH_FEED_HOOK, [strval($feed_id)]);
}

/**
 * Stops any scheduled update operations for a feed source. Runs on a schedule.
 *
 * @since 3.9
 *
 * @param int $feed_id The ID of the feed source ( wprss_feed )
 */
function wprss_feed_source_update_stop_schedule($feed_id)
{
    $timestamp = wprss_get_next_feed_source_update($feed_id);

    // If a schedule exists, unschedule it
    if ($timestamp !== false) {
        wp_unschedule_event($timestamp, WPRA_FETCH_FEED_HOOK, [strval($feed_id)]);
    }
}

/**
 * Returns the timestamp for the next global update
 *
 * @since 4.18
 *
 * @return int The timestamp of the next global update operation, or false if no update is scheduled.
 */
function wprss_get_next_global_update()
{
    return wp_next_scheduled(WPRA_FETCH_ALL_FEEDS_HOOK, []);
}

/**
 * Returns the timestamp for the next feed source update
 *
 * @since 3.9
 *
 * @param int  $feed_id  The ID of the feed source ( wprss_feed )
 * @param bool $explicit If true, the function won't default to the global update if the feed doesn't use its own
 *                       update interval.
 *
 * @return int The timestamp of the next update operation, or false if no update is scheduled.
 */
function wprss_get_next_feed_source_update($feed_id, $explicit = true)
{
    $next = wp_next_scheduled(WPRA_FETCH_FEED_HOOK, [strval($feed_id)]);

    if ($explicit) {
        return $next;
    }

    $meta = get_post_meta($feed_id, 'wprss_update_interval', true);

    return (empty($meta) || $meta === wprss_get_default_feed_source_update_interval())
        ? wprss_get_next_global_update()
        : $next;
}

/**
 * Reschedules a cron event, unscheduling any existing matching crons.
 *
 * @since 4.17
 *
 * @param int         $timestamp  The timestamp.
 * @param string      $event      The hook event.
 * @param string|null $recurrence The recurrence.
 * @param array       $args       Additional args.
 */
function wpra_reschedule($timestamp, $event, $recurrence = null, $args = [])
{
    $existing = wp_next_scheduled($event, $args);

    if ($existing !== false) {
        wp_unschedule_event($existing, $event, $args);
    }

    if ($recurrence === null) {
        wp_schedule_single_event($timestamp, $event, $args);
    } else {
        wp_schedule_event($timestamp, $recurrence, $event, $args);
    }
}

/**
 * Clears all events scheduled to a particular hook, regardless of their args.
 *
 * @since 4.17.9
 *
 * @param string $hook
 */
function wpra_clear_all_scheduled_hooks($hook)
{
    foreach (wpra_get_crons() as $key => $events) {
        if ($key === $hook) {
            foreach ($events as $event) {
                wp_clear_scheduled_hook($key, $event->args);
            }
        }
    }
}

/**
 * Retrieves all cron jobs from WordPress.
 *
 * @since 4.17.9
 *
 * @return array A mapping of hook names to sub-arrays of even objects.
 */
function wpra_get_crons()
{
    $cronsArray = _get_cron_array();
    $crons = [];
    foreach ($cronsArray as $ts => $list) {
        foreach ($list as $hook => $events) {
            $crons[$hook] = isset($crons[$hook]) ? $crons[$hook] : [];

            foreach ($events as $event) {
                $crons[$hook][] = (object) $event;
            }
        }
    }

    return $crons;
}

/**
 * Retrieves the cron schedules that WPRA uses.
 *
 * @since 4.17
 *
 * @return array
 */
function wpra_get_cron_schedules()
{
    return [
        'five_min' => array(
            'display' => __('Once every 5 minutes', 'wprss'),
            'interval' => MINUTE_IN_SECONDS * 5,
        ),
        'ten_min' => array(
            'display' => __('Once every 10 minutes', 'wprss'),
            'interval' => MINUTE_IN_SECONDS * 10,
        ),
        'fifteen_min' => array(
            'display' => __('Once every 15 minutes', 'wprss'),
            'interval' => MINUTE_IN_SECONDS * 15,
        ),
        'thirty_min' => array(
            'display' => __('Once every 30 minutes', 'wprss'),
            'interval' => MINUTE_IN_SECONDS * 30,
        ),
        'two_hours' => array(
            'display' => __('Once every 2 hours', 'wprss'),
            'interval' => HOUR_IN_SECONDS * 2,
        ),
        'weekly' => array(
            'display' => __('Once weekly', 'wprss'),
            'interval' => WEEK_IN_SECONDS,
        ),
    ];
}

/**
 * Registers the cron schedules to WordPress, avoiding duplicates.
 *
 * @since 3.0
 */
function wprss_filter_cron_schedules($schedules)
{
    // Register each WPRA schedule
    $wpraSchedules = wpra_get_cron_schedules();
    foreach ($wpraSchedules as $key => $schedule) {
        // If the interval already exists, skip the schedule
        if (wprss_schedule_interval_already_exists($schedules, $schedule['interval'])) {
            continue;
        }

        $schedules[$key] = $schedule;
    }

    return $schedules;
}

/**
 * Checks if a schedule interval already exists in a given list os schedules.
 *
 * @see wprss_filter_cron_schedules()
 *
 * @param array $schedules The schedules to search in.
 * @param int $interval The interval to search for.
 *
 * @return bool
 */
function wprss_schedule_interval_already_exists(array $schedules, $interval) {
    foreach ($schedules as $schedule) {
        if (isset($schedule['interval']) && $schedule['interval'] == $interval) {
            return true;
        }
    }

    return false;
}

/**
 * Deletes a custom cron schedule.
 *
 * Credits: WPCrontrol
 *
 * @since 3.7
 *
 * @param string $name The internal_name of the schedule to delete.
 */
function wprss_delete_schedule($name)
{
    $scheds = get_option('crontrol_schedules', array());
    unset($scheds[$name]);
    update_option('crontrol_schedules', $scheds);
}

/**
 * Parses the date time string into a UTC timestamp.
 * The string must be in the format: m/d/y h:m:s
 *
 * @since 3.9
 */
function wprss_strtotime($str)
{
    if (empty($str)) {
        return 0;
    }

    $parts = explode(' ', $str);
    $date = explode('/', $parts[0]);
    $time = explode(':', $parts[1]);

    return mktime($time[0], $time[1], $time[2], $date[1], $date[0], $date[2]);
}

/**
 * Returns the default value for the per feed source update interval
 *
 * @since 3.9
 */
function wprss_get_default_feed_source_update_interval()
{
    return 'global';
}
