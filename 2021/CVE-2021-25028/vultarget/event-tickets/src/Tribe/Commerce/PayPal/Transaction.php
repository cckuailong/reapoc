<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Transaction
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Transaction {

	/**
	 * @var string
	 */
	public static $option = 'tribe_commerce_paypal_transaction';

	/**
	 * @var string
	 */
	public static $undefined_status = 'undefined';

	/**
	 * @var string
	 */
	public static $unregistered_status = 'unregistered';

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var string
	 */
	protected $transaction_id;

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * Tribe__Tickets__Commerce__PayPal__Transaction constructor.
	 *
	 * @since 4.7
	 *
	 * @param string $transaction_id
	 */
	public function __construct( $transaction_id ) {
		$this->transaction_id = $transaction_id;
		$this->status         = self::$undefined_status;
	}

	/**
	 * Builds a new instance from data stored in the database or from default data.
	 *
	 * @since 4.7
	 *
	 * @param string $transaction_id
	 *
	 * @return \Tribe__Tickets__Commerce__PayPal__Transaction
	 */
	public static function build_from_id( $transaction_id ) {
		$option = get_option( self::$option . '-' . $transaction_id );

		if ( empty( $option ) ) {
			return new self( $transaction_id );
		}

		$instance = new  self( $transaction_id );
		$instance->set_status( Tribe__Utils__Array::get( $option, 'status', self::$undefined_status ) );

		$data = array_diff_key( $option, array( 'id' => 'id', 'status' => 'status' ) );

		foreach ( $data as $key => $value ) {
			$instance->set_data( $key, $value );
		}

		return $instance;
	}

	/**
	 * Sets the status of the transaction.
	 *
	 * @since 4.7
	 *
	 * @param $status
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}

	/**
	 * Gets the status of the transaction.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Sets a data entry on the transaction.
	 *
	 * @since 4.7
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set_data( $key, $value ) {
		$this->data[ $key ] = $value;
	}

	/**
	 * Returns a data value set on the transaction or a default value.
	 *
	 * @since 4.7
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_data( $key, $default = null ) {
		return Tribe__Utils__Array::get( $this->data, $key, $default );
	}

	/**
	 * Saves the transaction to database.
	 *
	 * @since 4.7
	 */
	public function save() {
		$existing = get_option( $this->get_option_name() );

		if ( empty( $existing ) ) {
			$existing = array();
		}

		$updated = array_merge( $existing, $this->to_array() );

		update_option( $this->get_option_name(), $updated );
	}

	/**
	 * Returns the array representation of the transaction.
	 *
	 * @since 4.7
	 *
	 * @return array
	 */
	public function to_array() {
		return array_merge( array(
			'id'     => $this->transaction_id,
			'status' => $this->status,
		), $this->data );
	}

	/**
	 * Returns the name of the option used to store the transaction data.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	protected function get_option_name() {
		return self::$option . '-' . $this->transaction_id;
	}

	/**
	 * Returns the transaction ID.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->transaction_id;
	}
}
