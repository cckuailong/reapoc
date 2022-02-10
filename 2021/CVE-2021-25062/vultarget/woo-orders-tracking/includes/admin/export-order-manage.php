<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_ADMIN_EXPORT_ORDER_MANAGE {
	private static $post_type = 'shop_order';

	private static function make_orders_setting( $settings ) {
		//check filter by order
		$args = array(
			'filter-order-status',
			'filter-order-billing-address',
			'filter-order-shipping-address',
			'filter-order-payment-method',
			'filter-order-shipping-method',
			'set-fields',
		);
		foreach ( $args as $key ) {
			if ( ! array_key_exists( $key, $settings ) ) {
				$settings[ $key ] = array();
			}
		}

		//set filename
		$filename             = $settings['filename'];
		$filename             = str_replace( array(
			'%y',
			'%m',
			'%d',
			'%h',
			'%i',
			'%s',
		), array(
			current_time( 'Y' ),
			current_time( 'm' ),
			current_time( 'd' ),
			current_time( 'H' ),
			current_time( 'i' ),
			current_time( 's' ),
		),
			$filename
		);
		$settings['filename'] = $filename . '.csv';

		return $settings;
	}

	private static function get_header_row( $fields = array() ) {
		$results = array();
		$default = self::get_fields_to_select();
		if ( empty( $fields ) ) {
			$results = $default;
		} else {
			$check_has_tracking_code = $check_has_carrier_id = false;
			$check_tracking_code     = $check_carrier_id = false;
			foreach ( $fields as $item ) {
				$t = explode( '{wotv}', $item );
				if ( is_array( $t ) && count( $t ) >= 2 ) {
					$field_type = trim( $t[0] );
					$field_key  = trim( $t[1] );
					if ( $field_key === '_vi_order_item_tracking_code' ) {
						$check_has_tracking_code = true;
					}
					if ( $field_key === '_vi_order_item_carrier_id' ) {
						$check_has_carrier_id = true;
					}
					foreach ( $default as $field_default ) {
						if ( $field_type === $field_default['type'] && $field_key === $field_default['key'] ) {
							$results[] = $field_default;
							if ( $field_default['key'] === '_vi_order_item_tracking_code' ) {
								$check_tracking_code = true;
							}
							if ( $field_default['key'] === '_vi_order_item_carrier_id' ) {
								$check_carrier_id = true;
							}
							continue;
						}
					}
				}
			}

			if ( ! $check_carrier_id && $check_has_carrier_id ) {
				$results[] = array(
					'type'  => 'order_item_meta',
					'key'   => '_vi_order_item_carrier_id',
					'title' => __( '( Order Item ) Carrier Slug', 'woo-orders-tracking' ),
				);
			}
			if ( ! $check_tracking_code && $check_has_tracking_code ) {
				$results[] = array(
					'type'  => 'order_item_meta',
					'key'   => '_vi_order_item_tracking_code',
					'title' => __( '( Order Item ) Tracking Number', 'woo-orders-tracking' ),
				);
			}
		}

		return $results;
	}

	private static function get_filter_by_shipping_method( $args ) {
		$methods = array();
		foreach ( $args as $string ) {
			$t = explode( ':', $string );
			if ( ! count( $t ) == 2 ) {
				continue;
			}
			list( $meta_key, $meta_value ) = array_map( 'trim', $t );
			$meta_key = addslashes( $meta_key );
			if ( ! array_key_exists( $meta_key, $methods ) ) {
				$methods[ $meta_key ] = array();
			}
			$methods[ $meta_key ][] = addslashes( $meta_value );
		}
		if ( ! empty( $methods ) ) {
			global $wpdb;
			$sql   = " SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type='shipping' AND order_item_id IN (  SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta  WHERE ";
			$where = array();
			foreach ( $methods as $method => $ids ) {
				$where[] = ' ( meta_key=\'instance_id\' AND meta_value IN (' . join( ',', $ids ) . ' ) AND order_item_id IN (  SELECT order_item_id FROM wp_woocommerce_order_itemmeta WHERE (meta_key=\'method_id\' AND meta_value = \'' . $method . '\' )))';
			}
			$where   = join( ' OR ', $where ) . ')';
			$sql     .= $where;
			$results = $wpdb->get_col( $sql );

			return $results;
		}

		return $methods;
	}

	private static function parse_expressions( $args ) {
		$results    = array();
		$delimiters = array(
			'<>' => 'NOT IN',
			'='  => 'IN',
		);
		foreach ( $args as $expressions ) {
			$expressions = trim( $expressions );
			$op          = '';
			foreach ( $delimiters as $item => $value ) {
				$t = explode( $item, $expressions );
				if ( count( $t ) == 2 ) {
					$op = $value;
					break;
				}
			}
			if ( ! $op ) {
				continue;
			}
			list( $meta_key, $meta_value ) = array_map( 'trim', $t );
			$meta_key = addslashes( $meta_key );
			if ( ! array_key_exists( $meta_key, $results ) ) {
				$results[ $meta_key ] = array();
			}
			if ( ! array_key_exists( $op, $results[ $meta_key ] ) ) {
				$results[ $meta_key ][ $op ] = array();;
			}
			$results[ $meta_key ][ $op ][] = addslashes( $meta_value );
		}

		return $results;
	}

	private static function get_orders_ids( $settings ) {
		global $wpdb;
		$sql       = 'SELECT DISTINCT ' . $wpdb->posts . '.ID FROM ' . $wpdb->posts;
		$left_join = array();
		$where     = array(
			'post_type = \'' . self::$post_type . '\'',
		);
		//filter by date
		switch ( $settings['filter-order-date'] ) {
			case 'date_created':
				if ( ! empty( $settings['filter-order-date-from'] ) ) {
					$date_from = date( 'Y-m-d H:i:s', strtotime( $settings['filter-order-date-from'] ) );
					$where[]   = ' post_date  >= \'' . $date_from . '\' ';
				}
				if ( ! empty( $settings['filter-order-date-to'] ) ) {
					$date_to = date( 'Y-m-d H:i:s', strtotime( $settings['filter-order-date-to'] ) + 86400 );
					$where[] = '  post_date  < \'' . $date_to . '\' ';
				}

				break;
			case 'date_modified':
				if ( ! empty( $settings['filter-order-date-from'] ) ) {
					$date_from = date( 'Y-m-d', strtotime( $settings['filter-order-date-from'] ) );
					$where[]   = '  post_modified  >= \'' . $date_from . '\'';
				}
				if ( ! empty( $settings['filter-order-date-to'] ) ) {
					$date_to = date( 'Y-m-d', strtotime( $settings['filter-order-date-to'] ) + 86400 );
					$where[] = '  post_modified  < \'' . $date_to . '\'';
				}
				break;
			case 'date_completed':
				if ( ! empty( $settings['filter-order-date-from'] ) || ! empty( $settings['filter-order-date-to'] ) ) {
					$left_join[] = ' INNER JOIN ' . $wpdb->postmeta . ' AS order_meta_date_complete  ON ' . $wpdb->posts . '.ID = order_meta_date_complete.post_id   ';
					$where[]     = 'order_meta_date_complete.meta_key = \'_completed_date\' ';
					if ( ! empty( $settings['filter-order-date-from'] ) ) {
						$date_from = date( 'Y-m-d', strtotime( $settings['filter-order-date-from'] ) );
						$where[]   = 'order_meta_date_complete.meta_value  >= \'' . $date_from . '\'';
					}
					if ( ! empty( $settings['filter-order-date-to'] ) ) {
						$date_to = date( 'Y-m-d', strtotime( $settings['filter-order-date-to'] ) + 86400 );
						$where[] = 'order_meta_date_complete.meta_value  < \'' . $date_to . '\'';
					}
				}
				break;
			case 'date_paid':
				if ( ! empty( $settings['filter-order-date-from'] ) || ! empty( $settings['filter-order-date-to'] ) ) {
					$left_join[] = ' INNER JOIN ' . $wpdb->postmeta . ' AS order_meta_date_paid ON ' . $wpdb->posts . '.ID = order_meta_date_paid.post_id   ';
					$where[]     = 'order_meta_date_paid.meta_key = \'_paid_date\' ';
					if ( ! empty( $settings['filter-order-date-from'] ) ) {
						$date_from = date( 'Y-m-d', strtotime( $settings['filter-order-date-from'] ) );
						$where[]   = 'order_meta_date_paid.meta_value  >= \'' . $date_from . '\'';
					}
					if ( ! empty( $settings['filter-order-date-to'] ) ) {
						$date_to = date( 'Y-m-d', strtotime( $settings['filter-order-date-to'] ) + 86400 );
						$where[] = 'order_meta_date_paid.meta_value  < \'' . $date_to . '\'';
					}
				}
				break;
		}

		//filter by status
		if ( ! empty( $settings['filter-order-status'] ) ) {
			$filter_by_status = $settings['filter-order-status'];
			$filter_by_status = '\'' . join( '\' , \'', $filter_by_status ) . '\'';
			$where[]          = 'post_status IN ( ' . $filter_by_status . ' )';
		}
		//filter by billing address
		if ( ! empty( $settings['filter-order-billing-address'] ) ) {
			$billing_address = self::parse_expressions( $settings['filter-order-billing-address'] );
			foreach ( $billing_address as $meta_key => $value ) {
				$table       = 'order_meta' . $meta_key;
				$left_join[] = ' INNER JOIN ' . $wpdb->postmeta . ' AS ' . $table . '  ON ' . $wpdb->posts . '.ID = ' . $table . '.post_id   ';
				foreach ( $value as $condition => $meta_value ) {
					$where[] = ' ( ' . $table . '.meta_key = \'' . $meta_key . '\' AND ' . $table . '.meta_value ' . $condition . ' ( \'' . join( '\' , \'', $meta_value ) . '\' ) )';
				}
			}
		}
		//filter by shipping address
		if ( ! empty( $settings['filter-order-shipping-address'] ) ) {
			$shipping_address = self::parse_expressions( $settings['filter-order-shipping-address'] );
			foreach ( $shipping_address as $meta_key => $value ) {
				$table       = 'order_meta' . $meta_key;
				$left_join[] = ' INNER JOIN ' . $wpdb->postmeta . ' AS ' . $table . '  ON ' . $wpdb->posts . '.ID = ' . $table . '.post_id   ';
				foreach ( $value as $condition => $meta_value ) {
					$where[] = ' ( ' . $table . '.meta_key = \'' . $meta_key . '\' AND ' . $table . '.meta_value ' . $condition . ' ( \'' . join( '\' , \'', $meta_value ) . '\' ) )';
				}
			}
		}

		if ( ! empty( $settings['filter-order-payment-method'] ) ) {
			$left_join[] = ' INNER JOIN ' . $wpdb->postmeta . ' AS order_meta_payment_method  ON ' . $wpdb->posts . '.ID = order_meta_payment_method.post_id   ';
			$where[]     = ' ( order_meta_payment_method.meta_key = \'_payment_method\' AND order_meta_payment_method.meta_value IN ( \'' . join( '\' , \'', $settings['filter-order-payment-method'] ) . '\' ) )';
		}
		if ( ! empty( $settings['filter-order-shipping-method'] ) ) {
			$where_shipping_method = self::get_filter_by_shipping_method( $settings['filter-order-shipping-method'] );
			if ( ! empty( $where_shipping_method ) ) {
				$where[] = ' ' . $wpdb->posts . '.ID IN ( ' . join( ' , ', $where_shipping_method ) . ' )';
			}
		}

		$sql .= join( ' ', $left_join ) . ' WHERE ' . join( ' AND ', $where );
		//sort order id
		switch ( $settings['sort-order'] ) {
			case 'order_id':
				$sql .= ' ORDER BY ' . $wpdb->posts . '.ID ' . $settings['sort-order-in'];
				break;
			case 'order_created':
				$sql .= ' ORDER BY ' . $wpdb->posts . '.post_date ' . $settings['sort-order-in'];
				break;
			case 'order_modification':
				$sql .= ' ORDER BY ' . $wpdb->posts . '.post_modified  ' . $settings['sort-order-in'];
				break;
			default:
				$sql .= ' ORDER BY ' . $wpdb->posts . '.ID DESC';
		}
		$results = $wpdb->get_col( $sql );

		return $results;
	}

	public static function get_data_export( $export_settings, $limit = '' ) {
		$settings        = new  VI_WOO_ORDERS_TRACKING_DATA();
		$results         = array();
		$export_settings = self::make_orders_setting( $export_settings );

		$results['filename']   = $export_settings['filename'];
		$results['header_row'] = $results['content'] = array();
		$results['header_row'] = self::get_header_row( $export_settings['set-fields'] );

		$order_ids = self::get_orders_ids( $export_settings );
		if ( empty( $order_ids ) ) {
			return $order_ids;
		}
		if ( $limit && $limit < count( $order_ids ) ) {
			$order_ids = array_slice( $order_ids, 0, $limit - 1 );
		}

		$countries_info = new WC_Countries();
		$list_countries = $countries_info->get_countries();

		foreach ( $order_ids as $id ) {
			$order                       = new WC_Order( $id );
			$order_shipping_country_code = $order->get_shipping_country();
			$order_billing_country_code  = $order->get_billing_country();
			foreach ( $order->get_items() as $item_id => $item_value ) {
				$order_data = array();
				$item_data  = $item_value->get_data();
				if ( $item_data['variation_id'] ) {
					$product = wc_get_product( $item_data['variation_id'] );
				} else {
					$product = wc_get_product( $item_data['product_id'] );
				}

				$product_categories1 = wp_get_post_terms( $item_data['product_id'], 'product_cat' );
				$product_category    = '';
				$product_categories  = array();
				if ( count( $product_categories1 ) > 0 ) {
					$product_category = $product_categories1[0]->name;
					foreach ( $product_categories1 as $term ) {
						$product_categories [] = $term->name;
					}
				}
				$item_tracking_data    = wc_get_order_item_meta( $item_id, '_vi_wot_order_item_tracking_data', true );
				$current_tracking_data = array(
					'tracking_number' => '',
					'carrier_slug'    => '',
					'carrier_url'     => '',
					'carrier_name'    => '',
					'carrier_type'    => '',
					'time'            => time(),
				);
				if ( $item_tracking_data ) {
					$item_tracking_data    = json_decode( $item_tracking_data, true );
					$current_tracking_data = array_pop( $item_tracking_data );
				}

				$carrier_slug    = $current_tracking_data['carrier_slug'];
				$tracking_number = $current_tracking_data['tracking_number'];
				$carrier_url     = $current_tracking_data['carrier_url'];
				$carrier_name    = $current_tracking_data['carrier_name'];
				$carrier_type    = $current_tracking_data['carrier_type'];
				$carrier         = $settings->get_shipping_carrier_by_slug( $carrier_slug, '', false );
				if ( is_array( $carrier ) && count( $carrier ) ) {
					$carrier_url  = $settings->get_url_tracking( $carrier['url'], $tracking_number, $carrier_slug, $order->get_shipping_postcode() );
					$carrier_name = $carrier['name'];
					$carrier_type = $carrier['carrier_type'];
				}

				foreach ( $results['header_row'] as $field ) {
					if ( ! is_array( $field ) ) {
						continue;
					}
					if ( $field['type'] === 'order_item_meta' ) {
						$order_data[ $field['key'] ] = wc_get_order_item_meta( $item_data['id'], $field['key'], true );
					} elseif ( $field['type'] === 'post_meta' ) {
						$order_data[ $field['key'] ] = get_post_meta( $id, $field['key'], true );
					} elseif ( $field ['type'] === 'wotv_field' ) {
						switch ( $field['key'] ) {
							case 'order_id':
								$order_data[ $field['key'] ] = $id;
								break;
							case 'tracking_number':
								$order_data[ $field['key'] ] = $tracking_number;
								break;
							case 'carrier_slug':
								$order_data[ $field['key'] ] = $carrier_slug;
								break;
							case 'carrier_url':
								$order_data[ $field['key'] ] = $carrier_url;
								break;
							case 'carrier_name':
								$order_data[ $field['key'] ] = $carrier_name;
								break;
							case 'carrier_type':
								$order_data[ $field['key'] ] = $carrier_type;
								break;
							case 'order_status':
								$order_data[ $field['key'] ] = $order->get_status();
								break;
							case 'order_subtotal':
								$order_data[ $field['key'] ] = $order->get_subtotal();
								break;
							case 'modification_date':
								$order_data[ $field['key'] ] = $order->get_date_modified() ? $order->get_date_modified()->format( ' Y-m-d H:i:s' ) : '';
								break;
							case 'create_date':
								$order_data[ $field['key'] ] = $order->get_date_created() ? $order->get_date_created()->format( ' Y-m-d H:i:s' ) : '';
								break;
							case 'shipping_method_title':
								$order_data[ $field['key'] ] = $order->get_shipping_method();
								break;
							case 'shipping_amount':
								$order_data[ $field['key'] ] = $order->get_shipping_total();
								break;
							case 'shipping_country_name':
								$order_data[ $field['key'] ] = $list_countries[ $order_shipping_country_code ];
								break;
							case 'billing_country_name':
								$order_data[ $field['key'] ] = $list_countries[ $order_billing_country_code ];
								break;
							case 'billing_state_name':
								$list_states_billing         = $countries_info->get_states( $order_billing_country_code );
								$order_billing_state_code    = $order->get_billing_state();
								$order_data[ $field['key'] ] = ( $order_billing_state_code && $list_states_billing && is_array( $list_states_billing ) ) ? $list_states_billing[ $order_billing_state_code ] : '';
								break;
							case 'shipping_state_name':
								$list_states_shipping        = $countries_info->get_states( $order_shipping_country_code );
								$order_shipping_state_code   = $order->get_shipping_state();
								$order_data[ $field['key'] ] = ( $order_shipping_state_code && $list_states_shipping && is_array( $list_states_shipping ) ) ? $list_states_shipping[ $order_shipping_state_code ] : '';
								break;
							case 'order_item_id':
								$order_data[ $field['key'] ] = $item_data['id'];
								break;
							case 'order_item_cost':
								$order_data[ $field['key'] ] = $item_data['subtotal'] / $item_data['quantity'];
								break;
							case 'product_name':
								$order_data[ $field['key'] ] = $item_data['name'];
								break;
							case 'product_sku':
								$order_data[ $field['key'] ] = $product ? $product->get_sku() : '';
								break;
							case 'product_link':
								$order_data[ $field['key'] ] = $product ? $product->get_permalink() : '';
								break;
							case 'product_img_link':
								$order_data[ $field['key'] ] = get_the_post_thumbnail_url( $item_data['variation_id'] ? $item_data['variation_id'] : $item_data['product_id'] );
								break;
							case 'product_current_price':
								$order_data[ $field['key'] ] = $product ? $product->get_price() : '';
								break;
							case 'product_short_description':
								$order_data[ $field['key'] ] = $product ? $product->get_short_description() : '';
								break;
							case 'product_description':
								$order_data[ $field['key'] ] = $product ? $product->get_description() : '';
								break;
							case 'product_tag':
								$product_tags       = wp_get_post_terms( $item_data['product_id'], 'product_tag' );
								$product_tags_array = array();
								if ( count( $product_tags ) > 0 ) {
									foreach ( $product_tags as $term ) {
										$product_tags_array[] = $term->name;
									}
								}
								$order_data[ $field['key'] ] = implode( ', ', $product_tags_array );
								break;
							case 'product_category':
								$order_data[ $field['key'] ] = $product_category;
								break;
							case 'product_all_category':
								$order_data[ $field['key'] ] = implode( ', ', $product_categories );
								break;
							default:
						}
					}
				}

				$results['content'][] = $order_data;
			}
		}

		return $results;
	}

	private static function get_order_ids_to_set_fields() {
		global $wpdb;
		$sql          = "SELECT count(*) FROM {$wpdb->posts} Where  post_type = 'shop_order'";
		$count_orders = $wpdb->get_col( $sql );
		$count_orders = $count_orders[0];
		if ( $count_orders > 1000 ) {
			$order_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} Where  post_type = 'shop_order' LIMIT 1000" );
		} else {
			$order_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} Where  post_type = 'shop_order'" );
		}

		return $order_ids;
	}

	private static function get_fields_post_meta() {
		$order_ids = self::get_order_ids_to_set_fields();
		$results   = array();
		global $wpdb;
		if ( $order_ids && is_array( $order_ids ) && count( $order_ids ) ) {
			$order_ids = join( ',', $order_ids );
			$fields    = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} Where  post_id IN ( {$order_ids} )" );
			if ( is_array( $fields ) && count( $fields ) ) {
				for ( $i = 0; $i < count( $fields ); $i ++ ) {
					$key = $fields[ $i ];
					if ( in_array( $key, array(
						'_billing_country',
						'_billing_state',
						'_shipping_country',
						'_shipping_state'
					) ) ) {
						$title     = ucwords( str_replace( array( '_billing_', '_shipping_' ), array(
								'( Billing )',
								'( Shipping )'
							), $key ) ) . ' Code';
						$results[] = array(
							'type'  => 'post_meta',
							'key'   => $key,
							'title' => $title,
						);
						continue;
					}
					$item = trim( str_replace( '_', ' ', $key ) );
					if ( strpos( $item, 'billing' ) === 0 ) {
						$item = '( Billing ) ' . $item;
					} elseif ( strpos( $item, 'shipping' ) === 0 ) {
						$item = '( Shipping ) ' . $item;
					} else {
						$item = '( Order ) ' . $item;
					}
					$item      = ucwords( $item );
					$results[] = array(
						'type'  => 'post_meta',
						'key'   => $key,
						'title' => $item,
					);
				}

				$field_other = array(
					array(
						'type'  => 'wotv_field',
						'key'   => 'order_subtotal',
						'title' => __( '( Order ) Order Subtotal', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'modification_date',
						'title' => __( '( Order ) Modification date', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'create_date',
						'title' => __( '( Order ) Create date', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'shipping_method_title',
						'title' => __( '( Shipping ) Shipping Method Title', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'shipping_amount',
						'title' => __( '( Shipping ) Shipping Amount', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'shipping_country_name',
						'title' => __( '( Shipping ) Country Name', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'billing_country_name',
						'title' => __( '( Billing ) Country Name', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'billing_state_name',
						'title' => __( '( Billing ) State Name', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'shipping_state_name',
						'title' => __( '( Shipping ) State Name', 'woo-orders-tracking' ),
					),
				);
				$results     = array_merge( $field_other, $results );
			}
		}

		return $results;
	}

	private static function get_fields_order_line_item() {
		$order_ids = self::get_order_ids_to_set_fields();
		$results   = array();
		global $wpdb;
		if ( $order_ids && is_array( $order_ids ) && count( $order_ids ) ) {
			$order_ids = join( ',', $order_ids );
			$fields    = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->prefix}woocommerce_order_itemmeta as table1  JOIN {$wpdb->prefix}woocommerce_order_items  as table2 ON table1.order_item_id = table2.order_item_id  Where table2.order_item_type ='line_item' AND table2.order_id  IN ( {$order_ids} )" );
			if ( is_array( $fields ) && count( $fields ) ) {
				for ( $i = 0; $i < count( $fields ); $i ++ ) {
					$key = $fields[ $i ];
					if ( in_array( $key, array( 'Items', '_line_tax_data' ) ) ) {
						continue;
					}
					if ( $key === '_vi_order_item_carrier_id' ) {
						$results[] = array(
							'type'  => 'order_item_meta',
							'key'   => '_vi_order_item_carrier_id',
							'title' => __( '( Order Item ) Carrier Slug', 'woo-orders-tracking' ),
						);
						continue;
					}
					$item      = trim( str_replace( array( '_vi_order_item_', '_' ), array( ' ', ' ' ), $key ) );
					$item      = '( Order Item )' . ucwords( $item );
					$results[] = array(
						'type'  => 'order_item_meta',
						'key'   => $key,
						'title' => $item,
					);
				}
				$t       = array(
					array(
						'type'  => 'wotv_field',
						'key'   => 'order_item_id',
						'title' => __( '( Order Item ) Order Item ID', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'order_item_cost',
						'title' => __( '( Order Item ) Order Cost', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_name',
						'title' => __( '( Order Item ) Product Name', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_sku',
						'title' => __( '( Order Item ) Product Sku', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_link',
						'title' => __( '( Order Item ) Product Link', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_img_link',
						'title' => __( '( Order Item ) Product Image Link', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_current_price',
						'title' => __( '( Order Item ) Product Current Price', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_short_description',
						'title' => __( '( Order Item ) Product Short Description', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_description',
						'title' => __( '( Order Item ) Product Description', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_tag',
						'title' => __( '( Order Item ) Product Tags', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_category',
						'title' => __( '( Order Item ) Product Category', 'woo-orders-tracking' ),
					),
					array(
						'type'  => 'wotv_field',
						'key'   => 'product_all_category',
						'title' => __( '( Order Item ) Product Categories', 'woo-orders-tracking' ),
					),
				);
				$results = array_merge( $t, $results );
			}
		}

		return $results;
	}

	public static function get_fields_to_select() {
		$field_other      = array(
			array(
				'type'  => 'wotv_field',
				'key'   => 'order_id',
				'title' => __( '( Order ) Order ID', 'woo-orders-tracking' ),
			),
			array(
				'type'  => 'wotv_field',
				'key'   => 'order_status',
				'title' => __( '( Order ) Order Status', 'woo-orders-tracking' ),
			),
			array(
				'type'  => 'wotv_field',
				'key'   => 'tracking_number',
				'title' => __( '( Order Item ) Tracking Number', 'woo-orders-tracking' ),
			),
			array(
				'type'  => 'wotv_field',
				'key'   => 'carrier_slug',
				'title' => __( '( Order Item ) Carrier Slug', 'woo-orders-tracking' ),
			),
			array(
				'type'  => 'wotv_field',
				'key'   => 'carrier_url',
				'title' => __( '( Order Item ) Tracking URL', 'woo-orders-tracking' ),
			),
			array(
				'type'  => 'wotv_field',
				'key'   => 'carrier_name',
				'title' => __( '( Order Item ) Carrier Name', 'woo-orders-tracking' ),
			),
		);
		$field_post_meta  = self::get_fields_post_meta();
		$field_order_item = self::get_fields_order_line_item();
		$results          = array_merge( $field_other, $field_post_meta, $field_order_item );

		return $results;
	}
}