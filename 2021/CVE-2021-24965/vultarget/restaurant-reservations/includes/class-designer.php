<?php defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'etfrtbDesigner' ) ) {
/**
 * Class to deliver data to the templates
 *
 * @since 0.1
 */
class etfrtbDesigner {

	/**
	 * The email type
	 *
	 * @param string $email_type Email type, eg - `rejected-user`
	 * @since 0.1
	 */
	public $email_type;

	/**
	 * The associated notification email
	 *
	 * This may be null if we're previewing a template.
	 *
	 * @param rtbNotification $notification
	 * @since 0.1
	 */
	public $notification;

	/**
	 * Available templates for this notification
	 *
	 * @param array $template_options
	 * @since 0.1
	 */
	public $template_options;

	/**
	 * Data to be fetched in the templates
	 *
	 * @param array $_data Key/value store
	 * @since 0.1
	 */
	private $_data = array();

	/**
	 * Set up the designer object with a specific email type. Most email types
	 * also expect an associated notification. But may not always have one if
	 * the email is being previewed.
	 *
	 * @param string $email_type Type of email being handled
	 * @param rtbNotification $notification
	 * @since 0.1
	 */
	public function setup( $email_type, $notification = null ) {

		global $rtb_controller;

		if ( ! $rtb_controller->permissions->check_permission( 'templates' ) ) { return; }

		$this->set( 'color_primary', get_option( 'etfrtb_color_primary', '#66BB7F' ) );
		$this->set( 'color_primary_text', get_option( 'etfrtb_color_primary_text', '#FFFFFF' ) );
		$this->set( 'color_button', get_option( 'etfrtb_color_button', '#66BB7F' ) );
		$this->set( 'color_button_text', get_option( 'etfrtb_color_button_text', '#FFFFFF' ) );
		$this->set( 'acknowledgement', get_option( 'etfrtb_acknowledgement', __( 'This message was sent by {site_link} on {current_time}. You are receiving this email because we received a booking request from this email address.', 'email-templates-for-rtb' ) ) );

		$this->set_logo( 'logo', get_option( 'etfrtb_logo', false ) );

		if ( is_a( $notification, 'rtbNotification' ) ) {
			$this->set_notification( $notification );
		}

		$this->set_email_type( $email_type );

		do_action( 'etfrtb_designer_setup', $this );
	}

	/**
	 * Get data for this email
	 *
	 * @param string $key Key of data to retrieve
	 * @since 0.1
	 */
	public function get( $key ) {

		if ( isset( $this->_data[$key] ) ) {
			return $this->_data[$key];
		}

		switch ( $key ) {

			case 'notification' :
				return null;

			case 'default' :
				return '';
		}
	}

	/**
	 * Set data for this email
	 *
	 * @param string $key
	 * @param mixed $value
	 * @since 0.1
	 */
	public function set( $key, $value ) {
		$this->_data[$key] = $value;
	}

	/**
	 * Set the logo and get height and width values
	 *
	 * @param string $url Optional url. If not provided, it will fetch the set
	 *  option.
	 * @since 0.1
	 */
	public function set_logo( $url = '' ) {

		$logo = get_option( 'etfrtb_logo', false );

		if ( !$logo ) {
			return;
		}

		$logo = wp_get_attachment_image_src( $logo, 'etfrtb_logo' );

		if ( !$logo ) {
			return;
		}

		// Ensure that the image is not wider or taller than 200px, and adjust
		// the width/height values if necessary, so that exact pixel sizes can
		// be put into the templates
		$width = $logo[1];
		$height = $logo[2];
		if ( $width > 200 || $height > 200 ) {
			$shrink = $width > $height ? 200 / $width : 200 / $height;
			$width = round( $width * $shrink );
			$height = round( $height * $shrink );
		}

		$this->set( 'logo', $logo[0] );
		$this->set( 'logo_width', $width );
		$this->set( 'logo_height', $height );
	}

	/**
	 * Set the email type and fetch associated data
	 *
	 * @param rtbBooking $booking
	 * @since 0.1
	 */
	public function set_email_type( $email_type ) {

		global $rtb_controller;

		$this->email_type = $email_type;

		switch( $this->email_type ) {

			case 'booking-admin' :
				$this->set( 'template', get_option( 'etfrtb_booking_admin_template', 'conversations.php' ) );
				$this->set( 'lead', get_option( 'etfrtb_booking_admin_headline', $rtb_controller->settings->get_setting( 'subject-booking-admin' ) ) );
				$this->set( 'footer_message', get_option( 'etfrtb_booking_admin_footer_message', '' ) );
				break;

			case 'booking-user' :
				$this->set( 'template', get_option( 'etfrtb_booking_user_template', 'conversations.php' ) );
				$this->set( 'lead', get_option( 'etfrtb_booking_user_headline', $rtb_controller->settings->get_setting( 'subject-booking-user' ) ) );
				$this->set( 'footer_message', get_option( 'etfrtb_booking_user_footer_message', '' ) );
				break;

			case 'confirmed-user' :
				$this->set( 'template', get_option( 'etfrtb_confirmed_user_template', 'conversations.php' ) );
				$this->set( 'lead', get_option( 'etfrtb_confirmed_user_headline', $rtb_controller->settings->get_setting( 'subject-confirmed-user' ) ) );
				$this->set( 'footer_message', get_option( 'etfrtb_confirmed_user_footer_message', '' ) );
				break;

			case 'rejected-user' :
				$this->set( 'template', get_option( 'etfrtb_rejected_user_template', 'conversations.php' ) );
				$this->set( 'lead', get_option( 'etfrtb_rejected_user_headline', $rtb_controller->settings->get_setting( 'subject-rejected-user' ) ) );
				$this->set( 'book_again', get_option( 'etfrtb_rejected_user_book_again', __( 'Book Another Time', 'email-templates-for-rtb' ) ) );
				$this->set( 'footer_message', get_option( 'etfrtb_rejected_user_footer_message', '' ) );
				break;

			case 'admin-notice' :
				$this->set( 'template', get_option( 'etfrtb_admin_notice_template', 'conversations.php' ) );
				$this->set( 'lead', get_option( 'etfrtb_admin_notice_headline', $rtb_controller->settings->get_setting( 'subject-admin-notice' ) ) );
				$this->set( 'footer_message', get_option( 'etfrtb_admin_notice_footer_message', '' ) );
				break;

			case 'reminder-user' :
				$this->set( 'template', get_option( 'etfrtb_reminder_user_template', 'conversations.php' ) );
				$this->set( 'lead', get_option( 'etfrtb_reminder_user_headline', $rtb_controller->settings->get_setting( 'subject-reminder-user' ) ) );
				$this->set( 'footer_message', get_option( 'etfrtb_reminder_user_footer_message', '' ) );
				break;

			case 'late-user' :
				$this->set( 'template', get_option( 'etfrtb_late_user_template', 'conversations.php' ) );
				$this->set( 'lead', get_option( 'etfrtb_late_user_headline', $rtb_controller->settings->get_setting( 'subject-late-user' ) ) );
				$this->set( 'footer_message', get_option( 'etfrtb_late_user_footer_message', '' ) );
				break;
		}

		// Set up default notification templates when no actual notification
		// is being sent. This is used for previewing templates in the
		// customizer.
		if ( !is_a( $this->notification, 'rtbNotificationEmail' ) ) {

			global $rtb_controller;

			switch( $this->email_type ) {

				case 'booking-admin' :
					$this->set( 'subject', $rtb_controller->settings->get_setting( 'subject-booking-admin' ) );
					$this->set( 'content', wpautop( $rtb_controller->settings->get_setting( 'template-booking-admin' ) ) );
					break;

				case 'booking-user' :
					$this->set( 'subject', $rtb_controller->settings->get_setting( 'subject-booking-user' ) );
					$this->set( 'content', wpautop( $rtb_controller->settings->get_setting( 'template-booking-user' ) ) );
					break;

				case 'confirmed-user' :
					$this->set( 'subject', $rtb_controller->settings->get_setting( 'subject-confirmed-user' ) );
					$this->set( 'content', wpautop( $rtb_controller->settings->get_setting( 'template-confirmed-user' ) ) );
					break;

				case 'rejected-user' :
					$this->set( 'subject', $rtb_controller->settings->get_setting( 'subject-rejected-user' ) );
					$this->set( 'content', wpautop( $rtb_controller->settings->get_setting( 'template-rejected-user' ) ) );
					break;

				case 'admin-notice' :
					$this->set( 'subject', $rtb_controller->settings->get_setting( 'subject-admin-notice' ) );
					$this->set( 'content', __( "This is an example of an Admin Update email. You can send a message to a customer from the list of bookings in your admin panel." ) );
					break;

				case 'reminder-user' :
					$this->set( 'subject', $rtb_controller->settings->get_setting( 'subject-reminder-user' ) );
					$this->set( 'content', wpautop( $rtb_controller->settings->get_setting( 'template-reminder-user' ) ) );
					break;

				case 'late-user' :
					$this->set( 'subject', $rtb_controller->settings->get_setting( 'subject-late-user' ) );
					$this->set( 'content', wpautop( $rtb_controller->settings->get_setting( 'template-late-user' ) ) );
					break;
			}
		}
	}

	/**
	 * Set the notification data for this email
	 *
	 * @param rtbNotification $notification
	 * @since 0.1
	 */
	public function set_notification( $notification ) {
		$this->notification = $notification;
		$this->set( 'subject', $notification->subject );
		$this->set( 'content', $notification->message );
	}

	/**
	 * Render the final email content
	 *
	 * Loads the appropriate template and returns the output.
	 *
	 * @since 0.1
	 */
	public function render() {

		// Return content with no template if none is selected
		if ( !$this->get( 'template' ) ) {
			return $this->get( 'content' );
		}

		$template_dirs = apply_filters(
			'etfrtb_template_directories',
			array(
				get_stylesheet_directory() . '/etfrtb_templates/',
				get_template_directory() . '/etfrtb_templates/',
				RTB_PLUGIN_DIR . '/templates/',
			)
		);

		$file = '';
		foreach( $template_dirs as $dir ) {
			if ( file_exists( $dir . $this->get( 'template' ) ) ) {
				$file = $dir . $this->get( 'template' );
				break;
			}
		}

		// Return content with no template if no matching file is found
		if ( empty( $file ) ) {
			return $this->get( 'content' );
		}

		ob_start();
		include $file;
		$output = ob_get_clean();

		// Process any template tags in the final output
		if ( is_a( $this->notification, 'rtbNotificationEmail' ) ) {
			$output = $this->notification->process_template( $output );
		}

		return $output;
	}
}
} // end if