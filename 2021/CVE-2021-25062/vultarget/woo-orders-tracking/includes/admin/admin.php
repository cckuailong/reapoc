<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class VI_WOO_ORDERS_TRACKING_ADMIN_ADMIN {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WOO_ORDERS_TRACKING_DATA();
		add_action( 'init', array( $this, 'init' ) );
		add_filter(
			'plugin_action_links_woo-orders-tracking/woo-orders-tracking.php', array(
				$this,
				'settings_link'
			)
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'update_data_new_version' ) );
	}

	public function update_data_new_version() {
		if ( ! get_option( 'vi_woo_orders_tracking_update_data_version_1.1.0' ) ) {
			$results = $this->query_order_item_meta( array(), array(
				'meta_key' => '_vi_order_item_tracking_code'
			), 100 );
			if ( count( $results ) ) {
				$process_items = array();
				$carriers      = array();
				foreach ( $results as $result ) {
					$item_id = $result['order_item_id'];
					if ( ! in_array( $item_id, $process_items ) ) {
						$process_items[]       = $item_id;
						$carrier_id_array      = wc_get_order_item_meta( $item_id, '_vi_order_item_carrier_id', false );
						$carrier_slug          = array_pop( $carrier_id_array );
						$tracking_number_array = wc_get_order_item_meta( $item_id, '_vi_order_item_tracking_code', false );
						$tracking_number       = array_pop( $tracking_number_array );
						if ( $tracking_number && $carrier_slug ) {
							if ( isset( $carriers[ $carrier_slug ] ) ) {
								$carrier = $carriers[ $carrier_slug ];
							} else {
								$carrier                   = $this->settings->get_shipping_carrier_by_slug( $carrier_slug );
								$carriers[ $carrier_slug ] = $carrier;
							}
							if ( is_array( $carrier ) && count( $carrier ) ) {
								$current_tracking_data = array(
									'tracking_number' => $tracking_number,
									'carrier_slug'    => $carrier_slug,
									'carrier_url'     => $carrier['url'],
									'carrier_name'    => $carrier['name'],
									'carrier_type'    => $carrier['carrier_type'],
									'time'            => time(),
								);
								$item_tracking_data    = array( $current_tracking_data );
								wc_update_order_item_meta( $item_id, '_vi_wot_order_item_tracking_data', json_encode( $item_tracking_data ) );
								wc_delete_order_item_meta( $item_id, '_vi_order_item_carrier_type' );
								wc_delete_order_item_meta( $item_id, '_vi_order_item_carrier_id' );
								wc_delete_order_item_meta( $item_id, '_vi_order_item_tracking_code' );
								wc_delete_order_item_meta( $item_id, '_vi_order_item_carrier_name' );
								wc_delete_order_item_meta( $item_id, '_vi_order_item_tracking_url' );
							}
						}

					}

				}
			} else {
				update_option( 'vi_woo_orders_tracking_update_data_version_1.1.0', time() );
			}
		}
	}

	protected function query_order_item_meta( $args1 = array(), $args2 = array(), $limit = 0 ) {
		global $wpdb;
		$sql  = "SELECT * FROM {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta WHERE woocommerce_order_items.order_item_id=woocommerce_order_itemmeta.order_item_id";
		$args = array();
		if ( count( $args1 ) ) {
			foreach ( $args1 as $key => $value ) {
				if ( is_array( $value ) ) {
					$sql .= " AND woocommerce_order_items.{$key} IN (" . implode( ', ', array_fill( 0, count( $value ), '%s' ) ) . ")";
					foreach ( $value as $v ) {
						$args[] = $v;
					}
				} else {
					$sql    .= " AND woocommerce_order_items.{$key}='%s'";
					$args[] = $value;
				}
			}
		}
		if ( count( $args2 ) ) {
			foreach ( $args2 as $key => $value ) {
				if ( is_array( $value ) ) {
					$sql .= " AND woocommerce_order_itemmeta.{$key} IN (" . implode( ', ', array_fill( 0, count( $value ), '%s' ) ) . ")";
					foreach ( $value as $v ) {
						$args[] = $v;
					}
				} else {
					$sql    .= " AND woocommerce_order_itemmeta.{$key}='%s'";
					$args[] = $value;
				}
			}
		}
		if ( $limit ) {
			$sql .= " LIMIT 0,{$limit}";
		}
		$query      = $wpdb->prepare( $sql, $args );
		$line_items = $wpdb->get_results( $query, ARRAY_A );

		return $line_items;
	}

	public function settings_link( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php' ) . '?page=woo-orders-tracking" title="' . __( 'Settings', 'woo-orders-tracking' ) . '">' . __( 'Settings', 'woo-orders-tracking' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woo-orders-tracking' );
		load_textdomain( 'woo-orders-tracking', VI_WOO_ORDERS_TRACKING_LANGUAGES . "woo-orders-tracking-$locale.mo" );
		load_plugin_textdomain( 'woo-orders-tracking', false, VI_WOO_ORDERS_TRACKING_LANGUAGES );

	}

	public function init() {
		$this->load_plugin_textdomain();
		if(class_exists('VillaTheme_Support')){
			new VillaTheme_Support(
				array(
					'support' => 'https://wordpress.org/support/plugin/woo-orders-tracking/',
					'docs' => 'http://docs.villatheme.com/?item=woo-orders-tracking',
					'review' => 'https://wordpress.org/support/plugin/woo-orders-tracking/reviews/?rate=5#rate-response',
					'pro_url' => 'https://1.envato.market/6ZPBE',
					'css' => VI_WOO_ORDERS_TRACKING_CSS,
					'image' => VI_WOO_ORDERS_TRACKING_IMAGES,
					'slug' => 'woo-orders-tracking',
					'menu_slug' => 'woo-orders-tracking',
					'version' => VI_WOO_ORDERS_TRACKING_VERSION
				)
			);

		}
	}
}