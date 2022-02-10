<?php

namespace TEC\Tickets\Commerce\Status;

/**
 * Class Voided.
 *
 * Normally when an Order is Voided means the the Authorization for payment failed. Which means this order needs to be
 * ignored and refunded, since it's a status that cannot be reversed into complete or anything else.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Status
 */
class Voided extends Status_Abstract {
	/**
	 * Slug for this Status.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const SLUG = 'voided';

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return __( 'Voided', 'event-tickets' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected $flags = [
		'backfill_purchaser',
		'count_refunded',
		'warning',
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

