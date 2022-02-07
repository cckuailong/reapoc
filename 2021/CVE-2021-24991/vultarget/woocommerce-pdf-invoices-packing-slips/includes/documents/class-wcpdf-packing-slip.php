<?php
namespace WPO\WC\PDF_Invoices\Documents;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Documents\\Packing_Slip' ) ) :

/**
 * Packing Slip Document
 * 
 * @class       \WPO\WC\PDF_Invoices\Documents\Packing_Slip
 * @version     2.0
 * @category    Class
 * @author      Ewout Fernhout
 */

class Packing_Slip extends Order_Document_Methods {
	/**
	 * Init/load the order object.
	 *
	 * @param  int|object|WC_Order $order Order to init.
	 */
	public function __construct( $order = 0 ) {
		// set properties
		$this->type		= 'packing-slip';
		$this->title	= __( 'Packing Slip', 'woocommerce-pdf-invoices-packing-slips' );
		$this->icon		= WPO_WCPDF()->plugin_url() . "/assets/images/packing-slip.svg";

		// Call parent constructor
		parent::__construct( $order );
	}

	public function get_title() {
		// override/not using $this->title to allow for language switching!
		return apply_filters( "wpo_wcpdf_{$this->slug}_title", __( 'Packing Slip', 'woocommerce-pdf-invoices-packing-slips' ), $this );
	}

	public function get_filename( $context = 'download', $args = array() ) {
		$order_count = isset($args['order_ids']) ? count($args['order_ids']) : 1;

		$name = _n( 'packing-slip', 'packing-slips', $order_count, 'woocommerce-pdf-invoices-packing-slips' );

		if ( $order_count == 1 ) {
			if ( isset( $this->settings['display_number'] ) ) {
				$suffix = (string) $this->get_number();
			} else {
				if ( empty( $this->order ) && isset( $args['order_ids'] ) ) {
					$order = WCX::get_order ( $args['order_ids'][0] );
					$suffix = is_callable( array( $order, 'get_order_number' ) ) ? $order->get_order_number() : '';
				} else {
					$suffix = is_callable( array( $this->order, 'get_order_number' ) ) ? $this->order->get_order_number() : '';
				}
			}
		} else {
			$suffix = date('Y-m-d'); // 2020-11-11
		}

		$filename = $name . '-' . $suffix . '.pdf';

		// Filter filename
		$order_ids = isset($args['order_ids']) ? $args['order_ids'] : array( $this->order_id );
		$filename = apply_filters( 'wpo_wcpdf_filename', $filename, $this->get_type(), $order_ids, $context );

		// sanitize filename (after filters to prevent human errors)!
		return sanitize_file_name( $filename );
	}

	public function init_settings() {
		// Register settings.
		$page = $option_group = $option_name = 'wpo_wcpdf_documents_settings_packing-slip';

		$settings_fields = array(
			array(
				'type'			=> 'section',
				'id'			=> 'packing_slip',
				'title'			=> '',
				'callback'		=> 'section',
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'enabled',
				'title'			=> __( 'Enable', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'packing_slip',
				'args'			=> array(
					'option_name'		=> $option_name,
					'id'				=> 'enabled',
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'display_billing_address',
				'title'			=> __( 'Display billing address', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'select',
				'section'		=> 'packing_slip',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'display_billing_address',
					'options' 		=> array(
						''				=> __( 'No' , 'woocommerce-pdf-invoices-packing-slips' ),
						'when_different'=> __( 'Only when different from shipping address' , 'woocommerce-pdf-invoices-packing-slips' ),
						'always'		=> __( 'Always' , 'woocommerce-pdf-invoices-packing-slips' ),
					),
					// 'description'	=> __( 'Display billing address (in addition to the default shipping address) if different from shipping address', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'display_email',
				'title'			=> __( 'Display email address', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'packing_slip',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'display_email',
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'display_phone',
				'title'			=> __( 'Display phone number', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'packing_slip',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'display_phone',
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'display_customer_notes',
				'title'			=> __( 'Display customer notes', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'packing_slip',
				'args'			=> array(
					'option_name'		=> $option_name,
					'id'				=> 'display_customer_notes',
					'store_unchecked'	=> true,
					'default'			=> 1,
				)
			),
		);


		// allow plugins to alter settings fields
		$settings_fields = apply_filters( 'wpo_wcpdf_settings_fields_documents_packing_slip', $settings_fields, $page, $option_group, $option_name );
		WPO_WCPDF()->settings->add_settings_fields( $settings_fields, $page, $option_group, $option_name );
		return;

	}

	/**
	 * Document number title
	 */
	public function get_number_title() {
		$number_title = __( 'Packing Slip Number:', 'woocommerce-pdf-invoices-packing-slips' );
		return apply_filters( "wpo_wcpdf_{$this->slug}_number_title", $number_title, $this );
	}

	/**
	 * Document date title
	 */
	public function get_date_title() {
		$date_title = __( 'Packing Slip Date:', 'woocommerce-pdf-invoices-packing-slips' );
		return apply_filters( "wpo_wcpdf_{$this->slug}_date_title", $date_title, $this );
	}

}

endif; // class_exists

return new Packing_Slip();