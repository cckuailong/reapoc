<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_ADMIN_ORDERS_TRACK_INFO {
	protected $settings;
	protected $carriers;
	protected $tracking_service_action_buttons;

	public function __construct() {
		$this->settings = new VI_WOO_ORDERS_TRACKING_DATA();
		VILLATHEME_ADMIN_SHOW_MESSAGE::get_instance();
		$this->carriers = array();
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ), 99 );
		add_action( 'admin_head-edit.php', array( $this, 'addCustomImportButton' ) );
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_new_order_admin_list_column' ) );
		add_action( 'manage_shop_order_posts_custom_column', array(
			$this,
			'manage_shop_order_posts_custom_column'
		), 10, 2 );
		add_action( 'wp_ajax_vi_wot_refresh_track_info', array( $this, 'vi_wot_refresh_track_info' ) );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ), 10 );
		add_filter( 'posts_where', array( $this, 'posts_where' ), 10, 2 );
		/*Woo Alidropship*/
		add_filter( 'vi_woo_alidropship_order_item_tracking_data', array(
			$this,
			'vi_woo_alidropship_order_item_tracking_data'
		), 10, 3 );
	}

	public function vi_woo_alidropship_order_item_tracking_data( $current_tracking_data, $item_id, $order_id ) {
		if ( ! empty( $current_tracking_data['carrier_slug'] ) ) {
			$carrier = $this->get_shipping_carrier_by_slug( $current_tracking_data['carrier_slug'] );
			if ( is_array( $carrier ) && count( $carrier ) ) {
				$current_tracking_data['carrier_name'] = $carrier['name'];
				$order                                 = wc_get_order( $order_id );
				$postal_code                           = '';
				if ( $order ) {
					$postal_code = $order->get_shipping_postcode();
				}
				$current_tracking_data['carrier_url'] = $this->settings->get_url_tracking( $carrier['url'], $current_tracking_data['tracking_number'], $current_tracking_data['carrier_slug'], $postal_code );
			}
		}

		return $current_tracking_data;
	}

	public static function set( $name, $set_name = false ) {
		return VI_WOO_ORDERS_TRACKING_DATA::set( $name, $set_name );
	}

	public function add_nonce_field() {
		wp_nonce_field( 'vi_wot_item_action_nonce', '_vi_wot_item_nonce' );
	}

	public function addCustomImportButton() {
		global $current_screen;
		if ( 'shop_order' != $current_screen->post_type ) {
			return;
		}
		add_action( 'admin_footer', array( $this, 'add_nonce_field' ) );
		?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                jQuery(".wrap .page-title-action").after("<a class='page-title-action' target='_blank' href='<?php esc_attr_e( esc_url( admin_url( 'admin.php' ) ) . '?page=woo-orders-tracking-import-csv' ); ?>'><?php esc_html_e( 'Import tracking number', 'woo-orders-tracking' ) ?></a>"
                    + "<a class='page-title-action' target='_blank' href='<?php esc_attr_e( esc_url( admin_url( 'admin.php' ) ) . '?page=woo-orders-tracking-export' ); ?>'><?php esc_html_e( 'Export tracking number ', 'woo-orders-tracking' ) ?></a>");
            });
        </script>
		<?php
	}

	public function add_new_order_admin_list_column( $columns ) {
		$columns['vi_wot_tracking_code'] = __( 'Tracking Number', 'woo-orders-tracking' );

		return $columns;
	}

	public function tracking_service_action_buttons_html( $tracking_link, $current_tracking_data, $tracking_status ) {
		if ( $this->tracking_service_action_buttons === null ) {
			$this->tracking_service_action_buttons = '';
			$service_carrier_type                  = $this->settings->get_params( 'service_carrier_type' );
			if ( $service_carrier_type !== 'trackingmore' ) {
				ob_start();
				?>
                <div class="<?php esc_attr_e( self::set( 'tracking-service-action-button-container' ) ) ?>">
                    <span class="woo_orders_tracking_icons-duplicate <?php esc_attr_e( self::set( array(
	                    'tracking-service-action-button',
	                    'tracking-service-copy'
                    ) ) ) ?>" title="<?php esc_attr_e( 'Copy tracking number', 'woo-orders-tracking' ) ?>">
                    </span>
                    <a href="{tracking_link}" target="_blank">
                        <span class="woo_orders_tracking_icons-redirect <?php esc_attr_e( self::set( array(
	                        'tracking-service-action-button',
	                        'tracking-service-track'
                        ) ) ) ?>" title="<?php esc_attr_e( 'Open tracking link', 'woo-orders-tracking' ) ?>">
                        </span>
                    </a>
                </div>
				<?php
				$this->tracking_service_action_buttons = ob_get_clean();
			}
		}
		$button_refresh_title = __( 'Update latest data', 'woo-orders-tracking' );
		if ( ! empty( $current_tracking_data['last_update'] ) ) {
			$button_refresh_title = sprintf( __( 'Last update: %s. Click to refresh.', 'woo-orders-tracking' ), date_i18n( 'Y-m-d H:i:s', $current_tracking_data['last_update'] ) );
		}

		return str_replace( array('{button_refresh_title}','{tracking_link}'), array($button_refresh_title,$tracking_link), $this->tracking_service_action_buttons );
	}

	public function get_shipping_carrier_by_slug( $slug ) {
		if ( ! isset( $this->carriers[ $slug ] ) ) {
			$this->carriers[ $slug ] = $this->settings->get_shipping_carrier_by_slug( $slug );
		}

		return $this->carriers[ $slug ];
	}

	/**
	 * @param $column
	 * @param $order_id
	 *
	 * @throws Exception
	 */
	public function manage_shop_order_posts_custom_column( $column, $order_id ) {
		global $wpdb;
		if ( $column === 'vi_wot_tracking_code' ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$line_items = $order->get_items();
				if ( count( $line_items ) ) {
					$tracking_list          = array();
					$tracking_html          = '';
					$transID                = $order->get_transaction_id();
					$paypal_method          = $order->get_payment_method();
					$paypal_added_trackings = get_post_meta( $order_id, 'vi_wot_paypal_added_tracking_numbers', true );
					if ( ! $paypal_added_trackings ) {
						$paypal_added_trackings = array();
					}
					$tracking_info = array();
					foreach ( $line_items as $item_id => $line_item ) {
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
						$tracking_number = apply_filters( 'vi_woo_orders_tracking_current_tracking_number', $current_tracking_data['tracking_number'], $item_id, $order_id );
						$carrier_url     = apply_filters( 'vi_woo_orders_tracking_current_tracking_url', $current_tracking_data['carrier_url'], $item_id, $order_id );
						$carrier_name    = apply_filters( 'vi_woo_orders_tracking_current_carrier_name', $current_tracking_data['carrier_name'], $item_id, $order_id );
						$carrier_slug    = apply_filters( 'vi_woo_orders_tracking_current_carrier_slug', $current_tracking_data['carrier_slug'], $item_id, $order_id );
						$carrier_type    = isset( $current_tracking_data['carrier_type'] ) ? $current_tracking_data['carrier_type'] : '';
						$tracking_status = isset( $current_tracking_data['status'] ) ? VI_WOO_ORDERS_TRACKING_DATA::convert_status( $current_tracking_data['status'] ) : '';
						if ( $tracking_number && ! in_array( $tracking_number, $tracking_list ) ) {
							$tracking_list[] = $tracking_number;
							$carrier         = $this->get_shipping_carrier_by_slug( $current_tracking_data['carrier_slug'] );
							if ( is_array( $carrier ) && count( $carrier ) ) {
								$carrier_url  = $carrier['url'];
								$carrier_name = $carrier['name'];
								$carrier_type = $carrier['carrier_type'];
							}
							$tracking          = array(
								'tracking_code' => $tracking_number,
								'tracking_url'  => $carrier_url,
								'carrier_name'  => $carrier_name,
								'carrier_id'    => $carrier_slug,
								'carrier_type'  => $carrier_type,
							);
							$tracking_info[]   = $tracking;
							$tracking_url_show = apply_filters( 'vi_woo_orders_tracking_current_tracking_url_show', $this->settings->get_url_tracking( $carrier_url, $tracking_number, $carrier_slug, $order->get_shipping_postcode() ), $item_id, $order_id );
							ob_start();
							$container_class = array( 'tracking-number-container' );
							if ( $tracking_status ) {
								$container_class[] = 'tracking-number-container-' . $tracking_status;
							}
							?>
                            <div class="<?php esc_attr_e( self::set( $container_class ) ) ?>"
                                 data-tracking_number="<?php echo $tracking_number ?>"
                                 data-carrier_slug="<?php echo $carrier_slug ?>"
                                 data-order_id="<?php echo $order_id ?>">
                                <a class="<?php esc_attr_e( self::set( 'tracking-number' ) ) ?>"
                                   href="<?php esc_attr_e( $tracking_url_show ) ?>"
                                   title="<?php esc_attr_e( "Tracking carrier {$carrier_name}", 'woo-orders-tracking' ) ?>"
                                   target="_blank"><?php echo $tracking_number ?></a>
								<?php
								echo $this->tracking_service_action_buttons_html( $tracking_url_show, $current_tracking_data, $tracking_status );
								if ( $transID && in_array( $paypal_method, $this->settings->get_params( 'supported_paypal_gateways' ) ) ) {
									$paypal_class = array( 'item-tracking-button-add-to-paypal-container' );
									if ( ! in_array( $tracking_number, $paypal_added_trackings ) ) {
										$paypal_class[] = 'paypal-active';
										$title          = esc_attr__( 'Add this tracking number to PayPal', 'woo-orders-tracking' );
									} else {
										$paypal_class[] = 'paypal-inactive';
										$title          = esc_attr__( 'This tracking number was added to PayPal', 'woo-orders-tracking' );
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
							<?php
							$tracking_html .= ob_get_clean();
						}
					}
					if ( $tracking_html ) {
						?>
                        <div class="<?php esc_attr_e( self::set( 'tracking-number-column-container' ) ) ?>">
							<?php
							echo $tracking_html;
							?>
                        </div>
						<?php
					}
					$tracking_info_count  = count( $tracking_info );
					$service_carrier_type = $this->settings->get_params( 'service_carrier_type' );
					if ( $tracking_info_count > 0 && $this->settings->get_params( 'service_carrier_enable' ) && $service_carrier_type === 'trackingmore' ) {
						$table_search           = $wpdb->prefix . 'wotv_woo_track_info';
						$shipping_country_code  = get_post_meta( $order_id, '_shipping_country', true );
						$tracking_service_count = 0;
						ob_start();
						for ( $i = 0; $i < $tracking_info_count; $i ++ ) {
							?>
                            <div class="<?php esc_attr_e( self::set( array(
								'order-tracking-info-wrap',
								'order-tracking-info-wrap-' . $order_id,
								'order-tracking-info-hidden'
							) ) ) ?>">
                                <div class="<?php esc_attr_e( self::set( array(
									'order-tracking-info-overlay',
									'order-tracking-info-overlay-' . $tracking_info[ $i ]['tracking_code'],
									'order-tracking-info-hidden'
								) ) ) ?>">
                                </div>
								<?php
								if ( $tracking_info[ $i ]['carrier_type'] === 'service-carrier' ) {
									$tracking_service_count ++;
									$get_order      = wc_get_order( $order_id );
									$customer_phone = $get_order->get_billing_phone();
									$customer_email = $get_order->get_billing_email();
									$customer_name  = $get_order->get_formatted_billing_full_name();
									?>
                                    <div class="<?php esc_attr_e( self::set( 'order-tracking-info-tracking-number' ) ) ?> "
                                         style="display: flex; justify-content: space-between;">
                                        <strong class="<?php esc_attr_e( self::set( 'order-tracking-info-tracking-number-code' ) ) ?>"
                                                title="<?php esc_attr_e( $tracking_info[ $i ]['tracking_code'] ) ?>"><?php echo $tracking_info[ $i ]['tracking_code'] ?></strong>
                                        <div class="<?php esc_attr_e( self::set( 'order-tracking-info-refresh' ) ) ?> <?php echo $tracking_info[ $i ]['tracking_url'] ? esc_attr( self::set( 'order-tracking-info-hidden' ) ) : '' ?>"
                                             data-order_id="<?php esc_attr_e( $order_id ) ?>"
                                             data-tracking_number="<?php esc_attr_e( $tracking_info[ $i ]['tracking_code'] ) ?>"
                                             data-carrier_name="<?php esc_attr_e( $tracking_info[ $i ]['carrier_name'] ) ?>"
                                             data-carrier_id="<?php esc_attr_e( $tracking_info[ $i ]['carrier_id'] ) ?>"
                                             title="<?php esc_html_e( 'Update latest data of this tracking number from Tracking More', 'woo-orders-tracking' ) ?>">
                                            <i class="dashicons dashicons-image-rotate"></i>
                                        </div>
                                    </div>
									<?php
									$arg_refresh            = array(
										'carrier_id'            => $tracking_info[ $i ]['carrier_id'],
										'carrier_name'          => $tracking_info[ $i ]['carrier_name'],
										'shipping_country_code' => $shipping_country_code,
										'tracking_code'         => $tracking_info[ $i ]['tracking_code'],
										'order_id'              => $order_id,
										'customer_phone'        => $customer_phone,
										'customer_email'        => $customer_email,
										'customer_name'         => $customer_name,
									);
									$search_tracking_number = "SELECT * FROM {$table_search} WHERE order_id = %s  AND  tracking_number = %s";
									$search_tracking_number = $wpdb->prepare( $search_tracking_number, $order_id, $tracking_info[ $i ]['tracking_code'] );
									$existing_track_info    = $wpdb->get_results( $search_tracking_number, ARRAY_A );
									if ( $existing_track_info ) {
										$current_track_info = $existing_track_info[0];
										if ( $current_track_info['track_info'] ) {
											?>
                                            <div class="<?php esc_attr_e( self::set( 'order-tracking-info-carrier' ) ) ?>">
												<?php echo esc_html__( 'Carrier: ', 'woo-orders-tracking' ) . $tracking_info[ $i ]['carrier_name'] ?>
                                            </div>
                                            <div class="<?php esc_attr_e( self::set( array(
												'order-tracking-info-status',
												'order-tracking-info-status-' . $tracking_info[ $i ]['tracking_code']
											) ) ) ?>">
												<?php echo esc_html__( 'Status: ', 'woo-orders-tracking' ); ?>
                                                <span title="<?php esc_attr_e( $current_track_info['last_event'] ) ?>"><?php echo $current_track_info['status']; ?></span>
                                            </div>
											<?php
										} else {
											echo $this->get_template( $arg_refresh, $tracking_info[ $i ] );
										}
									} else {
										?>
                                        <div class="<?php esc_attr_e( self::set( 'order-tracking-info-carrier' ) ) ?>">
											<?php echo esc_html__( 'Carrier: ', 'woo-orders-tracking' ) . $tracking_info[ $i ]['carrier_name'] ?>
                                        </div>
                                        <div class="<?php esc_attr_e( self::set( array(
											'order-tracking-info-status',
											'order-tracking-info-status-' . $tracking_info[ $i ]['tracking_code']
										) ) ) ?>">
											<?php echo esc_html__( 'Status: ', 'woo-orders-tracking' ) ?>
                                            <span></span>
                                        </div>
										<?php
									}
								}

								?>
                            </div>
							<?php
						}
						$tracking_item_html = ob_get_clean();

						if ( $tracking_service_count ) {
							?>
                            <div class="<?php esc_attr_e( self::set( array( 'order-tracking-info-icon' ) ) ) ?>"
                                 data-order_id="<?php esc_attr_e( $order_id ) ?>">
                                <i class="dashicons dashicons-plus"
                                   title="<?php esc_attr_e( 'View more', 'woo-orders-tracking' ) ?>"></i>
                            </div>
							<?php
							echo $tracking_item_html;
						}
					}
				}
			}
		}
	}

	/**
	 * @param $arg_refresh
	 * @param $tracking_info
	 *
	 * @return string
	 */
	private function get_template( $arg_refresh, $tracking_info ) {
		ob_start();
		$track_info = VI_WOO_ORDERS_TRACKING_TABLE_TRACKING::refresh_track_info_database( $arg_refresh, false );
		if ( $track_info ) {
			$data_track = VI_WOO_ORDERS_TRACKING_TABLE_TRACKING::get_track_info( $track_info['track_info'] );
			if ( ! $data_track ) {
				?>
                <div class="<?php esc_attr_e( self::set( 'order-tracking-info-carrier' ) ) ?>">
					<?php echo esc_html__( 'Carrier: ', 'woo-orders-tracking' ) . $tracking_info['carrier_name'] ?>
                </div>
                <div class="<?php esc_attr_e( self::set( array(
					'order-tracking-info-status',
					'order-tracking-info-status-' . $tracking_info['tracking_code']
				) ) ) ?> <?php echo $tracking_info['tracking_url'] ? esc_attr( self::set( 'order-tracking-info-hidden' ) ) : '' ?>">
					<?php echo esc_html__( 'Status: ', 'woo-orders-tracking' ) ?>
                    <span></span>
                </div>
				<?php
			} else {
				?>
                <div class="<?php esc_attr_e( self::set( 'order-tracking-info-carrier' ) ) ?>">
					<?php echo esc_html__( 'Carrier: ', 'woo-orders-tracking' ) . $tracking_info['carrier_name'] ?>
                </div>
                <div class="<?php esc_attr_e( self::set( array(
					'order-tracking-info-status',
					'order-tracking-info-status-' . $tracking_info['tracking_code']
				) ) ) ?>">
					<?php echo esc_html__( 'Status: ', 'woo-orders-tracking' ); ?>
                    <span title="<?php esc_attr_e( isset( $track_info['last_event'] ) ? $track_info['last_event'] : '' ) ?>"><?php echo isset( $track_info['status'] ) ? $track_info['status'] : ''; ?></span>
                </div>
				<?php
			}
		} else {
			?>

            <div class="<?php esc_attr_e( self::set( 'order-tracking-info-carrier' ) ) ?>">
				<?php echo esc_html__( 'Carrier: ', 'woo-orders-tracking' ) . $tracking_info['carrier_name'] ?>
            </div>
            <div class="<?php esc_attr_e( self::set( array(
				'order-tracking-info-status',
				'order-tracking-info-status-' . $tracking_info['tracking_code']
			) ) ) ?> <?php echo $tracking_info['tracking_url'] ? esc_attr( self::set( 'order-tracking-info-hidden' ) ) : '' ?>">
				<?php echo esc_html__( 'Status: ', 'woo-orders-tracking' ) ?>
                <span></span>
            </div>
			<?php
		}
		$html = ob_get_clean();

		return ent2ncr( $html );
	}

	/**
	 * For TrackingMore
	 */
	public function vi_wot_refresh_track_info() {
		$order_id        = isset( $_POST['order_id'] ) ? sanitize_text_field( stripslashes( $_POST['order_id'] ) ) : '';
		$tracking_number = isset( $_POST['tracking_number'] ) ? sanitize_text_field( stripslashes( $_POST['tracking_number'] ) ) : '';
		$carrier_name    = isset( $_POST['carrier_name'] ) ? sanitize_text_field( stripslashes( $_POST['carrier_name'] ) ) : '';
		$carrier_id      = isset( $_POST['carrier_id'] ) ? sanitize_text_field( stripslashes( $_POST['carrier_id'] ) ) : '';
		if ( $order_id && $tracking_number && $carrier_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$carrier            = $this->settings->get_shipping_carrier_by_slug( $carrier_id );
				$tracking_more_slug = $carrier_id;
				if ( is_array( $carrier ) && count( $carrier ) ) {
					$carrier_name = $carrier['name'];
					if ( ! empty( $carrier['tracking_more_slug'] ) ) {
						$tracking_more_slug = $carrier['tracking_more_slug'];
					}
				}
				$send_data    = array(
					'carrier_id'            => $tracking_more_slug,
					'carrier_name'          => $carrier_name,
					'shipping_country_code' => $order->get_shipping_country(),
					'tracking_code'         => $tracking_number,
					'order_id'              => $order_id,
					'customer_phone'        => $order->get_billing_phone(),
					'customer_email'        => $order->get_billing_email(),
					'customer_name'         => $order->get_formatted_billing_full_name(),
				);
				$data_refresh = VI_WOO_ORDERS_TRACKING_TABLE_TRACKING::refresh_track_info_database( $send_data, $insert = true );
				if ( $data_refresh && $data_refresh['track_info'] ) {
					$track_info = $data_refresh['track_info'];
					switch ( $track_info['status'] ) {
						case 'success':
							wp_send_json(
								array(
									'status'              => 'success',
									'message'             => esc_html__( 'Successfully update tracking info', 'woo-orders-tracking' ),
									'shipment_status'     => $track_info['data']['status'],
									'shipment_last_event' => $track_info['data']['last_event'],
									'data'                => $data_refresh,
									'c'                   => $send_data,
								)
							);
							break;
						case 'error':
							wp_send_json(
								array(
									'status'  => 'error',
									'message' => $track_info['data'],
									'data'    => $data_refresh,
									'c'       => $send_data,
								)
							);
							break;
					}

				} else {
					wp_send_json(
						array(
							'status'  => 'error',
							'message' => esc_html__( 'Can not get data', 'woo-orders-tracking' ),
						)
					);
				}
			} else {
				wp_send_json(
					array(
						'status'  => 'error',
						'message' => esc_html__( 'Can not get data', 'woo-orders-tracking' ),
					)
				);
			}

		} else {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Not enough data', 'woo-orders-tracking' ),
					'data'    => array(
						'carrier_id'    => $carrier_id,
						'carrier_name'  => $carrier_name,
						'tracking_code' => $tracking_number,
						'order_id'      => $order_id,
					),
				)
			);
		}
	}

	public function restrict_manage_posts() {
		global $typenow;
		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
			?>
            <input type="text" name="woo_orders_tracking_search_tracking"
                   placeholder="<?php esc_attr_e( 'Search tracking number', 'woo-orders-tracking' ) ?>"
                   autocomplete="off"
                   value="<?php echo isset( $_GET['woo_orders_tracking_search_tracking'] ) ? esc_attr__( htmlentities( sanitize_text_field( $_GET['woo_orders_tracking_search_tracking'] ) ) ) : '' ?>">
			<?php
		}
	}

	public function posts_join( $join, $wp_query ) {
		global $wpdb;
		$join .= " JOIN {$wpdb->prefix}woocommerce_order_items as wotg_woocommerce_order_items ON $wpdb->posts.ID=wotg_woocommerce_order_items.order_id";
		$join .= " JOIN {$wpdb->prefix}woocommerce_order_itemmeta as wotg_woocommerce_order_itemmeta ON wotg_woocommerce_order_items.order_item_id=wotg_woocommerce_order_itemmeta.order_item_id";

		return $join;
	}

	public function posts_where( $where, $wp_query ) {
		global $wpdb;
		$tracking_code = isset( $_GET['woo_orders_tracking_search_tracking'] ) ? $_GET['woo_orders_tracking_search_tracking'] : '';
		if ( isset( $_GET['filter_action'] ) && 'Filter' == $_GET['filter_action'] && $tracking_code ) {
			$where .= $wpdb->prepare( " AND wotg_woocommerce_order_itemmeta.meta_key='_vi_wot_order_item_tracking_data' AND wotg_woocommerce_order_itemmeta.meta_value like %s", '%' . $wpdb->esc_like( $tracking_code ) . '%' );
			add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 2 );
			add_filter( 'posts_distinct', array( $this, 'posts_distinct' ), 10, 2 );
		}

		return $where;
	}

	public function posts_distinct( $join, $wp_query ) {
		return 'DISTINCT';
	}


	public function admin_enqueue_script() {
		global $pagenow, $post_type;
		if ( $pagenow === 'edit.php' && $post_type === 'shop_order' ) {
			wp_enqueue_style( 'vi-wot-admin-order-manager-icon', VI_WOO_ORDERS_TRACKING_CSS . 'woo-orders-tracking-icons.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-order-manager-css', VI_WOO_ORDERS_TRACKING_CSS . 'admin-order-manager.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			$css = '.woo-orders-tracking-tracking-number-container-delivered a{color:' . $this->settings->get_params( 'timeline_track_info_status_background_delivered' ) . '}';
			$css .= '.woo-orders-tracking-tracking-number-container-pickup a{color:' . $this->settings->get_params( 'timeline_track_info_status_background_pickup' ) . '}';
			$css .= '.woo-orders-tracking-tracking-number-container-transit a{color:' . $this->settings->get_params( 'timeline_track_info_status_background_transit' ) . '}';
			$css .= '.woo-orders-tracking-tracking-number-container-pending a{color:' . $this->settings->get_params( 'timeline_track_info_status_background_pending' ) . '}';
			$css .= '.woo-orders-tracking-tracking-number-container-alert a{color:' . $this->settings->get_params( 'timeline_track_info_status_background_alert' ) . '}';
			wp_add_inline_style( 'vi-wot-admin-order-manager-css', $css );
			wp_enqueue_script( 'vi-wot-admin-order-manager-js', VI_WOO_ORDERS_TRACKING_JS . 'admin-order-manager.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_localize_script(
				'vi-wot-admin-order-manager-js',
				'vi_wot_admin_order_manager',
				array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'paypal_image'  => VI_WOO_ORDERS_TRACKING_PAYPAL_IMAGE,
					'loading_image' => VI_WOO_ORDERS_TRACKING_LOADING_IMAGE,
					'message_copy'  => __( 'Tracking number is copied to clipboard', 'woo-orders-tracking' ),
				)
			);
		}
	}
}