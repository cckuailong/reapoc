<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Errors
 *
 * An information repository for errors.
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Errors {

	/**
	 * Casts a numeric error code related to PayPal tickets to a localized string.
	 *
	 * @since 4.7
	 * @since 4.10.9 Use customizable ticket name functions.
	 *
	 * @param string|int $error_code
	 *
	 * @return string
	 */
	public static function error_code_to_message( $error_code = '-1' ) {
		$map = array(
			'-1'  => __( 'There was an error', 'event-tickets' ),
			'1'   => __( 'Attendee email and/or full name is missing', 'event-tickets' ),
			'2'   => esc_html( sprintf( __( 'Trying to oversell a %s but the current oversell policy does not allow it', 'event-tickets' ), tribe_get_ticket_label_singular_lowercase( 'paypal_error_oversell_policy' ) ) ),
			'3'   => esc_html( sprintf( __( '%s quantity is 0', 'event-tickets' ), tribe_get_ticket_label_singular( 'paypal_error_zero_quantity' ) ) ),

			// a numeric namespace reserved for front-end errors
			'101' => esc_html( sprintf( __( 'In order to purchase %s, you must enter your name and a valid email address.', 'event-tickets' ), tribe_get_ticket_label_plural_lowercase( 'paypal_error_missing_name_or_email' ) ) ),
			'102' => esc_html( sprintf( __( 'You can\'t add more %1$s than the total remaining %1$s.', 'event-tickets' ), tribe_get_ticket_label_plural_lowercase( 'paypal_error_added_too_many' ) ) ),
			'103' => esc_html( sprintf( __( 'You should add at least one %s.', 'event-tickets' ), tribe_get_ticket_label_singular_lowercase( 'paypal_error_zero_added' ) ) ),

			// a numeric namespace reserved for front-end messages
			'201' => esc_html( sprintf( __( "Your order is currently processing. Once completed, you'll receive your %s(s) in an email.", 'event-tickets' ), tribe_get_ticket_label_singular_lowercase( 'paypal_error_order_processing' ) ) ),
		);

		/**
		 * Allows filtering the errors map.
		 *
		 * @since 4.7
		 *
		 * @param array      $map        An associative array in the shape [ <error-code> => <error-message> ]
		 * @param int|string $error_code The current error code.
		 */
		$map = apply_filters( 'tribe_tickets_commerce_paypal_errors_map', $map, $error_code );

		return Tribe__Utils__Array::get( $map, $error_code, reset( $map ) );
	}
}
