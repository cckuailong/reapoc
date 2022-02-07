<?php

namespace Never5\DownloadMonitor\Shop\Checkout\PaymentGateway\PayPal;

use Never5\DownloadMonitor\Shop\Checkout\PaymentGateway;
use Never5\DownloadMonitor\Shop\Services\Services;
use Never5\DownloadMonitor\Dependencies\PayPal;

class PayPalGateway extends PaymentGateway\PaymentGateway {

	/** @var bool */
	private $sandbox = false;

	/**
	 * PayPal constructor.
	 */
	public function __construct() {

		$this->set_id( 'paypal' );
		$this->set_title( 'PayPal' );
		$this->set_description( __( 'Pay with PayPal', 'download-monitor' ) );

		parent::__construct();

		$this->set_sandbox( '1' == $this->get_option( 'sandbox_enabled' ) );

	}

	/**
	 * @return bool
	 */
	public function is_sandbox() {
		return $this->sandbox;
	}

	/**
	 * @param bool $sandbox
	 */
	public function set_sandbox( $sandbox ) {
		$this->sandbox = $sandbox;
	}

	/**
	 * Returns API context used in all PayPal API calls
	 *
	 * @return PayPal\Rest\ApiContext
	 */
	public function get_api_context() {

		$client_id     = "";
		$client_secret = "";

		if ( ! $this->is_sandbox() ) {
			// get live keys
			$client_id     = $this->get_option( 'client_id' );
			$client_secret = $this->get_option( 'client_secret' );

		} else {
			// get sandbox keys
			$client_id     = $this->get_option( 'sandbox_client_id' );
			$client_secret = $this->get_option( 'sandbox_client_secret' );
		}

		// remove spaces
		$client_id     = trim( $client_id );
		$client_secret = trim( $client_secret );

		return new PayPal\Rest\ApiContext(
			new PayPal\Auth\OAuthTokenCredential(
				$client_id,
				$client_secret
			)
		);
	}

	/**
	 * Setup PayPal extension
	 */
	public function setup_gateway() {

		// run execute payment listener
		$execute_payment_listener = new ExecutePaymentListener( $this );
		$execute_payment_listener->run();

	}

	/**
	 * Setup gateway settings
	 */
	protected function setup_settings() {

		$application_desc = __( "In order to allow users to pay via PayPal on your website, you need to create an application in PayPal's developer portal. After you've done so, please copy the Client ID and Secret and set them here.", 'download-monitor' );
		$application_desc .= "<br/>";
		$application_desc .= "<a href='https://developer.paypal.com/developer/applications/create' target='_blank'>" . __( "Click here to create a new PayPal application", 'download-monitor' ) . "</a>";
		$application_desc .= " - ";
		$application_desc .= "<a href='https://www.download-monitor.com/kb/payment-gateway-paypal' target='_blank'>" . __( "Click here to read the full documentation page", 'download-monitor' ) . "</a>";

		$sandbox_desc = __( 'The same fields from your PayPal application but from the "sandbox" mode.', 'download-monitor' );
		$sandbox_desc .= " <a href='https://www.download-monitor.com/kb/payment-gateway-paypal' target='_blank'>" . __( "Click here to read more on how to set this up", 'download-monitor' ) . "</a>";

		$this->set_settings( array(
			array(
				'name'  => 'invoice_prefix',
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Invoice Prefix', 'download-monitor' ),
				'desc'  => __( "This prefix is added to the paypal invoice ID. If you run multiple stores with the same PayPal account, enter an unique prefix per store here.", 'download-monitor' )
			),
			array(
				'name'  => '',
				'type'  => 'title',
				'title' => __( 'Application Details', 'download-monitor' )
			),
			array(
				'name' => '',
				'type' => 'desc',
				'text' => $application_desc
			),
			array(
				'name'  => 'client_id',
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Application Client ID', 'download-monitor' ),
				'desc'  => __( 'Your application client ID.', 'download-monitor' )
			),
			array(
				'name'  => 'client_secret',
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Application Client Secret', 'download-monitor' ),
				'desc'  => __( 'Your application client secret.', 'download-monitor' )
			),
			array(
				'name'  => '',
				'type'  => 'title',
				'title' => __( 'Test Settings', 'download-monitor' )
			),
			array(
				'name' => '',
				'type' => 'desc',
				'text' => $sandbox_desc
			),
			array(
				'name'     => 'sandbox_enabled',
				'type'     => 'checkbox',
				'label'    => __( 'Sandbox', 'download-monitor' ),
				'desc'     => __( 'Check to enable PayPal sandbox mode. This allows you to test your PayPal integration.', 'download-monitor' ),
				'cb_label' => __( 'Enable Sandbox', 'download-monitor' ),
				'std'      => 0
			),
			array(
				'name'  => 'sandbox_client_id',
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Sandbox Client ID', 'download-monitor' ),
				'desc'  => __( 'Your application sandbox client ID.', 'download-monitor' )
			),
			array(
				'name'  => 'sandbox_client_secret',
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Sandbox Client Secret', 'download-monitor' ),
				'desc'  => __( 'Your application sandbox client secret.', 'download-monitor' )
			),
		) );
	}

