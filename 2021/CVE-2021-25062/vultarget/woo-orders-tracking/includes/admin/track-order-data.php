<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!class_exists('VI_WOT_CURL_TrackingMore')) {
	class VI_WOT_CURL_TrackingMore {

		private static function request( $url, $arg = array() ) {
			if ( ! empty( $arg ) ) {
				$request = wp_remote_request( $url, $arg );
			} else {
				$request = wp_remote_request( $url );
			}
			$body = wp_remote_retrieve_body( $request );

			return $body;
		}

		private static function curl_detect( $type, $data ) {
			$url  = 'https://api.trackingmore.com/v2/';
			$body = '';
			switch ( $type ) {
				case 'detect_carrier':
					$url  .= '/carriers/detect';
					$body = json_encode( array(
						'tracking_number' => $data['tracking_number']
					) );
					break;
				case 'post_realtime':
					$url  .= '/trackings/realtime';
					$body = json_encode( array(
						'tracking_number'  => $data['tracking_number'],
						'carrier_code'     => $data['carrier_code'],
						'destination_code' => $data['destination_code'],
						'lang'             => 'en',
						'auto_correct'     => '0'
					) );
					break;
				case 'get':
					$url .= '/trackings/' . $data['carrier_code'] . '/' . $data['tracking_number'];
					break;
				case 'batch':
					$url  .= 'trackings/batch';
					$body = json_encode( $data['data'] );
					break;
			}

			$arg    = array(
				'method'  => $data['method'],
				'headers' => array(
					'Content-Type'         => 'application/json',
					'Trackingmore-Api-Key' => $data['api'],
				),
				'body'    => $body
			);
			$result = self::request( $url, $arg );
			if ( $result ) {
				return $result;
			}

			return false;
		}


		private static function detect_carrier( $tracking_number, $api ) {
			$detect_arg = array(
				'api'             => $api,
				'tracking_number' => $tracking_number,
				'method'          => 'POST',
			);
			$request    = self::curl_detect( 'detect_carrier', $detect_arg );
			$request    = json_decode( $request, true );
			if ( $request['meta']['code'] == 200 && is_array( $request['data'] ) ) {
				$carrier_code = $request['data'][0];
				$carrier_code = $carrier_code['code'];

				return $carrier_code;
			}

			return '';
		}

		private static function get_tracking( $tracking_number, $api, $carrier_code ) {
			$get_arg = array(
				'api'             => $api,
				'carrier_code'    => $carrier_code,
				'tracking_number' => $tracking_number,
				'method'          => 'GET',
			);
			$request = self::curl_detect( 'get', $get_arg );
			$request = json_decode( $request, true );
			if ( $request['meta']['code'] == 200 && $request['meta']['type'] == 'Success' ) {
				$data     = $request['data'];
				$tracking = self::tracking_info( $data );

				return $tracking;
			}

			return false;
		}

		private static function get_status_realtime( $tracking_number, $api, $carrier_code, $destination_code ) {
			$result     = array();
			$detect_arg = array(
				'api'              => $api,
				'tracking_number'  => $tracking_number,
				'carrier_code'     => $carrier_code,
				'destination_code' => $destination_code,
				'method'           => 'POST',
			);
			$request    = self::curl_detect( 'post_realtime', $detect_arg );
			$request    = json_decode( $request, true );
			if ( $request['meta']['code'] == 200 && $request['meta']['type'] == 'Success' ) {
				$data     = $request['data'];
				$tracking = self::tracking_info( $data );
				if ( $tracking ) {
					$result['status'] = 'success';
					$result['data']   = $tracking;
				} else {
					if ( $request['meta']['message'] === 'Success' ) {
						$result['status'] = 'error';
						$result['data']   = __( 'No result found for this tracking number.', 'woo-orders-tracking' );
					} else {
						$result['status'] = 'error';
						$result['data']   = $request['meta']['message'];
					}
				}
			} elseif ( $request['meta']['code'] == 429 ) {
				$data = self::get_tracking( $tracking_number, $api, $carrier_code );
				if ( $data ) {
					$result['status'] = 'success';
					$result['data']   = $data;
				} else {
					$result['status'] = 'error';
					$result['data']   = $request['meta']['message'];
				}
			} else {
				$result['status'] = 'error';
				$result['data']   = $request['meta']['message'];
			}

			return $result;
		}

		private static function tracking_info( $data ) {
			$tracking = array();
			if ( isset( $data['status'] ) ) {
				$status = $data['status'];
				if ( isset( $data['destination_info'] ) && isset( $data['destination_info']['trackinfo'] ) ) {
					$events = $data['destination_info']['trackinfo'];
					foreach ( $events as $event ) {
						$tracking[] = array(
							'time'        => $event['Date'],
							'description' => $event['StatusDescription'],
							'location'    => $event['Details'],
							'status'      => $event['checkpoint_status'],
						);
					}
				} elseif ( isset( $data['origin_info'] ) && isset( $data['origin_info']['trackinfo'] ) ) {
					$events = $data['origin_info']['trackinfo'];
					foreach ( $events as $event ) {
						$tracking[] = array(
							'time'        => $event['Date'],
							'description' => $event['StatusDescription'],
							'location'    => $event['Details'],
							'status'      => $event['checkpoint_status'],
						);
					}
				}
				$last_event = $data['lastEvent'];
				$results    = array(
					'status'     => $status,
					'tracking'   => $tracking,
					'last_event' => $last_event,
				);

				return $results;
			}

			return false;
		}

		public static function tracking_batch( $api, $data ) {
			$data_batch = array();
			foreach ( $data as $item ) {
				$data_batch[] = array(
					'carrier_code'     => isset( $item['tracking_more_slug'] ) ? $item['tracking_more_slug'] : $item['carrier_id'],
					'tracking_number'  => $item['tracking_code'],
					'destination_code' => $item['shipping_country_code'],
					'order_id'         => $item['order_id'],
					'customer_email'   => $item['customer_email'],
					'customer_name'    => $item['customer_name'],
					'customer_phone'   => $item['customer_phone'],
					'lang'             => 'en',
					'auto_correct'     => '0',
				);
			}
			$batch_arg = array(
				'api'    => $api,
				'data'   => $data_batch,
				'method' => 'POST',
			);
			$request   = self::curl_detect( 'batch', $batch_arg );
			if ( $request ) {
				$request = json_decode( $request, true );

				return $request;
			}

			return false;
		}

		public static function track_order( $value, $api, $destination_code, $carrier_id ) {
			if ( ! $carrier_id ) {
				$carrier_id = self::detect_carrier( $value, $api );
			}
			if ( $carrier_id === 'epacket' ) {
				$carrier_id = 'china-ems';
			}
			$result = self::get_status_realtime( $value, $api, $carrier_id, $destination_code );

			return $result;
		}
	}
}
class VI_WOO_ORDERS_TRACKING_ADMIN_TRACK_ORDER_DATA {
	private static function get_track_service( $service = '' ) {
		switch ( $service ) {
			case 'aftership':
				$class = 'VI_WOT_CURL_AfterShip';
				break;
			case 'trackingmore':
			default:
				$class = 'VI_WOT_CURL_TrackingMore';
				break;
		}

		return $class;
	}

	public static function tracking_info( $tracking_number, $api, $track_service, $carrier_id = '', $carrier_name = '', $shipping_country_code = '' ) {
		$tracking_number = strtoupper( $tracking_number );
		$class           = self::get_track_service( $track_service );
		$data            = $class::track_order( $tracking_number, $api, $shipping_country_code, $carrier_id );
		if ( $data && $data['status'] && $data['status'] === 'success' ) {
			$data['data']['carrier_name'] = $carrier_name;
		}

		return $data;
	}

	public static function tracking_batch( $track_service, $api, $data ) {
		$class = self::get_track_service( $track_service );
		$data  = $class::tracking_batch( $api, $data );

		return $data;
	}
}