<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function wpbs_action_ajax_open_booking_details()
{
    
    // Nonce
    check_ajax_referer('wpbs_open_booking_details', 'wpbs_token');

    if (!isset($_POST['id'])) {
        return false;
    }

    $booking_id = absint($_POST['id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return;
    }

    // If booking is unread, make it read
    if ($booking->get('is_read') == 0) {
        $booking_data = array(
            'is_read' => 1,
        );
        wpbs_update_booking($booking_id, $booking_data);
    }

    // Get modal content
    $booking_display = new WPBS_Booking_Details_Outputter($booking);
    $booking_display->display();

    wp_die();

}
add_action('wp_ajax_wpbs_open_booking_details', 'wpbs_action_ajax_open_booking_details');

function wpbs_action_ajax_booking_email_customer()
{   
    // Nonce
    check_ajax_referer('wpbs_booking_email_customer', 'wpbs_token');
    
    if (!isset($_POST['id'])) {
        return false;
    }

    $booking_id = absint($_POST['id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return;
    }
    parse_str($_POST['form_data'], $_POST['form_data']);

    // Send the email
    $mailer = new WPBS_Booking_Mailer($booking, $_POST['form_data']);
    $mailer->prepare('customer');
    $mailer->send();
    
    echo __('Email successfully sent.', 'wp-booking-system');

    wp_die();

}
add_action('wp_ajax_wpbs_booking_email_customer', 'wpbs_action_ajax_booking_email_customer');