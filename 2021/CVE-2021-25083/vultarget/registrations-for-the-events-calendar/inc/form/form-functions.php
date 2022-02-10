<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * global function for hooks to generate the form
 *
 * @since 1.0
 */
function rtec_the_registration_form( $atts = array() )
{
	if ( tribe_is_event() && is_single() ) {
		rtec_action_check_after_post();
	}

	$rtec = RTEC();
	global $rtec_options;
	$form = $rtec->form->instance();

	$doing_shortcode = isset( $atts['doing_shortcode'] ) ? $atts['doing_shortcode'] : false;
	$should_return_html_not_echo = isset( $atts['return_html'] ) ? $atts['return_html'] : false;

	$return_html = '';

	if ( $doing_shortcode ) {
		$event_id = isset( $atts['event'] ) ? (int)$atts['event'] : '';
		$return_html = '';
	} else {
		$event_id = get_the_ID();
	}

	if ( post_password_required( $event_id ) ) {
	    return '';
	}

	$form->build_form( $event_id );
	$fields_atts = $form->get_field_attributes();
	$event_meta = $form->get_event_meta();

	if ( $rtec->submission != NULL && $event_meta['post_id'] === (int)$_POST['rtec_event_id'] ) {

		$submission = $rtec->submission->instance();
		$submission->set_field_attributes( $fields_atts );
		$submission->custom_columns = $form->get_custom_column_keys();

		$raw_data = $submission->validate_input( $_POST );

		if ( $submission->has_errors() || ! $form->current_user_can_register() ) {
			$form->set_errors( $submission->get_errors() );
			$form->set_submission_data( $raw_data );
			$form->set_max_registrations();

			if ( $doing_shortcode || $should_return_html_not_echo ) {
				$return_html .= $form->get_form_html( $fields_atts, $atts );
				return $return_html;
			} else {
				echo $form->get_form_html( $fields_atts, $atts );
			}
		} else {
			$submission->custom_fields_label_name_pairs = $form->get_custom_fields_label_name_pairs();
			$submission->process_valid_submission( $raw_data );

			$message = $form->get_success_message_html();
			if ( $doing_shortcode || $should_return_html_not_echo ) {
				$return_html .= $message;
				return $return_html;
			} else {
				echo $message;
			}
		}

	} elseif ( ! $form->registrations_are_disabled() ) {

		if ( $form->registrations_available()
             && ! $form->registration_deadline_has_passed() ) {

		    if ( $form->current_user_can_register() ) {
			    $form->set_max_registrations();

			    if ( $doing_shortcode || $should_return_html_not_echo ) {
				    $return_html .= $form->get_form_html( $fields_atts, $atts );

				    return $return_html;
			    } else {
				    echo $form->get_form_html( $fields_atts, $atts );
			    }
            } else {
			    // header
			    $show_header = (isset( $atts['showheader'] ) && $atts['showheader'] === 'true');
			    $event_header_html = $show_header ? $form->get_event_header_html() : '';

			    $attendee_list_html = '';
			    $shortcode_attendee_disable = isset( $atts['attendeelist'] ) ? ($atts['attendeelist'] !== 'true') : true;
			    if ( $event_meta['show_registrants_data'] && ( ! $doing_shortcode || ! $shortcode_attendee_disable ) ) {

				    $attendee_list_fields = array();
				    $attendee_list_fields = apply_filters( 'rtec_attendee_list_fields', $attendee_list_fields );

				    $registrants_data = $rtec->db_frontend->get_registrants_data( $event_meta, $attendee_list_fields );
				    $attendee_list_html = rtec_attendee_list( $registrants_data );
			    }

			    // attendance counts
			    $include_message = isset( $rtec_options['include_attendance_count_message'] ) ? $rtec_options['include_attendance_count_message'] : false;
			    $locations = isset( $rtec_options['attendance_count_message_location'] ) ? $rtec_options['attendance_count_message_location'] : array( 'above_button', 'above_description_list' );
			    $attendance_count_html = '';
			    if ( $include_message && in_array( 'above_button', $locations, true ) ) {
				    $template = isset( $rtec_options['attendance_count_message_template'] ) ? $rtec_options['attendance_count_message_template'] : __( 'Attendance: {num} / {max}', 'registrations-for-the-events-calendar' );
				    $template = rtec_get_text( $template, __( 'Attendance: {num} / {max}', 'registrations-for-the-events-calendar' ) );
				    $attendance_count_html = rtec_attendance_count_display( $event_meta['post_id'], $template );
			    }

			    $outer_wrap_classes = '';
			    $location = isset( $rtec_options['template_location'] ) ? $rtec_options['template_location'] : 'tribe_events_single_event_before_the_content';
			    if ( $location !== 'shortcode' && class_exists( 'Tribe__Editor__Blocks__Abstract' ) && tribe_is_event() && is_single() ) {
				    $outer_wrap_classes .= ' rtec-js-placement';
			    }

			    $return_html .= '<div class="rtec-outer-wrap'.$outer_wrap_classes.'">';

			    $return_html .= $event_header_html;

			    $return_html .= $attendee_list_html;

			    $return_html .= $attendance_count_html;

			    $return_html .= '<div id="rtec" class="rtec" data-event="' . esc_attr( $event_meta['post_id'] ) . '">';

			    $return_html .= $form->please_log_in_html();

			    $return_html .= '</div>';

			    $return_html .= '</div>';


			    if ( $doing_shortcode === true || $should_return_html_not_echo ) {
				    return $return_html;
			    } else {
				    echo $return_html;
			    }

            }


		} else {
			// header
			$show_header = (isset( $atts['showheader'] ) && $atts['showheader'] === 'true');
			$event_header_html = $show_header ? $form->get_event_header_html() : '';

			$attendee_list_html = '';
			$shortcode_attendee_disable = isset( $atts['attendeelist'] ) ? ($atts['attendeelist'] !== 'true') : true;
			if ( $event_meta['show_registrants_data'] && ( ! $doing_shortcode || ! $shortcode_attendee_disable ) ) {

				$attendee_list_fields = array();
				$attendee_list_fields = apply_filters( 'rtec_attendee_list_fields', $attendee_list_fields );

				$registrants_data = $rtec->db_frontend->get_registrants_data( $event_meta, $attendee_list_fields );
				$attendee_list_html = rtec_attendee_list( $registrants_data );
			}

			// attendance counts
			$include_message = isset( $rtec_options['include_attendance_count_message'] ) ? $rtec_options['include_attendance_count_message'] : false;
			$locations = isset( $rtec_options['attendance_count_message_location'] ) ? $rtec_options['attendance_count_message_location'] : array( 'above_button', 'above_description_list' );
			$attendance_count_html = '';
			if ( $include_message && in_array( 'above_button', $locations, true ) ) {
				$template = isset( $rtec_options['attendance_count_message_template'] ) ? $rtec_options['attendance_count_message_template'] : __( 'Attendance: {num} / {max}', 'registrations-for-the-events-calendar' );
				$template = rtec_get_text( $template, __( 'Attendance: {num} / {max}', 'registrations-for-the-events-calendar' ) );
				$attendance_count_html = rtec_attendance_count_display( $event_meta['post_id'], $template );
			}

			$outer_wrap_classes = '';
			$location = isset( $rtec_options['template_location'] ) ? $rtec_options['template_location'] : 'tribe_events_single_event_before_the_content';
			if ( $location !== 'shortcode' && class_exists( 'Tribe__Editor__Blocks__Abstract' ) && tribe_is_event() && is_single() ) {
				$outer_wrap_classes .= ' rtec-js-placement';
			}

			$return_html .= '<div class="rtec-outer-wrap'.$outer_wrap_classes.'">';

			$return_html .= $event_header_html;

			$return_html .= $attendee_list_html;

			$return_html .= $attendance_count_html;


			$return_html .= '<div id="rtec" class="rtec" data-event="' . esc_attr( $event_meta['post_id'] ) . '">';

			$return_html .= $form->registrations_closed_message();

			if ( ! $form->registration_deadline_has_passed() ) {
				ob_start();

				$form->already_registered_visitor_html();

				$return_html .= ob_get_contents();
				ob_get_clean();
            }

			$return_html .= '</div>';

			$return_html .= '</div>';

			if ( $doing_shortcode === true || $should_return_html_not_echo ) {
				return $return_html;
			} else {
				echo $return_html;
			}

		}

	}
}

