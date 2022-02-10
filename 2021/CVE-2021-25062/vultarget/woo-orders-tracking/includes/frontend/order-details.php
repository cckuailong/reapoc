<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_FRONTEND_ORDER_DETAILS {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WOO_ORDERS_TRACKING_DATA();
		add_action( 'woocommerce_order_item_meta_end', array( $this, 'woocommerce_order_item_meta_end' ), 10, 4 );
	}

	/**
	 * @param $item_id
	 * @param $item
	 * @param $order WC_Order
	 * @param $plain_text
	 *
	 * @throws Exception
	 */
	public function woocommerce_order_item_meta_end( $item_id, $item, $order, $plain_text ) {
		if ( is_account_page() ) {
			VI_WOO_ORDERS_TRACKING_ADMIN_WOO_ORDER_EMAIL::include_tracking_info_after_order_item( $item_id, $order, $plain_text );
		}
	}
}