<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

class RTEC_Submission
{
	/**
	 * @var RTEC_Submission
	 * @since 1.0
	 */
    private static $instance;

	/**
	 * @var array
	 * @since 1.0
	 */
    public $submission = array();

	/**
	 * @var array
	 * @since 1.0
	 */
    public $errors = array();

	/**
	 * @var array
	 * @since 1.0
	 */
    protected $required_fields = array();

	/**
	 * @var array
	 * @since 1.3
	 */
	protected $custom_required_fields = array();

	private $field_attributes = array();

	/**
	 * @var array
	 * @since 1.0
	 */
    public $validate_check = array();

	/**
	 * @var array
	 * @since 1.0
	 */
	private $event_meta = array();

	public $custom_fields_label_name_pairs = array();

	/**
	 * Get the one true instance of EDD_Register_Meta.
	 *
	 * @since  1.0
	 * @return object $instance
	 */
	static public function instance() {
		if ( !self::$instance ) {
			self::$instance = new RTEC_Submission( $_POST );
		}

		return self::$instance;
	}

	/**
	 * Validates the initial data
	 *
	 * @param $post $_POST data
	 * @since 1.0
	 * @since 2.0 updates
	 *
	 */
	public function validate_input( $post )
	{
		global $rtec_options;

		// get form options from the db
		$unvalidated_submission = $post;
		$fields_atts = $this->field_attributes;
		$event_meta = rtec_get_event_meta( (int)$unvalidated_submission['rtec_event_id'] );
		$this->event_meta = $event_meta;
		$raw_data = array();
		$errors = array();
		$error_report = array();

		// for each submitted form field
		require_once RTEC_PLUGIN_DIR . 'inc/class-rtec-validator.php';
		$validator = new RTEC_Validator();

		if ( isset( $unvalidated_submission['rtec_user_comments'] ) && ! empty( $unvalidated_submission['rtec_user_comments'] ) ) {
			$errors[] = 'user_comments';
			$error_report['user_comments'] = $unvalidated_submission['rtec_user_comments'];
		}

		foreach ( $fields_atts as $show_field => $value ) {
			// check spam honeypot, error if not empty
			if ( isset( $unvalidated_submission[ 'rtec_' . $show_field ] ) && is_array( $unvalidated_submission[ 'rtec_' . $show_field ] ) ) {
				$unvalidated_submission[ 'rtec_' . $show_field ] = isset( $unvalidated_submission[ 'rtec_' . $show_field ] ) ? implode( ', ', $unvalidated_submission[ 'rtec_' . $show_field ] ) : '';
			} elseif ( ! isset( $unvalidated_submission[ 'rtec_' . $show_field ] ) ) {
				if ( $show_field === 'recaptcha' && isset( $unvalidated_submission['g-recaptcha-response'] ) ) {
					$unvalidated_submission[ 'rtec_' . $show_field ] = $unvalidated_submission['g-recaptcha-response'];
				} else {
					$unvalidated_submission[ 'rtec_' . $show_field ] = '';
				}
			}

			if ( $show_field === 'email' && ( $fields_atts[ $show_field ]['required'] || $unvalidated_submission['rtec_email'] !== '' ) ) {

				if ( isset( $rtec_options['check_for_duplicates'] ) && $rtec_options['check_for_duplicates'] === true ) {

					if ( isset( $unvalidated_submission['rtec_email'] ) && $this->registrant_check_for_duplicate_email( $unvalidated_submission['rtec_email'] ) ) {
						$errors[] = 'email_duplicate';
					}

				}

			}

			if ( $fields_atts[ $show_field ]['required'] ) {
				$valid = false;

				//temporary fix for phone numbers
				if ( $show_field === 'phone' ) {
					$phone_option = isset( $rtec_options['phone_format'] ) ? $rtec_options['phone_format'] : '1';

					if ( $phone_option !== '4' ) {
						$unvalidated_submission[ 'rtec_' . $show_field ] = preg_replace( '/[^0-9]/', '', $unvalidated_submission[ 'rtec_' . $show_field ] );
					}

					if ( $fields_atts['phone']['valid_params']['count'] === '' ) {
						$fields_atts[ $show_field ]['valid_type'] = 'length';
						$fields_atts[ $show_field ]['valid_params']['min'] = 1;
						$fields_atts[ $show_field ]['valid_params']['max'] = 1000;
					}

				}

				if ( isset( $unvalidated_submission[ 'rtec_' . $show_field ] ) === true && $unvalidated_submission[ 'rtec_' . $show_field ] !== '' ) {

					switch ( $fields_atts[ $show_field ]['valid_type'] ) {
						case 'email':
							$valid = $validator->email( $unvalidated_submission[ 'rtec_' . $show_field ] );
							break;
						case 'recaptcha':
							if ( $rtec_options['recaptcha_type'] === 'google' && ! empty( $rtec_options['recaptcha_secret_key'] ) ) {
								$valid = $validator->google_recaptcha( $unvalidated_submission['g-recaptcha-response'], $rtec_options['recaptcha_secret_key'] );
							} else {
								$recaptcha_strictness = 'normal';
								$recaptcha_strictness = apply_filters( 'rtec_recaptcha_strictness', $recaptcha_strictness );
								$valid                = $validator->num_equality( $unvalidated_submission[ 'rtec_' . $show_field . '_sum' ], $unvalidated_submission[ 'rtec_' . $show_field ], $recaptcha_strictness );
							}
							break;
						case 'google_recaptcha':
							if ( $rtec_options['recaptcha_type'] === 'google' && ! empty( $rtec_options['recaptcha_secret_key'] ) ) {
								$valid = $validator->google_recaptcha( $unvalidated_submission['g-recaptcha-response'], $rtec_options['recaptcha_secret_key'] );
							} else {
								$recaptcha_strictness = 'normal';
								$recaptcha_strictness = apply_filters( 'rtec_recaptcha_strictness', $recaptcha_strictness );
								$valid                = $validator->num_equality( $unvalidated_submission[ 'rtec_' . $show_field . '_sum' ], $unvalidated_submission[ 'rtec_' . $show_field ], $recaptcha_strictness );
							}
							break;
						case 'count':
							$valid = $validator->count( $unvalidated_submission[ 'rtec_' . $show_field ], $fields_atts[ $show_field ]['valid_params']['count'], $fields_atts[ $show_field ]['valid_params']['count_what'] );
							break;
						case 'numval':
							$valid = $validator->numval( $unvalidated_submission[ 'rtec_' . $show_field ], $fields_atts[ $show_field ]['valid_params']['min'], $fields_atts[ $show_field ]['valid_params']['max'] );
							break;
						case 'none':
							$valid = ( strlen( $unvalidated_submission[ 'rtec_' . $show_field ] ) > 0 );
							break;
						default:
							$valid = $validator->length( $unvalidated_submission[ 'rtec_' . $show_field ], $fields_atts[ $show_field ]['valid_params']['min'], $fields_atts[ $show_field ]['valid_params']['max'] );
					}

				}

				if ( ! $valid ) {
					$errors[] = $show_field;
					$error_report[$show_field] = $unvalidated_submission[ 'rtec_' . $show_field ];
				}
			}

			if ( $value['valid_type'] !== 'recaptcha' ) {
				$raw_data[ $show_field ] = isset( $unvalidated_submission[ 'rtec_' . $show_field ] ) ? $unvalidated_submission[ 'rtec_' . $show_field ] : '';
			}
		}

		$raw_data['title'] = $event_meta['title'];

		$raw_data['venue'] = $event_meta['venue_id'];
		$raw_data['venue_title'] = $event_meta['venue_title'];
		$raw_data['venue_address'] = $event_meta['venue_address'];
		$raw_data['venue_city'] = $event_meta['venue_city'];
		$raw_data['venue_state'] = $event_meta['venue_state'];
		$raw_data['venue_zip'] = $event_meta['venue_zip'];
		$raw_data['num_registered'] = $event_meta['num_registered'];

		$raw_data['date'] = $event_meta['start_date'];
		$raw_data['event_id'] = $unvalidated_submission['rtec_event_id'];

		$this->errors = $errors;

		if ( !empty( $errors ) ) {
			delete_transient( 'rtecSubmissionError' );
			$error_report['submission'] = $raw_data;
			set_transient( 'rtecSubmissionError', $error_report, 60 * 60 * 12 );
		}

		return $raw_data;
	}