/**
 * add element to the page to set a flag that form needs to be moved with JavaScript
 *
 * @since 2.4
 */
function rtec_the_move_flag()
{
	global $rtec_options;
	$location = isset( $rtec_options['template_location'] ) ? $rtec_options['template_location'] : 'tribe_events_single_event_before_the_content';
	$using_custom_template = isset( $rtec_options['using_custom_template'] ) ? $rtec_options['using_custom_template'] : false;

	if ( $location !== 'shortcode' && class_exists( 'Tribe__Editor__Blocks__Abstract' ) && tribe_is_event() && is_single() && ! $using_custom_template ) {
		echo '<span style="display:none;" id="rtec-js-move-flag" data-location="'.$location.'"></span>';
	}
}
add_action( 'tribe_events_single_event_meta_primary_section_end', 'rtec_the_move_flag' );

/**
 * To separate concerns and avoid potential problems with redirects, this function performs
 * a check to see if the registrationsTEC form was submitted and initiates form
 * before the template is loaded.
 *
 * @since 1.0
 */
function rtec_process_form_submission()
{
	require_once RTEC_PLUGIN_DIR . 'inc/class-rtec-submission.php';
	require_once RTEC_PLUGIN_DIR . 'inc/form/class-rtec-form.php';

	if ( isset( $_POST['lang'] ) && ! empty( $GLOBALS['sitepress'] ) && $GLOBALS['sitepress'] instanceof SitePress ) {
	    $lang = sanitize_text_field( $_POST['lang'] );
		global $sitepress;
		$sitepress->switch_lang( $lang, true );
	}

	$submission = new RTEC_Submission();
	$form = new RTEC_Form();

	$event_id = (int)$_POST['rtec_event_id'];

	$form->build_form( $event_id );
	$fields_atts = $form->get_field_attributes();

	$event_meta = $form->get_event_meta();

	$submission->set_field_attributes( $fields_atts );
	$raw_data = $submission->validate_input( $_POST );

	if ( $submission->has_errors() ) {
		$status = 'form';
	} else {
		if ( $submission->attendance_limit_not_reached() && $form->current_user_can_register() ) {
			$status = $submission->process_valid_submission( $raw_data );
		} else {
			$status = 'full';
		}
	}

	$status = apply_filters( 'rtec_the_submission_status', $status, $submission );

	rtec_output_message_after_submit( $status, $submission );

	die();
}
add_action( 'wp_ajax_nopriv_rtec_process_form_submission', 'rtec_process_form_submission' );
add_action( 'wp_ajax_rtec_process_form_submission', 'rtec_process_form_submission' );

