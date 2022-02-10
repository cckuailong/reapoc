<?php

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class RTEC_Email
 */
class RTEC_Email {
	/**
	 * @var string
	 */
	private $template_type = 'plain';

	/**
	 * @var string
	 */
	private $content_type = 'text';

	/**
	 * @var string
	 */
	private $subject = '{event-title}';

	/**
	 * @var string
	 */
	private $message_body = '';

	/**
	 * @var string
	 */
	private $headers = '';

	/**
	 * @var array
	 */
	private $recipients = array();

	/**
	 * @var array
	 */
	private $custom_template_pairs = array();

	/**
	 * @var boolean
	 */
	private $need_templating;

	/**
	 * @var string
	 */
	private $find_and_replace_message;

	/**
	 * @param array $args
	 * @param bool $need_templating
	 * @param string $event_id
	 * @param string $submission_email
	 */
	public function build_email( $args = array(), $need_templating = true, $event_id = '', $submission_email = '' )
	{
		if ( isset( $args['custom_template_pairs' ] ) ) {
			$this->custom_template_pairs = $args['custom_template_pairs' ];
		}
		$this->need_templating = $need_templating;
		$this->set_template_type( $args['template_type'] );
		$this->set_content_type( $args['content_type'] );

		$recipients = is_array( $args['recipients'] ) ? $args['recipients'] : explode(',' , $args['recipients'] );
		$recipients = apply_filters( 'rtec_email_recipients', $recipients, $args['template_type'], $args['body']['data'] );

		$this->set_recipients( $recipients );
		$this->set_subject( $args['subject'] );
		$this->set_header( $event_id, $submission_email, $args['body']['data'] );
		$this->set_message_body( $args['body'] );
	}

	/**
	 * @return bool
	 */
	public function send_email()
	{
		$attachments = isset( $this->attachments ) ? $this->attachments : array();
		$sent = wp_mail( $this->recipients, html_entity_decode( $this->subject, ENT_QUOTES, 'UTF-8' ), $this->message_body, $this->headers, $attachments );

		if ( $sent ) {
			return true;
		} else {
			$report = array(
				'recipients' => $this->recipients,
				'subject' => html_entity_decode( $this->subject, ENT_QUOTES, 'UTF-8' ),
				'body' => $this->find_and_replace_message,
				'headers' => $this->headers,
			);
			if ( ! empty( $errors ) ) {
				$report_errors = get_option( 'rtec_error_log', array() );

				$report_errors['email'] = $report;

				update_option( 'rtec_error_log', $report_errors, false );
			}

			return false;
		}	}

	/**
	 * @return string
	 */
	public function get_error_message()
	{
		return __( 'There was a problem sending the email', 'registrations-for-the-events-calendar' );
	}

	/**
	 * @return string
	 */
	public function get_content_type()
	{
		return $this->content_type;
	}

