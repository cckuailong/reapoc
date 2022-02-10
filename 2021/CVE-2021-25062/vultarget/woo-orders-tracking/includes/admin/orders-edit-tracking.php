<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_ADMIN_ORDERS_EDIT_TRACKING {
	private $settings;
	protected $carriers;

	public function __construct() {
		$this->settings                      = new VI_WOO_ORDERS_TRACKING_DATA();
		$this->carriers                      = array();
		$this->enqueue_action();
	}

	public static function set( $name, $set_name = false ) {
		return VI_WOO_ORDERS_TRACKING_DATA::set( $name, $set_name );
	}

	public function enqueue_action() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ), 9999 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'woocommerce_hidden_order_itemmeta' ) );
		add_action( 'woocommerce_after_order_itemmeta', array( $this, 'woocommerce_after_order_itemmeta' ), 10, 3 );
		add_action( 'wp_ajax_wotv_save_track_info_item', array( $this, 'wotv_save_track_info_item' ) );
		add_action( 'wp_ajax_wotv_save_track_info_all_item', array( $this, 'wotv_save_track_info_all_item' ) );
		add_action( 'wp_ajax_vi_woo_orders_tracking_add_tracking_to_paypal', array( $this, 'add_tracking_to_paypal' ) );
		add_action( 'add_meta_boxes', array( $this, 'order_details_add_meta_boxes' ) );
	}

	/**
	 * @throws Exception
	 */
	public function add_tracking_to_paypal() {
		$action_nonce = isset( $_POST['action_nonce'] ) ? wp_unslash( sanitize_text_field( $_POST['action_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $action_nonce, 'vi_wot_item_action_nonce' ) ) {
			return;
		}
		$response = array(
			'status'                 => 'error',
			'message'                => __( 'Cannot add tracking to PayPal', 'woo-orders-tracking' ),
			'message_content'        => '',
			'paypal_added_trackings' => '',
			'paypal_button_title'    => '',
		);
		$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
		$item_id  = isset( $_POST['item_id'] ) ? sanitize_text_field( $_POST['item_id'] ) : '';
		if ( $order_id && $item_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$transID                = $order->get_transaction_id();
				$paypal_method          = $order->get_payment_method();
				$item_tracking_data     = wc_get_order_item_meta( $item_id, '_vi_wot_order_item_tracking_data', true );
				$paypal_added_trackings = get_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', true );
				if ( ! $paypal_added_trackings ) {
					$paypal_added_trackings = array();
				}
				if ( $transID && $paypal_method && $item_tracking_data ) {
					$item_tracking_data    = json_decode( $item_tracking_data, true );
					$current_tracking_data = array_pop( $item_tracking_data );
					if ( $current_tracking_data['tracking_number'] ) {
						$response['message_content'] = '<div>' . sprintf( __( 'Tracking number: %s', 'woo-orders-tracking' ), $current_tracking_data['tracking_number'] ) . '</div>';
					}
					if ( ! in_array( $current_tracking_data['tracking_number'], $paypal_added_trackings ) ) {
						$send_paypal       = array(
							array(
								'trans_id'        => $transID,
								'carrier_name'    => $current_tracking_data['carrier_name'],
								'tracking_number' => $current_tracking_data['tracking_number'],
							)
						);
						$result_add_paypal = $this->add_trackinfo_to_paypal( $send_paypal, $paypal_method );
						if ( $result_add_paypal['status'] === 'error' ) {
							$response['message'] = empty( $result_add_paypal['data'] ) ? __( 'Cannot add tracking to PayPal', 'woo-orders-tracking' ) : $result_add_paypal['data'];
						} else {
							$response['status']       = 'success';
							$response['message']      = __( 'Add Tracking to PayPal successfully', 'woo-orders-tracking' );
							$paypal_added_trackings[] = $current_tracking_data['tracking_number'];
							update_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', $paypal_added_trackings );
							$response['paypal_added_trackings'] = implode( ',', array_filter( $paypal_added_trackings ) );
							$response['paypal_button_title']    = __( 'This tracking number was added to PayPal', 'woo-orders-tracking' );
						}
					} else {
						$response['status']              = 'success';
						$response['message']             = __( 'Add Tracking to PayPal successfully', 'woo-orders-tracking' );
						$response['paypal_button_title'] = __( 'This tracking number was added to PayPal', 'woo-orders-tracking' );
					}
				}
			}
		}

		wp_send_json( $response );
	}

	/**
	 * @throws Exception
	 */
	public function wotv_save_track_info_item() {
		$action_nonce = isset( $_POST['action_nonce'] ) ? wp_unslash( sanitize_text_field( $_POST['action_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $action_nonce, 'vi_wot_item_action_nonce' ) ) {
			return;
		}
		$order_id                 = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
		$item_id                  = isset( $_POST['item_id'] ) ? sanitize_text_field( $_POST['item_id'] ) : '';
		$item_name                = isset( $_POST['item_name'] ) ? sanitize_text_field( $_POST['item_name'] ) : '';
		$change_order_status      = isset( $_POST['change_order_status'] ) ? sanitize_text_field( $_POST['change_order_status'] ) : '';
		$send_mail                = isset( $_POST['send_mail'] ) ? sanitize_text_field( $_POST['send_mail'] ) : '';
		$add_to_paypal            = isset( $_POST['add_to_paypal'] ) ? sanitize_text_field( $_POST['add_to_paypal'] ) : '';
		$transID                  = isset( $_POST['transID'] ) ? sanitize_text_field( $_POST['transID'] ) : '';
		$paypal_method            = isset( $_POST['paypal_method'] ) ? sanitize_text_field( $_POST['paypal_method'] ) : '';
		$tracking_number          = isset( $_POST['tracking_code'] ) ? sanitize_text_field( $_POST['tracking_code'] ) : '';
		$carrier_slug             = isset( $_POST['carrier_id'] ) ? sanitize_text_field( $_POST['carrier_id'] ) : '';
		$carrier_name             = isset( $_POST['carrier_name'] ) ? sanitize_text_field( $_POST['carrier_name'] ) : '';
		$add_new_carrier          = isset( $_POST['add_new_carrier'] ) ? sanitize_text_field( $_POST['add_new_carrier'] ) : '';
		$carrier_type             = '';
		$order                    = wc_get_order( $order_id );
		$settings                 = $this->settings->get_params();
		$settings['order_status'] = $change_order_status;
		$response                 = array(
			'status'                   => 'success',
			'paypal_status'            => 'success',
			'paypal_message'           => '',
			'message'                  => __( 'Tracking saved', 'woo-orders-tracking' ),
			'detail'                   => '',
			'tracking_code'            => $tracking_number,
			'tracking_url'             => '',
			'tracking_url_show'        => '',
			'carrier_name'             => $carrier_name,
			'carrier_id'               => $carrier_slug,
			'carrier_type'             => $carrier_type,
			'item_id'                  => $item_id,
			'change_order_status'      => $change_order_status,
			'paypal_button_class'      => '',
			'paypal_button_title'      => '',
			'paypal_added_trackings'   => '',
			'tracking_service'         => '',
			'tracking_service_status'  => 'success',
			'tracking_service_message' => '',
			'digital_delivery'         => 0,
		);
		$settings['email_enable'] = $send_mail === 'yes' ? 1 : 0;
		$tracking_more_slug       = '';
		$digital_delivery         = 0;
		if ( $add_new_carrier ) {
			$tracking_url     = isset( $_POST['tracking_url'] ) ? sanitize_text_field( $_POST['tracking_url'] ) : '';
			$shipping_country = isset( $_POST['shipping_country'] ) ? sanitize_text_field( $_POST['shipping_country'] ) : '';
			$carrier_url      = $tracking_url;
			if ( $carrier_name && $tracking_url && $shipping_country ) {
				$custom_carriers_list = json_decode( $this->settings->get_params( 'custom_carriers_list' ), true );
				$custom_carrier       = array(
					'name'    => $carrier_name,
					'slug'    => 'custom_' . time(),
					'url'     => $tracking_url,
					'country' => $shipping_country,
					'type'    => 'custom',
				);
				$carrier_slug         = $custom_carrier['slug'];

				$custom_carriers_list[]           = $custom_carrier;
				$settings['custom_carriers_list'] = json_encode( $custom_carriers_list );
				$carrier_type                     = 'custom-carrier';
			} else {
				$response['status']  = 'error';
				$response['message'] = __( 'Not enough information', 'woo-orders-tracking' );
				wp_send_json( $response );
			}
		} else {
			$carrier = $this->settings->get_shipping_carrier_by_slug( $carrier_slug );
			if ( is_array( $carrier ) && count( $carrier ) ) {
				$carrier_url        = $carrier['url'];
				$carrier_name       = $carrier['name'];
				$carrier_type       = $carrier['carrier_type'];
				$tracking_more_slug = isset( $carrier['tracking_more_slug'] ) ? $carrier['tracking_more_slug'] : '';
				if ( isset( $carrier['digital_delivery'] ) ) {
					$digital_delivery             = $carrier['digital_delivery'];
					$response['digital_delivery'] = $digital_delivery;
					if ( $digital_delivery == 1 ) {
						$tracking_number = '';
					}
				}
			} else {
				$carrier_url = '';
			}
		}

		update_option( 'woo_orders_tracking_settings', $settings );

		if ( ! $order_id || ! $item_id || ( ! $tracking_number && $digital_delivery != 1 ) || ! $carrier_slug || ! $carrier_type ) {
			$response['status'] = 'error';
			wp_send_json( $response );
		}
		$now                   = time();
		$item_tracking_data    = wc_get_order_item_meta( $item_id, '_vi_wot_order_item_tracking_data', true );
		$current_tracking_data = array(
			'tracking_number' => '',
			'carrier_slug'    => '',
			'carrier_url'     => '',
			'carrier_name'    => '',
			'carrier_type'    => '',
			'time'            => $now,
		);
		$tracking_change       = true;
		if ( $item_tracking_data ) {
			$item_tracking_data = json_decode( $item_tracking_data, true );
			if ( $digital_delivery == 1 ) {
				$current_tracking_data = array_pop( $item_tracking_data );
				if ( ! empty( $current_tracking_data['tracking_number'] ) || empty( $current_tracking_data['carrier_slug'] ) || empty( $current_tracking_data['carrier_name'] ) || empty( $current_tracking_data['carrier_url'] ) ) {
					$item_tracking_data[] = $current_tracking_data;
				} elseif ( $current_tracking_data['carrier_url'] == $carrier_url ) {
					$tracking_change = false;
				}
			} else {
				foreach ( $item_tracking_data as $order_tracking_data_k => $order_tracking_data_v ) {
					$current_tracking_data = $order_tracking_data_v;
					if ( $current_tracking_data['tracking_number'] == $tracking_number ) {
						if ( $current_tracking_data['carrier_url'] == $carrier_url && $order_tracking_data_k === ( count( $item_tracking_data ) - 1 ) ) {
							$tracking_change = false;
						}
						unset( $item_tracking_data[ $order_tracking_data_k ] );
						break;
					}
				}
			}
			$item_tracking_data = array_values( $item_tracking_data );
		} else {
			$item_tracking_data = array();
		}

		$current_tracking_data['tracking_number'] = $tracking_number;
		$current_tracking_data['carrier_slug']    = $carrier_slug;
		$current_tracking_data['carrier_url']     = $carrier_url;
		$current_tracking_data['carrier_name']    = $carrier_name;
		$current_tracking_data['carrier_type']    = $carrier_type;

		do_action( 'vi_woo_orders_tracking_single_edit_tracking_change', $tracking_change, $current_tracking_data, $item_id, $order_id, $response );

		$response['carrier_id']   = $carrier_slug;
		$response['carrier_type'] = $carrier_type;
		$response['carrier_url']  = $carrier_url;

		$paypal_added_trackings = get_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', true );
		if ( ! $paypal_added_trackings ) {
			$paypal_added_trackings = array();
		}
		if ( $add_to_paypal === 'yes' && $transID && $paypal_method && ! in_array( $tracking_number, $paypal_added_trackings ) ) {
			$send_paypal       = array(
				array(
					'trans_id'        => $transID,
					'carrier_name'    => $carrier_name,
					'tracking_number' => $tracking_number,
				)
			);
			$result_add_paypal = $this->add_trackinfo_to_paypal( $send_paypal, $paypal_method );
			if ( $result_add_paypal['status'] === 'error' ) {
				$response['paypal_status']  = 'error';
				$response['paypal_message'] = empty( $result_add_paypal['data'] ) ? __( 'Cannot add tracking to PayPal', 'woo-orders-tracking' ) : $result_add_paypal['data'];
			} else {
				$paypal_added_trackings[] = $tracking_number;
				update_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', $paypal_added_trackings );
			}
		}
		$response['paypal_added_trackings'] = implode( ', ', array_filter( $paypal_added_trackings ) );
		if ( ! in_array( $tracking_number, $paypal_added_trackings ) ) {
			$response['paypal_button_class'] = 'active';
			$response['paypal_button_title'] = __( 'Add this tracking number to PayPal', 'woo-orders-tracking' );
		} else {
			$response['paypal_button_class'] = 'inactive';
			$response['paypal_button_title'] = __( 'This tracking number was added to PayPal', 'woo-orders-tracking' );
		}
		$tracking_url_import     = $this->settings->get_url_tracking( $carrier_url, $tracking_number, $carrier_slug, $order->get_shipping_postcode() );
		$result_refresh_database = array();
		if ( $carrier_type === 'service-carrier' ) {
			$send_data               = array(
				'carrier_id'            => $tracking_more_slug,
				'carrier_name'          => $carrier_name,
				'shipping_country_code' => $order->get_shipping_country(),
				'tracking_code'         => $tracking_number,
				'order_id'              => $order_id,
				'customer_phone'        => $order->get_billing_phone(),
				'customer_email'        => $order->get_billing_email(),
				'customer_name'         => $order->get_formatted_billing_full_name(),
			);
			$result_refresh_database = VI_WOO_ORDERS_TRACKING_TABLE_TRACKING::refresh_track_info_database( $send_data, true );
		} else {
			if ( $tracking_change && 'yes' === $send_mail ) {
				$imported = array(
					array(
						'order_item_id'   => $item_id,
						'order_item_name' => $item_name,
						'tracking_number' => $tracking_number,
						'tracking_url'    => $tracking_url_import,
						'carrier_name'    => $carrier_name,
					)
				);
				VI_WOO_ORDERS_TRACKING_ADMIN_IMPORT_CSV::send_mail( $order_id, $imported, true );
			}
		}
		$item_tracking_data[] = $current_tracking_data;
		wc_update_order_item_meta( $item_id, '_vi_wot_order_item_tracking_data', json_encode( $item_tracking_data ) );
		$response['tracking_url_show']   = $tracking_url_import;
		$response['detail_add_database'] = isset( $result_refresh_database['track_info'] ) ? $result_refresh_database['track_info'] : '';
		/*Make sure order item tracking is saved before trigger status change*/
		if ( $change_order_status ) {
			$order->update_status( substr( $change_order_status, 3 ) );
			$order->save();
		}
		wp_send_json( $response );
	}

	/**
	 * @throws Exception
	 */
	public function wotv_save_track_info_all_item() {
		$action_nonce = isset( $_POST['action_nonce'] ) ? wp_unslash( sanitize_text_field( $_POST['action_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $action_nonce, 'vi_wot_item_action_nonce' ) ) {
			return;
		}
		$order_id                 = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
		$change_order_status      = isset( $_POST['change_order_status'] ) ? sanitize_text_field( $_POST['change_order_status'] ) : '';
		$send_mail                = isset( $_POST['send_mail'] ) ? sanitize_text_field( $_POST['send_mail'] ) : '';
		$add_to_paypal            = isset( $_POST['add_to_paypal'] ) ? sanitize_text_field( $_POST['add_to_paypal'] ) : '';
		$transID                  = isset( $_POST['transID'] ) ? sanitize_text_field( $_POST['transID'] ) : '';
		$paypal_method            = isset( $_POST['paypal_method'] ) ? sanitize_text_field( $_POST['paypal_method'] ) : '';
		$tracking_number          = isset( $_POST['tracking_code'] ) ? sanitize_text_field( $_POST['tracking_code'] ) : '';
		$carrier_slug             = isset( $_POST['carrier_id'] ) ? sanitize_text_field( $_POST['carrier_id'] ) : '';
		$carrier_name             = isset( $_POST['carrier_name'] ) ? sanitize_text_field( $_POST['carrier_name'] ) : '';
		$add_new_carrier          = isset( $_POST['add_new_carrier'] ) ? sanitize_text_field( $_POST['add_new_carrier'] ) : '';
		$carrier_type             = '';
		$order                    = wc_get_order( $order_id );
		$settings                 = $this->settings->get_params();
		$settings['order_status'] = $change_order_status;
		$response                 = array(
			'status'                   => 'success',
			'paypal_status'            => 'success',
			'paypal_message'           => __( '', 'woo-orders-tracking' ),
			'message'                  => __( 'Tracking saved', 'woo-orders-tracking' ),
			'detail'                   => '',
			'tracking_code'            => $tracking_number,
			'tracking_url'             => '',
			'tracking_url_show'        => '',
			'carrier_name'             => $carrier_name,
			'carrier_id'               => $carrier_slug,
			'carrier_type'             => $carrier_type,
			'item_id'                  => '',
			'change_order_status'      => $change_order_status,
			'paypal_button_class'      => '',
			'paypal_button_title'      => '',
			'paypal_added_trackings'   => '',
			'tracking_service'         => '',
			'tracking_service_status'  => 'success',
			'tracking_service_message' => '',
			'digital_delivery'         => 0,
		);
		$settings['email_enable'] = $send_mail === 'yes' ? 1 : 0;
		$tracking_more_slug       = '';
		$digital_delivery         = 0;
		if ( $add_new_carrier ) {
			$carrier_name     = isset( $_POST['carrier_name'] ) ? sanitize_text_field( $_POST['carrier_name'] ) : '';
			$tracking_url     = isset( $_POST['tracking_url'] ) ? sanitize_text_field( $_POST['tracking_url'] ) : '';
			$shipping_country = isset( $_POST['shipping_country'] ) ? sanitize_text_field( $_POST['shipping_country'] ) : '';
			$carrier_url      = $tracking_url;
			if ( $carrier_name && $tracking_url && $shipping_country ) {
				$custom_carriers_list             = json_decode( $this->settings->get_params( 'custom_carriers_list' ), true );
				$custom_carrier                   = array(
					'name'    => $carrier_name,
					'slug'    => 'custom_' . time(),
					'url'     => $tracking_url,
					'country' => $shipping_country,
					'type'    => 'custom',
				);
				$carrier_slug                     = $custom_carrier['slug'];
				$custom_carriers_list[]           = $custom_carrier;
				$settings['custom_carriers_list'] = json_encode( $custom_carriers_list );
				update_option( 'woo_orders_tracking_settings', $settings );
				$carrier_type = 'custom-carrier';
			} else {
				update_option( 'woo_orders_tracking_settings', $settings );
				wp_send_json(
					array(
						'status'  => 'error',
						'message' => __( 'Not enough information', 'woo-orders-tracking' ),
						'details' => array(
							'carrier_name'     => $carrier_name,
							'tracking_url'     => $tracking_url,
							'shipping_country' => $shipping_country
						)
					)
				);
			}
		} else {
			update_option( 'woo_orders_tracking_settings', $settings );
			$carrier = $this->settings->get_shipping_carrier_by_slug( $carrier_slug );
			if ( is_array( $carrier ) && count( $carrier ) ) {
				$carrier_url        = $carrier['url'];
				$carrier_name       = $carrier['name'];
				$carrier_type       = $carrier['carrier_type'];
				$tracking_more_slug = isset( $carrier['tracking_more_slug'] ) ? $carrier['tracking_more_slug'] : '';
				if ( isset( $carrier['digital_delivery'] ) ) {
					$digital_delivery             = $carrier['digital_delivery'];
					$response['digital_delivery'] = $digital_delivery;
					if ( $digital_delivery == 1 ) {
						$tracking_number = '';
					}
				}
			} else {
				$carrier_url = '';
			}
		}
		$response['carrier_id']   = $carrier_slug;
		$response['carrier_type'] = $carrier_type;
		$response['carrier_url']  = $carrier_url;
		if ( ! $order_id || ( ! $tracking_number && $digital_delivery != 1 ) || ! $carrier_slug || ! $carrier_type ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'Not enough information', 'woo-orders-tracking' ),
				)
			);
		}
		$paypal_added_trackings = get_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', true );
		if ( ! $paypal_added_trackings ) {
			$paypal_added_trackings = array();
		}
		if ( $add_to_paypal === 'yes' && $transID && $paypal_method && ! in_array( $tracking_number, $paypal_added_trackings ) ) {
			$send_paypal       = array(
				array(
					'trans_id'        => $transID,
					'carrier_name'    => $carrier_name,
					'tracking_number' => $tracking_number,
				)
			);
			$result_add_paypal = $this->add_trackinfo_to_paypal( $send_paypal, $paypal_method );
			if ( $result_add_paypal['status'] === 'error' ) {
				$response['paypal_status']  = 'error';
				$response['paypal_message'] = empty( $result_add_paypal['data'] ) ? __( 'Cannot add tracking to PayPal', 'woo-orders-tracking' ) : $result_add_paypal['data'];
			} else {
				$paypal_added_trackings[] = $tracking_number;
				update_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', $paypal_added_trackings );
			}
		}
		$response['paypal_added_trackings'] = implode( ', ', array_filter( $paypal_added_trackings ) );
		if ( ! in_array( $tracking_number, $paypal_added_trackings ) ) {
			$response['paypal_button_class'] = 'active';
			$response['paypal_button_title'] = __( 'Add this tracking number to PayPal', 'woo-orders-tracking' );
		} else {
			$response['paypal_button_class'] = 'inactive';
			$response['paypal_button_title'] = __( 'This tracking number was added to PayPal', 'woo-orders-tracking' );
		}
		global $wpdb;

		$query       = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %s AND order_item_type='line_item'", $order_id );
		$line_items  = $wpdb->get_results( $query, ARRAY_A );
		$now         = time();
		$last_update = $now;
		if ( $line_items_count = count( $line_items ) ) {
			$send_mail_array         = array();
			$tracking_url_import     = $this->settings->get_url_tracking( $carrier_url, $tracking_number, $carrier_slug, $order->get_shipping_postcode() );
			$tracking_change_count   = 0;
			$status                  = '';
			$result_refresh_database = '';
			if ( $carrier_type === 'service-carrier' ) {
				$send_data               = array(
					'carrier_id'            => $tracking_more_slug,
					'carrier_name'          => $carrier_name,
					'shipping_country_code' => $order->get_shipping_country(),
					'tracking_code'         => $tracking_number,
					'order_id'              => $order_id,
					'customer_phone'        => $order->get_billing_phone(),
					'customer_email'        => $order->get_billing_email(),
					'customer_name'         => $order->get_formatted_billing_full_name(),
				);
				$result_refresh_database = VI_WOO_ORDERS_TRACKING_TABLE_TRACKING::refresh_track_info_database( $send_data, true );
			}
			for ( $i = 0; $i < $line_items_count; $i ++ ) {
				$line_item       = $line_items[ $i ];
				$item_id         = isset( $line_item['order_item_id'] ) ? $line_item['order_item_id'] : '';
				$order_item_name = isset( $line_item['order_item_name'] ) ? $line_item['order_item_name'] : '';
				if ( $item_id ) {
					$item_tracking_data    = wc_get_order_item_meta( $item_id, '_vi_wot_order_item_tracking_data', true );
					$current_tracking_data = array(
						'tracking_number' => '',
						'carrier_slug'    => '',
						'carrier_url'     => '',
						'carrier_name'    => '',
						'carrier_type'    => '',
						'time'            => $now,
					);
					$tracking_change       = true;
					if ( $item_tracking_data ) {
						$item_tracking_data = json_decode( $item_tracking_data, true );
						if ( $digital_delivery == 1 ) {
							$current_tracking_data = array_pop( $item_tracking_data );
							if ( ! empty( $current_tracking_data['tracking_number'] ) || empty( $current_tracking_data['carrier_slug'] ) || empty( $current_tracking_data['carrier_name'] ) || empty( $current_tracking_data['carrier_url'] ) ) {
								$item_tracking_data[] = $current_tracking_data;
							} elseif ( $current_tracking_data['carrier_url'] == $carrier_url ) {
								$tracking_change = false;
							}
						} else {
							foreach ( $item_tracking_data as $order_tracking_data_k => $order_tracking_data_v ) {
								$current_tracking_data = $order_tracking_data_v;
								if ( $current_tracking_data['tracking_number'] == $tracking_number ) {
									if ( $current_tracking_data['carrier_url'] == $carrier_url && $order_tracking_data_k === ( count( $item_tracking_data ) - 1 ) ) {
										$tracking_change = false;
									}
									unset( $item_tracking_data[ $order_tracking_data_k ] );
									break;
								}
							}
						}

						$item_tracking_data = array_values( $item_tracking_data );
					} else {
						$item_tracking_data = array();
					}

					$current_tracking_data['status']          = $status;
					$current_tracking_data['last_update']     = $last_update;
					$current_tracking_data['tracking_number'] = $tracking_number;
					$current_tracking_data['carrier_slug']    = $carrier_slug;
					$current_tracking_data['carrier_url']     = $carrier_url;
					$current_tracking_data['carrier_name']    = $carrier_name;
					$current_tracking_data['carrier_type']    = $carrier_type;
					$item_tracking_data[]                     = $current_tracking_data;
					wc_update_order_item_meta( $item_id, '_vi_wot_order_item_tracking_data', json_encode( $item_tracking_data ) );
					$send_mail_array[] = array(
						'order_item_name' => $order_item_name,
						'tracking_number' => $tracking_number,
						'tracking_url'    => $tracking_url_import,
						'carrier_name'    => $carrier_name,
					);
					if ( $tracking_change ) {
						$tracking_change_count ++;
					}
					do_action( 'vi_woo_orders_tracking_single_edit_tracking_change', $tracking_change, $current_tracking_data, $item_id, $order_id, $response );
				}
			}
			if ( 'yes' === $send_mail && count( $send_mail_array ) ) {
				VI_WOO_ORDERS_TRACKING_ADMIN_IMPORT_CSV::send_mail( $order_id, $send_mail_array, true );
			}
			$response['tracking_url_show']   = $tracking_url_import;
			$response['change_order_status'] = $change_order_status;
			$response['detail_add_database'] = isset( $result_refresh_database['track_info'] ) ? $result_refresh_database['track_info'] : '';
		} else {
			$response['status']  = 'error';
			$response['message'] = __( 'Order items not matched', 'woo-orders-tracking' );
		}
		if ( $change_order_status ) {
			$order->update_status( substr( $change_order_status, 3 ) );
			$order->save();
		}
		wp_send_json( $response );
	}

	public function tracking_exists( $tracking_data, $tracking_number ) {
		$key = - 1;
		foreach ( $tracking_data as $data_key => $data ) {
			if ( $data['tracking_number'] == $tracking_number ) {
				$key = $data_key;
				break;
			}
		}

		return $key;
	}

	public function add_trackinfo_to_paypal( $send_paypal, $paypal_method ) {
		$available_paypal_method = $this->settings->get_params( 'paypal_method' );
		$i                       = array_search( $paypal_method, $available_paypal_method );
		if ( is_numeric( $i ) ) {
			$sandbox = $this->settings->get_params( 'paypal_sandbox_enable' )[ $i ] ? true : false;
			if ( $sandbox ) {
				$client_id = $this->settings->get_params( 'paypal_client_id_sandbox' )[ $i ];
				$secret    = $this->settings->get_params( 'paypal_secret_sandbox' )[ $i ];
			} else {
				$client_id = $this->settings->get_params( 'paypal_client_id_live' )[ $i ];
				$secret    = $this->settings->get_params( 'paypal_secret_live' )[ $i ];
			}
			$result = VI_WOO_ORDERS_TRACKING_ADMIN_PAYPAL::add_tracking_number( $client_id, $secret, $send_paypal, $sandbox );
		} else {
			$result = array(
				'status' => 'error',
				'data'   => __( 'PayPal method not found', 'woo-orders-tracking' )
			);
		}

		return $result;
	}

	public function orders_tracking_edit_tracking_footer() {
		$order_id = isset( $_REQUEST['post'] ) ? wp_unslash( $_REQUEST['post'] ) : '';
		if ( $order_id ) {
			$this->settings = new VI_WOO_ORDERS_TRACKING_DATA();
			$countries      = new WC_Countries();
			$countries      = $countries->get_countries();
			$order          = wc_get_order( $order_id );
			if ( $order ) {
				$transID       = $order->get_transaction_id();
				$paypal_method = $order->get_payment_method();
				?>
                <div class="<?php esc_attr_e( self::set( array( 'edit-tracking-container', 'hidden' ) ) ) ?>">
					<?php wp_nonce_field( 'vi_wot_item_action_nonce', '_vi_wot_item_nonce' ) ?>
                    <div class="<?php esc_attr_e( self::set( 'overlay' ) ) ?>"></div>
                    <div class="<?php esc_attr_e( self::set( 'edit-tracking-content' ) ) ?>">
                        <div class="<?php esc_attr_e( self::set( 'edit-tracking-content-header' ) ) ?>">
                            <h2><?php esc_html_e( 'Edit tracking', 'woo-orders-tracking' ) ?></h2>
                            <span class="<?php esc_attr_e( self::set( 'edit-tracking-close' ) ) ?>"></span>
                        </div>
                        <div class="<?php esc_attr_e( self::set( 'edit-tracking-content-body' ) ) ?>">

                            <div class="<?php esc_attr_e( self::set( array(
								'edit-tracking-content-body-row',
								'edit-tracking-content-body-row-error',
								'hidden'
							) ) ) ?>">
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-send-email-wrap' ) ) ?>">
                                    <p class="description" style="color: red;"></p>
                                </div>

                            </div>
                            <div class="<?php esc_attr_e( self::set( array( 'edit-tracking-content-body-row' ) ) ) ?>">
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-number-wrap' ) ) ?>">
                                    <label for="<?php esc_attr_e( self::set( 'edit-tracking-number' ) ) ?>">
										<?php esc_html_e( 'Tracking number', 'woo-orders-tracking' ) ?>
                                    </label>
                                    <input type="text"
                                           id="<?php esc_attr_e( self::set( 'edit-tracking-number' ) ) ?>"
                                           class="<?php esc_attr_e( self::set( 'edit-tracking-number' ) ) ?>"
                                    >
                                </div>
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-carrier-wrap' ) ) ?>">
                                    <label for="<?php esc_attr_e( self::set( 'edit-tracking-carrier' ) ) ?>"><?php esc_html_e( 'Tracking carrier', 'woo-orders-tracking' ) ?></label>
                                    <select name="<?php esc_attr_e( self::set( 'edit-tracking-carrier' ) ) ?>"
                                            id="<?php esc_attr_e( self::set( 'edit-tracking-carrier' ) ) ?>"
                                            class=" <?php esc_attr_e( self::set( 'edit-tracking-carrier' ) ) ?>">
                                        <option value="shipping-carriers"
                                                selected><?php esc_html_e( 'Existing Carriers', 'woo-orders-tracking' ) ?></option>
                                        <option value="other"><?php esc_html_e( 'Add new', 'woo-orders-tracking' ) ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="<?php esc_attr_e( self::set( array(
								'edit-tracking-content-body-row',
								'edit-tracking-content-body-row-shipping-carrier'
							) ) ) ?>">
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-shipping-carrier-wrap' ) ) ?>">
                                    <label for="<?php esc_attr_e( self::set( 'edit-tracking-shipping-carrier' ) ) ?>"><?php esc_html_e( 'Shipping carrier', 'woo-orders-tracking' ) ?></label>
                                    <select name="<?php esc_attr_e( self::set( 'edit-tracking-shipping-carrier' ) ) ?>"
                                            id="<?php esc_attr_e( self::set( 'edit-tracking-shipping-carrier' ) ) ?>"
                                            class="<?php esc_attr_e( self::set( 'edit-tracking-shipping-carrier' ) ) ?> select2-hidden-accessible">

                                    </select>
                                </div>
                            </div>
                            <div class="<?php esc_attr_e( self::set( array(
								'edit-tracking-content-body-row',
								'edit-tracking-content-body-row-other-carrier'
							) ) ) ?>">
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-number-wrap' ) ) ?>">
                                    <label for="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-name' ) ) ?>">
										<?php esc_html_e( 'Carrier name', 'woo-orders-tracking' ) ?>
                                    </label>
                                    <input type="text"
                                           id="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-name' ) ) ?>"
                                           class="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-name' ) ) ?>">
                                </div>
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-carrier-wrap' ) ) ?>">
                                    <label for="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-country' ) ) ?>"><?php esc_html_e( 'Shipping country', 'woo-orders-tracking' ) ?></label>
                                    <select name="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-country' ) ) ?>"
                                            id="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-country' ) ) ?>"
                                            class="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-country' ) ) ?> select2-hidden-accessible"
                                            tabindex="-1" aria-hidden="true">
                                        <option value=""></option>
                                        <option value="Global"
                                                selected><?php esc_html_e( 'Global', 'woo-orders-tracking' ) ?></option>
										<?php
										foreach ( $countries as $country_code => $country_name ) {
											?>
                                            <option value="<?php esc_attr_e( $country_code ) ?>"><?php echo $country_name ?></option>
											<?php
										}
										?>
                                    </select>
                                </div>
                            </div>
                            <div class="<?php esc_attr_e( self::set( array(
								'edit-tracking-content-body-row',
								'edit-tracking-content-body-row-other-carrier'
							) ) ) ?>">
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-shipping-carrier-wrap' ) ) ?>">
                                    <label for="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-url' ) ) ?>"><?php esc_html_e( 'Carrier Tracking url', 'woo-orders-tracking' ) ?></label>
                                    <input type="text"
                                           id="<?php esc_attr_e( self::set( 'edit-tracking-other-carrier-url' ) ) ?>"
                                           placeholder="http://yourcarrier.com/{tracking_number}">
                                    <p class="description">
                                        {tracking_number}:<?php esc_html_e( 'The placeholder for tracking number of an item', 'woo-orders-tracking' ) ?></p>
                                    <p class="description">
                                        {postal_code}:<?php esc_html_e( 'The placeholder for postal code of an order', 'woo-orders-tracking' ) ?></p>
                                    <p class="description"><?php echo 'eg: https://www.dhl.com/en/express/tracking.html?AWB={tracking_number}&brand=DHL'; ?></p>
                                    <p class="description wotv-error-tracking-url "
                                       style="color: red"><?php esc_html_e( 'The tracking url will not include tracking number if message does not include ', 'woo-orders-tracking' ) ?>
                                        {tracking_number}</p>
                                </div>
                            </div>
							<?php
							$all_order_statuses = wc_get_order_statuses();
							unset( $all_order_statuses[ 'wc-' . $order->get_status() ] );
							?>
                            <div class="<?php esc_attr_e( self::set( array(
								'edit-tracking-content-body-row',
								'edit-tracking-content-body-row-change-order-status'
							) ) ) ?>">
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-change-order-status-wrap' ) ) ?>">
                                    <label for="<?php esc_attr_e( self::set( 'edit-tracking-change-order-status' ) ) ?>"><?php esc_html_e( 'Change order status to: ', 'woo-orders-tracking' ) ?></label>
                                    <select name="<?php esc_attr_e( self::set( 'order_status', true ) ) ?>"
                                            id="<?php esc_attr_e( self::set( 'order_status' ) ) ?>"
                                            class="<?php esc_attr_e( self::set( 'order_status' ) ) ?>">
                                        <option value=""><?php esc_html_e( 'Not Change', 'woo-orders-tracking' ) ?></option>
										<?php
										if ( count( $all_order_statuses ) ) {
											$order_status = $this->settings->get_params( 'order_status' );
											foreach ( $all_order_statuses as $status_id => $status_name ) {
												?>
                                                <option value="<?php esc_attr_e( $status_id ) ?>" <?php selected( $order_status, $status_id ) ?>><?php echo $status_name ?></option>
												<?php
											}
										}
										?>
                                    </select>
                                </div>
                            </div>
							<?php
							?>
                            <div class="<?php esc_attr_e( self::set( array( 'edit-tracking-content-body-row' ) ) ) ?>">
                                <div class="<?php esc_attr_e( self::set( 'edit-tracking-send-email-wrap' ) ) ?>">
                                    <input type="checkbox"
										<?php checked( $this->settings->get_params( 'email_enable' ), '1' ) ?>
                                           id="<?php esc_attr_e( self::set( 'edit-tracking-send-email' ) ) ?>"
                                           class="<?php esc_attr_e( self::set( 'edit-tracking-send-email' ) ) ?>">
                                    <label for="<?php esc_attr_e( self::set( 'edit-tracking-send-email' ) ) ?>"><?php esc_html_e( 'Send email to customer(if tracking info changes).', 'woo-orders-tracking' ) ?>
                                        <a target="_blank"
                                           href="<?php esc_attr_e( admin_url( 'admin.php?page=woo-orders-tracking#email' ) ) ?>"><?php esc_html_e( 'View settings', 'woo-orders-tracking' ) ?></a></label>
                                </div>
                            </div>
							<?php
							if ( $transID && in_array( $paypal_method, $this->settings->get_params( 'supported_paypal_gateways' ) ) ) {
								?>
                                <div class="<?php esc_attr_e( self::set( array(
									'edit-tracking-content-body-row',
									'edit-tracking-content-body-row-add-to-paypal'
								) ) ) ?>">
                                    <div class="<?php esc_attr_e( self::set( 'edit-tracking-send-email-wrap' ) ) ?>">
                                        <input type="hidden"
                                               value="<?php esc_attr_e( $paypal_method ) ?>"
                                               id="<?php esc_attr_e( self::set( 'edit-tracking-add-to-paypal-method' ) ) ?>"
                                        >
                                        <input type="checkbox"
                                               value="<?php esc_attr_e( $transID ) ?>"
                                               id="<?php esc_attr_e( self::set( 'edit-tracking-add-to-paypal' ) ) ?>"
                                               class="<?php esc_attr_e( self::set( 'edit-tracking-send-email' ) ) ?>">
                                        <label for="<?php esc_attr_e( self::set( 'edit-tracking-add-to-paypal' ) ) ?>">
											<?php esc_html_e( 'Add tracking number to PayPal. ', 'woo-orders-tracking' ) ?>
                                            <a target="_blank"
                                               href="<?php esc_attr_e( admin_url( 'admin.php?page=woo-orders-tracking#paypal' ) ) ?>"><?php esc_html_e( 'View settings', 'woo-orders-tracking' ) ?></a>
                                        </label>
                                        <img src="<?php esc_attr_e( VI_WOO_ORDERS_TRACKING_PAYPAL_IMAGE ) ?>">
                                    </div>
                                </div>
								<?php
							}
							?>
                        </div>
                        <div class="<?php esc_attr_e( self::set( 'edit-tracking-content-footer' ) ) ?>">
                                <span class=" button button-primary <?php esc_attr_e( self::set( 'edit-tracking-button-save' ) ) ?>">
                                    <?php esc_html_e( 'Save', 'woo-orders-tracking' ) ?>
                                </span>
                            <span class=" button <?php esc_attr_e( self::set( 'edit-tracking-button-cancel' ) ) ?>">
                                    <?php esc_html_e( 'Cancel', 'woo-orders-tracking' ) ?>
                                </span>
                        </div>
                    </div>
                    <div class="<?php esc_attr_e( self::set( array( 'saving-overlay', 'hidden' ) ) ) ?>"></div>
                </div>
				<?php
			}
		}
	}

	public function woocommerce_hidden_order_itemmeta( $hidden_order_itemmeta ) {
		$hidden_order_itemmeta[] = '_vi_order_item_tracking_code';
		$hidden_order_itemmeta[] = '_vi_order_item_tracking_url';
		$hidden_order_itemmeta[] = '_vi_order_item_carrier_name';
		$hidden_order_itemmeta[] = '_vi_order_item_carrier_id';
		$hidden_order_itemmeta[] = '_vi_order_item_carrier_type';
		$hidden_order_itemmeta[] = '_vi_wot_order_item_tracking_data';

		return $hidden_order_itemmeta;
	}

	public function get_shipping_carrier_by_slug( $slug ) {
		if ( ! isset( $this->carriers[ $slug ] ) ) {
			$this->carriers[ $slug ] = $this->settings->get_shipping_carrier_by_slug( $slug );
		}

		return $this->carriers[ $slug ];
	}

	/**
	 * @param $item_id
	 * @param $item WC_Order_Item_Product
	 * @param $product
	 *
	 * @throws Exception
	 */
	public function woocommerce_after_order_itemmeta( $item_id, $item, $product ) {
		if ( is_ajax() || ! is_a( $item, 'WC_Order_Item_Product' ) ) {
			return;
		}
		$order_id = $item->get_order_id();
		$order    = wc_get_order( $order_id );
		if ( $order ) {
			$transID               = $order->get_transaction_id();
			$paypal_method         = $order->get_payment_method();
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
			$tracking_number  = apply_filters( 'vi_woo_orders_tracking_current_tracking_number', $current_tracking_data['tracking_number'], $item_id, $order_id );
			$carrier_url      = apply_filters( 'vi_woo_orders_tracking_current_tracking_url', $current_tracking_data['carrier_url'], $item_id, $order_id );
			$carrier_name     = apply_filters( 'vi_woo_orders_tracking_current_carrier_name', $current_tracking_data['carrier_name'], $item_id, $order_id );
			$carrier_slug     = apply_filters( 'vi_woo_orders_tracking_current_carrier_slug', $current_tracking_data['carrier_slug'], $item_id, $order_id );
			$digital_delivery = 0;
			$carrier          = $this->get_shipping_carrier_by_slug( $current_tracking_data['carrier_slug'] );
			if ( is_array( $carrier ) && count( $carrier ) ) {
				$carrier_url  = $carrier['url'];
				$carrier_name = $carrier['name'];
				if ( $carrier['carrier_type'] === 'custom-carrier' && isset( $carrier['digital_delivery'] ) ) {
					$digital_delivery = $carrier['digital_delivery'];
				}
			}
			$tracking_url_show = apply_filters( 'vi_woo_orders_tracking_current_tracking_url_show', $this->settings->get_url_tracking( $carrier_url, $tracking_number, $carrier_slug, $order->get_shipping_postcode() ), $item_id, $order_id );
			?>
            <div class="<?php esc_attr_e( self::set( 'container' ) ) ?>">
                <div class="<?php esc_attr_e( self::set( 'item-details' ) ) ?>">
                    <div class="<?php esc_attr_e( self::set( 'item-tracking-code-label' ) ) ?>">
                        <span><?php esc_html_e( 'Tracking number', 'woo-orders-tracking' ) ?></span>
                    </div>
                    <div class="<?php esc_attr_e( self::set( 'item-tracking-code-value' ) ) ?>"
                         title="<?php esc_attr_e( $carrier_name ? "Tracking carrier {$carrier_name}" : '', 'woo-orders-tracking' ) ?>">
                        <a href="<?php esc_attr_e( $tracking_url_show ) ?>"
                           target="_blank"><?php echo $tracking_number ?></a>
                    </div>
                    <div class="<?php esc_attr_e( self::set( 'item-tracking-button-edit-container' ) ) ?>">
                    <span class="dashicons dashicons-edit <?php esc_attr_e( self::set( 'button-edit' ) ) ?>"
                          data-tracking_code="<?php esc_attr_e( $tracking_number ) ?>"
                          data-tracking_url="<?php esc_attr_e( $carrier_url ) ?>"
                          data-carrier_name="<?php esc_attr_e( $carrier_name ) ?>"
                          data-digital_delivery="<?php esc_attr_e( $digital_delivery ) ?>"
                          data-carrier_id="<?php esc_attr_e( $carrier_slug ) ?>"
                          data-order_id="<?php esc_attr_e( $order_id ) ?>"
                          data-item_name="<?php esc_attr_e( $item->get_name() ) ?>"
                          data-item_id="<?php esc_attr_e( $item_id ) ?>"
                          title="<?php esc_attr_e( 'Edit tracking', 'woo-orders-tracking' ) ?>"></span>
						<?php
						if ( $transID && in_array( $paypal_method, $this->settings->get_params( 'supported_paypal_gateways' ) ) ) {
							$paypal_added_trackings = get_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', true );
							if ( ! $paypal_added_trackings ) {
								$paypal_added_trackings = array();
							}
							$paypal_class = array( 'item-tracking-button-add-to-paypal-container' );
							if ( ! $tracking_number && $digital_delivery != 1 ) {
								$paypal_class[] = 'paypal-inactive';
								$title          = '';
							} else {
								if ( ! in_array( $tracking_number, $paypal_added_trackings ) ) {
									$paypal_class[] = 'paypal-active';
									$title          = esc_attr__( 'Add this tracking number to PayPal', 'woo-orders-tracking' );
								} else {
									$paypal_class[] = 'paypal-inactive';
									$title          = esc_attr__( 'This tracking number was added to PayPal', 'woo-orders-tracking' );
								}
							}

							?>
                            <span class="<?php esc_attr_e( self::set( $paypal_class ) ) ?>"
                                  data-item_id="<?php esc_attr_e( $item_id ) ?>"
                                  data-order_id="<?php esc_attr_e( $order_id ) ?>">
                            <img class="<?php esc_attr_e( self::set( 'item-tracking-button-add-to-paypal' ) ) ?>"
                                 title="<?php echo $title ?>"
                                 src="<?php esc_attr_e( VI_WOO_ORDERS_TRACKING_PAYPAL_IMAGE ) ?>">
                        </span>
							<?php
						}
						?>

                    </div>
                </div>
                <div class="<?php esc_attr_e( self::set( 'error' ) ) ?>"></div>
            </div>
			<?php
		}
	}

	public function order_details_add_meta_boxes() {
		global $pagenow;
		if ( $pagenow === 'post.php' ) {
			add_meta_box(
				'vi_wotv_edit_order_tracking',
				__( 'Tracking number', 'woo-orders-tracking' ),
				array( $this, 'add_meta_box_callback' ),
				'shop_order',
				'side',
				'core'
			);
		}
	}

	public function add_meta_box_callback() {
		global $post;
		$order_id = $post->ID;
		$order    = wc_get_order( $order_id );
		if ( $order ) {
			$transID                = $order->get_transaction_id();
			$paypal_method          = $order->get_payment_method();
			$paypal_added_trackings = get_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', true );
			if ( ! $paypal_added_trackings ) {
				$paypal_added_trackings = array();
			}
			?>
            <p style="text-align: center">
                <button type="button"
                        class="button <?php esc_attr_e( self::set( 'button-edit-all-tracking-number' ) ) ?>"
                        data-order_id="<?php esc_attr_e( $order_id ) ?>"
                        title="<?php esc_attr_e( 'Set tracking number for all items of this order', 'woo-orders-tracking' ) ?>">
					<?php esc_html_e( 'Bulk set tracking number', 'woo-orders-tracking' ) ?>
                </button>
            </p>
			<?php
			if ( $transID && in_array( $paypal_method, $this->settings->get_params( 'supported_paypal_gateways' ) ) ) {
				?>
                <div class="<?php esc_attr_e( self::set( 'item-tracking-paypal-added-tracking-numbers-container' ) ) ?>"
                     title="<?php esc_attr_e( 'Tracking numbers that were added to PayPal transaction of this order', 'woo-orders-tracking' ) ?>">
                    <img class="<?php esc_attr_e( self::set( 'item-tracking-paypal-added-tracking-numbers-icon' ) ) ?>"
                         src="<?php esc_attr_e( VI_WOO_ORDERS_TRACKING_PAYPAL_IMAGE ) ?>">
                    <input type="text"
                           class="<?php esc_attr_e( self::set( 'item-tracking-paypal-added-tracking-numbers-values' ) ) ?>"
                           readonly
                           value="<?php esc_attr_e( htmlentities( implode( ', ', array_filter( $paypal_added_trackings ) ) ) ) ?>">
                </div>
				<?php
			}
		}
	}

	public function admin_enqueue_script() {
		global $pagenow;
		if ( $pagenow === 'post.php' ) {
			$screen = get_current_screen();
			if ( is_a( $screen, 'WP_Screen' ) && $screen->id == 'shop_order' ) {
				wp_enqueue_style( 'vi-wot-admin-edit-order-select2', VI_WOO_ORDERS_TRACKING_CSS . 'select2.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
				wp_enqueue_style( 'vi-wot-admin-edit-order-css', VI_WOO_ORDERS_TRACKING_CSS . 'admin-edit-order.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
				wp_enqueue_script( 'vi-wot-admin-edit-order-select2', VI_WOO_ORDERS_TRACKING_JS . 'select2.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
				wp_enqueue_script( 'vi-wot-admin-edit-order-js', VI_WOO_ORDERS_TRACKING_JS . 'admin-edit-order.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
				wp_enqueue_script( 'vi-wot-admin-edit-carrier-functions-js', VI_WOO_ORDERS_TRACKING_JS . '/carrier-functions.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
				$shipping_carrier_default = $this->settings->get_params( 'shipping_carrier_default' );
				wp_localize_script( 'vi-wot-admin-edit-order-js',
					'vi_wot_edit_order',
					array(
						'ajax_url'                           => admin_url( 'admin-ajax.php' ),
						'shipping_carrier_default'           => $shipping_carrier_default,
						'shipping_carrier_default_url_check' => $this->settings->get_shipping_carrier_url( $shipping_carrier_default, 'custom-carrier' ),
						'custom_carriers_list'               => $this->settings->get_params( 'custom_carriers_list' ),
						'shipping_carriers_define_list'      => json_encode( VI_WOO_ORDERS_TRACKING_DATA::shipping_carriers() ),
						'error_empty_field'                  => esc_html__( 'Please fill full information for tracking', 'woo-orders-tracking' ),
						'exits_tracking_number'              => esc_html__( 'This tracking exists', 'woo-orders-tracking' ),
						'paypal_image'                       => VI_WOO_ORDERS_TRACKING_PAYPAL_IMAGE,
						'loading_image'                      => VI_WOO_ORDERS_TRACKING_LOADING_IMAGE,
					)
				);
				add_action( 'admin_footer-post.php', array( $this, 'orders_tracking_edit_tracking_footer' ) );
			}
		}
	}
}