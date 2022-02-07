<?php
namespace WPO\WC\PDF_Invoices\Documents;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Documents\\Legacy_Document' ) ) :

/**
 * Packing Slip Document
 * 
 * @class       \WPO\WC\PDF_Invoices\Documents\Legacy_Document
 * @version     2.0
 * @category    Class
 * @author      Ewout Fernhout
 */

class Legacy_Document extends Order_Document_Methods {
	/**
	 * Init/load the order object.
	 *
	 * @param  int|object|WC_Order $order Order to init.
	 */
	public function __construct( $order = 0 ) {
		// set properties
		$this->type		= 'legacy-document';
		$this->title	= __( 'Legacy Document', 'woocommerce-pdf-invoices-packing-slips' );
		$this->icon		= WPO_WCPDF()->plugin_url() . "/assets/images/packing-slip.svg";
	}

	public function set_props( $props, $call_parent_constructor = true ) {
		foreach ($props as $prop => $value) {
			$this->{$prop} = $value;
		}

		if ( $call_parent_constructor ) {
			// Call parent constructor
			$order = ( empty( $props['order'] ) && !empty( $this->order ) ) ? $this->order : $props['order'];
			parent::__construct( $order );
		}
	}

}

endif; // class_exists

return new Legacy_Document();