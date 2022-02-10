<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_SCHEDULE {
	public function __construct() {
		$this->schedule_event();
	}

	/**
	 * Schedule event
	 */
	public function schedule_event() {
		if ( ! wp_next_scheduled( 'vi_wot_refresh_track_info' ) ) {
			$event_time_send = '2:00';
			$event_time      = new DateTime( date( 'Y-m-d' ) . $event_time_send . ":00" );
			$time_check = explode( ":", $event_time_send );
			if ( $time_check[0] <= date( 'H' ) ) {
				$event_time->modify( '+1 day' );
			}
			wp_schedule_event( $event_time->format( 'U' ) + $event_time->getOffset(), 'daily', 'vi_wot_refresh_track_info' );
		}
		add_action( 'vi_wot_refresh_track_info', array( $this, 'vi_wot_refresh_track_info' ) );
	}

	public function vi_wot_refresh_track_info() {
		global $wpdb;
		$settings                = new VI_WOO_ORDERS_TRACKING_DATA();
		$service_carrier_type    = $settings->get_params( 'service_carrier_type' );
		$service_carrier_api_key = $settings->get_params( 'service_carrier_api_key' );
		$table_search = $wpdb->prefix . 'wotv_woo_track_info';
		$search_sql   = "SELECT tracking_number, carrier_id,carrier_name,shipping_country_code,order_id FROM {$table_search}";
		$result_sql   = $wpdb->get_results( $search_sql );
		if ( count( $result_sql ) ) {
			foreach ( $result_sql as $item ) {
				$track_info = VI_WOO_ORDERS_TRACKING_ADMIN_TRACK_ORDER_DATA::tracking_info( $item->tracking_number, $service_carrier_api_key, $service_carrier_type, $item->carrier_id, $item->carrier_name, $item->shipping_country_code );
				if ( $track_info ) {
					$date = date( 'Y-m-d H:i:s' );
					$sql  = "UPDATE {$table_search} SET status= %s, carrier_id= %s, carrier_name = %s, shipping_country_code = %s, track_info= %s, last_event= %s, modified_at= %s  WHERE tracking_number = %s";
					switch ( $track_info['status'] ) {
						case 'success':
							$t = $wpdb->query( $wpdb->prepare( $sql, $track_info['data']['status'], $item->carrier_id, $item->carrier_name, $item->shipping_country_code, json_encode( $track_info['data'] ), $track_info['data']['last_event'], $date, $item->tracking_number ) );
							break;
						case 'error':
							$t = $wpdb->query( $wpdb->prepare( $sql, '', $item->carrier_id, $item->carrier_name, $item->shipping_country_code, '', json_encode( $track_info['data'] ), $date, $item->tracking_number ) );
							break;
					}
				}
			}
		}
	}
}

new VI_WOO_ORDERS_TRACKING_SCHEDULE();
