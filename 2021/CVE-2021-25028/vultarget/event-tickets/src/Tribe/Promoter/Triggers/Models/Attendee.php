<?php


namespace Tribe\Tickets\Promoter\Triggers\Models;


use RuntimeException;
use Tribe\Tickets\Promoter\Triggers\Contracts\Attendee_Model;

/**
 * Class Attendee
 *
 * @since 4.12.3
 */
class Attendee implements Attendee_Model {
	/**
	 * @since 4.12.3
	 *
	 * @var array An array with keys, value pairs that represent an attendee.
	 */
	protected $data;

	/**
	 * Attendee constructor.
	 *
	 * @since 4.12.3
	 *
	 * @param array $data An array with keys, value pairs that represent an attendee.
	 */
	public function __construct( $data = [] ) {
		$this->data = $data;
	}

	/**
	 * @inheritDoc
	 */
	public function build() {
		if ( ! is_array( $this->data ) ) {
			throw new RuntimeException( "A valid array must be provided." );
		}
		$this->validate_fields();
	}

	/**
	 * Execute the validation of fields to make sure the values are present for all the required fields to construct
	 * a valid attendee.
	 *
	 * @since 4.12.3
	 * @return void
	 */
	private function validate_fields() {
		foreach ( $this->required_fields() as $field ) {
			if ( empty( $this->data[ $field ] ) ) {
				throw new RuntimeException( "The '{$field}' field, is a required field and is empty." );
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function required_fields() {
		return [
			'attendee_id',
			'holder_email',
			'event_id',
			'product_id',
		];
	}

	/**
	 * @inheritDoc
	 */
	public function id() {
		return $this->data['attendee_id'];
	}

	/**
	 * @inheritDoc
	 */
	public function email() {
		return $this->data['holder_email'];
	}

	/**
	 * @inheritDoc
	 */
	public function event_id() {
		return $this->data['event_id'];
	}

	/**
	 * @inheritDoc
	 */
	public function product_id() {
		return $this->data['product_id'];
	}

	/**
	 * @inheritDoc
	 */
	public function ticket_name() {
		return empty( $this->data['ticket_name'] ) ? '' : $this->data['ticket_name'];
	}
}