function rtec_output_message_after_submit( $status, $submission ) {
	global $rtec_options;

	$message_html = '';

	if ( $status === 'form' ) {
		$message_text = isset( $rtec_options['ajax_submission_error'] ) ? $rtec_options['ajax_submission_error'] : __( 'There was a problem with one or more of the entries you submitted. Please try again.', 'registrations-for-the-events-calendar' );
		$message_html .= '<p class="rtec-success-message tribe-events-notices rtec-scrollto">' . esc_html( $message_text ) . '</p>';

		$message_html .= '<pre id="rtec_sub_errors" style="display: none;">';
		$errors = $submission->get_errors();

		foreach ( $errors as $error ) {
			$message_html .=  esc_html( $error ) . '</br>';
		}

		$message_html .=  '</pre>';
	} elseif ( $status === 'filled' ) {
		$filled_text = isset( $rtec_options['event_has_filled_during_submit'] ) ? $rtec_options['event_has_filled_during_submit'] : __( 'Sorry! Registrations just filled up for this event. You are not registered.', 'registrations-for-the-events-calendar' );
		$message_text = rtec_get_text( $filled_text,  __( 'Sorry! Registrations just filled up for this event. You are not registered.', 'registrations-for-the-events-calendar' ) );
		$message_html .= '<p class="rtec-success-message tribe-events-notices rtec-scrollto">' . $message_text . '</p>';

	} elseif ( $status === 'success' ) {
	    $success_text = isset( $rtec_options['success_message'] ) ? $rtec_options['success_message'] : __( 'Success! Please check your email inbox for a confirmation message.', 'registrations-for-the-events-calendar' );
	    $message_text = rtec_get_text( $success_text,  __( 'Success! Please check your email inbox for a confirmation message.', 'registrations-for-the-events-calendar' ) );

		$message_html .= '<p class="rtec-success-message tribe-events-notices rtec-scrollto">' . $message_text . '</p>';

		$message_html = apply_filters( 'rtec_successful_submission_message', $message_html, $submission, $status );
	} else {
		$message_html = apply_filters( 'rtec_submission_' . $status, $message_html, $submission, $status );
	}

	echo $message_html;
}

/**
 * Checks for duplicate emails if the option is enabled
 *
 * @since 1.6
 */
