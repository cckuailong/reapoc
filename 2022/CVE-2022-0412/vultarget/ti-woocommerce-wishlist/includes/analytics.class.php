<?php
/**
 * Analytics function class
 *
 * @since             1.10.0
 * @package           TInvWishlist\Analytics
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Analytics function class
 */
class TInvWL_Analytics {

	/**
	 * Database table
	 *
	 * @var string
	 */
	private $table;

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * Constructor
	 *
	 * @param array $wishlist Object wishlist.
	 * @param string $plugin_name Plugin name.
	 *
	 * @global wpdb $wpdb
	 */
	function __construct( $wishlist, $plugin_name = TINVWL_PREFIX ) {
		global $wpdb;

		$this->wishlist = (array) $wishlist;
		$this->_name    = $plugin_name;
		$this->table    = sprintf( '%s%s_%s', $wpdb->prefix, $this->_name, 'analytics' );
	}

	/**
	 * Get wishlist id
	 *
	 * @return int
	 */
	function wishlist_id() {
		if ( is_array( $this->wishlist ) && array_key_exists( 'ID', $this->wishlist ) ) {
			return $this->wishlist['ID'];
		}

		return 0;
	}

	/**
	 * Get author wishlist
	 *
	 * @return int
	 */
	function wishlist_author() {
		if ( is_array( $this->wishlist ) && array_key_exists( 'author', $this->wishlist ) ) {
			return $this->wishlist['author'];
		}

		return 0;
	}

	/**
	 * Get product info
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 *
	 * @return mixed
	 */
	private function product_data( $product_id, $variation_id = 0 ) {
		$product_id   = absint( $product_id );
		$variation_id = absint( $variation_id );

		if ( 'product_variation' == get_post_type( $product_id ) ) { // WPCS: loose comparison ok.
			$variation_id = $product_id;
			$product_id   = wp_get_post_parent_id( $variation_id );
		}

		$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

		if ( ! $product_data || 'trash' === $product_data->post->post_status ) {
			return null;
		}

		$product_data->variation_id = absint( $product_data->variation_id );

		return $product_data;
	}

	/**
	 * Add views analitycs
	 *
	 * @param integer $wishlist_id If exist wishlist object, you can put 0.
	 * @param boolean $author is author wislist.
	 *
	 */
	function view_products( $wishlist, $author = null ) {
		$products = TInvWL_Public_Wishlist_View::instance()->get_current_products_query();
		if ( empty( $products ) || ! is_array( $products ) ) {
			return false;
		}
		$data = array();
		foreach ( $products as $key => $product ) {
			$data['product_ids'][ $key ]   = $product['product_id'];
			$data['variation_ids'][ $key ] = $product['variation_id'];
		}

		$this->add( ( $author ? 'author' : 'visite' ), $wishlist['ID'], $data['product_ids'], $data['variation_ids'] );
	}

	/**
	 * Apply analytics actions
	 *
	 * @param string $type Type action.
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 * @param integer $quantity Quantity applyed product.
	 *
	 * @return boolean
	 */
	private function _product( $type, $product_id, $variation_id = 0, $quantity = 1 ) {
		$wishlist_id = $this->wishlist_id();
		$quantity    = absint( $quantity );

		return $this->add( $type, $wishlist_id, $product_id, $variation_id, $quantity );
	}

	/**
	 * Add to cart product
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 * @param integer $quantity Quantity applyed product.
	 *
	 * @return boolean
	 */
	function cart_product( $product_id, $variation_id = 0, $quantity = 1 ) {
		return $this->_product( 'add_to_cart', $product_id, $variation_id, $quantity );
	}

	/**
	 * Bougt product
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 * @param integer $quantity Quantity applyed product.
	 *
	 * @return boolean
	 */
	function sell_product( $product_id, $variation_id = 0, $quantity = 1 ) {
		return $this->_product( 'sell', $product_id, $variation_id, $quantity );
	}

	/**
	 * Click to product page from wishlist
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 * @param integer $quantity Quantity applyed product.
	 *
	 * @return boolean
	 */
	function click_product_from_wl( $product_id, $variation_id = 0, $quantity = 1 ) {
		return $this->_product( 'click', $product_id, $variation_id, $quantity );
	}

