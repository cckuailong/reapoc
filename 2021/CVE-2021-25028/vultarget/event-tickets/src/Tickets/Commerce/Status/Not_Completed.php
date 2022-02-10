<?php

namespace TEC\Tickets\Commerce\Status;

/**
 * Class Denied.
 *
 * Used for handling Orders where Pending payment but never completed it, becoming Abandoned after a week..
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Status
 */
class Not_Completed extends Status_Abstract {
	/**
	 * Slug for this Status.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const SLUG = 'not-completed';

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return __( 'Not Completed', 'event-tickets' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected $flags = [
		'incomplete',
		'warning',
		'backfill_purchaser',
		'increase_stock',
		'archive_attendees',
		'decrease_sales',
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