	public function set_field_attributes( $fields_atts )
	{
		$this->field_attributes = $fields_atts;
	}

	/**
	 * Compares the allowed number of registrations with the current number
	 *
	 * @param int $num_registered
	 *
	 * @since 1.2
	 * @return bool
	 */
    public function attendance_limit_not_reached()
    {
    	$limit_registrations = isset( $this->event_meta['limit_registrations'] ) ? $this->event_meta['limit_registrations'] : false;
	    if ( $limit_registrations ) {
		    if ( $this->event_meta['registrations_left'] > 0 ) {
		    	return true;
		    } else {
		    	return false;
		    }

	    } else {
	    	return true;
	    }
    }

	/**
	 * Compares existing emails registered for this event with the submitted one
	 *
	 * @param string $email
	 *
	 * @since 1.6
	 * @return bool
	 */
	public function registrant_check_for_duplicate_email( $email ) {
		require_once RTEC_PLUGIN_DIR . 'inc/class-rtec-db.php';

		$email = is_email( $email ) ? $email : false;
		$event_id = (int)$this->event_meta['post_id'];

		$is_duplicate = 'not';

		if ( false !== $email ) {
			$db = New RTEC_Db();
			$is_duplicate = $db->is_duplicate_email( $email, $event_id );
		}

		return $is_duplicate;
	}

