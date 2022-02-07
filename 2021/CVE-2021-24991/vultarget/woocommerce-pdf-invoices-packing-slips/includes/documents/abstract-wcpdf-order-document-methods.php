<?php
namespace WPO\WC\PDF_Invoices\Documents;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Documents\\Order_Document_Methods' ) ) :

/**
 * Abstract Order Methods
 *
 * Collection of methods to be used on orders within a Document
 * Created as abstract rather than traits to support PHP versions older than 5.4
 *
 * @class       \WPO\WC\PDF_Invoices\Documents\Order_Document_Methods
 * @version     2.0
 * @category    Class
 * @author      Ewout Fernhout
 */

abstract class Order_Document_Methods extends Order_Document {
	public function is_refund( $order ) {
		if ( is_callable( array( $order, 'get_type' ) ) ) { // WC 3.0+
			$is_refund = $order->get_type() == 'shop_order_refund';
		} else {
			$is_refund = get_post_type( WCX_Order::get_id( $order ) ) == 'shop_order_refund';
		}

		return $is_refund;
	}

	public function get_refund_parent_id( $order ) {
		if ( is_callable( array( $order, 'get_parent_id' ) ) ) { // WC3.0+
			$parent_order_id = $order->get_parent_id();
		} else {
			$parent_order_id = wp_get_post_parent_id( WCX_Order::get_id( $order ) );
		}

		return $parent_order_id;
	}


	public function get_refund_parent( $order ) {
		// only try if this is actually a refund
		if ( ! $this->is_refund( $order ) ) {
			return $order;
		}

		$parent_order_id = $this->get_refund_parent_id( $order );
		$order = WCX::get_order( $parent_order_id );
		return $order;
	}

	/**
	 * Check if billing address and shipping address are equal
	 */
	public function ships_to_different_address() {
		// always prefer parent address for refunds
		if ( $this->is_refund( $this->order ) ) {
			$order = $this->get_refund_parent( $this->order );
		} else {
			$order = $this->order;
		}

		// only check if there is a shipping address at all
		if ( $formatted_shipping_address = $order->get_formatted_shipping_address() ) {
			$address_comparison_fields = apply_filters( 'wpo_wcpdf_address_comparison_fields', array(
				'first_name',
				'last_name',
				'company',
				'address_1',
				'address_2',
				'city',
				'state',
				'postcode',
				'country'
			), $this );
			
			foreach ($address_comparison_fields as $address_field) {
				$billing_field = WCX_Order::get_prop( $order, "billing_{$address_field}", 'view');
				$shipping_field = WCX_Order::get_prop( $order, "shipping_{$address_field}", 'view');
				if ( $shipping_field != $billing_field ) {
					// this address field is different -> ships to different address!
					return true;
				}
			}			
		}

		//if we got here, it means the addresses are equal -> doesn't ship to different address!
		return apply_filters( 'wpo_wcpdf_ships_to_different_address', false, $order, $this );
	}
	
	/**
	 * Return/Show billing address
	 */
	public function get_billing_address() {
		// always prefer parent billing address for refunds
		if ( $this->is_refund( $this->order ) ) {
			// temporarily switch order to make all filters / order calls work correctly
			$refund = $this->order;
			$this->order = $this->get_refund_parent( $this->order );
			$address = apply_filters( 'wpo_wcpdf_billing_address', $this->order->get_formatted_billing_address(), $this );
			// switch back & unset
			$this->order = $refund;
			unset($refund);
		} elseif ( $address = $this->order->get_formatted_billing_address() ) {
			// regular shop_order
			$address = apply_filters( 'wpo_wcpdf_billing_address', $address, $this );
		} else {
			// no address
			$address = apply_filters( 'wpo_wcpdf_billing_address', __('N/A', 'woocommerce-pdf-invoices-packing-slips' ), $this );
		}

		return $address;
	}
	public function billing_address() {
		echo $this->get_billing_address();
	}

	/**
	 * Check whether the billing address should be shown
	 */
	public function show_billing_address() {
		if( $this->get_type() != 'packing-slip' ) {
			return true;
		} else {
			return ! empty( $this->settings['display_billing_address'] ) && ( $this->ships_to_different_address() || $this->settings['display_billing_address'] == 'always' );
		}
	}

	/**
	 * Return/Show billing email
	 */
	public function get_billing_email() {
		$billing_email = WCX_Order::get_prop( $this->order, 'billing_email', 'view' );

		if ( !$billing_email && $this->is_refund( $this->order ) ) {
			// try parent
			$parent_order = $this->get_refund_parent( $this->order );
			$billing_email = WCX_Order::get_prop( $parent_order, 'billing_email', 'view' );
		}

		return apply_filters( 'wpo_wcpdf_billing_email', $billing_email, $this );
	}
	public function billing_email() {
		echo $this->get_billing_email();
	}
	
	/**
	 * Return/Show phone by type
	 */
	public function get_phone( $phone_type = 'billing' ) {
		$phone_type = "{$phone_type}_phone";
		$phone      = WCX_Order::get_prop( $this->order, $phone_type, 'view' );

		// on refund orders
		if ( ! $phone && $this->is_refund( $this->order ) ) {
			// try parent
			$parent_order = $this->get_refund_parent( $this->order );
			$phone        = WCX_Order::get_prop( $parent_order, $phone_type, 'view' );
		}

		return $phone;
	}

	public function get_billing_phone() {
		$phone = $this->get_phone( 'billing' );

		return apply_filters( "wpo_wcpdf_billing_phone", $phone, $this );
	}

	public function get_shipping_phone( $fallback_to_billing = false ) {
		$phone = $this->get_phone( 'shipping' );

		if( $fallback_to_billing && empty( $phone ) ) {
			$phone = $this->get_billing_phone();
		}

		return apply_filters( "wpo_wcpdf_shipping_phone", $phone, $this );
	}

