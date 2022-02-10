<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Status__Refunded
 *
 * @since 4.10
 *
 */


class Tribe__Tickets__Commerce__PayPal__Status__Refunded extends Tribe__Tickets__Status__Abstract {

	public $name          = 'Refunded';
	public $provider_name = 'refunded';
	public $post_type     = 'tribe_tpp_orders';

	public $warning        = true;
	public $count_refunded = true;

	//post status fields for tpp
	public $public                    = true;
	public $exclude_from_search       = false;
	public $show_in_admin_all_list    = true;
	public $show_in_admin_status_list = true;

}