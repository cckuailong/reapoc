<?php


/**
 * Class Tribe__Tickets__Commerce__PayPal__Status__Denied
 *
 * @since 4.10
 *
 */
class Tribe__Tickets__Commerce__PayPal__Status__Denied extends Tribe__Tickets__Status__Abstract {

	//This is a payment where the payment process failed, whether it be a credit card rejection or some other error.
	public $name          = 'Denied';
	public $provider_name = 'denied';
	public $post_type     = 'tribe_tpp_orders';

	public $incomplete     = true;
	public $warning        = true;
	public $count_canceled = true;

	//post status fields for tpp
	public $public                    = true;
	public $exclude_from_search       = false;
	public $show_in_admin_all_list    = true;
	public $show_in_admin_status_list = true;

}