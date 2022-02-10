<?php

/**
 * This is a module responsible for facilitating the "Leave a Review" notification.
 *
 * Use the `WPRSS_LEAVE_REVIEW_NOTIFICATION_DELAY` constant to change the amount of time (in seconds)
 * that the notice will be delayed by after first installation date. This date is determined by an option created
 * during plugin activation, if not already present. When retrieving this value, if it does not exist
 * (perhaps because this update was installed after activation happened), it will be deduced from the creation time
 * of the oldest feed source. If no feed sources exist, the time at that moment will be determined as the
 * first activation time from that point onward.
 */

// How much time must pass from activation to display notice
if (!defined('WPRSS_LEAVE_REVIEW_NOTIFICATION_DELAY')) {
    define('WPRSS_LEAVE_REVIEW_NOTIFICATION_DELAY', 60 * 60 * 24 * 7 * 4 * 1); // 1 month
}

// How many active feed sources to display notice
if (!defined('WPRSS_LEAVE_REVIEW_NOTIFICATION_MIN_ACTIVE_FEED_SOURCES')) {
    define('WPRSS_LEAVE_REVIEW_NOTIFICATION_MIN_ACTIVE_FEED_SOURCES', 1);
}

add_action('wprss_init', function() {
    wprss()->getLeaveReviewNotification();
});