	/**
	 * @param $sanitized_data
	 *
	 * @return string
	 */
	public function get_generic_confirmation( $sanitized_data )
	{
		global $rtec_options;

		$date_str = date_i18n( rtec_get_date_time_format(), strtotime( $sanitized_data['date'] ) );
		$body = '<p>';
		$body .= __( 'You are registered!', 'registrations-for-the-events-calendar' ) . "<br><br>";
		$first_message = sprintf( __( 'Event: %1$s at %2$s on %3$s', 'registrations-for-the-events-calendar' ), $sanitized_data['title'], $sanitized_data['venue_title'], $date_str );
		$body .= esc_html( $first_message ) . '<br>';
		$first = ! empty( $sanitized_data['first'] ) ? esc_html( $sanitized_data['first'] ) : '';
		$last = ! empty( $sanitized_data['last'] ) ? esc_html( $sanitized_data['last'] ) : '';
		$body .= sprintf ( __( 'Name', 'registrations-for-the-events-calendar' ) .': %1$s %2$s', $first, $last ) . "<br>";

		if ( ! empty( $sanitized_data['phone'] ) ) {
			$phone = esc_html( $sanitized_data['phone'] );
			$body .= sprintf (  __( 'Phone', 'registrations-for-the-events-calendar' ) .': %1$s', $phone ) . "<br>";
		}

		if ( ! empty( $sanitized_data['guests'] ) ) {
			$guests = esc_html( $sanitized_data['guests'] );
			$body .= sprintf (  __( 'Guests', 'registrations-for-the-events-calendar' ) .': %1$s', $guests ) . "<br>";
		}

		if ( ! empty( $sanitized_data['other'] ) ) {
			$other = esc_html( $sanitized_data['other'] );
			$body .= sprintf (  __( 'Other', 'registrations-for-the-events-calendar' ) .': %1$s', $other ) . "<br>";
		}

		if ( isset( $rtec_options['custom_field_names'] ) ) {

			if ( ! is_array( $rtec_options['custom_field_names'] ) ) {
				$rtec_options['custom_field_names'] = explode( ',', $rtec_options['custom_field_names'] );
			}

			foreach ( $rtec_options['custom_field_names'] as $field ) {

				if ( ! empty( $sanitized_data[ $field ] ) ) {
					$custom = esc_html( $sanitized_data[ $field ] );
					$body .= sprintf( '%s&#58; %s<br>', esc_html( $rtec_options[ $field . '_label' ] ), esc_html( $custom ) );
				}

			}
		}

		if ( ! empty( $sanitized_data['venue_address'] ) ) {
			$body .= "<br>" . __( 'The event will be held at this location', 'registrations-for-the-events-calendar' ) . ':' . "<br><br>";
			$body .= sprintf( '%1$s'. "\n", esc_html( $sanitized_data['venue_address'] ) );
			$body .= sprintf( '%1$s, %2$s %3$s'. "\n\n", esc_html( $sanitized_data['venue_city'] ), esc_html( $sanitized_data['venue_state'] ), esc_html( $sanitized_data['venue_zip'] ) );
		}

		$body .=  "<br><br>" . __( 'Thank You!', 'registrations-for-the-events-calendar' ) . '</p>';

		return $body;
	}

	/**
	 * @param $sanitized_data
	 *
	 * @return string
	 */
	public function get_generic_submission_notification( $sanitized_data )
	{
		global $rtec_options;

		$body = '';
		$date_str = date_i18n( rtec_get_date_time_format(), strtotime( $sanitized_data['date'] ) );
		$use_custom_notification = isset( $rtec_options['use_custom_notification'] ) ? $rtec_options['use_custom_notification'] : false;

		if ( $use_custom_notification && $rtec_options['message_source'] !== 'translate' ) {
			$body = $this->find_and_replace( $rtec_options['notification_message'], $sanitized_data );
		} else {
			$first_message = sprintf( __( 'The following submission was made for: %1$s at %2$s on %3$s', 'registrations-for-the-events-calendar' ), $sanitized_data['title'], $sanitized_data['venue_title'], $date_str );
			$body .= '<p>' . esc_html( $first_message ) . '</p>';
			$body .= RTEC_Email::all_fields_placeholder( $sanitized_data );

		}

		return $body;
	}


	/**
	 * @param $type
	 */
	protected function set_template_type( $type )
	{
		switch ( $type ) {
			case 'confirmation':
				$template_type = 'confirmation';
				break;
			case 'notification':
				$template_type = 'notification';
				break;
			default:
				$template_type = 'plain';
				break;
		}

		$this->template_type = $template_type;
	}

	/**
	 * @param $content_type
	 */
	protected function set_content_type( $content_type )
	{
		if ( $content_type === 'html' ) {
			$this->content_type = 'html';
		} else {
			$this->content_type = 'plain';
		}
	}

