<?php
namespace WPO\WC\PDF_Invoices\Legacy;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Legacy\\Legacy_Export' ) ) :

class Legacy_Export {

	public $template_path;
	public $template_default_base_path;

	public $order;
	public $template_type;
	public $order_id;
	public $output_body;
	public $document;

	public function __construct() {
		$this->template_path = WPO_WCPDF()->settings->get_template_path();
		$this->template_default_base_path = WPO_WCPDF()->plugin_path() . '/templates/';
		$this->order = false;
	}
	
	/**
	 * Redirect document function calls directly to document object
	 */
	public function __call( $name, $arguments ) {
		$human_readable_call = '$wpo_wcpdf->export->'.$name.'()';
		WPO_WCPDF_Legacy()->auto_enable_check( $human_readable_call );

		$callback_map = array(
			'wc_price'	=> 'format_price',
		);

		if ( $name == 'get_pdf' && empty( $this->document ) ) {
			$this->document = wcpdf_get_document( $arguments[0], $arguments[1], true );
		}

		if ( array_key_exists( $name, $callback_map ) && is_object( $this->document ) && is_callable( array( $this->document, $callback_map[$name] ) ) ) {
			wcpdf_deprecated_function( $human_readable_call, '2.0', '$this->'.$callback_map[$name].'()' );
			return call_user_func_array( array( $this->document, $callback_map[$name] ), $arguments );		
		} elseif ( is_object( $this->document ) && is_callable( array( $this->document, $name ) ) ) {
			wcpdf_deprecated_function( $human_readable_call, '2.0', '$this->'.$name.'()' );
			return call_user_func_array( array( $this->document, $name ), $arguments );
		} else {
			throw new \Exception("Call to undefined method ".__CLASS__."::{$name}()", 1);
		}
	}

	public function tmp_path( $type = '' ) {
		return WPO_WCPDF()->main->get_tmp_path( $type );
	}

	public function build_filename( $template_type, $order_ids, $context ) {
		if ( empty( $this->document ) ) {
			$this->document = wcpdf_get_document( $template_type, $order_ids, true );
		}

		return $this->document->get_filename();
	}

	public function get_display_number( $order_id ) {
		wcpdf_deprecated_function( '$wpo_wcpdf->export->get_display_number()', '2.0' );
		if ( empty( $this->document ) ) {
			// we don't know what document type we're handling, so we return the order number
			$order = WCX::get_order ( $order_id );
			$order_number = is_callable( array( $order, 'get_order_number' ) ) ? $order->get_order_number() : '';
			return $order_number;
		}

		if ( isset( $this->document->settings['display_number'] ) ) {
			$order_number = (string) $this->document->get_number();
		} else {
			if ( empty( $this->order ) ) {
				$order = WCX::get_order ( $order_ids[0] );
				$order_number = is_callable( array( $order, 'get_order_number' ) ) ? $order->get_order_number() : '';
			} else {
				$order_number = is_callable( array( $this->order, 'get_order_number' ) ) ? $this->order->get_order_number() : '';
			}
		}

		return $order_number;
	}
}

endif; // class_exists

return new Legacy_Export();
