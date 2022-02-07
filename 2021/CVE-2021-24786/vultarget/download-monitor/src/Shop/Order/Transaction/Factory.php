<?php

namespace Never5\DownloadMonitor\Shop\Order\Transaction;

class Factory {

	/**
	 * Make new order transaction
	 *
	 * @return OrderTransaction
	 */
	public function make() {
		$transaction = new OrderTransaction();

		$transaction->set_status( $this->make_status( 'pending' ) );

		try {
			$transaction->set_date_created( new \DateTimeImmutable(current_time( 'mysql' )) );
		} catch ( \Exception $e ) {
		}

		return $transaction;
	}

	/**
	 * Make new order status object for given key
	 *
	 * @param string $key
	 *
	 * @return OrderTransactionStatus
	 */
	public function make_status( $key ) {
		$status = new OrderTransactionStatus( '', '' );

		$status->set_key( $key );

		switch ( $key ) {
			case 'pending':
				$status->set_label( __( 'Pending', 'download-monitor' ) );
				break;
			case 'success':
				$status->set_label( __( 'Success', 'download-monitor' ) );
				break;
			case 'failed':
				$status->set_label( __( 'Failed', 'download-monitor' ) );
				break;
			default:
				$status->set_label( apply_filters( 'dlm_shop_order_transaction_status_label', $key ) );
				break;
		}


		return $status;
	}

}