	/**
	 * @param string $event_id
	 * @param $submission_email
	 */
	protected function set_header( $event_id = '', $submission_email, $data = array() )
	{
		global $rtec_options;

		if ( $event_id !== '' ) {
			$from_address = rtec_get_confirmation_from_address( $event_id );
		} else {
			$from_address = isset( $rtec_options['confirmation_from_address'] ) && is_email( $rtec_options['confirmation_from_address'] ) ? $rtec_options['confirmation_from_address'] : get_option( 'admin_email' );
		}

		$reply_to = $from_address;
		if ( $this->template_type === 'confirmation' ) {
			$working_template_type = 'confirmation';
		} else {
			$reply_to = is_email( $submission_email ) ? $submission_email : $from_address;
			$working_template_type = 'notification';
		}

		if ( ! empty ( $rtec_options[ $working_template_type . '_from'] ) && ! empty ( $rtec_options[ 'confirmation_from_address'] ) ) {
			$from_name = strpos( $rtec_options[ $working_template_type . '_from'], '{' ) !== false ? $this->strip_malicious( $this->find_and_replace( $rtec_options[ $working_template_type . '_from'], $data ) ) : $this->strip_malicious( $rtec_options[ $working_template_type . '_from'] );
			$email_from = str_replace( ':' , ' ', $from_name ) . ' <' . $from_address . '>';
			$headers  = "From: " . $email_from. "\r\n";
			$headers .= "Reply-To: " . $reply_to . "\r\n";
		} else {
			$from_address = get_option( 'admin_email' );
			$blog_name = get_bloginfo( 'name' );
			$email_from = ! empty( $blog_name ) ? $blog_name . ' <' . $from_address . '>' : 'WordPress <' . $from_address . '>';
			$headers  = "From: " . $email_from. "\r\n";
			$headers .= "Reply-To: " . $reply_to . "\r\n";
		}

		if ( $this->content_type === 'html') {
			$headers .= 'Content-Type: text/html; charset=utf-8' . "\r\n";
		} else {
			$headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
		}

		$headers = apply_filters( 'rtec_email_headers', $headers, $this->template_type, $data );

		$this->headers = $headers;
	}

	/**
	 * @param $emails
	 */
	protected function set_recipients( $emails )
	{
		$recipients = array();
		if ( ! is_array( $emails ) && is_email( $emails ) ) {
			$recipients = $emails;
		} elseif ( is_array( $emails ) ) {

			foreach ( $emails as $email ) {

				if ( is_email( $email ) ) {
					$recipients[] = $email;
				}

			}

		} else {
			$working_emails = explode( ',', $emails );

			foreach ( $working_emails as $email ) {

				if ( is_email( trim( $email ) ) ) {
					$recipients[] = trim( $email );
				}

			}

		}

		$this->recipients = $recipients;
	}

	/**
	 * @param $args
	 */
	protected function set_subject( $args )
	{
		if ( ! empty( $args['text'] ) ) {

			if ( strpos( $args['text'], '{' ) && ! empty( $args['data'] ) ) {
				$this->subject = $this->strip_malicious( $this->find_and_replace( $args['text'], $args['data'] ) );
			} else {
				$this->subject = $this->strip_malicious( $args['text'] );
			}

		} else {
			global $rtec_options;

			$event_title = ! empty( $args['data']['title'] ) ? $args['data']['title'] : __( 'Your Registration', 'registrations-for-the-events-calendar' );

			$defaults = array(
				'confirmation'  => $event_title,
				'notification'  => __( 'Registration Notification', 'registrations-for-the-events-calendar' ),
				'plain'         => __( 'Registration email', 'registrations-for-the-events-calendar' )
			);

			if ( $this->template_type === 'confirmation' || $this->template_type === 'notification' ) {

				if ( isset( $rtec_options[ $this->template_type . '_subject' ] ) ) {
					$this->subject = $this->strip_malicious( $this->find_and_replace( $rtec_options[ $this->template_type . '_subject' ], $args['data'] ) );
				} else {
					$this->subject = $defaults[ $this->template_type ];
				}

			} else {
				$this->subject = $this->strip_malicious( $defaults[ $this->template_type ] );
			}

		}

	}