function rtec_registrant_check_for_duplicate_email() {
	require_once RTEC_PLUGIN_DIR . 'inc/class-rtec-db.php';

	$email = is_email( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : false;
	$event_id = (int)$_POST['event_id'];

	$is_duplicate = false;

	if ( is_email( $email ) ) {
		$db = New RTEC_Db();
		$is_duplicate = $db->is_duplicate_email( $email, $event_id );
	}

	$approved = ! $is_duplicate;
	$approved = apply_filters( 'rtec_email_approved_for_registration', $approved, $email, $event_id );

	$response = array(
		'approved' => true,
		'message' => ''
	);

	if ( ! $approved ) {
		$options = get_option( 'rtec_options' );

		$message = isset( $options['error_duplicate_message'] ) ? $options['error_duplicate_message'] : 'You have already registered for this event';
		$message_text = rtec_get_text( $message, __( 'You have already registered for this event', 'registrations-for-the-events-calendar' ) );
		$response['approved'] = false;
		$response['message'] = '<p class="rtec-error-message" id="rtec-error-duplicate" role="alert">' . esc_html( $message_text ) . '</p>';
	}

	echo json_encode( $response );

	die();
}
add_action( 'wp_ajax_nopriv_rtec_registrant_check_for_duplicate_email', 'rtec_registrant_check_for_duplicate_email' );
add_action( 'wp_ajax_rtec_registrant_check_for_duplicate_email', 'rtec_registrant_check_for_duplicate_email' );

function rtec_refresh_event_info() {

	$rtec = RTEC();
	$event_id = (int)$_POST['event_id'];

	$event_meta = rtec_get_event_meta( $event_id );

	$to_include = array(
		'first',
		'last',
		'user_id'
	);
	$attendee_list_fields = apply_filters( 'rtec_attendee_list_fields', $to_include );
	$registrants_data = $rtec->db_frontend->get_registrants_data( $event_meta, $attendee_list_fields );

	ob_start();
	do_action( 'rtec_the_attendee_list', $registrants_data );
	$html = ob_get_contents();
	ob_get_clean();

	echo $html;

	die();
}
add_action( 'wp_ajax_nopriv_rtec_refresh_event_info', 'rtec_refresh_event_info' );
add_action( 'wp_ajax_rtec_refresh_event_info', 'rtec_refresh_event_info' );

/**
 * Set the form location right away
 *
 * @since 1.0
 *
 * @since 2.4   logic added for The Events Calendar 4.7
 */
function rtec_form_location_init()
{
	$options = get_option( 'rtec_options' );
	$location = isset( $options['template_location'] ) ? $options['template_location'] : 'tribe_events_single_event_before_the_content';
	$using_custom_template = isset( $options['using_custom_template'] ) ? $options['using_custom_template'] : false;

	if ( $using_custom_template ) {
		if ( $location !== 'shortcode' ) {
			add_action( $location, 'rtec_the_registration_form' );
		}
	} elseif ( ! class_exists( 'Tribe__Editor__Blocks__Abstract' ) ) {
		if ( $location !== 'shortcode' ) {
			add_action( $location, 'rtec_the_registration_form' );
		}
	}

}
add_action( 'init', 'rtec_form_location_init', 1 );

/**
 *
 * @since 2.2
 */
function rtec_add_visitor_action_listener() {
	if ( isset( $_POST['rtec_visitor_submit'] ) ) {
		rtec_visitor_send_action_link();
	}
}
add_action( 'init', 'rtec_add_visitor_action_listener', 99 );

function rtec_check_action_before_post() {

	if ( ! is_admin() && isset( $_GET['action'] ) && $_GET['action'] === 'unregister' ) {
		global $rtec_options;
		$rtec = RTEC();
		$action = sanitize_text_field( $_GET['action'] );

        $verification_data = array(
            'email' => isset( $_GET['email'] ) ? sanitize_text_field( $_GET['email'] ) : '',
            'token' => isset( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] ) : '',
            'action' => sanitize_text_field( $action )
        );

        $entry_exists = $rtec->db_frontend->maybe_verify_token( $verification_data );

		$args = array(
			'fields'   => array(
				'id',
				'event_id',
				'registration_date'
			),
			'where'    => array(
				array( 'action_key', $verification_data['token'], '=', 'string' )
			),
			'order_by' => 'registration_date'
		);

		$entries = RTEC()->db_frontend->retrieve_entries( $args, false, 1, 'DESC' );

		$event_id = isset( $entries[0] ) ? $entries[0]['event_id'] : 0;

		$form = $rtec->form->instance();

		$form->build_form( $event_id );

		if ( $event_id !== 0 && $form->registration_deadline_has_passed() ) {
			if ( method_exists ( 'Tribe__Notices' , 'set_notice' ) ) {
				Tribe__Notices::set_notice( 'unregistered', __( 'Deadline to unregister has passed.', 'registrations-for-the-events-calendar' ) );
			}
		} elseif ( $verification_data['action'] === 'unregister' && $entry_exists && $verification_data['token'] !== '' ) {

            $message = isset( $rtec_options['success_unregistration'] ) ? $rtec_options['success_unregistration'] : __( 'You have been unregistered.', 'registrations-for-the-events-calendar' );
            $o_message = rtec_get_text( $message, __( 'You have been unregistered.', 'registrations-for-the-events-calendar' ) );

            if ( method_exists ( 'Tribe__Notices' , 'set_notice' ) ) {
                Tribe__Notices::set_notice( 'unregistered', $o_message );
            }

        } else {

	        if ( method_exists ( 'Tribe__Notices' , 'set_notice' ) ) {
		        Tribe__Notices::set_notice( 'unregistered', __( 'No record found.', 'registrations-for-the-events-calendar' ) );
	        }
        }

    }

}
add_action( 'init', 'rtec_check_action_before_post' );

/**
 * Set the form location right away
 *
 * @since 2.4.3
 */
function rtec_use_footer_to_add_form() {
	global $rtec_options;
	$location = isset( $rtec_options['template_location'] ) ? $rtec_options['template_location'] : 'tribe_events_single_event_before_the_content';
	$using_custom_template = isset( $rtec_options['using_custom_template'] ) ? $rtec_options['using_custom_template'] : false;
	if ( $location !== 'shortcode' && class_exists( 'Tribe__Editor__Blocks__Abstract' ) && tribe_is_event() && is_single() && ! $using_custom_template ) {
		rtec_the_registration_form();
	}

	if ( isset( $rtec_options['display_type'] ) && $rtec_options['display_type'] === 'popup_modal' ) :
		?>
        <div class="rtec-modal-backdrop rtec-form-modal"></div>
        <div class="rtec-modal rtec-form-modal">
            <button type="button" class="rtec-button-link rtec-media-modal-close">x<span class="rtec-media-modal-icon"><span class="screen-reader-text">Close</span></span></button>
            <div class="rtec-modal-content">
            </div>
        </div>
	<?php
	endif;
}
add_action( 'wp_footer', 'rtec_use_footer_to_add_form', 1 );


