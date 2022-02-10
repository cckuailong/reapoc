<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_TABLE_TRACKING {

	public static function vi_wot_add_tracking_code_to_track_service( $data ) {
		$settings             = new VI_WOO_ORDERS_TRACKING_DATA();
		$service_carrier_type = $settings->get_params( 'service_carrier_type' );
		$api                  = $settings->get_params( 'service_carrier_api_key' );
		$result               = VI_WOO_ORDERS_TRACKING_ADMIN_TRACK_ORDER_DATA::tracking_batch( $service_carrier_type, $api, $data );

		return $result;

	}

	public static function vi_wot_add_track_info_to_database( $data ) {
		global $wpdb;
		$table_search = $wpdb->prefix . 'wotv_woo_track_info';
		$date         = date( 'Y-m-d H:i:s' );
		$result       = array();
		foreach ( $data as $item ) {
			$search_tracking_number = "SELECT count(*) FROM {$table_search} WHERE tracking_number = %s AND order_id = %s";
			$search_tracking_number = $wpdb->prepare( $search_tracking_number, $item['tracking_code'], $item['order_id'] );
			$check_exit_tracking    = $wpdb->get_col( $search_tracking_number );
			$check_exit_tracking    = $check_exit_tracking[0];
			if ( $check_exit_tracking > 0 ) {
				continue;
			} else {
				$sql                                                                   = "INSERT INTO {$table_search} (order_id,tracking_number, status, carrier_id, carrier_name, shipping_country_code, track_info,last_event, create_at,modified_at) VALUES( %s, %s,%s,%s,%s,%s,%s, %s, %s,%s) ";
				$result[ 'order-' . $item['order_id'] . '-' . $item['tracking_code'] ] = $wpdb->query( $wpdb->prepare( $sql, $item['order_id'], $item['tracking_code'], '', $item['carrier_id'], $item['carrier_name'], $item['shipping_country_code'], '', '', $date, $date ) );

				wp_schedule_single_event( time() + 3 * MONTH_IN_SECONDS, 'vi_wot_delete_track_info_scheduled_cleanup', array(
					$item['order_id'],
					$item['tracking_code']
				) );
			}
		}

		return $result;
	}

	public static function refresh_track_info_database( $data, $insert = false ) {
		$settings                = new VI_WOO_ORDERS_TRACKING_DATA();
		$service_carrier_type    = $settings->get_params( 'service_carrier_type' );
		$service_carrier_api_key = $settings->get_params( 'service_carrier_api_key' );
		global $wpdb;
		$table_search = $wpdb->prefix . 'wotv_woo_track_info';
		$date         = date( 'Y-m-d H:i:s' );

		$search_tracking_number = "SELECT count(*) FROM {$table_search} WHERE order_id = %s AND tracking_number = %s";
		$search_tracking_number = $wpdb->prepare( $search_tracking_number, $data['order_id'], $data['tracking_code'] );
		$check_exit_tracking    = $wpdb->get_col( $search_tracking_number );
		$check_exit_tracking    = $check_exit_tracking[0];

		if ( $check_exit_tracking > 0 ) {
			$track_info = VI_WOO_ORDERS_TRACKING_ADMIN_TRACK_ORDER_DATA::tracking_info( $data['tracking_code'], $service_carrier_api_key, $service_carrier_type, $data['carrier_id'], $data['carrier_name'], $data['shipping_country_code'] );
			$database   = array();
			if ( $track_info ) {
				$sql = "UPDATE {$table_search} SET status= %s, carrier_id= %s, carrier_name = %s, shipping_country_code = %s, track_info= %s, last_event= %s, modified_at= %s  WHERE tracking_number = %s";
				switch ( $track_info['status'] ) {
					case 'success':
						$database[ 'update-order-' . $data['order_id'] . '-' . $data['tracking_code'] ] = $wpdb->query( $wpdb->prepare( $sql, $track_info['data']['status'], $data['carrier_id'], $data['carrier_name'], $data['shipping_country_code'], json_encode( $track_info['data'] ), $track_info['data']['last_event'], $date, $data['tracking_code'] ) );
						break;
					case 'error':
						$database[ 'update-order-' . $data['order_id'] . '-' . $data['tracking_code'] ] = $wpdb->query( $wpdb->prepare( $sql, '', $data['carrier_id'], $data['carrier_name'], $data['shipping_country_code'], '', json_encode( $track_info['data'] ), $date, $data['tracking_code'] ) );
						break;
				}
			}

			return array( 'track_info' => $track_info, 'database' => $database );
		} else {
			if ( $insert ) {
				$data_batch = array(
					array(
						'shipping_country_code' => $data['shipping_country_code'],
						'carrier_id'            => $data['carrier_id'],
						'carrier_name'          => $data['carrier_name'],
						'tracking_code'         => $data['tracking_code'],
						'order_id'              => $data['order_id'],
						'customer_phone'              => $data['customer_phone'],
						'customer_email'              => $data['customer_email'],
						'customer_name'              => $data['customer_name'],
					)
				);
				$tt         = VI_WOO_ORDERS_TRACKING_ADMIN_TRACK_ORDER_DATA::tracking_batch( $service_carrier_type, $service_carrier_api_key, $data_batch );
				if ( ! $tt ) {
					return false;
				}
				$track_info = VI_WOO_ORDERS_TRACKING_ADMIN_TRACK_ORDER_DATA::tracking_info( $data['tracking_code'], $service_carrier_api_key, $service_carrier_type, $data['carrier_id'], $data['carrier_name'], $data['shipping_country_code'] );
				$database   = array();
				$sql        = "INSERT INTO {$table_search} (order_id,tracking_number, status, carrier_id, carrier_name, shipping_country_code, track_info,last_event, create_at,modified_at) VALUES( %s, %s,%s,%s,%s,%s,%s, %s, %s, %s ) ";
				if ( $track_info ) {
					switch ( $track_info['status'] ) {
						case 'success':
							$sql = $wpdb->prepare( $sql, array(
								$data['order_id'],
								$data['tracking_code'],
								$track_info['data']['status'],
								$data['carrier_id'],
								$track_info['data']['carrier_name'],
								$data['shipping_country_code'],
								json_encode( $track_info['data'] ),
								$track_info['data']['last_event'],
								$date,
								$date
							) );
							break;
						case 'error':
							$sql = $wpdb->prepare( $sql, array(
								$data['order_id'],
								$data['tracking_code'],
								'',
								$data['carrier_id'],
								$data['carrier_name'],
								$data['shipping_country_code'],
								'',
								json_encode( $track_info['data'] ),
								$date,
								$date
							) );
							break;
					}

				} else {
					$sql = $wpdb->prepare( $sql, array(
						$data['order_id'],
						$data['tracking_code'],
						'',
						$data['carrier_id'],
						$data['carrier_name'],
						$data['shipping_country_code'],
						'',
						'',
						$date,
						$date
					) );

				}
				$database[ 'insert-order-' . $data['order_id'] . '-' . $data['tracking_code'] ] = $wpdb->query( $sql );
				wp_schedule_single_event( time() + 3 * MONTH_IN_SECONDS, 'vi_wot_delete_track_info_scheduled_cleanup', array(
					$data['order_id'],
					$data['tracking_code']
				) );

				return array( 'track_info' => $track_info, 'database' => $database );
			}
		}

		return false;
	}

	public static function get_track_info( $trackinfo ) {
		if ( ! $trackinfo ) {
			return false;
		}
		$result = false;
		switch ( $trackinfo['status'] ) {
			case 'success':
				$result = array( 'status' => $trackinfo['status'], 'detail' => $trackinfo['data']['last_event'] );
				break;
			case 'error':
				$result = array( 'status' => 'error', 'detail' => $trackinfo['data'] );
				break;
		}

		return $result;
	}
}