	public function billing_phone() {
		echo $this->get_billing_phone();
	}

	public function shipping_phone( $fallback_to_billing = false ) {
		echo $this->get_shipping_phone( $fallback_to_billing );
	}
	
	/**
	 * Return/Show shipping address
	 */
	public function get_shipping_address() {
		// always prefer parent shipping address for refunds
		if ( $this->is_refund( $this->order ) ) {
			// temporarily switch order to make all filters / order calls work correctly
			$refund = $this->order;
			$this->order = $this->get_refund_parent( $this->order );
			$address = apply_filters( 'wpo_wcpdf_shipping_address', $this->order->get_formatted_shipping_address(), $this );
			// switch back & unset
			$this->order = $refund;
			unset($refund);
		} elseif ( $address = $this->order->get_formatted_shipping_address() ) {
			// regular shop_order
			$address = apply_filters( 'wpo_wcpdf_shipping_address', $address, $this );
		} else {
			// no address
			// use fallback for packing slip
			if ( apply_filters( 'wpo_wcpdf_shipping_address_fallback', ( $this->get_type() == 'packing-slip' ), $this ) ) {
				$address = $this->get_billing_address();
			} else{
				$address = apply_filters( 'wpo_wcpdf_shipping_address', __('N/A', 'woocommerce-pdf-invoices-packing-slips' ), $this );

			}
		}

		return $address;
	}
	public function shipping_address() {
		echo $this->get_shipping_address();
	}

	/**
	 * Check whether the shipping address should be shown
	 */
	public function show_shipping_address() {
		if( $this->get_type() != 'packing-slip' ) {
			return ! empty( $this->settings['display_shipping_address'] ) && ( $this->ships_to_different_address() || $this->settings['display_shipping_address'] == 'always' );
		} else {
			return true;
		}
	}

	/**
	 * Return/Show a custom field
	 */		
	public function get_custom_field( $field_name ) {
		if ( !$this->is_order_prop( $field_name ) ) {
			$custom_field = WCX_Order::get_meta( $this->order, $field_name, true );
		}
		// if not found, try prefixed with underscore (not when ACF is active!)
		if ( empty( $custom_field ) && substr( $field_name, 0, 1 ) !== '_' && !$this->is_order_prop( "_{$field_name}" ) && !class_exists('ACF') ) {
			$custom_field = WCX_Order::get_meta( $this->order, "_{$field_name}", true );
		}

		// WC3.0 fallback to properties
		$property = str_replace('-', '_', sanitize_title( ltrim($field_name, '_') ) );
		if ( empty( $custom_field ) && is_callable( array( $this->order, "get_{$property}" ) ) ) {
			$custom_field = $this->order->{"get_{$property}"}( 'view' );
		}

		// fallback to parent for refunds
		if ( empty( $custom_field ) && $this->is_refund( $this->order ) ) {
			$parent_order = $this->get_refund_parent( $this->order );
			if ( !$this->is_order_prop( $field_name ) ) {
				$custom_field = WCX_Order::get_meta( $parent_order, $field_name, true );
			}

			// WC3.0 fallback to properties
			if ( empty( $custom_field ) && is_callable( array( $parent_order, "get_{$property}" ) ) ) {
				$custom_field = $parent_order->{"get_{$property}"}( 'view' );
			}
		}

		return apply_filters( 'wpo_wcpdf_billing_custom_field', $custom_field, $this );
	}
	public function custom_field( $field_name, $field_label = '', $display_empty = false ) {
		$custom_field = $this->get_custom_field( $field_name );
		if (!empty($field_label)){
			// add a a trailing space to the label
			$field_label .= ' ';
		}

		if (!empty($custom_field) || $display_empty) {
			echo $field_label . nl2br ($custom_field);
		}
	}