function rtec_action_check_after_post() {
	if ( ! is_admin() && isset( $_GET['action'] ) && $_GET['action'] === 'unregister' ) {
		global $rtec_options;
		$rtec = RTEC();
		$action = sanitize_text_field( $_GET['action'] );

        $verification_data = array(
            'email' => isset( $_GET['email'] ) ? sanitize_text_field( $_GET['email'] ) : '',
            'token' => isset( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] ) : '',
            'action' => $action
        );

		$entry_exists = $rtec->db_frontend->maybe_verify_token( $verification_data );

		if ( $verification_data['action'] === 'unregister' && $entry_exists && $verification_data['token'] !== '' ) {

            $event_id = get_the_ID();
			$form = $rtec->form->instance();

			$form->build_form( $event_id );

			if ( $form->registration_deadline_has_passed() ) {
			    return;
            }

			$args = array(
				'fields'   => array(
					'id',
					'event_id',
					'registration_date'
				),
				'where'    => array(
					array( 'action_key', $verification_data['token'], '=', 'string' )
				),
				'order_by' => 'registration_date'
			);

			$entries = RTEC()->db_frontend->retrieve_entries( $args, false, 1, 'DESC' );

			$event_id = isset( $entries[0] ) ? $entries[0]['event_id'] : 0;

            $disable_notification = isset( $rtec_options['disable_notification'] ) ? $rtec_options['disable_notification'] : false;

            if ( ! $disable_notification ) {
                rtec_send_unregistration_notification( array( $entries[0]['id'] ) );
            }

			$record_was_deleted = $rtec->db_frontend->remove_record_by_action_key( $verification_data['token'] );
			if ( $record_was_deleted ) {
				$rtec->db_frontend->update_num_registered_meta_for_event( $event_id );

			}

		}

	}
}

/**
 *
 * @since 2.2
 */
function rtec_visitor_send_action_link() {
	global $rtec_options;

	$email_error_message = isset( $rtec_options['email_error_message'] ) ? esc_html( $rtec_options['email_error_message'] ) : __( 'Please enter the email you registered with.', 'registrations-for-the-events-calendar' );
	$email_error_message = rtec_get_text( $email_error_message, __( 'Please enter the email you registered with.', 'registrations-for-the-events-calendar' ) );

	if ( ! is_email( $_POST['rtec-visitor_email'] ) ) {

		if ( method_exists ( 'Tribe__Notices' , 'set_notice' ) ) {
			Tribe__Notices::set_notice( 'tool_status', $email_error_message );
		}

	} else {
        $email = sanitize_text_field( $_POST['rtec-visitor_email'] );
        $event_id = (int)$_POST['event_id'];
        $rtec = RTEC();
        $args = array(
            'fields' => array( 'event_id', 'action_key' ),
            'where' => array(
                array( 'email', $email, '=', 'string' ),
                array( 'event_id', $event_id, '=', 'int' ),
            )
        );
        $matches = $rtec->db_frontend->retrieve_entries( $args, false, 1 );

        if ( isset( $matches[0]['action_key'] ) ) {

            $unregister_link_text = isset( $rtec_options['unregister_link_text'] ) ? esc_html( $rtec_options['unregister_link_text'] ) : __( 'Unregister from this event', 'registrations-for-the-events-calendar' );
            $unregister_link_text = rtec_get_text( $unregister_link_text, __( 'Unregister from this event', 'registrations-for-the-events-calendar' ) );

            $message = rtec_generate_unregister_link( (int)$event_id, $matches[0]['action_key'], $email, $unregister_link_text );
            $header_image = isset( $rtec_options['html_email_header_img'] ) ? $rtec_options['html_email_header_img'] : false;

            $args = array(
                'template_type'         => 'confirmation',
                'content_type'          => 'html',
                'custom_template_pairs' => array(),
                'recipients'            => $email,
                'subject'               => array(
                    'text' => get_the_title( $event_id ),
                    'data' => array()
                ),
                'body'                  => array(
                    'message'      => $message,
                    'data'         => array(),
                    'header_image' => $header_image
                )
            );
            require_once RTEC_PLUGIN_DIR . 'inc/class-rtec-email.php';
            $unregister_email = new RTEC_Email();
            $unregister_email->build_email( $args, true, $event_id );

            $success = $unregister_email->send_email();

	        $email_success_message = isset( $rtec_options['success_send_message'] ) ? esc_html( $rtec_options['success_send_message'] ) : __( 'Check your email inbox for an unregister link.', 'registrations-for-the-events-calendar' );
	        $email_success_message = rtec_get_text( $email_success_message, __( 'Check your email inbox for an unregister link.', 'registrations-for-the-events-calendar' ) );

            if ( $success && method_exists ( 'Tribe__Notices' , 'set_notice' ) ) {
                Tribe__Notices::set_notice( 'tool_status', $email_success_message );
            }

        } else {

            if ( method_exists ( 'Tribe__Notices' , 'set_notice' ) ) {
                Tribe__Notices::set_notice( 'tool_status', $email_error_message );
            }

        }
	}

	return '';
}

