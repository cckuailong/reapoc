<?php
/**
 *  WordPress initializing
 */
function mec_find_wordpress_base_path_ef()
{
    $dir = dirname(__FILE__);
    
    do
    {
        if(file_exists($dir.'/wp-load.php') and file_exists($dir.'/wp-config.php')) return $dir;
    }
    while($dir = realpath($dir.'/..'));
    
    return NULL;
}

define('BASE_PATH', mec_find_wordpress_base_path_ef().'/');
if(!defined('WP_USE_THEMES')) define('WP_USE_THEMES', false);

global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
require BASE_PATH.'wp-load.php';

/** @var $main MEC_main **/

// MEC libraries
$main = MEC::getInstance('app.libraries.main');

// Blogs
$blogs = array(1);

// Current Blog ID
$multisite = (function_exists('is_multisite') and is_multisite());
$current_blog_id = get_current_blog_id();

// Database
$db = $main->getDB();

// Multisite
if($multisite) $blogs = $db->select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');

$sent_notifications = 0;
$now = current_time('Y-m-d H:i');

foreach($blogs as $blog)
{
    // Switch to Blog
    if($multisite) switch_to_blog($blog);

    // MEC notifications
    $notifications = $main->get_notifications();

    // MEC Settings
    $settings = $main->get_settings();

    // Booking is disabled
    if(!isset($settings['booking_status']) or (isset($settings['booking_status']) and !$settings['booking_status'])) continue;

    $hour = isset($notifications['event_finished']['hour']) ? trim($notifications['event_finished']['hour'], ', ') : 2;

    // Hour is invalid
    if(!$hour or ($hour and $hour < 0)) continue;

    // Check Last Run Date & Time
    $latest_run = get_option('mec_event_finished_last_run_datetime', NULL);
    if($latest_run and strtotime($latest_run) > strtotime('-1 Hour', strtotime($now))) continue;

    /**
     * Notification Sender Library
     * @var $notif MEC_notifications
     */
    $notif = $main->getNotifications();

    // It's time of the hour that we're going to check
    $time = strtotime('-'.$hour.' hours', strtotime($now));

    $tstart = floor($time / 3600) * 3600;
    $tend = $time + 3600;

    $mec_dates = $db->select("SELECT `post_id`, `tstart`, `tend` FROM `#__mec_dates` WHERE `tend` >= $tstart AND `tend` < $tend", 'loadObjectList');
    if(!count($mec_dates)) continue;

    foreach($mec_dates as $mec_date)
    {
        if(!get_post($mec_date->post_id)) continue;

        $result = $notif->event_finished($mec_date->post_id, $mec_date->tstart.':'.$mec_date->tend);
        if($result) $sent_notifications++;
    }

    // Last Run
    update_option('mec_event_finished_last_run_datetime', $now, false);
}

// Switch to Current Blog
if($multisite) switch_to_blog($current_blog_id);

echo sprintf(__('%s notification(s) sent.', 'modern-events-calendar-lite'), $sent_notifications);
exit;