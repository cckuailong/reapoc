<?php
/**
* A class that handles the main custom field functions
 */
if ( ! defined( 'ABSPATH' ) )
	exit; 

if ( !class_exists( 'rtbCustomFields' ) ) {
class rtbCustomFields {

	/**
	 * Option name for storing modified default fields
	 *
	 * @since 0.1
	 */
	public $modified_option_key;

	/**
	 * Common string tacked onto the end of error messages
	 *
	 * @since 0.1
	 */
	public $common_error_msg;

	/**
	 * Initialize the plugin and register hooks
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Option key where information about default fields that have
		// been modified and disabled is stored in the database
		$this->modified_option_key = apply_filters( 'cffrtb_modified_fields_option_key', 'cffrtb_modified_fields' );

		// Common string tacked onto the end of error messages
		$this->common_error_msg = sprintf( _x( 'Please try again. If the problem persists, you may need to refresh the page. If that does not solve the problem, please %scontact support%s for help.', 'A common phrase added to the end of error messages', 'custom-fields-for-rtb' ), '<a href="http://fivestarplugins.com/contact-us/">', '</a>' );

		// Validate user input for custom fields
		add_action( 'rtb_validate_booking_submission', array( $this, 'validate_custom_fields_input' ) );

		// Filter required phone setting when phone field is disabled
		add_filter( 'rtb-setting-require-phone', array( $this, 'never_require_phone' ) );

		// Insert/load custom field input with booking metadata
		add_filter( 'rtb_insert_booking_metadata', array( $this, 'insert_booking_metadata' ), 10, 2 );
		add_action( 'rtb_booking_load_post_data', array( $this, 'load_booking_meta_data' ), 10, 2 );

		// Print custom fields in notification template tags
		add_filter( 'rtb_notification_template_tags', array( $this, 'add_notification_template_tags' ), 10, 2 );
		add_filter( 'rtb_notification_template_tag_descriptions', array( $this, 'add_notification_template_tag_descriptions' ) );

		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		$currentScreen = get_current_screen();
		if ( $currentScreen->id == 'bookings_page_cffrtb-editor' ) {
			wp_enqueue_style( 'rtb-admin-css', RTB_PLUGIN_URL . '/assets/css/admin.css', array(), RTB_VERSION );
			wp_enqueue_script( 'rtb-admin-js', RTB_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), RTB_VERSION, true );
		}
	}

	/**
	 * Validate user input for custom fields
	 *
	 * @since 0.1
	 */
	public function validate_custom_fields_input( $booking ) {

		$fields = rtb_get_custom_fields();

		if ( !count( $fields ) ) {
			return;
		}

		foreach( $fields as $field ) {
			$validation = $field->validate_input( $booking );
		}
	}

	/**
	 * Add custom fields to metadata when a booking is saved
	 *
	 * @since 0.1
	 */
	public function insert_booking_metadata( $meta, $booking ) {

		if ( empty( $booking->custom_fields ) ) {
			return $meta;
		}

		if ( !is_array( $meta ) ) {
			$meta = array();
		}

		if ( !isset( $meta['custom_fields'] ) ) {
			$meta['custom_fields'] = array();
		}

		$meta['custom_fields'] = $booking->custom_fields;

		return $meta;
	}

	/**
	 * Add custom fields to metadata when booking is loaded
	 *
	 * @since 0.1
	 */
	public function load_booking_meta_data( $booking, $post ) {

		$meta = get_post_meta( $booking->ID, 'rtb', true );

		if ( empty( $meta['custom_fields'] ) ) {
			return;
		}

		$booking->custom_fields = $meta['custom_fields'];
	}

	/**
	 * Add custom fields as notification template tags
	 *
	 * @since 0.1
	 */
	public function add_notification_template_tags( $tags, $notification ) {
		global $rtb_controller;

		$fields = rtb_get_custom_fields();

		$cf = isset( $notification->booking->custom_fields ) ? $notification->booking->custom_fields : array();
		$checkbox_icon = apply_filters( 'cffrtb_checkbox_icon_notification', '', $notification );

		foreach( $fields as $field ) {

			if ( $field->type == 'fieldset' ) {
				continue;
			}

			if ( isset( $cf[ $field->slug ] ) ) {
				$display_val = apply_filters( 'cffrtb_display_value_notification', $rtb_controller->fields->get_display_value( $cf[ $field->slug ], $field, $checkbox_icon ), $cf[ $field->slug ], $field, $notification );
			} else {
				$display_val = '';
			}
			$tags[ '{cf-' . esc_attr( $field->slug ) . '}' ] = $display_val;
		}

		return $tags;
	}

	/**
	 * Add custom field notification template tag descriptions
	 *
	 * @since 0.1
	 */
	public function add_notification_template_tag_descriptions( $tags ) {

		$fields = rtb_get_custom_fields();

		foreach( $fields as $field ) {

			if ( $field->type == 'fieldset' ) {
				continue;
			}

			$tags[ '{cf-' . esc_attr( $field->slug ) . '}' ] = esc_html( $field->title );
		}

		return $tags;
	}

	/**
	 * Override the required phone setting when the phone field has been
	 * disabled.
	 *
	 * @param string $value The value of the setting
	 * @since 1.2.3
	 */
	public function never_require_phone( $value ) {
		global $rtb_controller;

		$modified = get_option( $rtb_controller->custom_fields->modified_option_key );

		if ( $modified && isset( $modified['phone'] ) && !empty( $modified['phone']['disabled'] ) ) {
			return '';
		}

		return $value;
	}

}
} // endif;