	/**
	 * @param $args
	 */
	protected function set_message_body( $args )
	{
		global $rtec_options;

		if ( ! empty( $args['message'] ) && ! empty( $args['data'] ) ) {
			$body = stripslashes( $this->find_and_replace( $args['message'], $args['data'] ) );
		} else {
			$body = stripslashes( $args['message'] );
		}

		$this->find_and_replace_message = $body;

		if ( $this->content_type === 'html' ) {

			$styling['bg_color'] = isset( $args['bg_color'] ) ? $args['bg_color'] : '#eee';

			ob_start();

			$custom_header_template = locate_template( 'rtec/email/header-generic.php', false, false );
			$header_template = $custom_header_template ? $custom_header_template : RTEC_PLUGIN_DIR . 'templates/email/header-generic.php';
			include $header_template;

			$custom_confirmation_template = locate_template( 'rtec/email/confirmation-body.php', false, false );
			$confirmation_template = $custom_confirmation_template ? $custom_confirmation_template: RTEC_PLUGIN_DIR . 'templates/email/confirmation-body.php';
			include $confirmation_template;


			$custom_footer_template = locate_template( 'rtec/email/footer-generic.php' , false, false );
			$footer_template = $custom_footer_template ? $custom_footer_template : RTEC_PLUGIN_DIR . 'templates/email/footer-generic.php';
			include $footer_template;

			$html = ob_get_contents();
			ob_end_clean();
			$this->message_body = $html;

		} else {
			$this->message_body = $body;
		}
	}