	/**
	 * Click to product page from wishlist by author
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 * @param integer $quantity Quantity applyed product.
	 *
	 * @return boolean
	 */
	function click_author_product_from_wl( $product_id, $variation_id = 0, $quantity = 1 ) {
		return $this->_product( 'author_click', $product_id, $variation_id, $quantity );
	}

	/**
	 * Bought product from wishlist
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 * @param integer $quantity Quantity applyed product.
	 *
	 * @return boolean
	 */
	function sell_product_from_wl( $product_id, $variation_id = 0, $quantity = 1 ) {
		return $this->_product( 'wishlist', $product_id, $variation_id, $quantity );
	}

	/**
	 * Gifted product
	 *
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 * @param integer $quantity Quantity applyed product.
	 *
	 * @return boolean
	 */
	function gifted_product( $product_id, $variation_id = 0, $quantity = 1 ) {
		return $this->_product( 'gift', $product_id, $variation_id, $quantity );
	}

	/**
	 * Add action product
	 *
	 * @param string $type Action field.
	 * @param integer $wishlist_id If exist wishlist object, you can put 0.
	 * @param integer $product_id Product id.
	 * @param integer $variation_id Product variation id.
	 * @param integer $quantity Quantity applied actions.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 */
	function add( $type = 'visite', $wishlist_id = 0, $product_id = 0, $variation_id = 0, $quantity = 1 ) {
		if ( ! in_array( $type, array(
			'add_to_cart',
			'author',
			'author_click',
			'click',
			'gift',
			'visite',
			'wishlist',
		) ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			return false;
		}
		if ( empty( $wishlist_id ) ) {
			$wishlist_id = $this->wishlist_id();
		}
		if ( empty( $wishlist_id ) ) {
			return false;
		}

		$product_id   = ( is_array( $product_id ) ) ? $product_id : array( $product_id );
		$variation_id = ( is_array( $variation_id ) ) ? $variation_id : array( $variation_id );
		if ( empty( $product_id ) && ! empty( $variation_id ) ) {
			$ids = array();
			foreach ( $product_id as $key => $id ) {
				$product_data = $this->product_data( $id, $variation_id[ $key ] );
				if ( $product_data ) {
					$ids['product_id'][ $key ]   = $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id();
					$ids['variation_id'][ $key ] = $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0;
				}
			}
			if ( empty( $ids ) ) {
				return false;
			}
			$product_id   = $ids['product_id'];
			$variation_id = $ids['variation_id'];
		}
		$data = array(
			'wishlist_id'  => $wishlist_id,
			'product_id'   => $product_id,
			'variation_id' => $variation_id,
		);

		foreach ( $product_id as $key => $id ) {
			$data['ID'][ $key ] = md5( implode( '|', array(
				'wishlist_id'  => $wishlist_id,
				'product_id'   => $id,
				'variation_id' => $variation_id[ $key ],
			) ) );
		}

		if ( 'visite' == $type ) { // WPCS: loose comparison ok.
			$user = wp_get_current_user();
			if ( $user->exists() ) {
				if ( $this->wishlist_author() == $user->ID ) { // WPCS: loose comparison ok.
					$type = 'author';
				}
			}
		}
		switch ( $type ) {
			case 'author':
				$data['visite_author'] = $quantity;
			case 'visite':
				$data['visite'] = $quantity;
				break;
			case 'author_click':
				$data['click_author'] = $quantity;
			case 'click':
				$data['click'] = $quantity;
				break;
			case 'add_to_cart':
				$data['cart'] = $quantity;
				break;
			case 'gift':
				$data['sell_as_gift'] = $quantity;
			case 'wishlist':
				$data['sell_of_wishlist'] = $quantity;
				break;
		}
		switch ( $type ) {
			case 'author':
			case 'visite':
				break;
			default :
				if ( empty( $product_id ) ) {
					return false;
				}
		}
		$fields     = array();
		$values     = array();
		$duplicates = array();

		foreach ( array_keys( $product_id ) as $index ) {
			foreach ( $data as $key => $value ) {
				if ( 0 === $index ) {
					$fields[] = $key;
					if ( in_array( $key, array(
						'cart',
						'sell_as_gift',
						'click_author',
						'click',
						'sell_of_wishlist',
						'visite',
						'visite_author',
					) ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
						$duplicates[] = sprintf( '`%s`=`%s`+%d', $key, $key, $value );
					}
				}

				if ( in_array( $key, array( 'product_id', 'variation_id', 'ID' ) ) ) {
					$values[ $index ][] = $value[ $index ];
				} else {
					$values[ $index ][] = $value;
				}
			}
		}

		$fields = '`' . implode( '`,`', $fields ) . '`';
		$data   = array();
		foreach ( $values as $value ) {
			$data[] = "('" . implode( "','", $value ) . "')";
		}
		$data       = implode( ",", $data );
		$duplicates = implode( ',', $duplicates );

		global $wpdb;

		return $wpdb->query( "INSERT INTO `$this->table` ($fields) VALUES $data ON DUPLICATE KEY UPDATE $duplicates" ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
	}

	/**
	 * Get products
	 *
	 * @param array $data Request.
	 *
	 * @return array
	 * @global wpdb $wpdb
	 */
	function get( $data = array() ) {
		$default = array(
			'count'    => 10,
			'field'    => null,
			'offset'   => 0,
			'order'    => 'ASC',
			'order_by' => 'visite',
			'external' => true,
			'sql'      => '',
		);

		foreach ( array_keys( $default ) as $_k ) {
			if ( array_key_exists( $_k, $data ) ) {
				$default[ $_k ] = $data[ $_k ];
				unset( $data[ $_k ] );
			}
		}

		$default['offset'] = absint( $default['offset'] );
		$default['count']  = absint( $default['count'] );
		if ( is_array( $default['field'] ) ) {
			$default['field'] = '`' . implode( '`,`', $default['field'] ) . '`';
		} elseif ( is_string( $default['field'] ) ) {
			$default['field'] = array( 'ID', $default['field'] );
			$default['field'] = '`' . implode( '`,`', $default['field'] ) . '`';
		} else {
			$default['field'] = '*';
		}
		$sql = "SELECT {$default[ 'field' ]} FROM `{$this->table}`";

		$where = '1';
		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach ( $data as $f => $v ) {
				$s = is_array( $v ) ? ' IN ' : '=';
				if ( is_array( $v ) ) {
					$v = "'" . implode( "','", $v ) . "'";
					$v = "($v)";
				} else {
					$v = "'$v'";
				}
				$data[ $f ] = sprintf( '`%s`%s%s', $f, $s, $v );
			}
			$where = implode( ' AND ', $data );
			$sql   .= ' WHERE ' . $where;
		}

		$sql .= sprintf( ' ORDER BY `%s` %s LIMIT %d,%d;', $default['order_by'], $default['order'], $default['offset'], $default['count'] );
		if ( ! empty( $default['sql'] ) ) {
			$replacer    = $replace = array();
			$replace[0]  = '{table}';
			$replacer[0] = $this->table;
			$replace[1]  = '{where}';
			$replacer[1] = $where;

			foreach ( $default as $key => $value ) {
				$i = count( $replace );

				$replace[ $i ]  = '{' . $key . '}';
				$replacer[ $i ] = $value;
			}

			$sql = str_replace( $replace, $replacer, $default['sql'] );
		}
		global $wpdb;
		$products = $wpdb->get_results( $sql, ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.

		if ( empty( $products ) ) {
			return array();
		}
		if ( $default['external'] ) {
			foreach ( $products as $k => $product ) {
				$product_data = $this->product_data( $product['variation_id'], $product['product_id'] );
				if ( $product_data ) {
					$product['product_id']   = $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id();
					$product['variation_id'] = $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0;
				}
				$product['data'] = $product_data;
				$products[ $k ]  = $product;
			}
		}

		return $products;
	}

	/**
	 * Get Analytics
	 *
	 * @param integer $wishlist_id If exist wishlist object, you can put 0.
	 *
	 * @return array
	 * @global wpdb $wpdb
	 */
	function get_wishlist( $wishlist_id = 0 ) {
		$wishlist_id = absint( $wishlist_id );
		if ( empty( $wishlist_id ) ) {
			$wishlist_id = $this->wishlist_id();
		}
		global $wpdb;

		if ( empty( $wishlist_id ) ) {
			$analytics = $wpdb->get_results( $wpdb->prepare( "SELECT `wishlist_id`,`visite`, `visite_author` FROM `$this->table` WHERE `product_id`=%d AND `variation_id`=%d;", 0, 0 ), ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		} else {
			$analytics = $wpdb->get_results( $wpdb->prepare( "SELECT `wishlist_id`,`visite`, `visite_author` FROM `$this->table` WHERE `wishlist_id`=%d AND `product_id`=%d AND `variation_id`=%d;", $wishlist_id, 0, 0 ), ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		}
		if ( empty( $analytics ) ) {
			return array();
		}
		foreach ( $analytics as $key => $analytic ) {
			foreach ( $analytic as $field => $value ) {
				$analytic[ $field ] = absint( $value );
			}
			$analytics[ $key ] = $analytic;
		}

		return $analytics;
	}

	/**
	 * Get Analytics Product
	 *
	 * @param type $wishlist_id If exist wishlist object, you can put 0.
	 * @param type $product_id Product id.
	 * @param type $variation_id Product variation id.
	 *
	 * @return type
	 * @global wpdb $wpdb
	 */
	function get_product( $wishlist_id = 0, $product_id = 0, $variation_id = 0 ) {

		$wishlist_id = absint( $wishlist_id );
		if ( empty( $wishlist_id ) ) {
			$wishlist_id = $this->wishlist_id();
		}
		$wishlist_id  = absint( $wishlist_id );
		$product_id   = absint( $product_id );
		$variation_id = absint( $variation_id );
		if ( ! empty( $product_id ) || ! empty( $variation_id ) ) {
			$product_data = $this->product_data( $product_id, $variation_id );
			if ( $product_data ) {
				$product_id   = $product_data->is_type( 'variation' ) ? $product_data->get_parent_id() : $product_data->get_id();
				$variation_id = $product_data->is_type( 'variation' ) ? $product_data->get_id() : 0;
			} else {
				$product_id   = 0;
				$variation_id = 0;
			}
		} else {
			$product_id   = 0;
			$variation_id = 0;
		}

		global $wpdb;
		$analytics = array();
		if ( empty( $product_id ) && empty( $variation_id ) ) {
			if ( empty( $wishlist_id ) ) {
				$analytics = $wpdb->get_results( $wpdb->prepare( "SELECT `wishlist_id`,`product_id`,`variation_id`,`sell`, `sell_of_wishlist`, `sell_as_gift` FROM `$this->table` WHERE `product_id`<>%d AND `variation_id`<>%d;", 0, 0 ), ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
			} else {
				$analytics = $wpdb->get_results( $wpdb->prepare( "SELECT `wishlist_id`,`product_id`,`variation_id`,`sell`, `sell_of_wishlist`, `sell_as_gift` FROM `$this->table` WHERE `wishlist_id`=%d AND `product_id`<>%d AND `variation_id`<>%d;", $wishlist_id, 0, 0 ), ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
			}
		} else {
			if ( empty( $wishlist_id ) ) {
				$analytics = $wpdb->get_results( $wpdb->prepare( "SELECT `wishlist_id`,`product_id`,`variation_id`,`sell`, `sell_of_wishlist`, `sell_as_gift` FROM `$this->table` WHERE `product_id`=%d AND `variation_id`=%d;", $product_id, $variation_id ), ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
			} else {
				$analytics = $wpdb->get_results( $wpdb->prepare( "SELECT `wishlist_id`,`product_id`,`variation_id`,`sell`, `sell_of_wishlist`, `sell_as_gift` FROM `$this->table` WHERE `wishlist_id`=%d AND `product_id`=%d AND `variation_id`=%d;", $wishlist_id, $product_id, $variation_id ), ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
			}
		}
		if ( empty( $analytics ) ) {
			return array();
		}

		foreach ( $analytics as $key => $analytic ) {
			foreach ( $analytic as $field => $value ) {
				$analytic[ $field ] = absint( $value );
			}
			$analytics[ $key ] = $analytic;
		}

		return $analytics;
	}
}
