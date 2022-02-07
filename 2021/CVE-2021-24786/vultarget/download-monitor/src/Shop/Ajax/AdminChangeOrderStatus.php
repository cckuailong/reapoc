<?php

namespace Never5\DownloadMonitor\Shop\Ajax;

use Never5\DownloadMonitor\Shop\Order;
use Never5\DownloadMonitor\Shop\Services\Services;

class AdminChangeOrderStatus extends Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'admin_change_order_status' );
	}

	/**
	 * AJAX callback method
	 *
	 * @return void
	 */
	public function run() {

		// check nonce
		$this->check_nonce();

		if ( ! current_user_can( 'manage_downloads' ) ) {
			$this->response( false, _( 'You are not allowed to do this.', 'download-monitor' ) );
		}

		$order_id   = absint( $_POST['order_id'] );
		$new_status = $_POST['status'];

		/** @var \Never5\DownloadMonitor\Shop\Order\WordPressRepository $order_repo */
		$order_repo = Services::get()->service( 'order_repository' );

		try {
			/** @var \Never5\DownloadMonitor\Shop\Order\Order $order */
			$order = $order_repo->retrieve_single( $order_id );
		} catch ( \Exception $exception ) {
			$this->response( false, $exception->getMessage() );
		}

		// set new status
		$order->set_status( Services::get()->service( 'order_status_factory' )->make( $new_status ) );

		// set modified time to now
		$order->set_date_modified_now();

		// persist new status
		$order_repo->persist( $order );

		$this->response( true );

		// bye
		exit;
	}

	/**
	 * @param bool $s
	 * @param string $e
	 */
	private function response( $s, $e = "" ) {
		wp_send_json( array( 'success' => $s, 'error' => $e ) );
		exit;
	}


}