<?php

namespace TEC\Tickets\Commerce\Status;

/**
 * Class Denied.
 *
 * Used for handling Orders where the payment process failed, whether it be a credit card rejection or some other error.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Status
 */
class Denied extends Status_Abstract {
	/**
	 * Slug for this Status.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const SLUG = 'denied';

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return __( 'Denied', 'event-tickets' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected $flags = [
		'incomplete',
		'warning',
		'backfill_purchaser',
		'count_canceled',
	];

	/**
	 * {@inheritdoc}
	 */
	protected $wp_arguments = [
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
	];
}