<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VI_WOO_ORDERS_TRACKING_WIDGET' ) ) {
	class VI_WOO_ORDERS_TRACKING_WIDGET extends WP_Widget {

		public function __construct() {
			parent::__construct(
				"vi_wot_track_order",
				__( 'Track Order', 'woo-orders-tracking' ),
				array(
					'classname'   => 'vi-wot-widget-form-tracking',
					'description' => 'Check the status of your shipment'
				) );
		}

		public function widget( $args, $instance ) {
			if ( isset( $instance['title'] ) ) {
				$title = apply_filters( 'widget_title', $instance['title'] );
				echo $args['before_widget'];
				if ( $title ) {
					echo $args['before_title'] . $title . $args['after_title'];
				}
			}
			echo do_shortcode( '[vi_wot_form_track_order]' );
			echo $args['after_widget'];
		}

		public function form( $instance ) {
			$title = ! empty( $instance['title'] ) ? $instance['title'] : 'Track Order'; ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
            </p>
            <p>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
            </p><?php
		}


		public function update( $new_instance, $old_instance ) {
			$instance          = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );

			return $instance;
		}
	}

}

class VI_WOO_ORDERS_TRACKING_FRONTEND_FRONTEND {
	protected $settings;

	public function __construct() {
		$this->settings = new  VI_WOO_ORDERS_TRACKING_DATA();
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_shortcode( 'vi_wot_form_track_order', array( $this, 'shortcode_form_track_order' ) );
		add_action( 'widgets_init', array( $this, 'register_example_widget' ) );
		add_filter( 'the_content', array( $this, 'track_order_page_content' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	public function wp_enqueue_scripts() {
		$service_tracking_page = $this->settings->get_params( 'service_tracking_page' );
		if ( $service_tracking_page && $service_tracking_page_url = get_the_permalink( $service_tracking_page ) ) {
			wp_enqueue_style( 'vi-wot-frontend-shortcode-form-search-css', VI_WOO_ORDERS_TRACKING_CSS . 'frontend-shortcode-form-search.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'vi-wot-frontend-shortcode-form-search-js', VI_WOO_ORDERS_TRACKING_JS . 'frontend-shortcode-form-search.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			wp_localize_script( 'vi-wot-frontend-shortcode-form-search-js', 'vi_wot_frontend_form_search',
				array(
					'ajax_url'         => admin_url( 'admin-ajax.php' ),
					'track_order_url'  => $service_tracking_page_url,
					'error_empty_text' => __( 'Please fill your order tracking number', 'woo-orders-tracking' ),
				) );
		}

		if ( $this->is_tracking_page() ) {
			wp_enqueue_style( 'vi-wot-frontend-shortcode-track-order-icons', VI_WOO_ORDERS_TRACKING_CSS . 'woo-orders-tracking-icons.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-frontend-shortcode-track-order-css', VI_WOO_ORDERS_TRACKING_CSS . 'frontend-shortcode-track-order.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-frontend-shortcode-track-order-icon', VI_WOO_ORDERS_TRACKING_CSS . 'frontend-shipment-icon.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_localize_script( 'vi-wot-frontend-shortcode-track-order-js',
				'vi_wot_shortcode_timeline',
				array(
					'ajax_url'        => admin_url( 'admin-ajax.php' ),
					'tracking_number' => isset( $_REQUEST['tracking_id'] ) ? sanitize_text_field( $_REQUEST['tracking_id'] ) : '',
				)
			);
			$css = '';
			//general
			$css .= $this->add_inline_style(
				array(
					'timeline_track_info_title_alignment',
					'timeline_track_info_title_color',
					'timeline_track_info_title_font_size',
				),
				'.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-title',
				array(
					'text-align',
					'color',
					'font-size',
				), array(
					'',
					'',
					'px'
				)
			);
			$css .= $this->add_inline_style(
				array(
					'timeline_track_info_status_color',
				),
				'.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap',
				array(
					'color',
				), array(
				'',
			) );
			$css .= $this->add_inline_style(
				array(
					'timeline_track_info_status_background_delivered',
				),
				'.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-delivered',
				array(
					'background-color',
				), array(
				'',
			) );
			$css .= $this->add_inline_style(
				array(
					'timeline_track_info_status_background_pickup',
				),
				'.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-pickup',
				array(
					'background-color',
				), array(
				'',
			) );
			$css .= $this->add_inline_style(
				array(
					'timeline_track_info_status_background_transit',
				),
				'.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-transit',
				array(
					'background-color',
				), array(
				'',
			) );
			$css .= $this->add_inline_style(
				array(
					'timeline_track_info_status_background_pending',
				),
				'.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-pending',
				array(
					'background-color',
				), array(
				'',
			) );
			$css .= $this->add_inline_style(
				array(
					'timeline_track_info_status_background_alert',
				),
				'.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-alert',
				array(
					'background-color',
				), array(
				'',
			) );
			/*
			 * template one
			 */
			if ( $this->settings->get_params( 'timeline_track_info_template' ) === '1' ) {
				$css .= $this->add_inline_style(
					array(
						'icon_delivered_color',
					),
					'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one
.woo-orders-tracking-shortcode-timeline-events-wrap
.woo-orders-tracking-shortcode-timeline-event
.woo-orders-tracking-shortcode-timeline-icon-delivered i:before',
					array(
						'color',
					),
					array(
						'',
					),
					array(
						'timeline_track_info_template_one',
					) );
				$css .= $this->add_inline_style(
					array(
						'icon_delivered_color',
					),
					'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one
.woo-orders-tracking-shortcode-timeline-events-wrap
.woo-orders-tracking-shortcode-timeline-event
.woo-orders-tracking-shortcode-timeline-icon-delivered svg circle',
					array(
						'fill',
					), array(
					''
				),
					array(
						'timeline_track_info_template_one'
					)
				);

				$css .= $this->add_inline_style(
					array(
						'icon_pickup_color',
					),
					'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one
.woo-orders-tracking-shortcode-timeline-events-wrap
.woo-orders-tracking-shortcode-timeline-event
.woo-orders-tracking-shortcode-timeline-icon-pickup i:before',
					array(
						'color',
					),
					array(
						''
					),
					array(
						'timeline_track_info_template_one'
					)
				);

				$css .= $this->add_inline_style(
					array(
						'icon_pickup_background',
					),
					'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one
.woo-orders-tracking-shortcode-timeline-events-wrap
.woo-orders-tracking-shortcode-timeline-event
.woo-orders-tracking-shortcode-timeline-icon-pickup ',
					array(
						'background-color',
					),
					array(
						'',
					),
					array(
						'timeline_track_info_template_one'
					) );

				$css .= $this->add_inline_style(
					array(
						'icon_transit_color',
					),
					'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one
.woo-orders-tracking-shortcode-timeline-events-wrap
.woo-orders-tracking-shortcode-timeline-event
.woo-orders-tracking-shortcode-timeline-icon-transit i:before',
					array(
						'color',
					),
					array(
						'',
					),
					array(
						'timeline_track_info_template_one'
					) );

				$css .= $this->add_inline_style(
					array(
						'icon_transit_background',
					),
					'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one
.woo-orders-tracking-shortcode-timeline-events-wrap
.woo-orders-tracking-shortcode-timeline-event
.woo-orders-tracking-shortcode-timeline-icon-transit ',
					array(
						'background-color',
					),
					array(
						'',
					),
					array(
						'timeline_track_info_template_one'
					) );
			}
			$css .= $this->settings->get_params( 'custom_css' );
			wp_add_inline_style( 'vi-wot-frontend-shortcode-track-order-css', $css );
		}

	}

	public function shortcode_form_track_order() {
		ob_start();
		?>
        <form action="<?php esc_attr_e( esc_url( get_the_permalink( $this->settings->get_params( 'service_tracking_page' ) ) ) ) ?>"
              method="get"
              class="vi-woocommerce-orders-tracking-form-search">
			<?php
			//			wp_nonce_field( 'woo_orders_tracking_nonce_action', 'woo_orders_tracking_nonce' );
			?>
            <div class="vi-woocommerce-orders-tracking-form-row">
                <input type="search"
                       id="vi-woocommerce-orders-tracking-form-search-tracking-number"
                       class="vi-woocommerce-orders-tracking-form-search-tracking-number"
                       placeholder="<?php esc_html_e( 'Tracking number', 'woo-orders-tracking' ) ?>"
                       name="tracking_id"
                       autocomplete="off"
                       value="<?php esc_attr_e( isset( $_REQUEST['tracking_id'] ) ? sanitize_text_field( $_REQUEST['tracking_id'] ) : '' ) ?>">
                <input type="submit" name="submit_tracking"
                       class="vi-woocommerce-orders-tracking-form-search-tracking-number-btnclick"
                       value="<?php esc_attr_e( 'Track', 'woo-orders-tracking' ) ?>">
            </div>
        </form>
		<?php
		$results = ob_get_clean();

		return $results;
	}

	public function register_example_widget() {
		register_widget( 'VI_WOO_ORDERS_TRACKING_WIDGET' );
	}

	protected function is_tracking_page() {
		$service_tracking_page = $this->settings->get_params( 'service_tracking_page' );
		$return                = false;
		if ( $service_tracking_page ) {
			$return = is_page( $service_tracking_page );
		}

		return $return;
	}

	public function track_order_page_content( $content ) {
		if ( $this->is_tracking_page() ) {
			if ( is_customize_preview() ) {
				ob_start();
				echo do_shortcode( '[vi_wot_form_track_order]' );
				echo do_shortcode( '[vi_wot_track_order_timeline tracking_code = "customize_preview" preview="true" ]' );
				$html    = ob_get_clean();
				$content .= $html;

				return ent2ncr( $content );
			} elseif ( $this->settings->get_params( 'service_carrier_enable' ) ) {
				ob_start();
				echo do_shortcode( '[vi_wot_form_track_order]' );
				if ( isset( $_GET['tracking_id'] ) ) {
					$tracking_number = sanitize_text_field( $_GET['tracking_id'] );
					echo do_shortcode( '[vi_wot_track_order_timeline tracking_code = ' . $tracking_number . ']' );
				}
				$html    = ob_get_clean();
				$content .= $html;

				return ent2ncr( $content );
			}
		}

		return $content;
	}

	/**
	 * @param $name
	 * @param bool $set_name
	 *
	 * @return string
	 */
	public static function set( $name, $set_name = false ) {
		return VI_WOO_ORDERS_TRACKING_DATA::set( $name, $set_name );
	}

	/**
	 *
	 */
	public function init() {
		add_shortcode( 'vi_wot_track_order_timeline', array( $this, 'shortcode_track_order_timeline' ) );
	}

	public function shortcode_track_order_timeline( $args ) {
		$tracking_code = $args['tracking_code'];
		if ( $tracking_code === 'customize_preview' && $args['preview'] === "true" ) {
			return $this->get_template( 'customize', 'require' );
		}

		return $this->get_template( 'shortcode_timeline', 'function', $tracking_code );
	}

	public static function display_timeline( $data, $tracking_code ) {
		$settings        = new VI_WOO_ORDERS_TRACKING_DATA();
		$sort_event      = $settings->get_params( 'timeline_track_info_sort_event' );
		$template        = $settings->get_params( 'timeline_track_info_template' );
		$title           = $settings->get_params( 'timeline_track_info_title' );
		$date_format     = $settings->get_params( 'timeline_track_info_date_format' );
		$time_format     = $settings->get_params( 'timeline_track_info_time_format' );
		$datetime_format = $date_format . ' ' . $time_format;
		$status          = VI_WOO_ORDERS_TRACKING_DATA::convert_status( $data['status'] );
		$status_text     = VI_WOO_ORDERS_TRACKING_DATA::get_status_text( $status );
		$track_info      = $data['tracking'];
		$carrier_name    = $data['carrier_name'];
		$title           = str_replace(
			array(
				'{carrier_name}',
				'{tracking_number}',
			),
			array(
				$carrier_name,
				strtoupper( $tracking_code )
			),
			$title
		);
		if ( $track_info && count( $track_info ) ) {
			if ( $sort_event === 'oldest_to_most_recent' ) {
				krsort( $track_info );
				$track_info = array_values( $track_info );
			}
			switch ( $template ) {
				case '1':
					$template_class = 'template-one';
					?>
                    <div class="<?php esc_attr_e( self::set( array(
						'shortcode-timeline-wrap-' . $template_class,
						'shortcode-timeline-wrap-' . $sort_event,
						'shortcode-timeline-wrap'
					) ) ) ?>">
						<?php
						if ( $title ) {
							?>
                            <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-title' ) ) ?>">
                                <span><?php echo $title ?></span>
                            </div>
							<?php
						}
						?>
                        <div class="<?php esc_attr_e( self::set( array(
							'shortcode-timeline-status-wrap',
							'shortcode-timeline-status-' . $status
						) ) ) ?>">
							<?php echo $status_text; ?>
                        </div>
						<?php
						if ( ! empty( $data['modified_at'] ) ) {
							?>
                            <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-last-update' ) ) ?>">
								<?php
								if ( $status !== 'delivered' && strtotime( $data['est_delivery_date'] ) > time() ) {
									?>
                                    <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-estimated-delivery-date' ) ) ?>">
										<?php esc_html_e( 'Estimated Delivery Date: ', 'woo-orders-tracking' ) ?>
                                        <span><?php echo date_format( date_create( $data['est_delivery_date'] ), $datetime_format ) ?></span>
                                    </div>
									<?php
								}
								?>
                                <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-last-update-text' ) ) ?>"><?php esc_html_e( 'Last Updated: ', 'woo-orders-tracking' ) ?>
                                    <span><?php echo date_format( date_create( $data['modified_at'] ), $datetime_format ) ?></span>
                                </div>
                            </div>
							<?php
						}

						?>
                        <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-events-wrap' ) ); ?>">
							<?php
							$track_info_count = count( $track_info );
							$event_no         = $sort_event === 'oldest_to_most_recent' ? 1 : $track_info_count;
							for ( $i = 0; $i < $track_info_count; $i ++ ) {
								$event_status = VI_WOO_ORDERS_TRACKING_DATA::convert_status( $track_info[ $i ]['status'] );
								?>
                                <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event' ) ) ?>">
                                    <div class="<?php esc_attr_e( self::set( array(
										'shortcode-timeline-icon',
										'shortcode-timeline-icon-' . $event_status
									) ) ) ?>"
                                         title="<?php echo VI_WOO_ORDERS_TRACKING_DATA::get_status_text( $event_status ) ?>">
										<?php
										echo self::get_icon_status( $event_status, $template );
										?>
                                    </div>
                                    <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-content-wrap' ) ) ?>">
                                        <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-content' ) ) ?>">
                                            <span class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-des' ) ) ?>">
												<?php echo esc_html( "$event_no. {$track_info[ $i ]['description']}" ) ?>
                                            </span>
                                            <div>
                                                <span class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-location' ) ) ?>">
                                                    <?php echo trim( $track_info[ $i ]['location'], ' ' ) ?>
                                                </span>
                                                <span class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-time' ) ) ?>">
                                                    <?php echo date_format( date_create( $track_info[ $i ]['time'] ), $datetime_format ); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<?php
								if ( $sort_event === 'oldest_to_most_recent' ) {
									$event_no ++;
								} else {
									$event_no --;
								}
							}
							?>
                        </div>
                    </div>
					<?php
					break;
				case '2':
					$template_class = 'template-two';
					?>
                    <div class="<?php esc_attr_e( self::set( array(
						'shortcode-timeline-wrap-' . $template_class,
						'shortcode-timeline-wrap-' . $sort_event,
						'shortcode-timeline-wrap'
					) ) ) ?>">
						<?php
						if ( $title ) {
							?>
                            <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-title' ) ) ?>">
                                <span><?php echo $title ?></span>
                            </div>
							<?php
						}
						?>
                        <div class="<?php esc_attr_e( self::set( array(
							'shortcode-timeline-status-wrap',
							'shortcode-timeline-status-' . $status
						) ) ) ?>">
							<?php echo $status_text; ?>
                        </div>
						<?php
						if ( ! empty( $data['modified_at'] ) ) {
							?>
                            <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-last-update' ) ) ?>">
								<?php
								if ( $status !== 'delivered' && strtotime( $data['est_delivery_date'] ) > time() ) {
									?>
                                    <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-estimated-delivery-date' ) ) ?>">
										<?php esc_html_e( 'Estimated Delivery Date: ', 'woo-orders-tracking' ) ?>
                                        <span><?php echo date_format( date_create( $data['est_delivery_date'] ), $datetime_format ) ?></span>
                                    </div>
									<?php
								}
								?>
                                <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-last-update-text' ) ) ?>"><?php esc_html_e( 'Last Updated: ', 'woo-orders-tracking' ) ?>
                                    <span><?php echo date_format( date_create( $data['modified_at'] ), $datetime_format ) ?></span>
                                </div>
                            </div>
							<?php
						}
						?>
                        <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-events-wrap' ) ); ?>">
							<?php
							$group_event      = '';
							$track_info_count = count( $track_info );
							for ( $i = 0; $i < count( $track_info ); $i ++ ) {
								ob_start();
								$event_status = VI_WOO_ORDERS_TRACKING_DATA::convert_status( $track_info[ $i ]['status'] );
								?>
                                <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event' ) ) ?>">
                                    <div class="<?php esc_attr_e( self::set( array(
										'shortcode-timeline-icon',
										'shortcode-timeline-icon-' . $event_status
									) ) ) ?>">
                                    </div>
                                    <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-content-wrap' ) ) ?>">
                                        <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-content-date' ) ) ?>">
											<?php
											echo date_format( date_create( $track_info[ $i ]['time'] ), $datetime_format )
											?>
                                        </div>
                                        <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-content-des-wrap' ) ) ?>">
                                            <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-content-des' ) ) ?>">
												<?php echo $track_info[ $i ]['description'] ?>
                                            </div>
                                            <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-event-location' ) ) ?>">
												<?php echo trim( $track_info[ $i ]['location'], ' ' ) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<?php
								$group_event .= ob_get_clean();
								if ( $i < $track_info_count - 1 ) {
									if ( strtotime( date( 'Y-m-d', strtotime( $track_info[ $i ]['time'] ) ) ) !== strtotime( date( 'Y-m-d', strtotime( $track_info[ $i + 1 ]['time'] ) ) ) ) {
										echo '<div class="woo-orders-tracking-shortcode-timeline-events-group">' . $group_event . '</div>';
										$group_event = '';
									}
								} else {
									echo '<div class="woo-orders-tracking-shortcode-timeline-events-group">' . $group_event . '</div>';
									$group_event = '';
								}
							}
							?>
                        </div>
                    </div>
					<?php
					break;
			}
		} else {
			?>
            <p><?php esc_html_e( 'No result found for this tracking number.', 'woo-orders-tracking' ) ?></p>
			<?php
		}
	}

	/**
	 * @param $tracking_code
	 *
	 * @throws Exception
	 */
	private function shortcode_timeline( $tracking_code ) {
		global $wpdb;
		?>
        <div class="<?php esc_attr_e( self::set( 'shortcode-timeline-container' ) ); ?>"
             data-tracking_code="<?php esc_attr_e( $tracking_code ) ?>">
			<?php
			$service_carrier_type = $this->settings->get_params( 'service_carrier_type' );
			$found_tracking       = false;
			if ( $service_carrier_type === 'trackingmore' ) {
				$table_track_info = $wpdb->prefix . 'wotv_woo_track_info';
				$sql              = "SELECT * FROM {$table_track_info} WHERE tracking_number = %s";
				$data             = $wpdb->get_results( $wpdb->prepare( $sql, $tracking_code ) );
				if ( $data && count( $data ) ) {
					$found_tracking = true;
					$old_tracking   = $data[0];
					$old_track_info = $old_tracking->track_info;
					if ( ! $old_track_info ) {
						$carrier_name       = $old_tracking->carrier_name;
						$carrier_id         = $old_tracking->carrier_id;
						$carrier            = $this->settings->get_shipping_carrier_by_slug( $carrier_id );
						$tracking_more_slug = $carrier_id;
						if ( is_array( $carrier ) && count( $carrier ) ) {
							$carrier_name = $carrier['name'];
							if ( ! empty( $carrier['tracking_more_slug'] ) ) {
								$tracking_more_slug = $carrier['tracking_more_slug'];
							}
						}
						$shipping_country_code   = $old_tracking->shipping_country_code;
						$service_carrier_api_key = $this->settings->get_params( 'service_carrier_api_key' );
						$date                    = date( 'Y-m-d H:i:s' );
						$new_data                = VI_WOO_ORDERS_TRACKING_ADMIN_TRACK_ORDER_DATA::tracking_info( $tracking_code, $service_carrier_api_key, $service_carrier_type, $tracking_more_slug, $carrier_name, $shipping_country_code );
						$sql                     = "UPDATE {$table_track_info} SET status= %s, carrier_id= %s, carrier_name = %s, shipping_country_code = %s, track_info= %s, last_event= %s, modified_at= %s  WHERE tracking_number = %s";
						if ( $new_data ) {
							switch ( $new_data['status'] ) {
								case 'success':
									$t = $wpdb->query( $wpdb->prepare( $sql, $new_data['data']['status'], $carrier_id, $carrier_name, $shipping_country_code, json_encode( $new_data['data'] ), $new_data['data']['last_event'], $date, $tracking_code ) );

									$this->display_timeline( $new_data['data'], $tracking_code );
									break;
								case 'error':
									$t = $wpdb->query( $wpdb->prepare( $sql, '', $carrier_id, $carrier_name, $shipping_country_code, '', json_encode( $new_data['data'] ), $date, $tracking_code ) );
									?>
                                    <p><?php esc_html_e( 'Sorry, no tracking information is available now.', 'woo-orders-tracking' ) ?></p>
									<?php
									break;
							}
						} else {
							$found_tracking = false;
						}
					} else {
						$this->display_timeline( json_decode( $old_track_info, true ), $tracking_code );
					}
				} else {
					$found_tracking = false;
				}
			}
			if ( ! $found_tracking ) {
				?>
                <p><?php esc_html_e( 'No result found for this tracking number', 'woo-orders-tracking' ) ?></p>
				<?php
			}
			?>
        </div>
		<?php
	}

	public static function get_track_info( $detailList ) {
		$track_info = array();
		foreach ( $detailList as $item ) {
			$time = $item['time'];
			if ( $item['timeZone'] ) {
				$time = date( 'Y-m-d H:i:s', strtotime( "{$item['time']} {$item['timeZone']}" ) );
			}
			$track_info[] = array(
				'time'        => $time,
				'description' => $item['desc'],
				'location'    => '',
				'status'      => $item['status'],
			);
		}

		return $track_info;
	}

	private static function get_icon_status_delivered( $setting_icon ) {
		$icons = VI_WOO_ORDERS_TRACKING_DATA::get_delivered_icons();

		return isset( $icons[ $setting_icon ] ) ? "<i class='{$icons[$setting_icon]}'></i>" : '';
	}

	private static function get_icon_status_pickup( $setting_icon ) {
		$icons = VI_WOO_ORDERS_TRACKING_DATA::get_pickup_icons();

		return isset( $icons[ $setting_icon ] ) ? "<i class='{$icons[$setting_icon]}'></i>" : '';
	}

	private static function get_icon_status_transit( $setting_icon ) {
		$icons = VI_WOO_ORDERS_TRACKING_DATA::get_transit_icons();

		return isset( $icons[ $setting_icon ] ) ? "<i class='{$icons[$setting_icon]}'></i>" : '';
	}

	public static function get_default_icon() {
		return '<span class="woo-orders-tracking-icon-default"></span>';
	}

	public static function get_icon_status( $status, $template, $icon = '' ) {
		$settings = new VI_WOO_ORDERS_TRACKING_DATA();
		$result   = '';
		if ( $template === '1' ) {
			switch ( $status ) {
				case 'delivered':
					if ( ! $icon ) {
						$icon = $settings->get_params( 'icon_delivered', 'timeline_track_info_template_one' );
					}
					$result = self::get_icon_status_delivered( $icon );
					break;
				case 'pickup':
					if ( ! $icon ) {
						$icon = $settings->get_params( 'icon_pickup', 'timeline_track_info_template_one' );
					}
					$result = self::get_icon_status_pickup( $icon );
					break;
				case 'transit':
					if ( ! $icon ) {
						$icon = $settings->get_params( 'icon_transit', 'timeline_track_info_template_one' );
					}
					$result = self::get_icon_status_transit( $icon );
					break;
				case 'alert':
					$result = '<span class="woo_orders_tracking_icons-warning"></span>';
					break;
				default:
					$result = self::get_default_icon();
			}
		}

		return $result;
	}

	/**
	 * @param $name
	 * @param $type
	 * @param string $tracking_code
	 *
	 * @return string
	 */
	protected function get_template( $name, $type, $tracking_code = '' ) {
		ob_start();
		if ( $type === 'require' ) {
			require_once VI_WOO_ORDERS_TRACKING_TEMPLATES . $name . '.php';
		} elseif ( $type === 'function' ) {
			$this->$name( $tracking_code );
		}
		$html = ob_get_clean();

		return ent2ncr( $html );
	}

	private function add_inline_style( $name, $element, $style, $suffix = '', $type = array(), $echo = false ) {
		$return = $element . '{';
		if ( is_array( $name ) && count( $name ) ) {
			foreach ( $name as $key => $value ) {
				$t      = isset( $type[ $key ] ) ? $type[ $key ] : '';
				$return .= $style[ $key ] . ':' . $this->settings->get_params( $name[ $key ], $t ) . $suffix[ $key ] . ';';
			}
		}
		$return .= '}';
		if ( $echo ) {
			echo $return;
		}

		return $return;
	}
}