	/**
	 * Check if there are validation errors from the submitted data
	 * 
	 * @since 1.0
	 * @return bool
	 */
    public function has_errors()
    {
        return ! empty( $this->errors );
    }

	/**
	 * The fields that have errors
	 * 
	 * @since 1.0
	 * @return array
	 */
    public function get_errors() 
    {
        return $this->errors;
    }

	/**
	 * data from the submission
	 * 
	 * @since 1.0
	 * @return array
	 */
    public function get_data()
    {
        return $this->submission;
    }

	/**
	 * Removes anything that might cause problems
	 * 
	 * @since 1.0
	 */
	public function sanitize_submission( $raw_data )
	{
		$submission = $raw_data;
		$sanitized_data = array();
		// for each submitted form field
		foreach ( $submission as $input_key => $input_value ) {
			// strip potentially malicious header strings
			$new_val = $this->strip_malicious( $input_value );
			// replace single quotes
			$new_val = str_replace( "'", '`', $new_val );
			// assign the sanitized value
			$sanitized_data[ $input_key ] = $new_val;
		}

		return $sanitized_data;
	}

	/**
	 * Meant to be called only after submission has been validated
	 *
	 * @since 1.0
	 */
	public function process_valid_submission( $raw_data ) {
		global $rtec_options;

		$rtec = RTEC();
		$db = $rtec->db_frontend->instance();

		$disable_confirmation = isset( $rtec_options['disable_confirmation'] ) ? $rtec_options['disable_confirmation'] : false;
		$disable_notification = isset( $rtec_options['disable_notification'] ) ? $rtec_options['disable_notification'] : false;
		$return = 'success';
		$status = 'n';


		$sanitized_data = $this->sanitize_submission( $raw_data );
		$sanitized_data['action_key'] = sha1( uniqid( '', true ) );
		$data = $this->get_db_data( $sanitized_data, $status );

		$db->insert_entry( $data, $this->field_attributes );

		$this->submission = $sanitized_data;

		$confirmation_success = false;
		$email = isset( $sanitized_data['email'] ) ? $sanitized_data['email'] : '';
		$custom_template_pairs = rtec_get_custom_name_label_pairs();

		if ( $this->email_given( $email ) && ! $disable_confirmation ) {
			require_once RTEC_PLUGIN_DIR . 'inc/class-rtec-email.php';
			$confirmation_message = new RTEC_Email();
			$fresh_options = get_option( 'rtec_options' );
			$message = isset( $fresh_options['confirmation_message'] ) ? __( $fresh_options['confirmation_message'], 'registrations-for-the-events-calendar' ) : $confirmation_message->get_generic_confirmation( $sanitized_data );

			$args = array(
				'template_type' => 'confirmation',
				'content_type'  => 'html',
				'custom_template_pairs' => $custom_template_pairs,
				'recipients'    => $sanitized_data['email'],
				'subject'       => array(
					'text' => '',
					'data' => $sanitized_data
				),
				'body'          => array(
					'message' => $message,
					'data'    => $sanitized_data,
					'header_image' => ''
				)
			);
			$confirmation_message->build_email( $args, true, $sanitized_data['event_id'] );
			$confirmation_success = $confirmation_message->send_email();

			if ( ! $confirmation_success ) {
				$error_message = $confirmation_message->get_error_message();
			}

		} else {
			$status = 'n';
		}

		if ( ! $disable_notification ) {
			require_once RTEC_PLUGIN_DIR . 'inc/class-rtec-email.php';
			$notification_message = new RTEC_Email();
			$use_custom_notification = isset( $rtec_options['use_custom_notification'] ) ? $rtec_options['use_custom_notification'] : false;
			if ( !$use_custom_notification || rtec_using_translations() ) {
				$message = $notification_message->get_generic_submission_notification( $sanitized_data, $this->field_attributes );
			} else {
				$message = isset( $rtec_options['notification_message'] ) ? $rtec_options['notification_message'] : $notification_message->get_generic_submission_notification( $sanitized_data );
			}
			$recipients = rtec_get_notification_email_recipients( $sanitized_data['event_id'] );

			$args = array(
				'template_type' => 'notification',
				'content_type'  => 'html',
				'custom_template_pairs' => $custom_template_pairs,
				'recipients'    => $recipients,
				'subject'       => array(
					'text' => '',
					'data' => $sanitized_data
				),
				'body'          => array(
					'message' => $message,
					'data'    => $sanitized_data
				)
			);
			$notification_message->build_email( $args, true, '', $email );
			$success = $notification_message->send_email();

			if ( ! $success ) {
				$error_message = $notification_message->get_error_message();
			}

		}


		if ( ! empty( $data['event_id'] ) ) {
			$event_meta = rtec_get_event_meta( (int)$data['event_id'] );
		}

		$email = isset( $sanitized_data['email'] ) ? $sanitized_data['email'] : '';
		if ( $this->email_given( $email ) && ! $disable_confirmation && ! $confirmation_success ) {
			return 'email';
		}

		do_action( 'rtec_after_registration_submit', $this );

		return $return;
	}

	/**
	 * Removes anything that could potentially be malicious
	 * 
	 * @param $value
	 * @since 1.0
	 * @return string
	 */
	private function strip_malicious( $value )
	{
		$malicious = array( 'to:', 'cc:', 'bcc:', 'content-type:', 'mime-version:', 'multipart-mixed:', 'content-transfer-encoding:' );

		foreach ( $malicious as $m ) {

			if( stripos( $value, $m ) !== false ) {
				return 'untrusted';
			}

		}
		$value = str_replace( array( '\r', '\n', '%0a', '%0d'), ' ' , $value);

		return trim( $value );
	}

	/**
	 * Did the user supply an email?
	 * 
	 * @since 1.0
	 * @return bool
	 */
    public function email_given( $email )
    {
        if ( ! empty( $email ) ) {
            return true;
        }

        return false;
    }

	/**
	 * @since 1.0
	 * @since 2.0   update needed
	 * @return array
	 */
	public function get_db_data( $sanitized_data, $status = 'n' )
	{
		$data = array();
		foreach ( $sanitized_data as $key => $value ) {
			$data[$key] = $value;
		}
		$data['status'] = $status;

		return $data;
	}
}
RTEC_Submission::instance();