function rtec_send_unregister_link() {
	global $rtec_options;
	$event_id = (int)$_POST['event_id'];
	$email = sanitize_text_field( $_POST['email'] );

	$email_error_message = isset( $rtec_options['email_error_message'] ) ? esc_html( $rtec_options['email_error_message'] ) : __( 'Please enter the email you registered with.', 'registrations-for-the-events-calendar' );
	$email_error_message = rtec_get_text( $email_error_message, __( 'Please enter the email you registered with.', 'registrations-for-the-events-calendar' ) );
	if ( ! is_email( $email )
         || $event_id === 0 ) {
		$return = array(
			'error' => __( $email_error_message, 'registrations-for-the-events-calendar' )
		);

		echo wp_json_encode( $return );
		die();
	}

	$rtec = RTEC();
	$args = array(
		'fields' => array(
			'id',
			'first',
			'last',
			'email',
			'venue',
			'phone',
			'other',
			'event_id',
			'custom',
			'action_key',
		),
		'where' => array(
			array( 'email', $email, '=', 'string' ),
			array( 'event_id', $event_id, '=', 'int' ),
		)
	);
	$matches = $rtec->db_frontend->retrieve_entries( $args, false, 1 );

	if ( isset( $matches[0]['action_key'] ) ) {
		$registration = $matches[0];

		$db = $rtec->db_frontend->instance();
		$form = new RTEC_Form();

		$form->build_form( $registration['event_id'] );
		$fields_atts = $form->get_field_attributes();
		$event_meta = $form->get_event_meta();

		$custom_columns = $form->get_custom_column_keys();

        if ( isset( $registration['custom'] ) ) {
            $registration['first'] = isset( $registration['first_name'] ) ? $registration['first_name'] : '';
            $registration['last']  = isset( $registration['last_name'] ) ? $registration['last_name'] : '';
        }

		$sanitized_data = array_merge( $event_meta, $registration );

		$sanitized_data['date'] = $event_meta['start_date'];

		$unregister_link_text = isset( $rtec_options['unregister_link_text'] ) ? esc_html( $rtec_options['unregister_link_text'] ) : __( 'Unregister from this event', 'registrations-for-the-events-calendar' );
		$unregister_link_text = rtec_get_text( $unregister_link_text, __( 'Unregister from this event', 'registrations-for-the-events-calendar' ) );

		$unregister_message_template = isset( $rtec_options['unregister_message'] ) ? $rtec_options['unregister_message'] : rtec_generate_unregister_link( (int)$event_id, $matches[0]['action_key'], $email, $unregister_link_text );
		$header_image = isset( $rtec_options['html_email_header_img'] ) ? $rtec_options['html_email_header_img'] : false;

		$args = array(
			'template_type'         => 'confirmation',
			'content_type'          => 'html',
			'custom_template_pairs' => array(),
			'recipients'            => $email,
			'subject'               => array(
				'text' => get_the_title( $event_id ),
				'data' => $sanitized_data
			),
			'body'                  => array(
				'message'      => $unregister_message_template,
				'data'         => $sanitized_data,
				'header_image' => $header_image
			)
		);
		require_once RTEC_PLUGIN_DIR . 'inc/class-rtec-email.php';
		$unregister_email = new RTEC_Email();
		$unregister_email->build_email( $args, true, $event_id );

		$success = $unregister_email->send_email();

		$email_success_message = isset( $rtec_options['success_send_message'] ) ? esc_html( $rtec_options['success_send_message'] ) : __( 'Check your email inbox for an unregister link.', 'registrations-for-the-events-calendar' );
		$email_success_message = rtec_get_text( $email_success_message, __( 'Check your email inbox for an unregister link.', 'registrations-for-the-events-calendar' ) );

		$return = array(
			'success' => '<p class="rtec-success-message tribe-events-notices rtec-scrollto">' . __( $email_success_message, 'registrations-for-the-events-calendar' ) . '</p>'
		);

		echo wp_json_encode( $return );
		die();

	} else {

		$return = array(
			'error' => __( $email_error_message, 'registrations-for-the-events-calendar' )
		);

		echo wp_json_encode( $return );
		die();

	}


	die();
}
add_action( 'wp_ajax_rtec_send_unregister_link', 'rtec_send_unregister_link' );
add_action( 'wp_ajax_nopriv_rtec_send_unregister_link', 'rtec_send_unregister_link' );

function rtec_attendee_list( $registrants_data ) {
	ob_start();
	do_action( 'rtec_the_attendee_list', $registrants_data );
	$attendee_html = ob_get_contents();
	ob_get_clean();

	return $attendee_html;
}

function rtec_the_default_attendee_list( $registrants_data )
{
	$rtec = RTEC();
	$form = $rtec->form->instance();

	$form->get_registrants_data_html( $registrants_data );
}
add_action( 'rtec_the_attendee_list', 'rtec_the_default_attendee_list', 10, 1 );


function rtec_attendance_count_display_find_replace( $text, $search_replace ) {
	$working_text = $text;

	foreach ( $search_replace as $search => $replace ) {
		$working_text = str_replace( $search, $replace, $working_text );
	}

	return $working_text;
}

