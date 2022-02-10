<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbNotificationEmail' ) ) {
/**
 * Class to handle an email notification for Restaurant Reservations
 *
 * This class extends rtbNotification and must implement the following methods:
 *	prepare_notification() - set up and validate data
 *	send_notification()
 *
 * @since 0.0.1
 */
class rtbNotificationEmail extends rtbNotification {

	/**
	 * Recipient email
	 * @since 0.0.1
	 */
	public $to_email;

	/**
	 * From email
	 * @since 0.0.1
	 */
	public $from_email;

	/**
	 * From name
	 * @since 0.0.1
	 */
	public $from_name;

	/**
	 * Email subject
	 * @since 0.0.1
	 */
	public $subject;

	/**
	 * Email message body
	 * @since 0.0.1
	 */
	public $message;

	/**
	 * Email headers
	 * @since 0.0.1
	 */
	public $headers;

	/**
	 * Inidividual booking related to this notification, if applicable
	 * @since 0.0.1
	 */
	public $booking;

	/**
	 * Prepare and validate notification data
	 *
	 * @return boolean if the data is valid and ready for transport
	 * @since 0.0.1
	 */
	public function prepare_notification() {

		$this->set_to_email();
		$this->set_from_email();
		$this->set_subject();
		$this->set_headers();
		$this->set_message();
		
		// Return false if we're missing any of the required information
		if ( 	empty( $this->to_email) ||
				empty( $this->from_email) ||
				empty( $this->from_name) ||
				empty( $this->subject) ||
				empty( $this->headers) ||
				empty( $this->message) ) {
			return false;
		}

		return true;
	}

	/**
	 * Set to email
	 * @since 0.0.1
	 */
	public function set_to_email() {
		global $rtb_controller;

		if ( $this->target == 'user' ) {
			
			$to_email = empty( $this->booking->email ) ? null : $this->booking->email;
		} 
		else {
			
			$to_email = $rtb_controller->settings->get_setting( 'admin-email-address' );
		}

		$this->to_email = apply_filters( 'rtb_notification_email_to_email', $to_email, $this );

	}

	/**
	 * Set from email
	 * @since 0.0.1
	 */
	public function set_from_email() {
		global $rtb_controller;

		if ( ! empty( $this->from_email ) and ! empty( $this->from_name ) ) {

			$from_email = $this->from_email;
			$from_name = $this->from_name;
		}
		elseif ( $this->target == 'user' ) {

			$from_email = $rtb_controller->settings->get_setting( 'reply-to-address' );
			$from_name = $rtb_controller->settings->get_setting( 'reply-to-name' );
		} 
		else {

			$from_email = $this->booking->email;
			$from_name = $this->booking->name;
		}

		$this->from_email = apply_filters( 'rtb_notification_email_from_email', $from_email, $this );
		$this->from_name = apply_filters( 'rtb_notification_email_from_name', $from_name, $this );

	}

	/**
	 * Set email subject
	 * @since 0.0.1
	 */
	public function set_subject() {

		global $rtb_controller;

		if( $this->event == 'new_submission' ) {
			if ( $this->target == 'admin' ) {
				$subject = $rtb_controller->settings->get_setting( 'subject-booking-admin' );
			} elseif ( $this->target == 'user' ) {
				$subject = $rtb_controller->settings->get_setting( 'subject-booking-user' );
			}

		} elseif ( $this->event == 'rtb_confirmed_booking' ) {
			$subject = $rtb_controller->settings->get_setting( 'subject-booking-confirmed-admin' );

		}elseif ( $this->event == 'pending_to_confirmed' ) {
			$subject = $rtb_controller->settings->get_setting( 'subject-confirmed-user' );

		} elseif ( $this->event == 'pending_to_closed' ) {
			$subject = $rtb_controller->settings->get_setting( 'subject-rejected-user' );

		} elseif ( $this->event == 'booking_cancelled' ) {
			if ( $this->target == 'admin' ) {
				$subject = $rtb_controller->settings->get_setting( 'subject-booking-cancelled-admin' );
			} elseif ( $this->target == 'user' ) {
				$subject = $rtb_controller->settings->get_setting( 'subject-booking-cancelled-user' );
			}

		} elseif ( $this->event == 'late_user' ) {
			$subject = $rtb_controller->settings->get_setting( 'subject-late-user' );

		} elseif ( $this->event == 'reminder' ) {
			$subject = $rtb_controller->settings->get_setting( 'subject-reminder-user' );

		// Use a subject that's been appended manually if available
		} else {
			$subject = empty( $this->subject ) ? '' : $this->subject;
		}

		$this->subject = $this->process_subject_template( apply_filters( 'rtb_notification_email_subject', $subject, $this ) );

	}

