<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajax callback when submitting the form
 *
 */
function wpbs_submit_form()
{
    // Nonce
    check_ajax_referer('wpbs_form_ajax', 'wpbs_token');

    // Get Form ID
    $form_id = absint(!empty($_POST['form']['id']) ? $_POST['form']['id'] : 0);
    $form = wpbs_get_form($form_id);

    if (is_null($form)) {
        return;
    }

    $calendar_id = absint(!empty($_POST['calendar']['id']) ? $_POST['calendar']['id'] : 0);
    $calendar = wpbs_get_calendar($calendar_id);

    if (is_null($calendar)) {
        return;
    }

    // Validate Form
    $form_validator = new WPBS_Form_Validator($form, $calendar, $_POST['form_data'], $_POST['form']['language']);
    $form_validator->sanitize_fields();
    $form_validator->validate_fields();
    $form_validator->validate_dates($_POST['form'], $_POST['calendar']);
    $form_validator->set_error_message();

    // Get form fields after sanitization and validation
    $form_fields = $form_validator->get_form_fields();

    // Set the form arguments
    $form_args = array(
        'language' => ($_POST['form']['language'] == 'auto' ? wpbs_get_locale() : $_POST['form']['language']),
    );

    if ($form_validator->has_errors() === true) {
        
        // Errors were found, we show the form again
        $form_outputter = new WPBS_Form_Outputter($form, $form_args, $form_fields, $calendar_id);

        // Response
        $response = array('success' => false);
        $response['html'] = $form_outputter->get_display();

    } else {

        $payment_confirmation = apply_filters('wpbs_submit_form_before', false, $_POST, $form, $form_args, $form_fields, $calendar_id);
        if($payment_confirmation !== false){
            echo $payment_confirmation;
            wp_die();
        }

        // Add booking
        
        $start_date = wpbs_convert_js_to_php_timestamp($_POST['calendar']['start_date']);
        $end_date = wpbs_convert_js_to_php_timestamp($_POST['calendar']['end_date']);

        /**
         * Prepare calendar data to be inserted
         *
         */
        $booking_data = array(
            'calendar_id' => absint($calendar_id),
            'form_id' => absint($form_id),
            'start_date' => date('Y-m-d 00:00:00', $start_date),
            'end_date' => date('Y-m-d 00:00:00', $end_date),
            'fields' => $form_fields,
            'status' => 'pending',
            'is_read' => '0',
            'date_created' => current_time('Y-m-d H:i:s'),
            'date_modified' => current_time('Y-m-d H:i:s'),
        );

        // Insert booking into the database
        $booking_id = wpbs_insert_booking($booking_data);


        do_action('wpbs_submit_form_after', $booking_id, $_POST, $form, $form_args, $form_fields);

        /**
         * Send emails
         *
         */
        foreach (array('admin', 'user') as $notification_type) {

            if (wpbs_get_form_meta($form_id, $notification_type . '_notification_enable', true) == 'on') {
                $email = new WPBS_Form_Mailer($form, $calendar, $booking_id, $form_fields, $_POST['form']['language'], $start_date, $end_date);
                $email->prepare($notification_type);
                $email->send();
            }
        }

        /**
         * Form Confirmation
         *
         */
        $response = array('success' => true);

        $confirmation_type = wpbs_get_form_meta($form_id, 'form_confirmation_type', true);
        $response['confirmation_type'] = $confirmation_type;

        if ($confirmation_type == 'message') {
            $confirmation_message = (wpbs_get_form_meta($form_id, 'form_confirmation_message_translation_' . $form_args['language'], true)) ? wpbs_get_form_meta($form_id, 'form_confirmation_message_translation_' . $form_args['language'], true) : wpbs_get_form_meta($form_id, 'form_confirmation_message', true);
            $response['confirmation_message'] = $confirmation_message;
        } elseif ($confirmation_type == 'redirect') {
            $confirmation_redirect_url = wpbs_get_form_meta($form_id, 'form_confirmation_redirect_url', true);
            $response['confirmation_redirect_url'] = $confirmation_redirect_url;
        }

        /**
         * Tracking Script
         *
         */
        $tracking_script = wpbs_get_form_meta($form_id, 'tracking_script', true);
        if (!empty($tracking_script)) {
            $response['tracking_script'] = $tracking_script;
        }
    }

    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_nopriv_wpbs_submit_form', 'wpbs_submit_form');
add_action('wp_ajax_wpbs_submit_form', 'wpbs_submit_form');