	public function is_order_prop( $key ) {
		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '<' ) ) {
			return false; // WC 2.X didn't have CRUD
		}
		// Taken from WC class
		$order_props = array(
			// Abstract order props
			'parent_id',
			'status',
			'currency',
			'version',
			'prices_include_tax',
			'date_created',
			'date_modified',
			'discount_total',
			'discount_tax',
			'shipping_total',
			'shipping_tax',
			'cart_tax',
			'total',
			'total_tax',
			// Order props
			'customer_id',
			'order_key',
			'billing_first_name',
			'billing_last_name',
			'billing_company',
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_country',
			'billing_email',
			'billing_phone',
			'shipping_first_name',
			'shipping_last_name',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_state',
			'shipping_postcode',
			'shipping_country',
			'payment_method',
			'payment_method_title',
			'transaction_id',
			'customer_ip_address',
			'customer_user_agent',
			'created_via',
			'customer_note',
			'date_completed',
			'date_paid',
			'cart_hash',
		);
		return in_array($key, $order_props);
	}

	/**
	 * Return/show product attribute
	 */
	public function get_product_attribute( $attribute_name, $product ) {
		// first, check the text attributes
		$attributes = $product->get_attributes();
		$attribute_key = @wc_attribute_taxonomy_name( $attribute_name );
		if (array_key_exists( sanitize_title( $attribute_name ), $attributes) ) {
			$attribute = $product->get_attribute ( $attribute_name );
		} elseif (array_key_exists( sanitize_title( $attribute_key ), $attributes) ) {
			$attribute = $product->get_attribute ( $attribute_key );
		}

		if (empty($attribute)) {
			// not a text attribute, try attribute taxonomy
			$attribute_key = @wc_attribute_taxonomy_name( $attribute_name );
			$product_id = WCX_Product::get_prop($product, 'id');
			$product_terms = @wc_get_product_terms( $product_id, $attribute_key, array( 'fields' => 'names' ) );
			// check if not empty, then display
			if ( !empty($product_terms) ) {
				$attribute = array_shift( $product_terms );
			}
		}

		// WC3.0+ fallback parent product for variations
		if ( empty($attribute) && version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) && $product->is_type( 'variation' ) ) {
			$product = wc_get_product( $product->get_parent_id() );
			$attribute = $this->get_product_attribute( $attribute_name, $product );
		}

		return isset($attribute) ? $attribute : false;
	}
	public function product_attribute( $attribute_name, $product ) {
		echo $this->get_product_attribute( $attribute_name, $product );
	}

	/**
	 * Return/Show order notes
	 * could use $order->get_customer_order_notes(), but that filters out private notes already
	 */		
	public function get_order_notes( $filter = 'customer', $include_system_notes = true ) {
		if ( $this->is_refund( $this->order ) ) {
			$post_id = $this->get_refund_parent_id( $this->order );
		} else {
			$post_id = $this->order_id;
		}

		if ( empty( $post_id ) ) {
			return; // prevent order notes from all orders showing when document is not loaded properly
		}

		if ( function_exists('wc_get_order_notes') ) { // WC3.2+
			$type = ( $filter == 'private' ) ? 'internal' : $filter;
			$notes = wc_get_order_notes( array(
				'order_id' => $post_id,
				'type'     => $type, // use 'internal' for admin and system notes, empty for all
			) );

			if ( $include_system_notes === false ) {
				foreach ($notes as $key => $note) {
					if ( $note->added_by == 'system' ) {
						unset($notes[$key]);
					}
				}
			}

			return $notes;
		} else {

			$args = array(
				'post_id' 	=> $post_id,
				'approve' 	=> 'approve',
				'type' 		=> 'order_note'
			);

			remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

			$notes = get_comments( $args );

			add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

			if ( $notes ) {
				foreach( $notes as $key => $note ) {
					if ( $filter == 'customer' && !get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ) {
						unset($notes[$key]);
					}
					if ( $filter == 'private' && get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ) {
						unset($notes[$key]);
					}					
				}
				return $notes;
			}
		}

	}
	public function order_notes( $filter = 'customer', $include_system_notes = true ) {
		$notes = $this->get_order_notes( $filter, $include_system_notes );
		if ( $notes ) {
			foreach( $notes as $note ) {
				$css_class   = array( 'note', 'note_content' );
				$css_class[] = $note->customer_note ? 'customer-note' : '';
				$css_class[] = 'system' === $note->added_by ? 'system-note' : '';
				$css_class   = apply_filters( 'woocommerce_order_note_class', array_filter( $css_class ), $note );
				$content = isset($note->content) ? $note->content : $note->comment_content; 
				?>
				<div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
					<?php echo wpautop( wptexturize( wp_kses_post( $content ) ) ); ?>
				</div>
				<?php
			}
		}
	}

	/**
	 * Return/Show the current date
	 */
	public function get_current_date() {
		return apply_filters( 'wpo_wcpdf_date', date_i18n( wcpdf_date_format( $this, 'current_date' ) ) );
	}
	public function current_date() {
		echo $this->get_current_date();
	}

	/**
	 * Return/Show payment method  
	 */
	public function get_payment_method() {
		$payment_method_label = __( 'Payment method', 'woocommerce-pdf-invoices-packing-slips' );

		if ( $this->is_refund( $this->order ) ) {
			$parent_order = $this->get_refund_parent( $this->order );
			$payment_method_title = WCX_Order::get_prop( $parent_order, 'payment_method_title', 'view' );
		} else {
			$payment_method_title = WCX_Order::get_prop( $this->order, 'payment_method_title', 'view' );
		}

		$payment_method = __( $payment_method_title, 'woocommerce' );

		return apply_filters( 'wpo_wcpdf_payment_method', $payment_method, $this );
	}
	public function payment_method() {
		echo $this->get_payment_method();
	}

	/**
	 * Return/Show shipping method  
	 */
	public function get_shipping_method() {
		$shipping_method_label = __( 'Shipping method', 'woocommerce-pdf-invoices-packing-slips' );
		$shipping_method = __( $this->order->get_shipping_method(), 'woocommerce' );
		return apply_filters( 'wpo_wcpdf_shipping_method', $shipping_method, $this );
	}
	public function shipping_method() {
		echo $this->get_shipping_method();
	}

	/**
	 * Return/Show order number
	 */
	public function get_order_number() {
		// try parent first
		if ( $this->is_refund( $this->order ) ) {
			$parent_order = $this->get_refund_parent( $this->order );
			$order_number = $parent_order->get_order_number();
		} else {
			$order_number = $this->order->get_order_number();
		}

		// Trim the hash to have a clean number but still 
		// support any filters that were applied before.
		$order_number = ltrim($order_number, '#');
		return apply_filters( 'wpo_wcpdf_order_number', $order_number, $this );
	}
	public function order_number() {
		echo $this->get_order_number();
	}

	/**
	 * Return/Show the order date
	 */
	public function get_order_date() {
		if ( $this->is_refund( $this->order ) ) {
			$parent_order = $this->get_refund_parent( $this->order );
			$order_date = WCX_Order::get_prop( $parent_order, 'date_created' );
		} else {
			$order_date = WCX_Order::get_prop( $this->order, 'date_created' );
		}

		$date = $order_date->date_i18n( wcpdf_date_format( $this, 'order_date' ) );
		$mysql_date = $order_date->date( "Y-m-d H:i:s" );
		return apply_filters( 'wpo_wcpdf_order_date', $date, $mysql_date, $this );
	}
	public function order_date() {
		echo $this->get_order_date();
	}

	/**
	 * Return the order items
	 */
	public function get_order_items() {
		$items = $this->order->get_items();
		$data_list = array();
	
		if( sizeof( $items ) > 0 ) {
			foreach ( $items as $item_id => $item ) {
				// Array with data for the pdf template
				$data = array();

				// Set the item_id
				$data['item_id'] = $item_id;
				
				// Set the id
				$data['product_id'] = $item['product_id'];
				$data['variation_id'] = $item['variation_id'];

				// Compatibility: WooCommerce Composit Products uses a workaround for
				// setting the order before the item name filter, so we run this first
				if ( class_exists('WC_Composite_Products') ) {
					$order_item_class = apply_filters( 'woocommerce_order_item_class', '', $item, $this->order );
				}
				
				// Set item name
				$data['name'] = apply_filters( 'woocommerce_order_item_name', $item['name'], $item, false );
				
				// Set item quantity
				$data['quantity'] = $item['qty'];

				// Set the line total (=after discount)
				$data['line_total'] = $this->format_price( $item['line_total'] );
				$data['single_line_total'] = $this->format_price( $item['line_total'] / max( 1, abs( $item['qty'] ) ) );
				$data['line_tax'] = $this->format_price( $item['line_tax'] );
				$data['single_line_tax'] = $this->format_price( $item['line_tax'] / max( 1, abs( $item['qty'] ) ) );
				
				$data['tax_rates'] = $this->get_tax_rate( $item, $this->order, false );
				$data['calculated_tax_rates'] = $this->get_tax_rate( $item, $this->order, true );
				
				// Set the line subtotal (=before discount)
				$data['line_subtotal'] = $this->format_price( $item['line_subtotal'] );
				$data['line_subtotal_tax'] = $this->format_price( $item['line_subtotal_tax'] );
				$data['ex_price'] = $this->get_formatted_item_price( $item, 'total', 'excl' );
				$data['price'] = $this->get_formatted_item_price( $item, 'total' );
				$data['order_price'] = $this->order->get_formatted_line_subtotal( $item ); // formatted according to WC settings

				// Calculate the single price with the same rules as the formatted line subtotal (!)
				// = before discount
				$data['ex_single_price'] = $this->get_formatted_item_price( $item, 'single', 'excl' );
				$data['single_price'] = $this->get_formatted_item_price( $item, 'single' );

				// Pass complete item array
				$data['item'] = $item;
				
				// Get the product to add more info
				if ( is_callable( array( $item, 'get_product' ) ) ) { // WC4.4+
					$product = $item->get_product();
				} elseif ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '4.4', '<' ) ) {
					$product = $this->order->get_product_from_item( $item );
				} else {
					$product = null;
				}
				
				// Checking fo existance, thanks to MDesigner0 
				if( !empty( $product ) ) {
					// Thumbnail (full img tag)
					$data['thumbnail'] = $this->get_thumbnail( $product );

					// Set item SKU
					$data['sku'] = is_callable( array( $product, 'get_sku' ) ) ? $product->get_sku() : '';
	
					// Set item weight
					$data['weight'] = is_callable( array( $product, 'get_weight' ) ) ? $product->get_weight() : '';
					
					// Set item dimensions
					$data['dimensions'] = $product instanceof \WC_Product ? WCX_Product::get_dimensions( $product ) : '';
				
					// Pass complete product object
					$data['product'] = $product;
				
				} else {
					$data['product'] = null;
				}
				
				// Set item meta
				if (function_exists('wc_display_item_meta')) { // WC3.0+
					$data['meta'] = wc_display_item_meta( $item, array(
						'echo'      => false,
					) );
				} else {
					if ( version_compare( WOOCOMMERCE_VERSION, '2.4', '<' ) ) {
						$meta = new \WC_Order_Item_Meta( $item['item_meta'], $product );
					} else { // pass complete item for WC2.4+
						$meta = new \WC_Order_Item_Meta( $item, $product );
					}
					$data['meta'] = $meta->display( false, true );
				}

				$data_list[$item_id] = apply_filters( 'wpo_wcpdf_order_item_data', $data, $this->order, $this->get_type() );
			}
		}

		return apply_filters( 'wpo_wcpdf_order_items_data', $data_list, $this->order, $this->get_type() );
	}

	/**
	 * Get the tax rates/percentages for an item
	 * @param  object $item order item
	 * @param  object $order WC_Order
	 * @param  bool $force_calculation force calculation of rates rather than retrieving from db
	 * @return string $tax_rates imploded list of tax rates
	 */
	public function get_tax_rate( $item, $order, $force_calculation = false ) {
		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$tax_data_container = ( $item['type'] == 'line_item' ) ? 'line_tax_data' : 'taxes';
			$tax_data_key = ( $item['type'] == 'line_item' ) ? 'subtotal' : 'total';
			$line_total_key = ( $item['type'] == 'line_item' ) ? 'line_total' : 'total';
			$line_tax_key = ( $item['type'] == 'shipping' ) ? 'total_tax' : 'line_tax';

			$tax_class = isset($item['tax_class']) ? $item['tax_class'] : '';
			$line_tax = $item[$line_tax_key];
			$line_total = $item[$line_total_key];
			$line_tax_data = $item[$tax_data_container];
		} else {
			$tax_data_key = ( $item['type'] == 'line_item' ) ? 'subtotal' : 'total';
			$tax_class = $item['tax_class'];
			$line_total = $item['line_total'];
			$line_tax = $item['line_tax'];
			$line_tax_data = maybe_unserialize( isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '' );
		}

		// first try the easy wc2.2+ way, using line_tax_data
		if ( !empty( $line_tax_data ) && isset($line_tax_data[$tax_data_key]) ) {
			$tax_rates = array();

			$line_taxes = $line_tax_data[$tax_data_key];
			foreach ( $line_taxes as $tax_id => $tax ) {
				if ( isset($tax) && $tax !== '' ) {
					$tax_rate = $this->get_tax_rate_by_id( $tax_id, $order );
					if ( $tax_rate !== false && $force_calculation === false ) {
						$tax_rates[] = $tax_rate . ' %';
					} else {
						$tax_rates[] = $this->calculate_tax_rate( $line_total, $line_tax );
					}
				}
			}

			// apply decimal setting
			if (function_exists('wc_get_price_decimal_separator')) {
				foreach ($tax_rates as &$tax_rate) {
					$tax_rate = str_replace('.', wc_get_price_decimal_separator(), strval($tax_rate) );
				}
			}

			$tax_rates = implode(', ', $tax_rates );
			return $tax_rates;
		}

		if ( $line_tax == 0 ) {
			return '-'; // no need to determine tax rate...
		}

		if ( version_compare( WOOCOMMERCE_VERSION, '2.1' ) >= 0 && !apply_filters( 'wpo_wcpdf_calculate_tax_rate', false ) ) {
			// WC 2.1 or newer is used
			$tax = new \WC_Tax();
			$taxes = $tax->get_rates( $tax_class );

			$tax_rates = array();

			foreach ($taxes as $tax) {
				$tax_rates[$tax['label']] = round( $tax['rate'], 2 ).' %';
			}

			if (empty($tax_rates)) {
				// one last try: manually calculate
				$tax_rates[] = $this->calculate_tax_rate( $line_total, $line_tax );
			}

			$tax_rates = implode(' ,', $tax_rates );
		} else {
			// Backwards compatibility/fallback: calculate tax from line items
			$tax_rates[] = $this->calculate_tax_rate( $line_total, $line_tax );
		}
		
		return $tax_rates;
	}

	public function calculate_tax_rate( $price_ex_tax, $tax ) {
		$precision = apply_filters( 'wpo_wcpdf_calculate_tax_rate_precision', 1 );
		if ( $price_ex_tax != 0) {
			$tax_rate = round( ($tax / $price_ex_tax)*100, $precision ).' %';
		} else {
			$tax_rate = '-';
		}
		return $tax_rate;
	}

	/**
	 * Returns the percentage rate (float) for a given tax rate ID.
	 * @param  int    $rate_id  woocommerce tax rate id
	 * @return float  $rate     percentage rate
	 */
	public function get_tax_rate_by_id( $rate_id, $order = null ) {
		global $wpdb;
		// WC 3.7+ stores rate in tax items!
		if ( $order_rates = $this->get_tax_rates_from_order( $order ) ) {
			if ( isset( $order_rates[ $rate_id ] ) ) {
				return (float) $order_rates[ $rate_id ];
			}
		}

		$rate = $wpdb->get_var( $wpdb->prepare( "SELECT tax_rate FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %d;", $rate_id ) );
		if ($rate === NULL) {
			return false;
		} else {
			return (float) $rate;
		}
	}

	public function get_tax_rates_from_order( $order ) {
		if ( !empty( $order ) && is_callable( array( $order, 'get_version' ) ) && version_compare( $order->get_version(), '3.7', '>=' ) && version_compare( WC_VERSION, '3.7', '>=' ) ) {
			$tax_rates = array();
			$tax_items = $order->get_items( array('tax') );

			if ( empty( $tax_items ) ) {
				return $tax_rates;
			}

			foreach( $tax_items as $tax_item_key => $tax_item ) {
				if ( is_callable( array( $order, 'get_created_via' ) ) && $order->get_created_via() == 'subscription' ) {
					// subscription renewals didn't properly record the rate_percent property between WC3.7 and WCS3.0.1
					// so we use a fallback if the rate_percent = 0 and the amount != 0
					$rate_percent = $tax_item->get_rate_percent();
					$tax_amount = $tax_item->get_tax_total() + $tax_item->get_shipping_tax_total();
					if ( $tax_amount > 0 && $rate_percent > 0 ) {
						$tax_rates[ $tax_item->get_rate_id() ] = $rate_percent;
					} else {
						continue; // not setting the rate will let the plugin fall back to the rate from the settings
					}
				} else {
					$tax_rates[ $tax_item->get_rate_id() ] = $tax_item->get_rate_percent();
				}

			}
			return $tax_rates;
		} else {
			return false;
		}
	}

	/**
	 * Returns a an array with rate_id => tax rate data (array) of all tax rates in woocommerce
	 * @return array  $tax_rate_ids  keyed by id
	 */
	public function get_tax_rate_ids() {
		global $wpdb;
		$rates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates" );

		$tax_rate_ids = array();
		foreach ($rates as $rate) {
			$rate_id = $rate->tax_rate_id;
			unset($rate->tax_rate_id);
			$tax_rate_ids[$rate_id] = (array) $rate;
		}

		return $tax_rate_ids;
	}

	/**
	 * Returns the main product image ID
	 * Adapted from the WC_Product class
	 * (does not support thumbnail sizes)
	 *
	 * @access public
	 * @return string
	 */
	public function get_thumbnail_id ( $product ) {
		$product_id = WCX_Product::get_id( $product );

		if ( has_post_thumbnail( $product_id ) ) {
			$thumbnail_id = get_post_thumbnail_id ( $product_id );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $product_id ) ) && has_post_thumbnail( $parent_id ) ) {
			$thumbnail_id = get_post_thumbnail_id ( $parent_id );
		} else {
			$thumbnail_id = false;
		}

		return $thumbnail_id;
	}

	/**
	 * Returns the thumbnail image tag
	 * 
	 * uses the internal WooCommerce/WP functions and extracts the image url or path
	 * rather than the thumbnail ID, to simplify the code and make it possible to
	 * filter for different thumbnail sizes
	 *
	 * @access public
	 * @return string
	 */
	public function get_thumbnail ( $product ) {
		// Get default WooCommerce img tag (url/http)
		if ( version_compare( WOOCOMMERCE_VERSION, '3.3', '>=' ) ) {
			$thumbnail_size = 'woocommerce_thumbnail';
		} else {
			$thumbnail_size = 'shop_thumbnail';
		}
		$size = apply_filters( 'wpo_wcpdf_thumbnail_size', $thumbnail_size );
		$thumbnail_img_tag_url = $product->get_image( $size, array( 'title' => '' ) );
		
		// Extract the url from img
		preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $thumbnail_img_tag_url, $thumbnail_url );
		$thumbnail_url = array_pop($thumbnail_url);
		// remove http/https from image tag url to avoid mixed origin conflicts
		$contextless_thumbnail_url = ltrim( str_replace(array('http://','https://'), '', $thumbnail_url ), '/' );

		// convert url to path
		if ( defined('WP_CONTENT_DIR') && strpos( WP_CONTENT_DIR, ABSPATH ) !== false ) {
			$forwardslash_basepath = str_replace('\\','/', ABSPATH);
			$contextless_site_url = str_replace(array('http://','https://'), '', trailingslashit(get_site_url()));
		} else {
			// bedrock e.a
			$forwardslash_basepath = str_replace('\\','/', WP_CONTENT_DIR);
			$contextless_site_url = str_replace(array('http://','https://'), '', trailingslashit(WP_CONTENT_URL));
		}
		$thumbnail_path = str_replace( $contextless_site_url, trailingslashit( $forwardslash_basepath ), $contextless_thumbnail_url);
		
		// fallback if thumbnail file doesn't exist
		if (apply_filters('wpo_wcpdf_use_path', true) && !file_exists($thumbnail_path)) {
			if ($thumbnail_id = $this->get_thumbnail_id( $product ) ) {
				$thumbnail_path = get_attached_file( $thumbnail_id );
			}
		}

		// Thumbnail (full img tag)
		if ( apply_filters('wpo_wcpdf_use_path', true) && file_exists($thumbnail_path) ) {
			// load img with server path by default
			$thumbnail = sprintf('<img width="90" height="90" src="%s" class="attachment-shop_thumbnail wp-post-image">', $thumbnail_path );
		} elseif ( apply_filters('wpo_wcpdf_use_path', true) && !file_exists($thumbnail_path) ) {
			// should use paths but file not found, replace // with http(s):// for dompdf compatibility
			if ( substr( $thumbnail_url, 0, 2 ) === "//" ) {
				$prefix = is_ssl() ? 'https://' : 'http://';
				$https_thumbnail_url = $prefix . ltrim( $thumbnail_url, '/' );
				$thumbnail_img_tag_url = str_replace($thumbnail_url, $https_thumbnail_url, $thumbnail_img_tag_url);
			}
			$thumbnail = $thumbnail_img_tag_url;
		} else {
			// load img with http url when filtered
			$thumbnail = $thumbnail_img_tag_url;
		}

		// die($thumbnail);
		return $thumbnail;
	}

	/**
	 * Return the order totals listing
	 */
	public function get_woocommerce_totals() {
		// get totals and remove the semicolon
		$totals = apply_filters( 'wpo_wcpdf_raw_order_totals', $this->order->get_order_item_totals(), $this->order );
		
		// remove the colon for every label
		foreach ( $totals as $key => $total ) {
			$label = $total['label'];
			$colon = strrpos( $label, ':' );
			if( $colon !== false ) {
				$label = substr_replace( $label, '', $colon, 1 );
			}		
			$totals[$key]['label'] = $label;
		}

		// WC2.4 fix order_total for refunded orders
		// not if this is the actual refund!
		if ( ! $this->is_refund( $this->order ) && apply_filters( 'wpo_wcpdf_remove_refund_totals', true, $this ) ) {
			$total_refunded = is_callable( array( $this->order, 'get_total_refunded' ) ) ? $this->order->get_total_refunded() : 0;
			if ( version_compare( WOOCOMMERCE_VERSION, '2.4', '>=' ) && isset($totals['order_total']) && $total_refunded ) {
				if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
					$tax_display = get_option( 'woocommerce_tax_display_cart' );
				} else {
					$tax_display = WCX_Order::get_prop( $this->order, 'tax_display_cart' );
				}

				$totals['order_total']['value'] = wc_price( $this->order->get_total(), array( 'currency' => WCX_Order::get_prop( $this->order, 'currency' ) ) );
				$order_total    = $this->order->get_total();
				$tax_string     = '';

				// Tax for inclusive prices
				if ( wc_tax_enabled() && 'incl' == $tax_display ) {
					$tax_string_array = array();
					if ( 'itemized' == get_option( 'woocommerce_tax_total_display' ) ) {
						foreach ( $this->order->get_tax_totals() as $code => $tax ) {
							$tax_amount         = $tax->formatted_amount;
							$tax_string_array[] = sprintf( '%s %s', $tax_amount, $tax->label );
						}
					} else {
						$tax_string_array[] = sprintf( '%s %s', wc_price( $this->order->get_total_tax(), array( 'currency' => WCX_Order::get_prop( $this->order, 'currency' ) ) ), WC()->countries->tax_or_vat() );
					}
					if ( ! empty( $tax_string_array ) ) {
						if ( version_compare( WOOCOMMERCE_VERSION, '2.6', '>=' ) ) {
							$tax_string = ' ' . sprintf( __( '(includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) );
						} else {
							// use old capitalized string
							$tax_string = ' ' . sprintf( __( '(Includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) );
						}
					}
				}

				$totals['order_total']['value'] .= $tax_string;
			}

			// remove refund lines (shouldn't be in invoice)
			foreach ( $totals as $key => $total ) {
				if ( strpos($key, 'refund_') !== false ) {
					unset( $totals[$key] );
				}
			}

		}

		return apply_filters( 'wpo_wcpdf_woocommerce_totals', $totals, $this->order, $this->get_type() );
	}
	
	/**
	 * Return/show the order subtotal
	 */
	public function get_order_subtotal( $tax = 'excl', $discount = 'incl' ) { // set $tax to 'incl' to include tax, same for $discount
		//$compound = ($discount == 'incl')?true:false;
		$subtotal = $this->order->get_subtotal_to_display( false, $tax );

		$subtotal = ($pos = strpos($subtotal, ' <small')) ? substr($subtotal, 0, $pos) : $subtotal; //removing the 'excluding tax' text			
		
		$subtotal = array (
			'label'	=> __('Subtotal', 'woocommerce-pdf-invoices-packing-slips' ),
			'value'	=> $subtotal, 
		);
		
		return apply_filters( 'wpo_wcpdf_order_subtotal', $subtotal, $tax, $discount, $this );
	}
	public function order_subtotal( $tax = 'excl', $discount = 'incl' ) {
		$subtotal = $this->get_order_subtotal( $tax, $discount );
		echo $subtotal['value'];
	}

	/**
	 * Return/show the order shipping costs
	 */
	public function get_order_shipping( $tax = 'excl' ) { // set $tax to 'incl' to include tax
		$shipping_cost = WCX_Order::get_prop( $this->order, 'shipping_total', 'view' );
		$shipping_tax = WCX_Order::get_prop( $this->order, 'shipping_tax', 'view' );

		if ($tax == 'excl' ) {
			$formatted_shipping_cost = $this->format_price( $shipping_cost );
		} else {
			$formatted_shipping_cost = $this->format_price( $shipping_cost + $shipping_tax );
		}

		$shipping = array (
			'label'	=> __('Shipping', 'woocommerce-pdf-invoices-packing-slips' ),
			'value'	=> $formatted_shipping_cost,
			'tax'	=> $this->format_price( $shipping_tax ),
		);
		return apply_filters( 'wpo_wcpdf_order_shipping', $shipping, $tax, $this );
	}
	public function order_shipping( $tax = 'excl' ) {
		$shipping = $this->get_order_shipping( $tax );
		echo $shipping['value'];
	}

	/**
	 * Return/show the total discount
	 */
	public function get_order_discount( $type = 'total', $tax = 'incl' ) {
		if ( $tax == 'incl' ) {
			switch ($type) {
				case 'cart':
					// Cart Discount - pre-tax discounts. (deprecated in WC2.3)
					$discount_value = $this->order->get_cart_discount();
					break;
				case 'order':
					// Order Discount - post-tax discounts. (deprecated in WC2.3)
					$discount_value = $this->order->get_order_discount();
					break;
				case 'total':
					// Total Discount
					if ( version_compare( WOOCOMMERCE_VERSION, '2.3' ) >= 0 ) {
						$discount_value = $this->order->get_total_discount( false ); // $ex_tax = false
					} else {
						// WC2.2 and older: recalculate to include tax
						$discount_value = 0;
						$items = $this->order->get_items();;
						if( sizeof( $items ) > 0 ) {
							foreach( $items as $item ) {
								$discount_value += ($item['line_subtotal'] + $item['line_subtotal_tax']) - ($item['line_total'] + $item['line_tax']);
							}
						}
					}

					break;
				default:
					// Total Discount - Cart & Order Discounts combined
					$discount_value = $this->order->get_total_discount();
					break;
			}
		} else { // calculate discount excluding tax
			if ( version_compare( WOOCOMMERCE_VERSION, '2.3' ) >= 0 ) {
				$discount_value = $this->order->get_total_discount( true ); // $ex_tax = true
			} else {
				// WC2.2 and older: recalculate to exclude tax
				$discount_value = 0;

				$items = $this->order->get_items();;
				if( sizeof( $items ) > 0 ) {
					foreach( $items as $item ) {
						$discount_value += ($item['line_subtotal'] - $item['line_total']);
					}
				}
			}
		}

		$discount = array (
			'label'		=> __('Discount', 'woocommerce-pdf-invoices-packing-slips' ),
			'value'		=> $this->format_price( $discount_value ),
			'raw_value'	=> $discount_value,
		);

		if ( round( $discount_value, 3 ) != 0 ) {
			return apply_filters( 'wpo_wcpdf_order_discount', $discount, $type, $tax, $this );
		}
	}
	public function order_discount( $type = 'total', $tax = 'incl' ) {
		$discount = $this->get_order_discount( $type, $tax );
		echo $discount['value'];
	}

	/**
	 * Return the order fees
	 */
	public function get_order_fees( $tax = 'excl' ) {
		if ( $_fees = $this->order->get_fees() ) {
			foreach( $_fees as $id => $fee ) {
				if ($tax == 'excl' ) {
					$fee_price = $this->format_price( $fee['line_total'] );
				} else {
					$fee_price = $this->format_price( $fee['line_total'] + $fee['line_tax'] );
				}

				$fees[ $id ] = array(
					'label' 		=> $fee['name'],
					'value'			=> $fee_price,
					'line_total'	=> $this->format_price( $fee['line_total'] ),
					'line_tax'		=> $this->format_price( $fee['line_tax'] )
				);
			}
			return $fees;
		}
	}
	
	/**
	 * Return the order taxes
	 */
	public function get_order_taxes() {
		$tax_label = __( 'VAT', 'woocommerce-pdf-invoices-packing-slips' ); // register alternate label translation
		$tax_label = __( 'Tax rate', 'woocommerce-pdf-invoices-packing-slips' );
		$tax_rate_ids = $this->get_tax_rate_ids();
		if ( $order_taxes = $this->order->get_taxes() ) {
			foreach ( $order_taxes as $key => $tax ) {
				if ( WCX::is_wc_version_gte_3_0() ) {
					$taxes[ $key ] = array(
						'label'					=> $tax->get_label(),
						'value'					=> $this->format_price( $tax->get_tax_total() + $tax->get_shipping_tax_total() ),
						'rate_id'				=> $tax->get_rate_id(),
						'tax_amount'			=> $tax->get_tax_total(),
						'shipping_tax_amount'	=> $tax->get_shipping_tax_total(),
						'rate'					=> isset( $tax_rate_ids[ $tax->get_rate_id() ] ) ? ( (float) $tax_rate_ids[$tax->get_rate_id()]['tax_rate'] ) . ' %': '',
					);
				} else {
					$taxes[ $key ] = array(
						'label'					=> isset( $tax[ 'label' ] ) ? $tax[ 'label' ] : $tax[ 'name' ],
						'value'					=> $this->format_price( ( $tax[ 'tax_amount' ] + $tax[ 'shipping_tax_amount' ] ) ),
						'rate_id'				=> $tax['rate_id'],
						'tax_amount'			=> $tax['tax_amount'],
						'shipping_tax_amount'	=> $tax['shipping_tax_amount'],
						'rate'					=> isset( $tax_rate_ids[ $tax['rate_id'] ] ) ? ( (float) $tax_rate_ids[$tax['rate_id']]['tax_rate'] ) . ' %': '',
					);
				}

			}
			
			return apply_filters( 'wpo_wcpdf_order_taxes', $taxes, $this );
		}
	}

	/**
	 * Return/show the order grand total
	 */
	public function get_order_grand_total( $tax = 'incl' ) {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1' ) >= 0 ) {
			// WC 2.1 or newer is used
			$total_unformatted = $this->order->get_total();
		} else {
			// Backwards compatibility
			$total_unformatted = $this->order->get_order_total();
		}

		if ($tax == 'excl' ) {
			$total = $this->format_price( $total_unformatted - $this->order->get_total_tax() );
			$label = __( 'Total ex. VAT', 'woocommerce-pdf-invoices-packing-slips' );
		} else {
			$total = $this->format_price( ( $total_unformatted ) );
			$label = __( 'Total', 'woocommerce-pdf-invoices-packing-slips' );
		}
		
		$grand_total = array(
			'label' => $label,
			'value'	=> $total,
		);			

		return apply_filters( 'wpo_wcpdf_order_grand_total', $grand_total, $tax, $this );
	}
	public function order_grand_total( $tax = 'incl' ) {
		$grand_total = $this->get_order_grand_total( $tax );
		echo $grand_total['value'];
	}


	/**
	 * Return/Show shipping notes
	 */
	public function get_shipping_notes() {
		if ( $this->is_refund( $this->order ) ) {
			// return reason for refund if order is a refund
			if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
				$shipping_notes = $this->order->get_reason();
			} elseif ( is_callable( array( $this->order, 'get_refund_reason' ) ) ) {
				$shipping_notes = $this->order->get_refund_reason();
			} else {
				$shipping_notes = wpautop( wptexturize( WCX_Order::get_prop( $this->order, 'customer_note', 'view' ) ) );
			}
		} else {
			$shipping_notes = wpautop( wptexturize( WCX_Order::get_prop( $this->order, 'customer_note', 'view' ) ) );
		}

		// check document specific setting
		if( isset($this->settings['display_customer_notes']) && $this->settings['display_customer_notes'] == 0 ) {
			$shipping_notes = false;
		}

		return apply_filters( 'wpo_wcpdf_shipping_notes', $shipping_notes, $this );
	}
	public function shipping_notes() {
		echo $this->get_shipping_notes();
	}

	/**
	 * wrapper for wc_price, ensuring currency is always passed
	 */
	public function format_price( $price, $args = array() ) {
		if ( function_exists( 'wc_price' ) ) { // WC 2.1+
			$args['currency'] = WCX_Order::get_prop( $this->order, 'currency' );
			$formatted_price = wc_price( $price, $args );
		} else {
			$formatted_price = woocommerce_price( $price );
		}

		return $formatted_price;
	}
	public function wc_price( $price, $args = array() ) {
		return $this->format_price( $price, $args );
	}
	
	/**
	 * Gets price - formatted for display.
	 *
	 * @access public
	 * @param mixed $item
	 * @return string
	 */
	public function get_formatted_item_price ( $item, $type, $tax_display = '' ) {
		if ( ! isset( $item['line_subtotal'] ) || ! isset( $item['line_subtotal_tax'] ) ) {
			return;
		}

		$divide_by = ($type == 'single' && $item['qty'] != 0 )?abs($item['qty']):1; //divide by 1 if $type is not 'single' (thus 'total')
		if ( $tax_display == 'excl' ) {
			$item_price = $this->format_price( ($this->order->get_line_subtotal( $item )) / $divide_by );
		} else {
			$item_price = $this->format_price( ($this->order->get_line_subtotal( $item, true )) / $divide_by );
		}

		return $item_price;
	}

	public function get_invoice_number() {
		// Call the woocommerce_invoice_number filter and let third-party plugins set a number.
		// Default is null, so we can detect whether a plugin has set the invoice number
		$third_party_invoice_number = apply_filters( 'woocommerce_invoice_number', null, $this->order_id );
		if ($third_party_invoice_number !== null) {
			return $third_party_invoice_number;
		}

		if ( $invoice_number = $this->get_number('invoice') ) {
			return $formatted_invoice_number = $invoice_number->get_formatted();
		} else {
			return '';
		}
	}

	public function invoice_number() {
		echo $this->get_invoice_number();
	}

	public function get_invoice_date() {
		if ( $invoice_date = $this->get_date('invoice') ) {
			return $invoice_date->date_i18n( wcpdf_date_format( $this, 'invoice_date' ) );
		} else {
			return '';
		}
	}

	public function invoice_date() {
		echo $this->get_invoice_date();
	}

	public function get_document_notes() {
		if ( $document_notes = $this->get_notes( $this->get_type() ) ) {
			return $document_notes;
		} else {
			return '';
		}
	}

	public function document_notes() {
		$document_notes = $this->get_document_notes();
		if( $document_notes == strip_tags( $document_notes ) ) {
			echo nl2br($document_notes);
		} else {
			echo $document_notes;
		}
	}


}

endif; // class_exists