	/**
	 * Set email headers
	 * @since 0.0.1
	 */
	public function set_headers( $headers = null ) {

		global $rtb_controller;

		$from_email = apply_filters( 'rtb_notification_email_header_from_email', $rtb_controller->settings->get_setting( 'from-email-address' ) );

		$headers = "From: =?UTF-8?Q?" . 
			quoted_printable_encode( 
				html_entity_decode( 
					$rtb_controller->settings->get_setting( 'reply-to-name' ), 
					ENT_QUOTES, 
					'UTF-8' 
				) 
			) . 
			"?= <" . $from_email . ">\r\n";

		$headers .= "Reply-To: =?UTF-8?Q?" . 
			quoted_printable_encode( 
				html_entity_decode( 
					$this->from_name, 
					ENT_QUOTES, 
					'UTF-8' 
				) 
			) . 
			"?= <" . $this->from_email . ">\r\n";
		
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";

		$this->headers = apply_filters( 'rtb_notification_email_headers', $headers, $this );

	}

	/**
	 * Set email message body
	 * @since 0.0.1
	 */
	public function set_message() {

		if ( $this->event == 'new_submission' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-booking-user' );
			} elseif ( $this->target == 'admin' ) {
				$template = $this->get_template( 'template-booking-admin' );
			}

		} elseif ( $this->event == 'rtb_confirmed_booking' ) { 
			if ( $this->target == 'admin' ) {
				$template = $this->get_template( 'template-booking-confirmed-admin' );
			}

		} elseif ( $this->event == 'pending_to_confirmed' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-confirmed-user' );
			}

		} elseif ( $this->event == 'pending_to_closed' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-rejected-user' );
			}

		} elseif ( $this->event == 'booking_cancelled' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-booking-cancelled-user' );
			} elseif ( $this->target == 'admin' ) {
				$template = $this->get_template( 'template-booking-cancelled-admin' );
			}

		} elseif ( $this->event == 'late_user' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-late-user' );
			}

		} elseif ( $this->event == 'reminder' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-reminder-user' );
			}

		// Use a message that's been appended manually if available
		} else {
			$template = empty( $this->message ) ? '' : $this->message;
		}

		$template = apply_filters( 'rtb_notification_email_template', $template, $this );

		if ( ! empty( $this->manual_message ) ) {
			$this->message = $this->manual_message;
		}
		elseif ( empty( $template ) ) {
			$this->message = '';
		} else {
			$this->message = wpautop( $this->process_template( $template ) );
		}

	}

	/**
	 * Process template tags for email subjects
	 * @since 0.0.1
	 */
	public function process_subject_template( $subject ) {

		$template_tags = array(
			'{user_name}'		=> ! empty( $this->booking->name ) ? $this->booking->name : '',
			'{party}'			=> ! empty( $this->booking->party ) ? $this->booking->party : '',
			'{date}'			=> ! empty( $this->booking->date ) ? $this->booking->format_date( $this->booking->date ) : '',
		);

		$template_tags = apply_filters( 'rtb_notification_email_subject_template_tags', $template_tags, $this );

		return str_replace( array_keys( $template_tags ), array_values( $template_tags ), $subject );

	}

	/**
	 * Send notification
	 * @since 0.0.1
	 */
	public function send_notification() {
		return wp_mail( $this->to_email, $this->subject, $this->message, $this->headers, apply_filters( 'rtb_notification_email_attachments', array(), $this ) );
	}
}
} // endif;