function rtec_attendance_count_display( $event_id, $template, $classes = '' ) {
	$event_meta = rtec_get_event_meta( $event_id );

	if ( $event_meta['registrations_disabled'] ) {
		return;
	}

	$max = $event_meta['limit_registrations'] ? $event_meta['max_registrations'] : '∞';
	$remaining = $event_meta['limit_registrations'] ? max( 0, (int)$max - (int)$event_meta['num_registered'] ) : '∞';

	$find_replace = array(
		'{num}' => $event_meta['num_registered'],
		'{max}' => $max,
		'{remaining}' => $remaining
	);
	$template_html = rtec_sanitize_outputted_html( rtec_attendance_count_display_find_replace( $template, $find_replace ) );

	$html = '';
	if ( ! empty( $event_id ) ) {
        $html .= '<div class="rtec-attendance-display'.$classes.'">';
		$html .= '<span>' . $template_html . '</span>';
		$html .= '</div>';
	}

	return $html;
}

add_action( 'tribe_events_after_the_meta', 'rtec_attendance_count_above_description_list' );
add_action( 'tribe_template_before_include:events/v2/list/event/description', 'rtec_attendance_count_above_description_list' );
function rtec_attendance_count_above_description_list() {
	global $rtec_options;

	$include_message = isset( $rtec_options['include_attendance_count_message'] ) ? $rtec_options['include_attendance_count_message'] : false;
	$locations = isset( $rtec_options['attendance_count_message_location'] ) ? $rtec_options['attendance_count_message_location'] : array( 'above_button', 'above_description_list' );
	if ( $include_message && in_array( 'above_description_list', $locations, true ) ) {
		$template = isset( $rtec_options['attendance_count_message_template'] ) ? $rtec_options['attendance_count_message_template'] : __( 'Attendance: {num} / {max}', 'registrations-for-the-events-calendar' );
		$template = rtec_get_text( $template, __( 'Attendance: {num} / {max}', 'registrations-for-the-events-calendar' ) );
		echo rtec_attendance_count_display( get_the_id(), $template, ' tribe-common-b2' );
	}
}

/**
 * Load the critical notice for logged in users.
 */
function rtec_new_user_notice() {
	//set_transient( 'rtec_new_messages', 'yes', 1* WEEK_IN_SECONDS );
	if ( ! function_exists( 'tribe_is_event') ) {
		return;
	}

    if ( ! tribe_is_event() || ! is_single() ) {
        return;
    }
	// Don't do anything for guests.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// Only show this to users who are not tracked.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Don't show if already dismissed.
	$new_status = get_transient( 'rtec_new_messages' );
	if ( $new_status !== 'yes' ) {
        return;
	}

	$rtec_statuses = get_option( 'rtec_statuses', array() );

	if ( isset( $rtec_statuses['new_user_dismiss'] ) ) {
		return;
	}

	?>
    <div class="rtec-new-user-notice rtec-new-user-notice-hide">
        <div class="rtec-new-user-notice-icon">
            <img src="<?php echo esc_url( RTEC_PLUGIN_URL . 'img/RTEC-Logo-150x150.png' ); ?>" width="45" alt="RTEC icon" />
        </div>
        <div class="rtec-new-user-notice-text">
            <h3><?php esc_html_e( 'Questions About Registration?', 'registrations-for-the-events-calendar' ); ?></h3>
            <p>
				<?php
				// Translators: %s is the link to the article where more details about critical are listed.
				esc_html_e( 'Since you are logged-in, the first, last, and email fields are filled in with your user information. Also, the recaptcha field is only required for logged-out visitors.', 'registrations-for-the-events-calendar' );
				?>
            </p>
            <p>
		        <?php
		        // Translators: %s is the link to the article where more details about critical are listed.
		        $doc_url = admin_url() . 'admin.php?page=rtec-support';
		        echo sprintf( esc_html__( 'Visit the %ssupport page%s in your WordPress dashboard if you have questions.', 'registrations-for-the-events-calendar' ), '<a href="' . esc_url( $doc_url ) . '" target="_blank">', '</a>' );
		        ?>
            </p>
        </div>
        <div class="rtec-new-user-notice-close">&times;</div>
    </div>
    <style type="text/css">
        .rtec-new-user-notice {
            position: fixed;
            bottom: 20px;
            right: 15px;
            background: #fff;
            box-shadow: 0 0 10px 0 #dedede;
            padding: 10px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 425px;
            max-width: calc( 100% - 30px );
            border-radius: 6px;
            transition: bottom 700ms ease;
            z-index: 10000;
        }

        .rtec-new-user-notice h3 {
            font-size: 13px;
            color: #222;
            font-weight: 700;
            margin: 0 0 7px;
            padding: 0;
            line-height: 1;
            border: none;
        }

        .rtec-new-user-notice p {
            font-size: 12px;
            color: #7f7f7f;
            font-weight: 400;
            margin: 0 0 7px;
            padding: 0;
            line-height: 1.2;
            border: none;
        }

        .rtec-new-user-notice p a {
            color: #7f7f7f;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            text-decoration: underline;
            font-weight: 400;
        }

        .rtec-new-user-notice p a:hover {
            color: #666;
        }

        .rtec-new-user-notice-icon img {
            height: auto;
            display: block;
            margin: 0;
        }

        .rtec-new-user-notice-icon {
            padding: 0;
            border-radius: 4px;
            flex-grow: 0;
            flex-shrink: 0;
            margin-right: 12px;
            overflow: hidden;
        }

        .rtec-new-user-notice-close {
            padding: 10px;
            margin: -12px -9px 0 0;
            border: none;
            box-shadow: none;
            border-radius: 0;
            color: #7f7f7f;
            background: transparent;
            line-height: 1;
            align-self: flex-start;
            cursor: pointer;
            font-weight: 400;
        }
        .rtec-new-user-notice-close:hover,
        .rtec-new-user-notice-close:focus{
            color: #111;
        }

        .rtec-new-user-notice.rtec-new-user-notice-hide {
            bottom: -200px;
        }
    </style>
	<?php

	if ( ! wp_script_is( 'jquery', 'queue' ) ) {
		wp_enqueue_script( 'jquery' );
	}
	?>
    <script>
        if ( 'undefined' !== typeof jQuery ) {
            jQuery( document ).ready( function ( $ ) {
                /* Don't show the notice if we don't have a way to hide it (no js, no jQuery). */
                $( document.querySelector( '.rtec-new-user-notice' ) ).removeClass( 'rtec-new-user-notice-hide' );
                $( document.querySelector( '.rtec-new-user-notice-close' ) ).on( 'click', function ( e ) {
                    e.preventDefault();
                    $( this ).closest( '.rtec-new-user-notice' ).addClass( 'rtec-new-user-notice-hide' );
                    $.ajax( {
                        url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                        method: 'POST',
                        data: {
                            action: 'rtec_dismiss_new_user_notice',
                            nonce: '<?php echo esc_js( wp_create_nonce( 'rtec-new-user-notice' ) ); ?>',
                        }
                    } );
                } );
            } );
        }
    </script>
	<?php
}

