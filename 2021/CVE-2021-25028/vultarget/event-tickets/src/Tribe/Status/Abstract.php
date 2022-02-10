<?php


/**
 * Class Tribe__Tickets__Status__Abstract
 *
 * @since 4.10
 *
 */
abstract class Tribe__Tickets__Status__Abstract {

	public $name                = '';
	public $provider_name       = '';
	public $additional_names    = [];
	public $post_type           = '';
	public $incomplete          = false;
	public $warning             = false;
	public $trigger_option      = false;
	public $attendee_generation = false;
	public $attendee_dispatch   = false;
	public $stock_reduced       = false;
	public $count_attendee      = false;
	public $count_sales         = false;
	public $count_completed     = false;
	public $count_canceled      = false;
	public $count_incomplete    = false;
	public $count_refunded      = false;
	public $count_not_going     = false;

	/**
	 * Status  Quantity
	 *
	 * @var int
	 */
	protected $qty        = 0;

	/**
	 * Status Line Total
	 *
	 * @var int
	 */
	protected $line_total = 0;

	/**
	 * Get this Status' Quantity of Tickets by Post Type
	 *
	 * @return int
	 */
	public function get_qty() {
		return $this->qty;
	}

	/**
	 * Add to the  Status' Order Quantity
	 *
	 * @param int $value
	 */
	public function add_qty( $value ) {
		$this->qty += $value;
	}

	/**
	 * Remove from the  Status' Order Quantity
	 *
	 * @param int $value
	 */
	public function remove_qty( $value ) {
		$this->qty -= $value;
	}

	/**
	 * Get  Status' Order Amount of all Orders for a Post Type
	 *
	 * @return int
	 */
	public function get_line_total() {
		return $this->line_total;
	}

	/**
	 * Add to the  Status' Line Total
	 *
	 * @param int $value
	 */
	public function add_line_total( $value ) {
		$this->line_total += $value;
	}

	/**
	 * Remove from the  Status' Line Total
	 *
	 * @param int $value
	 */
	public function remove_line_total( $value ) {
		$this->line_total -= $value;
	}

}