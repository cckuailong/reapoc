<?php


/**
 * Class Tribe__Tickets__Commerce__PayPal__Statuses__Complete
 *
 * @since 4.10
 *
 */
class Tribe__Tickets__Commerce__PayPal__Status__Completed extends Tribe__Tickets__Status__Abstract {

	//This is a payment that has been paid and the product delivered to the customer.
	public $name          = 'Completed';
	public $provider_name = 'completed';
	public $post_type     = 'tribe_tpp_orders';

	public $trigger_option      = true;
	public $attendee_generation = true;
	public $attendee_dispatch   = true;
	public $stock_reduced       = true;
	public $count_attendee      = true;
	public $count_sales         = true;
	public $count_completed     = true;

	//post status fields for tpp
	public $public                    = true;
	public $exclude_from_search       = false;
	public $show_in_admin_all_list    = true;
	public $show_in_admin_status_list = true;


}