	/**
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return PaymentGateway\Result
	 */
	public function process( $order ) {

		$payer = $this->get_payer( $order );

		$transaction = $this->get_transaction( $order );

		$redirectUrls = new PayPal\Api\RedirectUrls();
		$redirectUrls->setReturnUrl( $this->get_execute_payment_url( $order ) )
		             ->setCancelUrl( $this->get_cancel_url( $order->get_id(), $order->get_hash() ) );

		$payment = new PayPal\Api\Payment();
		$payment->setIntent( 'sale' )
		        ->setPayer( $payer )
		        ->setTransactions( array( $transaction ) )
		        ->setRedirectUrls( $redirectUrls );

		try {
			$payment->create( $this->get_api_context() );
		} catch ( \Exception $ex ) {
			return new PaymentGateway\Result( false, '', 'Could not create payment. Please check your PayPal logs.' );
		}

		// create local transaction
		/** @var \Never5\DownloadMonitor\Shop\Order\Transaction\OrderTransaction $dlm_transaction */
		$dlm_transaction = Services::get()->service( 'order_transaction_factory' )->make();
		$dlm_transaction->set_amount( $order->get_total() );
		$dlm_transaction->set_processor( $this->get_id() );
		$dlm_transaction->set_processor_nice_name( $this->get_title() );
		$dlm_transaction->set_processor_transaction_id( $payment->getId() );
		$dlm_transaction->set_processor_status( $payment->getState() );

		// add transaction to order
		$order->add_transaction( $dlm_transaction );

		// persist order
		try {
			Services::get()->service( 'order_repository' )->persist( $order );
		} catch ( \Exception $exception ) {
			return new PaymentGateway\Result( false, '', 'Error saving order with PayPal transaction.' );
		}

		// get the URL where user can pay
		$approvalUrl = $payment->getApprovalLink();

		return new PaymentGateway\Result( true, $approvalUrl );
	}

	/**
	 * Get the URL for executing a payment
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return string
	 */
	private function get_execute_payment_url( $order ) {
		return add_query_arg( array(
			'order_id'      => $order->get_id(),
			'order_hash'    => $order->get_hash(),
			'paypal_action' => 'execute_payment'
		), Services::get()->service( 'page' )->get_checkout_url( 'complete' ) );
	}

	/**
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return PayPal\Api\Payer
	 */
	private function get_payer( $order ) {
		$oc = $order->get_customer();

		// create address
		$address = new PayPal\Api\Address();

		if ( '' != $oc->get_address_1() ) {
			$address->setLine1( $oc->get_address_1() );
		}


		if ( '' != $oc->get_address_2() ) {
			$address->setLine2( $oc->get_address_2() );
		}

		if ( '' != $oc->get_postcode() ) {
			$address->setPostalCode( $oc->get_postcode() );
		}


		if ( '' != $oc->get_city() ) {
			$address->setCity( $oc->get_city() );
		}


		if ( '' != $oc->get_state() ) {
			$address->setState( $oc->get_state() );
		}

		if ( '' != $oc->get_country() ) {
			$address->setCountryCode( $oc->get_country() );
		}

		if ( '' != $oc->get_phone() ) {
			$address->setPhone( $oc->get_phone() );
		}
		$payer_info = new PayPal\Api\PayerInfo();
		$payer_info->setEmail( $oc->get_email() );
		$payer_info->setFirstName( $oc->get_first_name() );
		$payer_info->setLastName( $oc->get_last_name() );
		$payer_info->setBillingAddress( $address );

		$payer = new PayPal\Api\Payer();
		$payer->setPaymentMethod( 'paypal' );
		$payer->setPayerInfo( $payer_info );

		return $payer;
	}

	/**
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return PayPal\Api\Transaction
	 */
	private function get_transaction( $order ) {

		$currency = Services::get()->service( 'currency' )->get_shop_currency();

		// generate items
		$items = array();
		foreach ( $order->get_items() as $order_item ) {
			$item = new PayPal\Api\Item();
			$item->setName( $order_item->get_label() )
			     ->setCurrency( $currency )
			     ->setQuantity( $order_item->get_qty() )
			     ->setSku( $order_item->get_product_id() )
			     ->setPrice( $this->cents_to_full( $order_item->get_subtotal() ) );
			$items[] = $item;
		}

		// set items in list
		$itemList = new PayPal\Api\ItemList();
		$itemList->setItems( $items );


		// set order details
		$details = new PayPal\Api\Details();
		$details->setTax( 0 )/** @todo add tax support later */
		        ->setSubtotal( $this->cents_to_full( $order->get_subtotal() ) );

		// set amount
		$amount = new PayPal\Api\Amount();
		$amount->setCurrency( $currency )
		       ->setTotal( $this->cents_to_full( $order->get_total() ) )
		       ->setDetails( $details );

		// setup transactions
		$invoiceStr = $order->get_id();
		$invoice_prefix = $this->get_option( 'invoice_prefix' );
		if ( ! empty( $invoice_prefix ) ) {
			$invoiceStr = $this->get_option( 'invoice_prefix' ) . $invoiceStr;
		}

		// setup transaction object
		$transaction = new PayPal\Api\Transaction();
		$transaction->setAmount( $amount )
		            ->setItemList( $itemList )
		            ->setDescription( sprintf( "%s - Order #%d ", get_bloginfo( 'name' ), $order->get_id() ) )
		            ->setInvoiceNumber( $invoiceStr );

		return $transaction;
	}


	/**
	 * @param float $fl_cents
	 *
	 * @return int
	 */
	private function cents_to_full( $fl_cents ) {
		return number_format( ( $fl_cents / 100 ), 2 );
	}
}