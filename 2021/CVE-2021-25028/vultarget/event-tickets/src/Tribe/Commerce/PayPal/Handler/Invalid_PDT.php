<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Handler__Invalid_PDT
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Handler__Invalid_PDT implements Tribe__Tickets__Commerce__PayPal__Handler__Interface {

	/**
	 * @var string
	 */
	protected $transaction;

	public function __construct( $transaction ) {
		$this->transaction = $transaction;
	}

	/**
	 * Checks the request to see if payment data was communicated
	 *
	 * @since 4.7
	 */
	public function check_response() {
		//noop
	}

	/**
	 * Validates a PayPal transaction ensuring that it is authentic
	 *
	 * @since 4.7
	 *
	 * @param $transaction
	 *
	 * @return array|bool
	 */
	public function validate_transaction( $transaction = null ) {
		// no-op

		return false;
	}

	/**
	 * Saves the invalid transaction data to the database.
	 *
	 * @since 4.7
	 */
	public function save_transaction() {
		// save the transaction for future reconciliation
		$transaction_object = new Tribe__Tickets__Commerce__PayPal__Transaction( $this->transaction );
		$transaction_object->set_status( Tribe__Tickets__Commerce__PayPal__Transaction::$unregistered_status );
		$transaction_object->set_data( 'handler', 'PDT' );

		foreach ( array( 'amt', 'cc', 'cm', 'st' ) as $key ) {
			$value = Tribe__Utils__Array::get( $_GET, $key, '' );
			$transaction_object->set_data( $key, $value );
		}

		$transaction_object->save();
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
