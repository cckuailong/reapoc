<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbNotificationSMS' ) ) {
/**
 * Class to handle an SMS notification for Restaurant Reservations
 *
 * This class extends rtbNotification and must implement the following methods:
 *	prepare_notification() - set up and validate data
 *	send_notification()
 *
 * @since 2.1.0
 */
class rtbNotificationSMS extends rtbNotification {

	/**
	 * Recipient phone number
	 * @since 2.1.0
	 */
	public $phone_number;

	/**
	 * Text message body
	 * @since 2.1.0
	 */
	public $message;

	/**
	 * The license key received for RTB Ultimate
	 * @since 2.1.0
	 */
	public $license_key;

	/**
	 * Email used for purchase, to validate message sending
	 * @since 2.1.0
	 */
	public $purchase_email;


	/**
	 * Prepare and validate notification data
	 *
	 * @return boolean if the data is valid and ready for transport
	 * @since 2.1.0
	 */
	public function prepare_notification() {

		$this->set_phone_number();
		$this->set_message();
		$this->set_license_key();
		$this->set_purchase_email();

		// Return false if we're missing any of the required information
		if ( 	empty( $this->phone_number) ||
				empty( $this->message) ||
				empty( $this->license_key) ||
				empty( $this->purchase_email)  ) {
			return false;
		}

		return true;
	}

	/**
	 * Set phone number
	 * @since 2.1.0
	 */
	public function set_phone_number() {

		$phone_number = $this->booking->phone;

		$this->phone_number = apply_filters( 'rtb_notification_sms_phone_number', $phone_number, $this );

	}

	/**
	 * Set text message body
	 * @since 2.1.0
	 */
	public function set_message() {

		if ( $this->event == 'late_user' ) {
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

		$this->message = apply_filters( 'rtb_notification_sms_template', $this->process_template( $template ), $this );

	}

	/**
	 * Set license key
	 * @since 2.1.0
	 */
	public function set_license_key() {

		if ( ! get_option( 'rtb-ultimate-license-key' ) ) { add_option( 'rtb-ultimate-license-key', 'no_license_key_entered' ); }

		$this->license_key = get_option( 'rtb-ultimate-license-key' );

	}

	/**
	 * Set purchase email
	 * @since 2.1.0
	 */
	public function set_purchase_email() {

		global $rtb_controller;

		$this->purchase_email = $rtb_controller->settings->get_setting( 'ultimate-purchase-email' );

	}

	/**
	 * Send notification
	 * @since 2.1.0
	 */
	public function send_notification() {
		global $rtb_controller;

		$url = add_query_arg(
			array(
				'license_key' 	=> urlencode( $this->license_key ),
				'admin_email' 	=> urlencode( $this->purchase_email ),
				'phone_number' 	=> urlencode( $this->phone_number ),
				'message'		=> urlencode( $this->message ),
				'country_code'	=> urlencode( $rtb_controller->settings->get_setting( 'rtb-country-code' ) )
			),
			'http://www.fivestarplugins.com/sms-handling/sms-client.php'
		);

		$opts = array('http'=>array('method'=>"GET"));
		$context = stream_context_create($opts);
		$return = json_decode( file_get_contents( $url, false, $context ) );

		return isset($return->success) ? $return->success : false;
	}
}
} // endif;
