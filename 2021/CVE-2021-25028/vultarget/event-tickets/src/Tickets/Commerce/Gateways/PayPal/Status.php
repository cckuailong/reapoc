<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal;

use TEC\Tickets\Commerce\Status as Commerce_Status;

/**
 * Class Status
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Status {

	/**
	 * Order Status in PayPal for created.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	CONST CREATED = 'CREATED';

	/**
	 * Order Status in PayPal for saved.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	CONST SAVED = 'SAVED';

	/**
	 * Order Status in PayPal for approved.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	CONST APPROVED = 'APPROVED';

	/**
	 * Order Status in PayPal for voided.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	CONST VOIDED = 'VOIDED';

	/**
	 * Order Status in PayPal for completed.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	CONST COMPLETED = 'COMPLETED';

	/**
	 * Order Status in PayPal for payer action required.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	CONST PAYER_ACTION_REQUIRED = 'PAYER_ACTION_REQUIRED';

	/**
	 * Default mapping from PayPal Status to Tickets Commerce
	 *
	 * @since 5.1.9
	 *
	 * @var array
	 */
	protected $default_map = [
		self::CREATED => Commerce_Status\Created::SLUG,
		self::SAVED => Commerce_Status\Pending::SLUG,
		self::APPROVED => Commerce_Status\Approved::SLUG,
		self::VOIDED => Commerce_Status\Voided::SLUG,
		self::COMPLETED => Commerce_Status\Completed::SLUG,
		self::PAYER_ACTION_REQUIRED => Commerce_Status\Action_Required::SLUG,
	];

	/**
	 * Gets the valid mapping of the statuses.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function get_valid_statuses() {
		return $this->default_map;
	}

	/**
	 * Checks if a given PayPal status is valid.
	 *
	 * @since 5.1.9
	 *
	 * @param string $status Status from PayPal.
	 *
	 * @return bool
	 */
	public function is_valid_status( $status ) {
		$statuses = $this->get_valid_statuses();
		return isset( $statuses[ $status ] );
	}

	/**
	 * Converts a valid PayPal status into a commerce status object.
	 *
	 * @since 5.1.9
	 *
	 * @param string $paypal_status A PayPal status string.
	 *
	 * @return false|Commerce_Status\Status_Interface|null
	 */
	public function convert_to_commerce_status( $paypal_status ) {
		if ( ! $this->is_valid_status( $paypal_status ) ) {
			return false;
		}
		$statuses = $this->get_valid_statuses();

		return tribe( Commerce_Status\Status_Handler::class )->get_by_slug( $statuses[ $paypal_status ] );
	}
}