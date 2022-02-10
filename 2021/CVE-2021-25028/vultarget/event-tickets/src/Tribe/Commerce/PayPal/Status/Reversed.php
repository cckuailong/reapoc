<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Status__Reversed
 *
 * @since 5.1.6
 *
 */


class Tribe__Tickets__Commerce__PayPal__Status__Reversed extends Tribe__Tickets__Status__Abstract {

	public $name          = 'Reversed';
	public $provider_name = 'reversed';
	public $post_type     = 'tribe_tpp_orders';

	public $warning        = true;
	public $count_refunded = true;

	//post status fields for tpp
	public $public                    = true;
	public $exclude_from_search       = false;
	public $show_in_admin_all_list    = true;
	public $show_in_admin_status_list = true;

}