add_action( 'wp_footer', 'rtec_new_user_notice', 300 );

function rtec_dismiss_new_user_notice() {

	check_ajax_referer( 'rtec-new-user-notice', 'nonce' );

	$rtec_statuses = get_option( 'rtec_statuses', array() );

	if ( ! is_array( $rtec_statuses ) ) {
		$rtec_statuses = array();
    }

	$rtec_statuses['new_user_dismiss'] = true;

	update_option( 'rtec_statuses', $rtec_statuses, false );

	wp_die();

}

add_action( 'wp_ajax_rtec_dismiss_new_user_notice', 'rtec_dismiss_new_user_notice' );

/**
* outputs the custom js from the "Customize" tab on the Settings page
 *
 * @since 1.0
*/
function rtec_custom_js() {
	$options = get_option( 'rtec_options' );
	$rtec_custom_js = isset( $options[ 'custom_js' ] ) ? $options[ 'custom_js' ] : '';

	if ( ! empty( $rtec_custom_js ) ) {
?>
<!-- Registrations For the Events Calendar JS -->
<script type="text/javascript">
	jQuery(document).ready(function($) {
		<?php echo stripslashes( $rtec_custom_js ) . "\r\n"; ?>
	});
</script>
<?php
	}
}
add_action( 'wp_footer', 'rtec_custom_js', 20 );

/**
 * outputs the custom css from the "Customize" tab on the Settings page
 *
 * @since 1.0
 */
function rtec_custom_css() {
	$options = get_option( 'rtec_options' );
	$rtec_custom_css = isset( $options[ 'custom_css' ] ) ? $options[ 'custom_css' ] : '';

	if ( ! empty( $rtec_custom_css ) ) {
		echo "<!-- Registrations For the Events Calendar CSS -->" . "\r\n";
		echo "<style type='text/css'>" . "\r\n";
		if ( ! empty( $rtec_custom_css ) ) {
			echo stripslashes( $rtec_custom_css ) . "\r\n";
		}
		echo "</style>" . "\r\n";
	}
}
add_action( 'wp_head', 'rtec_custom_css' );

/**
 * javascript and CSS files for the feed
 *
 * @since 1.0
 */
function rtec_scripts_and_styles() {
	wp_enqueue_style( 'rtec_styles', trailingslashit( RTEC_PLUGIN_URL ) . 'css/rtec-styles.css', array(), RTEC_VERSION );
	wp_enqueue_script( 'rtec_scripts', trailingslashit( RTEC_PLUGIN_URL ) . 'js/rtec-scripts.js', array( 'jquery' ), RTEC_VERSION, true );

	wp_register_script( 'rtec_recaptcha', 'https://www.google.com/recaptcha/api.js' );

	$options = get_option( 'rtec_options' );
	$check_for_duplicates = isset( $options['check_for_duplicates'] ) ? $options['check_for_duplicates'] : false;
	wp_localize_script( 'rtec_scripts', 'rtec', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'checkForDuplicates' => $check_for_duplicates,
            'translations' => array(
                    'honeypotClear' => __( 'I am not a robot', 'registrations-for-the-events-calendar' )
            )
		)
	);
}
add_action( 'wp_enqueue_scripts', 'rtec_scripts_and_styles' );