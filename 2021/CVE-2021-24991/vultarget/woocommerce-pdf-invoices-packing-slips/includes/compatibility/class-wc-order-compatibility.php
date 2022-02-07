<?php
namespace WPO\WC\PDF_Invoices\Compatibility;

/**
 * Derived from SkyVerge WooCommerce Plugin Framework https://github.com/skyverge/wc-plugin-framework/
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( '\\WPO\\WC\\PDF_Invoices\\Compatibility\\Order' ) ) :

/**
 * WooCommerce order compatibility class.
 *
 * @since 4.6.0-dev
 */
class Order extends Data {


	/** @var array mapped compatibility properties, as `$new_prop => $old_prop` */
	protected static $compat_props = array(
		'date_completed' => 'completed_date',
		'date_paid'      => 'paid_date',
		'date_modified'  => 'modified_date',
		'date_created'   => 'order_date',
		'customer_id'    => 'customer_user',
		'discount'       => 'cart_discount',
		'discount_tax'   => 'cart_discount_tax',
		'shipping_total' => 'total_shipping',
		'type'           => 'order_type',
		'currency'       => 'order_currency',
		'version'        => 'order_version',
	);


	/**
	 * Backports WC_Order::get_id() method to pre-2.6.0
	 *
	 * @since 4.2.0
	 * @param \WC_Order $order order object
	 * @return string|int order ID
	 */
	public static function get_id( $order ) {

		if ( method_exists( $order, 'get_id' ) ) {

			return $order->get_id();

		} else {

			return isset($order->id) ? $order->id : false;
		}
	}


	/**
	 * Gets an order property.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $object the order object
	 * @param string $prop the property name
	 * @param string $context if 'view' then the value will be filtered
	 * @return mixed
	 */
	public static function get_prop( $object, $prop, $context = 'edit', $compat_props = array() ) {

		// backport a few specific properties to pre-3.0
		if ( WC_Core::is_wc_version_lt_3_0() ) {

			// converge the shipping total prop for the raw context
			if ( 'shipping_total' === $prop && 'view' !== $context ) {

				$prop = 'order_shipping';

			// get the post_parent and bail early
			} elseif ( 'parent_id' === $prop ) {

				return $object->post->post_parent;
			}
		}

		$value = parent::get_prop( $object, $prop, $context, self::$compat_props );

		// 3.0+ date getters return a DateTime object, where previously MySQL date strings were returned
		if ( WC_Core::is_wc_version_lt_3_0() && in_array( $prop, array( 'date_completed', 'date_paid', 'date_modified', 'date_created' ), true ) ) {
			// parent fallback for empty date values in refunds
			if ( empty( $value ) && $object->order_type == 'refund' ) {
				$parent_order_id = wp_get_post_parent_id( $object->id );
				$parent_order = wc_get_order( $parent_order_id );
				$value = parent::get_prop( $parent_order, $prop, $context, self::$compat_props );
			}

			// abort mission if still empty
			if ( empty( $value ) ) {
				return $value;
			}

			if ( is_numeric( $value ) ) { // incidental for WC2.7 orders
				$value = new WC_DateTime( "@{$value}", new \DateTimeZone( 'UTC' ) );
				$value->setTimezone( new \DateTimeZone( wc_timezone_string() ) );
			} else {
				// Strings are defined in local WP timezone. Convert to UTC.
				if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $value, $date_bits ) ) {
					$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : wc_timezone_offset();
					$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
				} else {
					$timestamp = wc_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', wc_string_to_timestamp( $value ) ) ) );
				}
				$value = new WC_DateTime( "@{$timestamp}", new \DateTimeZone( 'UTC' ) );
			}

