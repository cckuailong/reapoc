<?php

class Tribe__Tickets__Commerce__PayPal__Handler__PDT implements Tribe__Tickets__Commerce__PayPal__Handler__Interface {

	/**
	 * Set up hooks for PDT transaction handling
	 *
	 * @since 4.7
	 */
	public function hook() {
		add_action( 'template_redirect', array( $this, 'check_response' ) );
	}

	/**
	 * Checks the request to see if payment data was communicated
	 *
	 * @since 4.7
	 */
	public function check_response() {
		if ( ! isset( $_GET['tx'] ) ) {
			return;
		}

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );
		/** @var Tribe__Tickets__Commerce__PayPal__Gateway $gateway */
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		$results = $this->validate_transaction( $_GET['tx'] );

		if ( false === $results ) {
			return false;
		}

		$gateway->set_transaction_data( $gateway->parse_transaction( $results ) );

		// since the purchase has completed, reset the invoice number
		$gateway->reset_invoice_number();

		// this will redirect to the success page
		$paypal->generate_tickets();
	}

	/**
	 * Validates a PayPal transaction ensuring that it is authentic
	 *
	 * @since 4.7
	 *
	 * @param string $transaction
	 *
	 * @return array|bool
	 */
	public function validate_transaction( $transaction = null ) {
		/**
		 * Allows short-circuiting the validation of a transaction with the PayPal server.
		 *
		 * Returning a non `null` value in  this will prevent any request for validation to
		 * the PayPal server from being sent.
		 *
		 * @since 4.7
		 *
		 * @param bool        $validated
		 * @param string|null $transaction The transaction ID (hash) if available; the transaction data
		 *                                 might be in the $_GET superglobal.
		 */
		$validated = apply_filters( 'tribe_tickets_commerce_paypal_validate_transaction', null, $transaction );

		if ( null !== $validated ) {
			return $validated;
		}

		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		$args = array(
			'httpversion' => '1.1',
			'timeout'     => 60,
			'user-agent'  => 'EventTickets/' . Tribe__Tickets__Main::VERSION,
			'body'        => array(
				'cmd' => '_notify-synch',
				'tx'  => $transaction,
				'at'  => $gateway->identity_token,
			),
		);

		$response = wp_safe_remote_post( $gateway->get_cart_url(), $args );

		if (
			is_wp_error( $response )
			|| ! ( 0 === strpos( $response['body'], 'SUCCESS' ) )
		) {
			return false;
		}

		return $this->parse_transaction_body( $response['body'] );
	}

	/**
	 * Parses flat transaction text
	 *
	 * @since 4.7
	 *
	 * @param string $transaction
	 *
	 * @return array
	 */
	public function parse_transaction_body( $transaction ) {
		$results = array();

		$body    = explode( "\n", $transaction );

		foreach ( $body as $line ) {
			if ( ! trim( $line ) ) {
				continue;
			}

			$line            = explode( '=', $line );
			$var             = array_shift( $line );
			$results[ $var ] = urldecode( implode( '=', $line ) );
		}

		return $results;
	}

	/**
	 * Returns the configuration status of the handler.
	 *
	 * @since 4.7
	 *
	 * @param string $field Which configuration status field to return, either `slug` or `label`
	 * @param string  $slug Optionally return the specified field for the specified status.
	 *
	 * @return bool|string The current, or specified, configuration status slug or label
	 *                     or `false` if the specified field or slug was not found.
	 */
	public function get_config_status( $field = 'slug', $slug = null ) {
		return _x( 'incomplete', 'a PayPal configuration status', 'event-tickets' );
	}
}
