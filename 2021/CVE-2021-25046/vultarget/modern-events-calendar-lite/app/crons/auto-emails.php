<?php
/**
 *  WordPress initializing
 */
function mec_find_wordpress_base_path_ae()
{
    $dir = dirname(__FILE__);
    
    do
    {
        if(file_exists($dir.'/wp-load.php') and file_exists($dir.'/wp-config.php')) return $dir;
    }
    while($dir = realpath($dir.'/..'));
    
    return NULL;
}

define('BASE_PATH', mec_find_wordpress_base_path_ae().'/');
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

$sent_emails = 0;
$now = current_time('Y-m-d H:i');

$now_start = strtotime($now);
$now_end = $now_start + 60;

foreach($blogs as $blog)
{
    // Switch to Blog
    if($multisite) switch_to_blog($blog);

    // MEC Settings
    $settings = $main->get_settings();

    // Auto Emails is disabled
    if(!isset($settings['auto_emails_module_status']) or (isset($settings['auto_emails_module_status']) and !$settings['auto_emails_module_status'])) continue;

    /**
     * Notification Sender Library
     * @var $notif MEC_notifications
     */
    $notif = $main->getNotifications();

    // All Emails
    $emails = get_posts(array('post_type'=>$main->get_email_post_type(), 'numberposts'=>-1, 'post_status'=>'publish'));

    // Send Emails
    foreach($emails as $email)
    {
        $time = get_post_meta($email->ID, 'mec_time', true);
        if($time == '') $time = 1;

        $type = get_post_meta($email->ID, 'mec_type', true);
        if($type == '') $type = 'day';

        $afterbefore = get_post_meta($email->ID, 'mec_afterbefore', true);
        if($afterbefore == '') $afterbefore = 'before';

        $all = get_post_meta($email->ID, 'mec_all', true);
        if($all == '') $all = 1;

        $events = get_post_meta($email->ID, 'mec_events', true);
        if(!is_array($events)) $events = array();

        if($type === 'day') $plus_minus = $time * (3600 * 24);
        elseif($type === 'hour') $plus_minus = $time * 3600;
        else $plus_minus = $time * 60;

        if($afterbefore === 'after') $plus_minus *= -1;

        $occ_start = $now_start + $plus_minus;
        $occ_end = $now_end + $plus_minus;

        $query = "SELECT `post_id`, `tstart`, `tend` FROM `#__mec_dates` WHERE `tstart`>=".$occ_start." AND `tend`<".$occ_end;
        if(!$all and count($events)) $query .= " AND `post_id` IN (".implode(',', $events).")";

        // Fetch Event Occurrences
        $occurrences = $db->select($query, 'loadObjectList');

        foreach($occurrences as $occurrence)
        {
            $bookings = $main->get_bookings($occurrence->post_id, $occurrence->tstart);

            foreach($bookings as $booking)
            {
                $result = $notif->auto_email($booking->ID, $email->post_title, $email->post_content, $occurrence->tstart.':'.$occurrence->tend);
                if($result) $sent_emails++;
            }
        }
    }
}

// Switch to Current Blog
if($multisite) switch_to_blog($current_blog_id);

echo sprintf(__('%s emails(s) sent.', 'modern-events-calendar-lite'), $sent_emails);
exit;