			// Set local timezone or offset.
			if ( get_option( 'timezone_string' ) ) {
				$value->setTimezone( new \DateTimeZone( wc_timezone_string() ) );
			} else {
				$value->set_utc_offset( wc_timezone_offset() );
			}
		}

		return $value;
	}


	/**
	 * Sets an order's properties.
	 *
	 * Note that this does not save any data to the database.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $object the order object
	 * @param array $props the new properties as $key => $value
	 * @return \WC_Order
	 */
	public static function set_props( $object, $props, $compat_props = array() ) {

		return parent::set_props( $object, $props, self::$compat_props );
	}

	/**
	 * Backports WC_Order::set_address_prop() to pre-3.0
	 * Saves by default.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $order the order object
	 * @param string $prop Name of prop to set.
	 * @param string $address Name of address to set. billing or shipping.
	 * @param mixed  $value Value of the prop.
	 * @param bool   $save whether to save the order/property
	 * @return \WC_Order
	 */
	public static function set_address_prop( \WC_Order $order, $prop, $address = 'billing', $value = null, $save = true ) {
		if ( WC_Core::is_wc_version_gte_3_0() ) {
			if ( is_callable( array( $order, "set_{$address}_{$prop}" ) ) ) {
				$order->{"set_{$address}_{$prop}"}( $value );
				if ($save === true) {
					$order->save();
				}
			}
		} else {
			// wc 2.6 or older
			if ($save === true) {
				// store directly in postmeta
				update_post_meta( $order->id, "_{$address}_{$prop}", $value );
			} else {
				// only change property in the order
				$order->$prop = $value;
			}
		}

		return $order;
	}

	/**
	 * Implements WC_Order::get_item_meta for 3.0+
	 * @param  \WC_Order $order the order object
	 * @param  int     $item_id the item id
	 * @param  int     $key     the meta key
	 * @param  boolean $single  single or multiple
	 * @return mixed            item meta
	 */
	public static function get_item_meta( $object, $item_id, $key = '', $single = false ) {
		if (function_exists('wc_get_order_item_meta')) {
			$item_meta = wc_get_order_item_meta( $item_id, $key, $single );
		} else {
			$item_meta = $object->get_item_meta( $item_id, $key, $single );
		}
		return $item_meta;
	}

	/**
	 * Backports WC_Order::get_status() to pre-3.0.0
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $order the order object
	 * @return string order status
	 */
	public static function get_status( \WC_Order $order ) {

		if ( method_exists( $order, 'get_status' ) ) {
			return $order->get_status();
		} else {
			return $order->status;
		}
	}

	/**
	 * Order item CRUD compatibility method to add a coupon to an order.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $order the order object
	 * @param array $code the coupon code
	 * @param int $discount the discount amount.
	 * @param int $discount_tax the discount tax amount.
	 * @return int the order item ID
	 */
	public static function add_coupon( \WC_Order $order, $code = array(), $discount = 0, $discount_tax = 0 ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			$item = new \WC_Order_Item_Coupon();

			$item->set_props( array(
				'code'         => $code,
				'discount'     => $discount,
				'discount_tax' => $discount_tax,
				'order_id'     => $order->get_id(),
			) );

			$item->save();

			$order->add_item( $item );

			return $item->get_id();

		} else {

			return $order->add_coupon( $code, $discount, $discount_tax );
		}
	}


	/**
	 * Order item CRUD compatibility method to add a fee to an order.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $order the order object
	 * @param object $fee the fee to add
	 * @return int|\WC_Order_Item the order item ID
	 */
	public static function add_fee( \WC_Order $order, $fee ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			$item = new \WC_Order_Item_Fee();

			$item->set_props( array(
				'name'      => $fee->name,
				'tax_class' => $fee->taxable ? $fee->tax_class : 0,
				'total'     => $fee->amount,
				'total_tax' => $fee->tax,
				'taxes'     => array(
					'total' => $fee->tax_data,
				),
				'order_id'  => $order->get_id(),
			) );

			$item->save();

			$order->add_item( $item );

			return $item->get_id();

		} else {

			return $order->add_fee( $fee );
		}
	}


	/**
	 * Order item CRUD compatibility method to update an order coupon.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $order the order object
	 * @param int|\WC_Order_Item $item the order item ID
	 * @param array $args {
	 *     The coupon item args.
	 *
	 *     @type string $code         the coupon code
	 *     @type float  $discount     the coupon discount amount
	 *     @type float  $discount_tax the coupon discount tax amount
	 * }
	 * @return int|bool the order item ID or false on failure
	 */
	public static function update_coupon( \WC_Order $order, $item, $args ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			if ( is_numeric( $item ) ) {
				$item = $order->get_item( $item );
			}

			if ( ! is_object( $item ) || ! $item->is_type( 'coupon' ) ) {
				return false;
			}

			if ( ! $order->get_id() ) {
				$order->save();
			}

			$item->set_order_id( $order->get_id() );
			$item->set_props( $args );
			$item->save();

			return $item->get_id();

		} else {

			// convert 3.0+ args for backwards compatibility
			if ( isset( $args['discount'] ) ) {
				$args['discount_amount'] = $args['discount'];
			}
			if ( isset( $args['discount_tax'] ) ) {
				$args['discount_amount_tax'] = $args['discount_tax'];
			}

			return $order->update_coupon( $item, $args );
		}
	}


	/**
	 * Order item CRUD compatibility method to update an order fee.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $order the order object
	 * @param int $item the order item ID
	 * @param array $args {
	 *     The fee item args.
	 *
	 *     @type string $name       the fee name
	 *     @type string $tax_class  the fee's tax class
	 *     @type float  $line_total the fee total amount
	 *     @type float  $line_tax   the fee tax amount
	 * }
	 * @return int|bool the order item ID or false on failure
	 */
	public static function update_fee( \WC_Order $order, $item, $args ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			if ( is_numeric( $item ) ) {
				$item = $order->get_item( $item );
			}

			if ( ! is_object( $item ) || ! $item->is_type( 'fee' ) ) {
				return false;
			}

			if ( ! $order->get_id() ) {
				$order->save();
			}

			$item->set_order_id( $order->get_id() );
			$item->set_props( $args );
			$item->save();

			return $item->get_id();

		} else {

			return $order->update_fee( $item, $args );
		}
	}


	/**
	 * Backports wc_reduce_stock_levels() to pre-3.0.0
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $order the order object
	 */
	public static function reduce_stock_levels( \WC_Order $order ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {
			wc_reduce_stock_levels( $order->get_id() );
		} else {
			$order->reduce_order_stock();
		}
	}


	/**
	 * Backports wc_update_total_sales_counts() to pre-3.0.0
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Order $order the order object
	 */
	public static function update_total_sales_counts( \WC_Order $order ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {
			wc_update_total_sales_counts( $order->get_id() );
		} else {
			$order->record_product_sales();
		}
	}


}


endif; // Class exists check
