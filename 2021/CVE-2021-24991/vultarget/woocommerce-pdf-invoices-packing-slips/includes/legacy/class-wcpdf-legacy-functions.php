<?php
namespace WPO\WC\PDF_Invoices\Legacy;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Legacy\\Legacy_Functions' ) ) :

class Legacy_Functions {

	/**
	 * Get template name from slug
	 */
	public function get_template_name ( $template_type ) {
		switch ( $template_type ) {
			case 'invoice':
				$template_name = apply_filters( 'wpo_wcpdf_invoice_title', __( 'Invoice', 'woocommerce-pdf-invoices-packing-slips' ) );
				break;
			case 'packing-slip':
				$template_name = apply_filters( 'wpo_wcpdf_packing_slip_title', __( 'Packing Slip', 'woocommerce-pdf-invoices-packing-slips' ) );
				break;
			default:
				// try to 'unslug' the name
				$template_name = ucwords( str_replace( array( '_', '-' ), ' ', $template_type ) );
				break;
		}

		return apply_filters( 'wpo_wcpdf_template_name', $template_name, $template_type );
	}

	/**
	 * Redirect document function calls directly to document object
	 */
	public function __call( $name, $arguments ) {
		$human_readable_call = '$wpo_wcpdf->functions->'.$name.'()';
		WPO_WCPDF_Legacy()->auto_enable_check( $human_readable_call );

		if ( is_object( WPO_WCPDF_Legacy()->export->document ) && is_callable( array( WPO_WCPDF_Legacy()->export->document, $name ) ) ) {
			return call_user_func_array( array( WPO_WCPDF_Legacy()->export->document, $name ), $arguments );
		} else {
			throw new \Exception("Call to undefined method ".__CLASS__."::{$name}()", 1);
		}
	}

}

endif; // class_exists

return new Legacy_Functions();