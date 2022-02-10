<?php
/**
 * Legacy local product function class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Products
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Legacy local product function class
 */
class TInvWL_Product_Legacy {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;
	/**
	 * Array products
	 *
	 * @var array
	 */
	private $products;
	/**
	 * Autoincremet
	 *
	 * @var integer
	 */
	private $products_autoinc;
	/**
	 * This class
	 *
	 * @var \TInvWL_Product_Local
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Product_Local
	 */
	public static function instance( $plugin_name = TINVWL_PREFIX ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name );
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 */
	function __construct( $plugin_name = TINVWL_PREFIX ) {

		$this->_name            = $plugin_name;
		$this->products         = array();
		$this->products_autoinc = 0;

		$products = filter_input( INPUT_COOKIE, 'tinv_wishlist' );
		if ( ! empty( $products ) ) {
			$products = urldecode( $products );
			$products = @json_decode( $products, true ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			if ( is_array( $products ) ) {
				$this->products = $products;
			}
		}
		if ( ! empty( $this->products ) ) {
			foreach ( $this->products as $product ) {
				if ( array_key_exists( 'ID', $product ) ) {
					$this->products_autoinc = absint( $product['ID'] );
				}
			}
		}
		$this->products_autoinc ++;
	}

	/**
	 * Add product in cookies
	 *
	 * @param array $data New product.
	 *
	 * @return integer
	 */
	function add_cookies( $data ) {
		$data['ID']       = $this->products_autoinc;
		$this->products[] = $data;
		$this->update_cookie();
		$this->products_autoinc ++;

		return $data['ID'];
	}

	/**
	 * Update product in cookies
	 *
	 * @param array $data Product.
	 * @param array $where requset.
	 *
	 * @return boolean
	 */
	function update_cookies( $data, $where = array() ) {
		$_update = false;
		foreach ( $this->products as $id => $product ) {
			$_where = true;
			foreach ( $where as $field => $value ) {
				if ( $product[ $field ] != $value ) { // WPCS: loose comparison ok.
					$_where = false;
				}
				if ( $_where ) {
					$_update = true;
					foreach ( $data as $_field => $_value ) {
						if ( array_key_exists( $_field, $product ) ) {
							$product[ $_field ] = $_value;
						}
					}
					$this->products[ $id ] = $product;
				}
			}
		}
		if ( $_update ) {
			$this->update_cookie();

			return true;
		}

		return false;
	}

	/**
	 * Update cookie
	 */
	function update_cookie() {
		@setcookie( 'tinv_wishlist', urlencode( wp_json_encode( $this->products ) ), time() + 31 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged WordPress.Sniffs.VIP.RestrictedFunctions
	}

	/**
	 * Add\Update product
	 *
	 * @param array $data Object product.
	 *
	 * @return boolean
	 */
	function add_product( $data = array() ) {
		$_data = filter_var_array( $data, array(
			'product_id'   => FILTER_VALIDATE_INT,
			'variation_id' => FILTER_VALIDATE_INT,
			'quantity'     => FILTER_VALIDATE_INT,
		) );
		if ( empty( $_data['quantity'] ) ) {
			$_data['quantity'] = 1;
		}
		$product_data = $this->check_product( $_data['product_id'], $_data['variation_id'] );

		if ( false === $product_data ) {
			return false;
		}
		if ( $product_data ) {
			$data['quantity'] = $product_data['quantity'] + $_data['quantity'];

			return $this->update( $data );
		} else {
			return $this->add( $data );
		}
	}

	/**
	 * Add product
	 *
	 * @param array $data Object product.
	 *
	 * @return boolean
	 */
	function add( $data = array() ) {
		$default = array(
			'date'         => current_time( 'Y-m-d H:i:s' ),
			'product_id'   => 0,
			'quantity'     => 1,
			'variation_id' => 0,
		);

		$data = filter_var_array( $data, apply_filters( 'tinvwl_wishlist_product_add_field', array(
			'product_id'   => FILTER_VALIDATE_INT,
			'quantity'     => FILTER_VALIDATE_INT,
			'variation_id' => FILTER_VALIDATE_INT,
		) ) );
		$data = array_filter( $data );

		$data = tinv_array_merge( $default, $data );

		if ( empty( $data['product_id'] ) ) {
			return false;
		}

		$product_data = $this->product_data( $data['product_id'], $data['variation_id'] );

		if ( $data['quantity'] <= 0 || ! $product_data ) {
			return false;
		}

		if ( $product_data->is_sold_individually() ) {
			$data['quantity'] = 1;
		}

		$data                 = apply_filters( 'tinvwl_wishlist_product_add', $data );
		$data['product_id']   = $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id();
		$data['variation_id'] = $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0;

		$this->add_cookies( $data );

		return true;
	}

	/**
	 * Get products by wishlist
	 *
	 * @param array $data Request.
	 *
	 * @return array
	 */
	function get_wishlist( $data = array() ) {
		return $this->get( $data );
	}

	/**
	 * Check existing product
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variaton id.
	 *
	 * @return mixed
	 */
	function check_product( $product_id, $variation_id = 0 ) {
		$product_id   = absint( $product_id );
		$variation_id = absint( $variation_id );

		$product_data = $this->product_data( $product_id, $variation_id );

		if ( ! $product_data ) {
			return false;
		}

		$products = $this->get( array(
			'product_id'   => $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id(),
			'variation_id' => $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0,
			'count'        => 1,
			'external'     => false,
		) );

		return array_shift( $products );
	}

	/**
	 * Get products
	 *
	 * @param array $data Request.
	 *
	 * @return array
	 */
	function get( $data = array() ) {
		$default = array(
			'count'    => 10,
			'offset'   => 0,
			'external' => true,
		);

		foreach ( array_keys( $default ) as $_k ) {
			if ( array_key_exists( $_k, $data ) ) {
				$default[ $_k ] = $data[ $_k ];
				unset( $data[ $_k ] );
			}
		}

		$products = $this->products;
		if ( 0 < count( $data ) ) {
			foreach ( $products as $key => $product ) {
				foreach ( $data as $field => $value ) {
					if ( array_key_exists( $field, $product ) ) {
						if ( is_array( $value ) ) {
							$_is = false;
							foreach ( $value as $subvalue ) {
								if ( $product[ $field ] === $subvalue ) {
									$_is = true;
								}
							}
							if ( ! $_is ) {
								$products[ $key ] = null;
							}
						} else {
							if ( $product[ $field ] !== $value ) {
								$products[ $key ] = null;
							}
						}
					}
				}
			}
			$products = array_filter( $products );
		}

		$products = array_slice( $products, $default['offset'], $default['count'] );

		if ( empty( $products ) ) {
			return array();
		}

		foreach ( $products as $k => $product ) {
			$product = filter_var_array( $product, array(
				'ID'           => FILTER_VALIDATE_INT,
				'wishlist_id'  => FILTER_VALIDATE_INT,
				'product_id'   => FILTER_VALIDATE_INT,
				'variation_id' => FILTER_VALIDATE_INT,
				'author'       => FILTER_VALIDATE_INT,
				'date'         => FILTER_SANITIZE_STRING,
				'quantity'     => FILTER_VALIDATE_INT,
				'price'        => FILTER_SANITIZE_NUMBER_FLOAT,
				'in_stock'     => FILTER_VALIDATE_BOOLEAN,
			) );

			$product['wishlist_id'] = 0;
			if ( $default['external'] ) {
				$product_data = $this->product_data( $product['variation_id'], $product['product_id'] );
				if ( $product_data ) {
					$product['product_id']   = $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id();
					$product['variation_id'] = $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0;
				}
				$product['data'] = $product_data;
			}
			$products[ $k ] = apply_filters( 'tinvwl_wishlist_product_get', $product );
		}

		return $products;
	}

	/**
	 * Get product info
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 *
	 * @return mixed
	 */
	function product_data( $product_id, $variation_id = 0 ) {
		$product_id   = absint( $product_id );
		$variation_id = absint( $variation_id );

		if ( 'product_variation' == get_post_type( $product_id ) ) { // WPCS: loose comparison ok.
			$variation_id = $product_id;
			$product_id   = wp_get_post_parent_id( $variation_id );
		}

		$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

		if ( ! $product_data || 'trash' === get_post( $product_data->get_id() )->post_status ) {
			return null;
		}

		$product_data->variation_id = absint( ( $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0 ) );

		return $product_data;
	}

	/**
	 * Update product
	 *
	 * @param array $data Object product.
	 *
	 * @return boolean
	 */
	function update( $data = array() ) {
		$data = filter_var_array( $data, apply_filters( 'tinvwl_wishlist_product_update_field', array(
			'product_id'   => FILTER_VALIDATE_INT,
			'quantity'     => FILTER_VALIDATE_INT,
			'variation_id' => FILTER_VALIDATE_INT,
		) ) );
		$data = array_filter( $data );

		if ( ! array_key_exists( 'variation_id', $data ) ) {
			$data['variation_id'] = 0;
		}

		if ( empty( $data['product_id'] ) ) {
			return false;
		}
		$product_data = $this->product_data( $data['product_id'], $data['variation_id'] );
		if ( ! $product_data ) {
			return false;
		}

		if ( $product_data->is_sold_individually() ) {
			$data['quantity'] = 1;
		}

		$data                 = apply_filters( 'tinvwl_wishlist_product_update', $data );
		$data['product_id']   = $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id();
		$data['variation_id'] = $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0;

		return $this->update_cookies( $data, array(
			'product_id'   => $data['product_id'],
			'variation_id' => $data['variation_id'],
		) );
	}

	/**
	 * Remove product from wishlist
	 *
	 * @param integer $wishlist_id Not Used.
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 *
	 * @return boolean
	 */
	function remove_product_from_wl( $wishlist_id = 0, $product_id = 0, $variation_id = 0 ) {

		if ( empty( $product_id ) ) {
			$this->products = array();
			$this->update_cookie();
		}
		$product_data = $this->product_data( $product_id, $variation_id );
		if ( ! $product_data ) {
			return false;
		}

		foreach ( $this->products as $key => $product ) {
			if ( ( $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id() ) == $product['product_id'] && ( $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0 ) == $product['variation_id'] ) { // WPCS: loose comparison ok.
				$this->products[ $key ] = null;
			}
		}

		$c = count( $this->products );

		$this->products = array_filter( $this->products );

		if ( count( $this->products ) < $c ) {
			$this->update_cookie();
			do_action( 'tinvwl_wishlist_product_removed_from_wishlist', $wishlist_id, ( $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id() ), ( $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0 ) );
		}

		return true;
	}

	/**
	 * Remove product
	 *
	 * @param integer $product_id Product id.
	 *
	 * @return boolean
	 */
	function remove_product( $product_id = 0 ) {
		if ( empty( $product_id ) ) {
			return false;
		}

		foreach ( $this->products as $key => $product ) {
			if ( $product['product_id'] == $product_id ) { // WPCS: loose comparison ok.
				$this->products[ $key ] = null;
			}
		}

		$c = count( $this->products );

		$this->products = array_filter( $this->products );

		if ( count( $this->products ) < $c ) {
			$this->update_cookie();
			do_action( 'tinvwl_wishlist_product_removed_by_product', $product_id );
		}

		return true;
	}

	/**
	 * Remove product by ID
	 *
	 * @param integer $id Product id.
	 *
	 * @return boolean
	 */
	function remove( $id = 0 ) {
		if ( empty( $id ) ) {
			return false;
		}

		foreach ( $this->products as $key => $product ) {
			if ( $product['ID'] == $id ) { // WPCS: loose comparison ok.
				$this->products[ $key ] = null;
			}
		}

		$c              = count( $this->products );
		$this->products = array_filter( $this->products );

		if ( count( $this->products ) < $c ) {
			$this->update_cookie();
			do_action( 'tinvwl_wishlist_product_removed_by_id', $id );
		}

		return true;
	}
}