	/**
	 * Used by some features to add dynamic fields to emails, etc..
	 *
	 * @param $text string  text from email that needs to replace dynamic fields
	 *
	 * @since 1.3
	 * @return string   text with dynamic fields inserted
	 */
	protected function find_and_replace( $text, $sanitized_data )
	{
		global $rtec_options;

		$working_text = $text;

		if ( ! $this->need_templating ) {
			return $working_text;
		}

		$date_str = isset( $sanitized_data['date'] ) ? date_i18n( rtec_get_date_time_format(), strtotime( $sanitized_data['date'] ) ) : '';
		$start_date = '';
		$start_time = '';
		$end_date = '';
		$end_time = '';
		$event_link = '';
		$event_cost = function_exists( 'tribe_get_cost' ) ? tribe_get_cost( $sanitized_data['event_id'] ) : '';
		$first = isset( $sanitized_data['first'] ) ? $sanitized_data['first'] : '';
		$last = isset( $sanitized_data['last'] ) ? $sanitized_data['last'] : '';
		$email = isset( $sanitized_data['email'] ) ? $sanitized_data['email'] : '';
		$phone = isset( $sanitized_data['phone'] ) ? rtec_format_phone_number( $sanitized_data['phone'] ) : '';
		$other = isset( $sanitized_data['other'] ) ? $sanitized_data['other'] : '';
		$mvt_label = isset( $sanitized_data['mvt_label'] ) ? $sanitized_data['mvt_label'] : '';
		$ical_link = '';
		if ( isset( $sanitized_data['event_id'] ) && ! empty( $sanitized_data['event_id'] ) ) {
			$event_link = get_the_permalink( $sanitized_data['event_id'] );
			$ical_link = add_query_arg( 'ical', 1, $event_link );
		}
		if ( is_callable( 'tribe_get_start_date' ) && is_callable( 'tribe_get_end_date' ) ) {
			$time_format = rtec_get_time_format();

			$start_date = tribe_get_start_date( $sanitized_data['event_id'], false );
			$start_time = tribe_get_start_date( $sanitized_data['event_id'], false, $time_format );
			$end_date = tribe_get_end_date( $sanitized_data['event_id'], false );
			$end_time = tribe_get_end_date( $sanitized_data['event_id'], false, $time_format );
		}

		$all_fields = '';
		if ( (strpos( $text, '{all-fields}' )) !== false && ! empty( $sanitized_data['event_id'] ) ) {
			$all_fields = RTEC_Email::all_fields_placeholder( $sanitized_data );
		}

		if ( ! empty( $sanitized_data ) ) {
			$search_replace = array(
				'{venue}' => $sanitized_data['venue_title'],
				'{venue-address}' => $sanitized_data['venue_address'],
				'{venue-city}' => $sanitized_data['venue_city'],
				'{venue-state}' => $sanitized_data['venue_state'],
				'{venue-zip}' => $sanitized_data['venue_zip'],
				'{event-title}' => $sanitized_data['title'],
				'{event-date}' => $date_str,
				'{start-date}' => $start_date,
				'{start-time}' => $start_time,
				'{end-date}' => $end_date,
				'{end-time}' => $end_time,
				'{event-url}' => $event_link,
				'{event-cost}' => $event_cost,
				'{first}' => $first,
				'{last}' => $last,
				'{email}' => $email,
				'{phone}' => $phone,
				'{other}' => $other,
				'{venue-or-tier}' => $mvt_label,
				'{ical-url}' => $ical_link,
				'{all-fields}' => $all_fields
			);

			$sanitized_data['event_id'] = isset( $sanitized_data['event_id'] ) ? $sanitized_data['event_id'] : $sanitized_data['post_id'];
			$search_replace = apply_filters( 'rtec_email_templating', $search_replace, $sanitized_data );

			if ( $this->get_content_type() === 'plain' ) {
				$search_replace['{nl}'] = "\n";
			} else {
				$search_replace['{nl}'] = '<br />';
			}

			// add custom
			if ( is_array( $this->custom_template_pairs ) && ! empty( $this->custom_template_pairs ) ) {

				foreach ( $this->custom_template_pairs  as $field => $atts ) {

					if ( $atts['label'] !== '' ) {

						if ( isset( $sanitized_data['custom'][ $field ] ) ) {
							$search_replace['{' . str_replace( '&#42;', '', stripslashes( $atts['label'] ) ) . '}'] =  $sanitized_data['custom'][ $field ]['value'];
						} elseif( isset( $sanitized_data['custom'][ $atts['label'] ] ) ) {
							$search_replace['{' . str_replace( '&#42;', '', stripslashes( $atts['label'] ) ) . '}'] =  $sanitized_data['custom'][ $atts['label'] ];
						} elseif ( isset( $sanitized_data[ $field ] ) ) {
							$search_replace['{' . str_replace( '&#42;', '', stripslashes( $atts['label'] ) ) . '}'] =  $sanitized_data[ $field ];
						} else {
							$search_replace['{' . str_replace( '&#42;', '', stripslashes( $atts['label'] ) ) . '}'] = '';
						}

					}

				}

			}

			foreach ( $search_replace as $search => $replace ) {
				$working_text = str_replace( $search, $replace, $working_text );
			}

			if ( strpos( $working_text, '{unregister-link}' ) !== false ) {
				if ( isset( $sanitized_data['action_key'] ) && $sanitized_data['action_key'] != '' ) {
					$unregister_link_text = isset( $rtec_options['unregister_link_text'] ) ? $rtec_options['unregister_link_text'] : __( 'Unregister from this event', 'registrations-for-the-events-calendar' );
					$unregister_link_text = rtec_get_text( $unregister_link_text, __( 'Unregister from this event', 'registrations-for-the-events-calendar' ) );
					$u_link = rtec_generate_unregister_link( $sanitized_data['event_id'], $sanitized_data['action_key'], $sanitized_data['email'], $unregister_link_text );
				} else {
					$u_link = '';
				}

				$working_text = str_replace( '{unregister-link}', $u_link, $working_text );
			}

		}

		return $working_text;
	}

