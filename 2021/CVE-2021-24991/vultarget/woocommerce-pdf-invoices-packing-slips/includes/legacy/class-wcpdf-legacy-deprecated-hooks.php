<?php
namespace WPO\WC\PDF_Invoices\Legacy;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Legacy\\Deprecated_Hooks' ) ) :

class Deprecated_Hooks {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( has_filter( 'wpo_wcpdf_invoice_number' ) ) {
			add_filter( 'wpo_wcpdf_formatted_document_number', array( $this, 'wpo_wcpdf_invoice_number' ), 10, 4 );
		}
	}

	public function wpo_wcpdf_invoice_number( $formatted_number, $number, $document_type, $order_id ) {
		if ( $document_type == 'invoice' ) {
			// prepare filter arguments
			$invoice_number = $number->get_plain();
			$order = WCX::get_order( $order_id );
			$order_number = $order->get_order_number();
			$order_date = WCX_Order::get_prop( $order, 'date_created' );
			$mysql_order_date = $order_date->date( "Y-m-d H:i:s" );
			// apply filter
			$formatted_number = apply_filters( 'wpo_wcpdf_invoice_number', $invoice_number, $order_number, $order_id, $mysql_order_date );
		}
		return $formatted_number;
	}
}

endif; // class_exists

return new Deprecated_Hooks();