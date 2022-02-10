<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save booking data
 *
 */
function wpbs_save_booking_data($data)
{

    if (!isset($_POST['booking_id'])) {
        return false;
    }

    /**
     * Save Calendar Data
     */

    $booking_id = absint($_POST['booking_id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    // Get action
    $action = sanitize_text_field($_POST['booking_action']);

    // Set status
    if ($action == 'delete') {
        $status = 'trash';

        // Save current status
        wpbs_add_booking_meta($booking_id, 'before_trash_status', $booking->get('status'));

    } elseif ($action == 'restore') {
        
        // Get old status
        $status = (in_array(wpbs_get_booking_meta($booking_id, 'before_trash_status', true), array('pending', 'accepted'))) ? wpbs_get_booking_meta($booking_id, 'before_trash_status', true) : 'pending';

        // Delete it from the database
        wpbs_deelte_booking_meta($booking_id, 'before_trash_status');

    } else {
        $status = 'accepted';
    }

    // Prepare Data
    $booking_data = array(
        'status' => $status,
        'date_modified' => current_time('Y-m-d H:i:s'),
    );

    // Update Booking
    wpbs_update_booking($booking_id, $booking_data);

    /**
     * Send Email
     */

    // Parse $_POST data
    parse_str($_POST['email_form_data'], $_POST['email_form_data']);

    // Check if we need to send an email
    if (isset($_POST['email_form_data']['booking_email_accept_booking_enable']) && !empty($_POST['email_form_data']['booking_email_accept_booking_enable'])) {

        // Send the email
        $mailer = new WPBS_Booking_Mailer($booking, $_POST['email_form_data']);
        $mailer->prepare('accept_booking');
        $mailer->send();

    }

}
add_action('wpbs_save_calendar_data', 'wpbs_save_booking_data');

/**
 * Permanently Delete Booking
 *
 */
function wpbs_action_permanently_delete_booking()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_permanently_delete_booking')) {
        return;
    }

    if (empty($_GET['booking_id'])) {
        return;
    }

    if (empty($_GET['calendar_id'])) {
        return;
    }

    $booking_id = $_GET['booking_id'];

    $calendar_id = $_GET['calendar_id'];

    // Delete Booking
    wpbs_delete_booking($booking_id);

    // Delete Booking Meta
    $booking_meta = wpbs_get_booking_meta($booking_id);
    if (!empty($booking_meta)) {
        foreach ($booking_meta as $key => $value) {
            wpbs_delete_booking_meta($booking_id, $key);
        }
    }

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'edit-calendar', 'calendar_id' => $calendar_id, 'wpbs_message' => 'booking_permanently_delete_success'), admin_url('admin.php')));
}
add_action('wpbs_action_permanently_delete_booking', 'wpbs_action_permanently_delete_booking');
