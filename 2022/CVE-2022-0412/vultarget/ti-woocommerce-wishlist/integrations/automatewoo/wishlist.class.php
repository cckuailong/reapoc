<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class TINVWL_AutomateWoo_Wishlist extends AutomateWoo\Wishlist {

	public $id;
	public $owner_id;
	public $items;


	/**
	 * @return int
	 */
	function get_id() {
		return absint( $this->id );
	}

	/**
	 * @return int
	 */
	function get_user_id() {
		return absint( $this->owner_id );
	}

	/**
	 * @return Customer|bool
	 */
	function get_customer() {
		return AutomateWoo\Customer_Factory::get_by_user_id( $this->get_user_id() );
	}

	/**
	 * @return string
	 */
	function get_integration() {
		return 'tinv';
	}

	/**
	 * @return array
	 */
	function get_items() {

		if ( isset( $this->items ) ) {
			return $this->items;
		}

		$this->items = [];

		$products = tinvwl_get_wishlist_products( $this->get_id(), array( 'count' => 9999999 ) );
		if ( $products ) {
			foreach ( $products as $product ) {
				$this->items[] = $product['product_id'];
			}
		}

		$this->items = array_unique( $this->items );

		return $this->items;
	}

	/**
	 * @return string
	 */
	function get_link() {
		$url = tinv_url_wishlist( $this->get_id() );

		return $url;
	}


	/**
	 * @return string
	 */
	protected function get_date_created_option_name() {
		return '_automatewoo_wishlist_date_created_' . $this->get_id();
	}


	/**
	 * @return DateTime|false UTC
	 */
	function get_date_created() {
		$val = get_option( $this->get_date_created_option_name() );
		if ( ! $val ) {
			return false;
		}

		return new DateTime( $val );
	}


	/**
	 * @param DateTime $date UTC
	 */
	function set_date_created( $date ) {
		if ( ! is_a( $date, 'DateTime' ) ) {
			return;
		}
		update_option( $this->get_date_created_option_name(), $date->to_mysql_string(), false );
	}

}
