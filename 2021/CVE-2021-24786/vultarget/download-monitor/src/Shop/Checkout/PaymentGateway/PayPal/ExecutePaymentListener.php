<?php

namespace Never5\DownloadMonitor\Shop\Checkout\PaymentGateway\PayPal;

use Never5\DownloadMonitor\Dependencies\PayPal;
use Never5\DownloadMonitor\Shop\Services\Services;
use PHPUnit\Runner\Exception;

class ExecutePaymentListener {

	private $gateway;

	/**
	 * ExecutePaymentListener constructor.
	 *
	 * @param PayPalGateway $gateway
	 */
	public function __construct( $gateway ) {
		$this->gateway = $gateway;
	}

	public function run() {
		if ( isset( $_GET['paypal_action'] ) && 'execute_payment' === $_GET['paypal_action'] ) {
			$this->executePayment();
		}
	}

	/**
	 * Execute payment based on GET parameters
	 */
	private function executePayment() {

		/**
		 * Get order
		 */

		$order_id   = ( isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : "" );
		$order_hash = ( isset( $_GET['order_hash'] ) ? $_GET['order_hash'] : "" );

		if ( empty( $order_id ) || empty( $order_hash ) ) {
			$this->execute_failed( $order_id, $order_hash );
		}

		/** @var \Never5\DownloadMonitor\Shop\Order\Repository $order_repo */
		$order_repo = Services::get()->service( 'order_repository' );
		try {
			$order = $order_repo->retrieve_single( $order_id );
		} catch ( \Exception $exception ) {
			/**
			 * @todo log error in PayPal error log ($exception->getMessage())
			 */
			$this->execute_failed( $order_id, $order_hash );

			return;
		}


		/**
		 * Get Payment by paymentId
		 */
		$paymentId = $_GET['paymentId'];
		$payment   = PayPal\Api\Payment::get( $paymentId, $this->gateway->get_api_context() );

		/**
		 * Setup PaymentExecution object
		 */
		$execution = new PayPal\Api\PaymentExecution();
		$execution->setPayerId( $_GET['PayerID'] );


		/**
		 * Execute the payement
		 */
		try {

			/**
			 * Execute the payment
			 */
			$result = $payment->execute( $execution, $this->gateway->get_api_context() );

			// if payment is not approved, exit;
			if ( $result->getState() !== "approved" ) {
				throw new Exception( sprintf( "Execute payment state is %s", $result->getState() ) );
			}

			/**
			 * Update transaction in local database
			 */

			// update the order status to 'completed'
			$transactions = $order->get_transactions();
			foreach ( $transactions as $transaction ) {
				if ( $transaction->get_processor_transaction_id() == $result->getId() ) {
					$transaction->set_status( Services::get()->service( 'order_transaction_factory' )->make_status( 'success' ) );
					$transaction->set_processor_status( $result->getState() );

					try {
						$transaction->set_date_modified( new \DateTimeImmutable( current_time( 'mysql' ) ) );
					} catch ( \Exception $e ) {

					}

					$order->set_transactions( $transactions );
					break;
				}

			}

			// set order as completed, this also persists the order
			$order->set_completed();

			/**
			 * Redirect user to "clean" complete URL
			 */
			wp_redirect( $this->gateway->get_success_url( $order->get_id(), $order->get_hash() ), 302 );
			exit;

		} catch ( \Exception $ex ) {
			/**
			 * @todo add error logging for separate PayPal log
			 */
			$this->execute_failed( $order->get_id(), $order->get_hash() );

			return;
		}

	}

	/**
	 * This method gets called when execute failed. Reason for fail will be logged in PayPal log (if enabled).
	 * User will be redirected to the checkout 'failed' endpoint.
	 *
	 * @param int $order_id
	 * @param string $order_hash
	 */
	private function execute_failed( $order_id, $order_hash ) {
//		echo 'failed';
//		exit();
		wp_redirect( $this->gateway->get_failed_url( $order_id, $order_hash ), 302 );
		exit;
	}


}