	public static function all_fields_placeholder( $sanitized_data ) {
		global $rtec_options;

		$first_text = isset( $rtec_options['first_label'] ) ? $rtec_options['first_label'] : __( 'First', 'registrations-for-the-events-calendar' );
		$last_text = isset( $rtec_options['last_label'] ) ? $rtec_options['last_label'] : __( 'Last', 'registrations-for-the-events-calendar' );
		$email_text = isset( $rtec_options['email_label'] ) ? $rtec_options['email_label'] : __( 'Email', 'registrations-for-the-events-calendar' );

		$first_label = rtec_get_text( $first_text, __( 'First', 'registrations-for-the-events-calendar' ) );
		$last_label = rtec_get_text( $last_text, __( 'Last', 'registrations-for-the-events-calendar' ) );
		$email_label = rtec_get_text( $email_text, __( 'Email', 'registrations-for-the-events-calendar' ) );
		$first = ! empty( $sanitized_data['first'] ) ? esc_html( $sanitized_data['first'] ) . ' ' : ' ';
		$last = ! empty( $sanitized_data['last'] ) ? esc_html( $sanitized_data['last'] ) : '';

		$body = '<table><tbody>';
		if ( ! empty( $sanitized_data['first'] ) ) {
			$body .= '<tr>';
			$body .= sprintf( '<td>%s&#58;</td><td>%s</td>', esc_html( $first_label ), esc_html( $first ) );
			$body .= '</tr>';
		}

		if ( ! empty( $sanitized_data['last'] ) ) {
			$body .= sprintf( '<td>%s&#58;</td><td>%s</td>', esc_html( $last_label ), esc_html( $last ) );
		}

		if ( ! empty( $sanitized_data['email'] ) ) {
			$email = esc_html( $sanitized_data['email'] );
			$body .= '<tr>';
			$body .= sprintf( '<td>%s&#58;</td><td>%s</td>', esc_html( $email_label ), esc_html( $email ) );
			$body .= '</tr>';
		}

		if ( ! empty( $sanitized_data['phone'] ) ) {
			$phone_label = rtec_get_text( $rtec_options['phone_label'], __( 'Phone', 'registrations-for-the-events-calendar' ) );
			$phone = rtec_format_phone_number( esc_html( $sanitized_data['phone'] ) );
			$body .= '<tr>';
			$body .= sprintf( '<td>%s&#58;</td><td>%s</td>', esc_html( $phone_label ), esc_html( $phone ) );
			$body .= '</tr>';
		}

		if ( ! empty( $sanitized_data['other'] ) ) {
			$other_label = rtec_get_text( $rtec_options['other_label'], __( 'Other', 'registrations-for-the-events-calendar' ) );
			$other = esc_html( $sanitized_data['other'] );
			$body .= '<tr>';
			$body .= sprintf( '<td>%s&#58;</td><td>%s</td>', esc_html( $other_label ), esc_html( $other ) );
			$body .= '</tr>';
		}

		if ( isset( $rtec_options['custom_field_names'] ) ) {

			if ( ! is_array( $rtec_options['custom_field_names'] ) ) {
				$rtec_options['custom_field_names'] = explode( ',', $rtec_options['custom_field_names'] );
			}

			foreach ( $rtec_options['custom_field_names'] as $field ) {

				if ( ! empty( $sanitized_data[ $field ] ) ) {
					$custom = esc_html( $sanitized_data[ $field ] );
					$body .= '<tr>';
					$body .= sprintf( '<td>%s&#58;</td><td>%s</td>', esc_html( $rtec_options[ $field . '_label' ] ), esc_html( $custom ) );
					$body .= '</tr>';
				}

			}
		}
		$body .= '</tbody></table>';

		return $body;
	}

	/**
	 * Removes anything that could potentially be malicious
	 *
	 * @param $value
	 * @since 1.0
	 * @since 2.1       replace to: with space instead of to
	 * @return string
	 */
	private function strip_malicious( $value )
	{
		$malicious = array( 'cc:', 'bcc:', 'content-type:', 'mime-version:', 'multipart-mixed:', 'content-transfer-encoding:' );

		foreach ( $malicious as $m ) {

			if ( stripos( $value, $m ) !== false ) {
				return 'It looks like your message contains something potentially harmful.';
			}

		}
		$value = str_replace( array( '\r', '\n', '%0a', '%0d', 'to:' ), ' ' , $value);

		return trim( $value );
	}

}