<?php

use AutomateWoo\Exceptions\InvalidArgument;
use AutomateWoo\Workflow;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class Trigger_Wishlist_Reminder
 */
class TINVWL_Trigger_Wishlist_Reminder extends AutomateWoo\Triggers\AbstractBatchedDailyTrigger
{

	public $supplied_data_items = ['customer', 'wishlist', 'product'];

	const SUPPORTS_QUEUING = false;

	function load_admin_details()
	{
		$this->title = sprintf(__('Wishlist Reminder (TI WooCommerce Wishlist)', 'ti-woocommerce-wishlist'));
		$this->group = __('Wishlists', 'ti-woocommerce-wishlist');
		$this->description = __("Setting the 'Reminder Interval' field to 30 means this trigger will fire every 30 days for any users that have items in their wishlist. This trigger is checked daily. Please note this doesn't work for guests because their wishlist data only exists in their session data.", 'ti-woocommerce-wishlist');
	}


	/**
	 * Add options to the trigger
	 */
	function load_fields()
	{

		$period = new AutomateWoo\Fields\Positive_Number();
		$period->set_name('interval');
		$period->set_title(__('Reminder interval (days)', 'ti-woocommerce-wishlist'));
		$period->set_description(__('E.g. Reminder any customers with items in a Wishlist every 30 days.', 'ti-woocommerce-wishlist'));
		$period->set_required();

		$once_only = new AutomateWoo\Fields\Checkbox();
		$once_only->set_name('once_only');
		$once_only->set_title(__('Once per customer', 'ti-woocommerce-wishlist'));
		$once_only->set_description(__('If checked the trigger will fire only once for each customer for each wishlist they create. Most customers only use the one wishlist so use with caution. Setting a high Reminder interval may be a better plan.', 'ti-woocommerce-wishlist'));

		$this->add_field($period);
		$this->add_field($this->get_field_time_of_day());
		$this->add_field($once_only);
	}


	/**
	 * Get a batch of items to process for given workflow.
	 *
	 * @param Workflow $workflow
	 * @param int $offset The batch query offset.
	 * @param int $limit The max items for the query.
	 *
	 * @return array[] Array of items in array format. Items will be stored in the database so they should be IDs not objects.
	 */
	public function get_batch_for_workflow(Workflow $workflow, int $offset, int $limit): array
	{
		$tasks = [];

		$wl = new TInvWL_Wishlist();
		$wishlists = $wl->get(array(
			'count' => 9999999,
		));

		foreach ($wishlists as $wishlist) {
			$tasks[] = array(
				'wishlist' => $wishlist,
			);
		}

		return $tasks;
	}

	/**
	 * Process a single item for a workflow to process.
	 *
	 * @param Workflow $workflow
	 * @param array $item
	 *
	 * @throws InvalidArgument If wishlist is not set
	 * @throws RuntimeException If the item fails to be processed.
	 */
	public function process_item_for_workflow(Workflow $workflow, array $item)
	{
		if (!isset($item['wishlist'])) {
			throw InvalidArgument::missing_required('wishlist');
		}

		$wishlist = isset($item['wishlist']) && !empty($item['wishlist']['author']) ? $item['wishlist'] : false;

		if (!$wishlist) {
			throw new RuntimeException('Wishlist not found.');
		}

		if (empty($item['wishlist']['author'])) {
			throw new RuntimeException('Wishlist customer not found.');
		}

		$normalized_wishlist = new TINVWL_AutomateWoo_Wishlist();
		$normalized_wishlist->id = $item['wishlist']['ID'];
		$normalized_wishlist->owner_id = $item['wishlist']['author'];
		$normalized_wishlist->date = DateTime::createFromFormat("Y-m-d H:i:s", $item['wishlist']['date']);
		$normalized_wishlist->get_items();

		$workflow->maybe_run(
			[
				'customer' => AutomateWoo\Customer_Factory::get_by_user_id($item['wishlist']['author']),
				'wishlist' => $normalized_wishlist,
			]
		);
	}


	/**
	 * @param $workflow Workflow
	 *
	 * @return bool
	 */
	function validate_workflow($workflow)
	{
		$customer = $workflow->data_layer()->get_customer();

		if (!$customer) {
			return false;
		}

		if (!$this->validate_wishlist_date_created($workflow)) {
			return false;
		}

		// Only do this once for each user for each workflow and each wishlist
		if ($workflow->get_trigger_option('once_only')) {
			if ($workflow->has_run_for_data_item('wishlist')) {
				return false;
			}
		}

		$interval = absint($workflow->get_trigger_option('interval'));

		if (!$interval) {
			return false;
		}

		if ($workflow->has_run_for_data_item('wishlist', $interval * DAY_IN_SECONDS)) {
			return false;
		}

		return true;
	}


	/**
	 * Check that the wishlist was created at least 1 interval ago by using the date created property.
	 *
	 * The date created property was added in v3.7 so we must assume that wishlists might not have this set.
	 *
	 * @param Workflow $workflow
	 *
	 * @return bool
	 */
	protected function validate_wishlist_date_created($workflow)
	{
		$wishlist = $workflow->data_layer()->get_wishlist();
		$interval = absint($workflow->get_trigger_option('interval'));

		if (!$interval) {
			return false;
		}

		$date_created = $wishlist->date;

		$min_interval_date = new DateTime();
		$min_interval_date->modify("-$interval days");

		return $date_created->getTimestamp() < $min_interval_date->getTimestamp();
	}
}
