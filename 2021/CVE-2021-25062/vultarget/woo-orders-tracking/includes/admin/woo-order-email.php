<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_ADMIN_WOO_ORDER_EMAIL {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WOO_ORDERS_TRACKING_DATA();
		add_action( 'woocommerce_email_after_order_table', array(
			$this,
			'include_tracking_info_to_woocommerce_email'
		), 20, 4 );
	}

	/**
	 * @param $order
	 * @param $sent_to_admin
	 * @param $plain_text
	 * @param $email
	 *
	 * @throws Exception
	 */
	public function include_tracking_info_to_woocommerce_email( $order, $sent_to_admin, $plain_text, $email ) {
		$email_woo_enable = $this->settings->get_params( 'email_woo_enable' );
		$email_woo_status = $this->settings->get_params( 'email_woo_status' );
		$type             = $email ? $email->id : ( isset( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : '' );
		if ( $email_woo_enable && $email_woo_status && is_array( $email_woo_status ) && in_array( $type, $email_woo_status ) ) {
			$this->include_tracking_info( $order );
		}
	}

	/**
	 * @param $order
	 * @param $sent_to_admin
	 * @param $plain_text
	 * @param $email
	 *
	 * @throws Exception
	 */
	public function woocommerce_email_after_order_table( $order, $sent_to_admin, $plain_text, $email ) {
		$this->include_tracking_info( $order );
	}

	/**
	 * @param $order WC_Order
	 *
	 * @throws Exception
	 */
	public function include_tracking_info( $order ) {
		if ( $order ) {
			$tracking_info = array();
			foreach ( $order->get_items() as $item_id => $item_value ) {
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

				$carrier_id    = $current_tracking_data['carrier_slug'];
				$tracking_code = $current_tracking_data['tracking_number'];
				$tracking_url  = $current_tracking_data['carrier_url'];
				$carrier_name  = $current_tracking_data['carrier_name'];
				$carrier_type  = $current_tracking_data['carrier_type'];
				$carrier       = $this->settings->get_shipping_carrier_by_slug( $carrier_id, '', false );
				if ( is_array( $carrier ) && count( $carrier ) ) {
					$tracking_url = $this->settings->get_url_tracking( $carrier['url'], $tracking_code, $carrier_id, $order->get_shipping_postcode() );
					$carrier_name = $carrier['name'];
					$carrier_type = $carrier['carrier_type'];
				}

				if ( $tracking_code && $carrier_id && $carrier_type && $tracking_url ) {
					$t = array(
						'tracking_code' => $tracking_code,
						'tracking_url'  => $tracking_url,
						'carrier_name'  => $carrier_name,
					);
					if ( ! in_array( $t, $tracking_info ) ) {
						$tracking_info[] = $t;
					}
				}
			}
			if ( empty( $tracking_info ) ) {
				return;
			}
			ob_start();
			?>
            <h2 class="email-upsell-title"><?php esc_html_e( 'Tracking information', 'woo-orders-tracking' ) ?></h2>

			<?php
			$text = '';
			foreach ( $tracking_info as $item ) {
				$text .= ', <a href="' . $item['tracking_url'] . '" target="_blank">' . $item['tracking_code'] . '</a>' . esc_html__( ' by ', 'woo-orders-tracking' ) . $item['carrier_name'];
			}
			$text = trim( $text, ',' );
			echo '<p>' . esc_html__( 'Your tracking number: ', 'woo-orders-tracking' ) . $text . '</p>';
			$html = ob_get_clean();
			echo ent2ncr( $html );
		}
	}

	/**
	 * @param $item_id
	 * @param $order WC_Order
	 * @param $plain_text
	 *
	 * @throws Exception
	 */
	public static function include_tracking_info_after_order_item( $item_id, $order, $plain_text ) {
		if ( $order && $plain_text === false ) {
			$settings              = new VI_WOO_ORDERS_TRACKING_DATA();
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

			$carrier_id    = $current_tracking_data['carrier_slug'];
			$tracking_code = $current_tracking_data['tracking_number'];
			$tracking_url  = $current_tracking_data['carrier_url'];
			$carrier_name  = $current_tracking_data['carrier_name'];
			$carrier       = $settings->get_shipping_carrier_by_slug( $carrier_id, '', false );
			if ( is_array( $carrier ) && count( $carrier ) ) {
				$tracking_url = $settings->get_url_tracking( $carrier['url'], $tracking_code, $carrier_id, $order->get_shipping_postcode() );
				$carrier_name = $carrier['name'];
			}
			if ( $tracking_code && $tracking_url ) {
				?>
                <div class="<?php esc_attr_e( 'woo-orders-tracking-orders-details' ) ?>"
                     style="margin: 5px 0;display: flex; flex-wrap: wrap;justify-content: space-between">
                    <div class="<?php esc_attr_e( 'woo-orders-tracking-orders-details-tracking-number' ) ?>">
                        <span><?php esc_html_e( 'Tracking Number: ', 'woo-orders-tracking' ) ?></span>
                        <a href="<?php esc_attr_e( $tracking_url ) ?>" rel="nofollow"
                           title="<?php esc_attr_e( "Tracking carrier {$carrier_name}", 'woo-orders-tracking' ) ?>"
                           target="_blank"><?php echo $tracking_code ?></a>
                    </div>
                    <div class="<?php esc_attr_e( 'woo-orders-tracking-orders-details-tracking-carrier' ) ?>">
                        <span><?php esc_html_e( 'Carrier: ', 'woo-orders-tracking' ) ?></span>
                        <a href="<?php esc_attr_e( $tracking_url ) ?>" rel="nofollow"
                           title="<?php esc_attr_e( "Tracking carrier {$carrier_name}", 'woo-orders-tracking' ) ?>"
                           target="_blank"><?php echo $carrier_name ?></a>
                    </div>
                </div>
				<?php
			}
		}
	}
}