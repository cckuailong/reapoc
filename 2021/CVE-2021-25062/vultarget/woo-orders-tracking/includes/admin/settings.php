<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}


class VI_WOO_ORDERS_TRACKING_ADMIN_SETTINGS {
	private $settings;
	private $schedule_send_emails;
	private $shipping_countries;
	private $service_carriers_list;

	public function __construct() {
		$this->settings              = new VI_WOO_ORDERS_TRACKING_DATA();
		$this->service_carriers_list = VI_WOO_ORDERS_TRACKING_DATA::service_carriers_list();
		$this->enqueue_action();
	}

	public static function set( $name, $set_name = false ) {
		return VI_WOO_ORDERS_TRACKING_DATA::set( $name, $set_name );
	}

	public function enqueue_action() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'admin_init', array( $this, 'orders_tracking_setting_save_data' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ) );

		add_action( 'admin_footer', array( $this, 'orders_tracking_admin_footer' ) );


		add_action( 'wp_ajax_wotv_admin_add_new_shipping_carrier', array(

			$this,

			'wotv_admin_add_new_shipping_carrier'

		) );

		add_action( 'wp_ajax_wotv_admin_edit_shipping_carrier', array( $this, 'wotv_admin_edit_shipping_carrier' ) );

		add_action( 'wp_ajax_wotv_admin_delete_shipping_carrier', array(

			$this,

			'wotv_admin_delete_shipping_carrier'

		) );

		add_action( 'wp_ajax_wotv_admin_choose_default_shipping_carrier', array(

			$this,

			'wotv_admin_choose_default_shipping_carrier'

		) );

		add_action( 'media_buttons', array( $this, 'preview_emails_button' ) );

		add_action( 'wp_ajax_wot_preview_emails', array( $this, 'wot_preview_emails' ) );

		add_action( 'wp_ajax_wot_test_connection_paypal', array( $this, 'wot_test_connection_paypal' ) );
		add_action( 'wp_ajax_woo_orders_tracking_search_page', array( $this, 'search_page' ) );
	}

	public function admin_menu() {
		add_menu_page(
			__( 'Orders Tracking for WooCommerce settings', 'woo-orders-tracking' ),
			__( 'Orders Tracking', 'woo-orders-tracking' ),
			'manage_options',
			'woo-orders-tracking',
			array( $this, 'settings_callback' ),
			'dashicons-location',
			'2'
		);
	}


	public function orders_tracking_setting_save_data() {
		global $pagenow;
		global $woo_orders_tracking_settings;
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( $pagenow === 'admin.php' && isset( $_REQUEST['page'] ) && sanitize_text_field( $_REQUEST['page'] ) === 'woo-orders-tracking' ) {
			if ( ! isset( $_POST['woo-orders-tracking-settings-save-button'] ) ) {
				return;
			}
			if ( ! isset( $_POST['_vi_wot_setting_nonce'] ) || ! wp_verify_nonce( $_POST['_vi_wot_setting_nonce'], 'vi_wot_setting_action_nonce' ) ) {
				return;
			}
			$args                           = $this->settings->get_params();
			$args['service_carrier_enable'] = isset( $_POST['woo-orders-tracking-settings']['service_carrier']['service_carrier_enable'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['service_carrier']['service_carrier_enable'] ) : '';

			$args['service_carrier_type']    = isset( $_POST['woo-orders-tracking-settings']['service_carrier']['service_carrier_type'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['service_carrier']['service_carrier_type'] ) : '';
			$args['service_tracking_page']   = isset( $_POST['woo-orders-tracking-settings']['service_carrier']['service_tracking_page'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['service_carrier']['service_tracking_page'] ) : '';
			$args['service_carrier_api_key'] = isset( $_POST['woo-orders-tracking-settings']['service_carrier']['service_carrier_api_key'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['service_carrier']['service_carrier_api_key'] ) : '';
			if ( $args['service_carrier_type'] ) {
				$args['service_carrier_default'] = isset( $_POST['woo-orders-tracking-settings']['service_carrier'][ 'service_carrier_default_' . $args['service_carrier_type'] ] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['service_carrier'][ 'service_carrier_default_' . $args['service_carrier_type'] ] ) : array();
			}
			$args['email_woo_enable']         = isset( $_POST['woo-orders-tracking-settings']['email_woo']['email_woo_enable'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['email_woo']['email_woo_enable'] ) : '';
			$args['email_woo_status']         = isset( $_POST['woo-orders-tracking-settings']['email_woo']['email_woo_status'] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['email_woo']['email_woo_status'] ) : array();
			$args['email_woo_position']       = isset( $_POST['woo-orders-tracking-settings']['email_woo']['email_woo_position'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['email_woo']['email_woo_position'] ) : 'after_order_table';
			$args['email_time_send']          = isset( $_POST['woo-orders-tracking-settings']['email']['email_time_send'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['email']['email_time_send'] ) : '';
			$args['email_time_send_type']     = isset( $_POST['woo-orders-tracking-settings']['email']['email_time_send_type'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['email']['email_time_send_type'] ) : '';
			$args['email_number_send']        = isset( $_POST['woo-orders-tracking-settings']['email']['email_number_send'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['email']['email_number_send'] ) : '';
			$args['email_subject']            = isset( $_POST['woo-orders-tracking-settings']['email']['email_subject'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['email']['email_subject'] ) : '';
			$args['email_heading']            = isset( $_POST['woo-orders-tracking-settings']['email']['email_heading'] ) ? self::stripslashes( $_POST['woo-orders-tracking-settings']['email']['email_heading'] ) : '';
			$args['email_content']            = isset( $_POST['woo-orders-tracking-settings']['email']['email_content'] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['email']['email_content'] ) : '';
			$args['paypal_sandbox_enable']    = isset( $_POST['woo-orders-tracking-settings']['paypal']['paypal_sandbox_enable'] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['paypal']['paypal_sandbox_enable'] ) : array();
			$args['paypal_method']            = isset( $_POST['woo-orders-tracking-settings']['paypal']['paypal_method'] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['paypal']['paypal_method'] ) : array();
			$args['paypal_client_id_live']    = isset( $_POST['woo-orders-tracking-settings']['paypal']['paypal_client_id_live'] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['paypal']['paypal_client_id_live'] ) : array();
			$args['paypal_client_id_sandbox'] = isset( $_POST['woo-orders-tracking-settings']['paypal']['paypal_client_id_sandbox'] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['paypal']['paypal_client_id_sandbox'] ) : array();
			$args['paypal_secret_live']       = isset( $_POST['woo-orders-tracking-settings']['paypal']['paypal_secret_live'] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['paypal']['paypal_secret_live'] ) : array();
			$args['paypal_secret_sandbox']    = isset( $_POST['woo-orders-tracking-settings']['paypal']['paypal_secret_sandbox'] ) ? self::stripslashes_deep( $_POST['woo-orders-tracking-settings']['paypal']['paypal_secret_sandbox'] ) : array();

			update_option( 'woo_orders_tracking_settings', $args );
			$woo_orders_tracking_settings = $args;
		}
	}


	private static function stripslashes( $value ) {
		return sanitize_text_field( stripslashes( $value ) );
	}

	private static function stripslashes_deep( $value ) {

		if ( is_array( $value ) ) {

			$value = array_map( 'stripslashes_deep', $value );

		} else {

			$value = wp_kses_post( stripslashes( $value ) );

		}


		return $value;

	}


	public function settings_callback() {
		$this->settings = new VI_WOO_ORDERS_TRACKING_DATA();
		?>

        <div class="wrap">

            <h2><?php esc_html_e( 'Orders Tracking for WooCommerce settings', 'woo-orders-tracking' ); ?></h2>

            <div class="vi-ui raised">

                <form action="" class="vi-ui form" method="post">

					<?php

					wp_nonce_field( 'vi_wot_setting_action_nonce', '_vi_wot_setting_nonce' );

					?>

                    <div class="vi-ui vi-ui-main top tabular attached menu ">

                        <a class="item active" data-tab="shipping_carriers">

							<?php esc_html_e( 'Shipping Carriers', 'woo-orders-tracking' ) ?>

                        </a>


                        <a class="item " data-tab="email">

							<?php esc_html_e( 'Email', 'woo-orders-tracking' ) ?>

                        </a>
                        <a class="item " data-tab="email_woo">

							<?php esc_html_e( 'WooCommerce Email', 'woo-orders-tracking' ) ?>

                        </a>
                        <a class="item " data-tab="sms">

							<?php esc_html_e( 'SMS', 'woo-orders-tracking' ) ?>

                        </a>

                        <a class="item " data-tab="paypal">

							<?php esc_html_e( 'PayPal', 'woo-orders-tracking' ) ?>

                        </a>

                        <a class="item" data-tab="tracking_service">

							<?php esc_html_e( 'Tracking Service', 'woo-orders-tracking' ) ?>

                        </a>
                    </div>

                    <div class="vi-ui bottom attached tab segment active" data-tab="shipping_carriers">

						<?php

						$this->shipping_carriers_settings();

						?>

                    </div>

                    <div class="vi-ui bottom attached tab segment" data-tab="email">

						<?php

						$this->email_settings();

						?>

                    </div>

                    <div class="vi-ui bottom attached tab segment" data-tab="email_woo">

						<?php

						$this->email_woo_settings();

						?>

                    </div>
                    <div class="vi-ui bottom attached tab segment" data-tab="sms">
						<?php
						$this->sms_settings();
						?>
                    </div>

                    <div class="vi-ui bottom attached tab segment" data-tab="paypal">

						<?php

						$this->paypal_settings();

						?>

                    </div>


                    <div class="vi-ui bottom attached tab segment" data-tab="tracking_service">

						<?php

						$this->tracking_service_settings();

						?>

                    </div>

                    <p>

                        <button type="submit"

                                name="<?php esc_attr_e( self::set( 'settings-save-button' ) ) ?>"

                                class="<?php esc_attr_e( self::set( 'settings-save-button' ) ) ?> vi-ui button primary">

							<?php

							esc_html_e( 'Save', 'woo-orders-tracking' )

							?>

                        </button>

                    </p>

                </form>

            </div>

        </div>

		<?php

		do_action( 'villatheme_support_woo-orders-tracking' );

	}

	private function sms_settings() {
		?>
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'sms_text_new' ) ) ?>">
						<?php esc_html_e( 'Message text when new tracking is added', 'woocommerce-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'sms_text' ) ) ?>">
						<?php esc_html_e( 'Message text when tracking changes', 'woocommerce-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'sms_provider' ) ) ?>">
						<?php esc_html_e( 'SMS provider', 'woocommerce-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'sms_from_number' ) ) ?>">
						<?php esc_html_e( 'From number', 'woocommerce-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'sms_twilio_app_id' ) ) ?>">
						<?php esc_html_e( 'App ID', 'woocommerce-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'sms_twilio_app_token' ) ) ?>">
						<?php esc_html_e( 'AUTH TOKEN', 'woocommerce-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'bitly_access_token' ) ) ?>">
						<?php esc_html_e( 'Bitly access token', 'woocommerce-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                    <p class="description"><?php echo wp_kses_post( __( 'Using Bitly to shorten your tracking url helps reduce message characters. Please read <a href="https://support.bitly.com/hc/en-us/articles/230647907-How-do-I-find-my-OAuth-access-token-" target="_blank">How to get Access Token</a>', 'woocommerce-orders-tracking' ) ) ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'send_test_sms' ) ) ?>">
						<?php esc_html_e( 'Send test SMS', 'woocommerce-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            </tbody>
        </table>
		<?php
	}

	private function shipping_carriers_settings() {

		$countries = new WC_Countries();

		$countries = $countries->get_countries();

		?>

        <div class="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-overlay', 'hidden' ) ) ) ?>">
        </div>
        <div class="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-header' ) ) ) ?>">

            <div class="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-filter-wrap' ) ) ) ?>">

                <div class="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-filter-type-wrap' ) ) ) ?>">

                    <select name=""

                            id="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-filter-type' ) ) ) ?>"

                            class="vi-ui dropdown fluid <?php esc_attr_e( self::set( array( 'setting-shipping-carriers-filter-type' ) ) ) ?>"

                    >

                        <option value="all"><?php esc_html_e( 'All Carriers', 'woo-orders-tracking' ) ?></option>

                        <option value="custom"><?php esc_html_e( 'Custom Carriers ', 'woo-orders-tracking' ) ?></option>


                    </select>

                </div>

                <div class="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-filter-country-wrap' ) ) ) ?>">

                    <select name=""

                            id="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-filter-country' ) ) ) ?>"

                            class="select2-hidden-accessible <?php esc_attr_e( self::set( array( 'setting-shipping-carriers-filter-country' ) ) ) ?>"

                            tabindex="-1" aria-hidden="true"

                    >

                        <option value="all_country"

                                selected><?php esc_html_e( 'All Countries ', 'woo-orders-tracking' ) ?></option>

                        <option value="Global"><?php esc_html_e( 'Global', 'woo-orders-tracking' ) ?></option>

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

            <div class="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-search-wrap' ) ) ) ?>">
                <span class="vi-ui button olive <?php esc_attr_e( self::set( array( 'setting-shipping-carriers-add-new-carrier' ) ) ) ?>"><?php esc_html_e( 'Add Carriers ', 'woo-orders-tracking' ) ?></span>

                <input type="text"

                       placeholder="<?php esc_attr_e( 'Search carrier name', 'woo-orders-tracking' ) ?>"

                       class="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-filter-search' ) ) ) ?>">

            </div>

        </div>

        <div class="<?php esc_attr_e( self::set( array( 'setting-shipping-carriers-list-wrap' ) ) ) ?>">


        </div>

        <div class="<?php esc_attr_e( self::set( array(

			'setting-shipping-carriers-list-search-wrap',

			'hidden'

		) ) ) ?>">


        </div>

		<?php

	}

	private function email_settings() {
		$this->settings = new  VI_WOO_ORDERS_TRACKING_DATA();
		if ( $this->schedule_send_emails ) {
			$orders = get_option( 'vi_wot_send_mails_for_import_csv_function_orders' );
			if ( $orders ) {
				$orders = json_decode( $orders, true );
				if ( count( $orders ) ) {
					$gmt_offset = intval( get_option( 'gmt_offset' ) );
					?>
                    <div class="vi-ui positive message">
                        <div class="header">
							<?php printf( __( 'Next schedule: <strong>%s</strong>', 'woo-orders-tracking' ), date_i18n( 'F j, Y g:i:s A', ( $this->schedule_send_emails + HOUR_IN_SECONDS * $gmt_offset ) ) ); ?>
                        </div>
                        <p><?php printf( __( 'Order(s) to send next: %s', 'woo-orders-tracking' ), implode( ',', array_splice( $orders, 0, $this->settings->get_params( 'email_number_send' ) ) ) ); ?></p>
                    </div>
					<?php
				}
			}
		}
		?>

        <table class="form-table">

            <tbody>

            <tr>

                <th scope="row">

                    <label for="<?php esc_attr_e( self::set( 'setting-email-subject' ) ) ?>">

						<?php esc_html_e( 'Email subject', 'woo-orders-tracking' ) ?>

                    </label>

                </th>

                <td>

                    <input type="text"

                           name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[email][email_subject]"

                           id="<?php esc_attr_e( self::set( 'setting-email-subject' ) ) ?>"

                           value="<?php esc_attr_e( htmlentities( $this->settings->get_params( 'email_subject' ) ) ) ?>">

                </td>

            </tr>


            <tr>

                <th scope="row">

                    <label for="<?php esc_attr_e( self::set( 'setting-email-heading' ) ) ?>">

						<?php esc_html_e( 'Email heading', 'woo-orders-tracking' ) ?>

                    </label>

                </th>

                <td>

                    <input type="text"

                           name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[email][email_heading]"

                           id="<?php esc_attr_e( self::set( 'setting-email-heading' ) ) ?>"

                           value="<?php esc_attr_e( htmlentities( $this->settings->get_params( 'email_heading' ) ) ) ?>">

                </td>

            </tr>


            <tr>

                <th scope="row">

                    <label for="<?php esc_attr_e( self::set( 'setting-email-content' ) ) ?>">

						<?php esc_html_e( 'Email content', 'woo-orders-tracking' ) ?>

                    </label>

                </th>

                <td>
					<?php wp_editor( stripslashes( $this->settings->get_params( 'email_content' ) ), 'wot-email-content', array(

						'editor_height' => 300,

						'textarea_name' => 'woo-orders-tracking-settings[email][email_content]'

					) ) ?>
                    <table class="vi-ui celled table <?php esc_attr_e( self::set( 'table-of-placeholders' ) ) ?>">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'Placeholder', 'woo-orders-tracking' ) ?></th>
                            <th><?php esc_html_e( 'Explanation', 'woo-orders-tracking' ) ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{tracking_table}</td>
                            <td><?php esc_html_e( 'List of tracking urls for current order', 'woo-orders-tracking' ) ?></td>
                        </tr>
                        <tr>
                            <td>{order_id}</td>
                            <td><?php esc_html_e( 'ID of current order', 'woo-orders-tracking' ) ?></td>
                        </tr>
                        <tr>
                            <td>{billing_first_name}</td>
                            <td><?php esc_html_e( 'Billing first name', 'woo-orders-tracking' ) ?></td>
                        </tr>
                        <tr>
                            <td>{billing_last_name}</td>
                            <td><?php esc_html_e( 'Billing last name', 'woo-orders-tracking' ) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <th>
                    <label>
						<?php esc_html_e( 'Tracking number column', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label>
						<?php esc_html_e( 'Carrier name column', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label>
						<?php esc_html_e( 'Tracking url column', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="vi-ui blue message">
                        <div class="header">
							<?php esc_html_e( 'Settings for sending emails when importing tracking numbers', 'woo-orders-tracking' ) ?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php esc_attr_e( self::set( 'email_number_send' ) ) ?>">
						<?php esc_html_e( 'Number of emails sent per time', 'woo-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <input type="number" min="1"
                           class="<?php esc_attr_e( self::set( 'email_number_send' ) ) ?>"
                           id="<?php esc_attr_e( self::set( 'email_number_send' ) ) ?>"
                           name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[email][email_number_send]"
                           value="<?php esc_attr_e( $this->settings->get_params( 'email_number_send' ) ) ?>">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php esc_attr_e( self::set( 'email_time_send' ) ) ?>">
						<?php esc_html_e( 'Delay between each time', 'woo-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <div class="vi-ui right labeled input">
                        <input type="number" min="0"
                               class="<?php esc_attr_e( self::set( 'email_time_send' ) ) ?>"
                               id="<?php esc_attr_e( self::set( 'email_time_send' ) ) ?>"
                               name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[email][email_time_send]"
                               value="<?php esc_attr_e( $this->settings->get_params( 'email_time_send' ) ) ?>">
                        <label for="amount"
                               class="vi-ui label" style="padding: 0;">
                            <select name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[email][email_time_send_type]"
                                    id="<?php esc_attr_e( self::set( 'email_time_send_type' ) ) ?>"
                                    class="vi-ui dropdown <?php esc_attr_e( self::set( 'email_time_send_type' ) ) ?>">
								<?php
								$delay_time_type = array(
									'day'    => __( 'Day', 'woo-orders-tracking' ),
									'hour'   => __( 'Hour', 'woo-orders-tracking' ),
									'minute' => __( 'Minute', 'woo-orders-tracking' ),
								);
								foreach ( $delay_time_type as $key => $value ) {
									$selected = '';
									if ( $this->settings->get_params( 'email_time_send_type' ) == $key ) {
										$selected = 'selected="selected"';
									}
									?>
                                    <option value="<?php esc_attr_e( $key ) ?>" <?php echo $selected ?>><?php echo esc_html__( $value ) ?></option>
									<?php
								}
								?>
                            </select>
                        </label>
                    </div>
                    <p class="description"><?php esc_html_e( 'If you import tracking numbers for 100 orders and all 100 orders have tracking numbers updated, not all 100 emails will be sent at a time.', 'woo-orders-tracking' ) ?></p>
                    <p class="description"><?php _e( 'If you set <strong>"Number of emails sent per time"</strong> to 10 and <strong>"Delay between each time"</strong> to 10 minutes, by the time the import completes, it will send 10 first email and wait 10 minutes to send next 10 emails and continue this until all emails are sent.', 'woo-orders-tracking' ) ?></p>
                </td>
            </tr>
            </tbody>
        </table>
		<?php
	}

	private function email_woo_settings() {
		$this->settings = new  VI_WOO_ORDERS_TRACKING_DATA();
		?>
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="<?php esc_attr_e( self::set( 'setting-email-woo-enable' ) ) ?>">
						<?php esc_html_e( 'Enable', 'woo-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <div class="vi-ui toggle checkbox">
                        <input type="checkbox"
                               name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[email_woo][email_woo_enable]"
                               id="<?php esc_attr_e( self::set( 'setting-email-woo-enable' ) ) ?>"
                               value="1" <?php checked( $this->settings->get_params( 'email_woo_enable' ), '1' ) ?>>
                    </div>
                    <p class="description"><?php esc_html_e( 'Include tracking information in WooCommerce order email.', 'woo-orders-tracking' ) ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php esc_attr_e( self::set( 'setting-email-woo-status' ) ) ?>">
						<?php esc_html_e( 'Order status email', 'woo-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
					<?php
					$email_woo_status = $this->settings->get_params( 'email_woo_status' );
					?>
                    <select name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[email_woo][email_woo_status][]"
                            id="<?php esc_attr_e( self::set( 'setting-email-woo-status' ) ) ?>"
                            class="vi-ui fluid dropdown <?php esc_attr_e( self::set( 'setting-email-woo-status' ) ) ?>"
                            multiple>
                        <option value="cancelled_order" <?php echo in_array( 'cancelled_order', $email_woo_status ) ? 'selected' : ""; ?>><?php esc_html_e( 'Cancelled', 'woo-orders-tracking' ) ?></option>
                        <option value="customer_completed_order" <?php echo in_array( 'customer_completed_order', $email_woo_status ) ? 'selected' : ""; ?>><?php esc_html_e( 'Completed', 'woo-orders-tracking' ) ?></option>
                        <option value="customer_invoice" <?php echo in_array( 'customer_invoice', $email_woo_status ) ? 'selected' : ""; ?>><?php esc_html_e( 'Customer Invoice', 'woo-orders-tracking' ) ?></option>
                        <option value="customer_note" <?php echo in_array( 'customer_note', $email_woo_status ) ? 'selected' : ""; ?>><?php esc_html_e( 'Customer Note', 'woo-orders-tracking' ) ?></option>
                        <option value="failed_order" <?php echo in_array( 'failed_order', $email_woo_status ) ? 'selected' : ""; ?>><?php esc_html_e( 'Failed', 'woo-orders-tracking' ) ?></option>
                        <option value="customer_on_hold_order" <?php echo in_array( 'customer_on_hold_order', $email_woo_status ) ? 'selected' : ""; ?>><?php esc_html_e( 'On Hold', 'woo-orders-tracking' ) ?></option>
                        <option value="customer_processing_order" <?php echo in_array( 'customer_processing_order', $email_woo_status ) ? 'selected' : ""; ?>><?php esc_html_e( 'Processing', 'woo-orders-tracking' ) ?></option>
                        <option value="customer_refunded_order" <?php echo in_array( 'customer_refunded_order', $email_woo_status ) ? 'selected' : ""; ?>><?php esc_html_e( 'Refunded', 'woo-orders-tracking' ) ?></option>
                    </select>
                    <p class="description"><?php esc_html_e( 'Select orders status email to  include the tracking information.', 'woo-orders-tracking' ) ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php esc_attr_e( self::set( 'setting-email-woo-status' ) ) ?>">
						<?php esc_html_e( 'Tracking info position', 'woo-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
					<?php
					$email_woo_positions = array(
						'before_order_table' => esc_html__( 'Before order table - PREMIUM FEATURE', 'woo-orders-tracking' ),
						'after_order_item'   => esc_html__( 'After each order item - PREMIUM FEATURE', 'woo-orders-tracking' ),
						'after_order_table'  => esc_html__( 'After order table', 'woo-orders-tracking' ),
					);
					?>
                    <select name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[email_woo][email_woo_position]"
                            id="<?php esc_attr_e( self::set( 'setting-email-woo-status' ) ) ?>"
                            class="vi-ui fluid dropdown <?php esc_attr_e( self::set( 'setting-email-woo-status' ) ) ?>">
						<?php
						foreach ( $email_woo_positions as $email_woo_position_k => $email_woo_position_v ) {
							if ( $email_woo_position_k === 'after_order_table' ) {
								?>
                                <option value="<?php esc_attr_e( $email_woo_position_k ) ?>"
                                        selected><?php esc_html_e( $email_woo_position_v ) ?></option>
								<?php
							} else {
								?>
                                <option value="<?php esc_attr_e( $email_woo_position_k ) ?>"
                                        disabled="disabled"><?php esc_html_e( $email_woo_position_v ) ?></option>
								<?php
							}
						}
						?>
                    </select>
                    <p class="description"><?php esc_html_e( 'Where in the email to place tracking information?', 'woo-orders-tracking' ) ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label>
						<?php esc_html_e( 'Tracking content html', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label>
						<?php esc_html_e( 'Tracking Number html', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            <tr>
                <th>
                    <label>
						<?php esc_html_e( 'Tracking Carrier html', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                </td>
            </tr>
            </tbody>
        </table>
		<?php
	}

	private function paypal_settings() {
		$available_gateways       = WC()->payment_gateways->get_available_payment_gateways();
		$available_paypal_methods = array();
		foreach ( $available_gateways as $method_id => $method ) {
			if ( in_array( $method_id, $this->settings->get_params( 'supported_paypal_gateways' ) ) ) {
				$available_paypal_methods[] = $method;
			}
		}
		if ( is_array( $available_paypal_methods ) && count( $available_paypal_methods ) ) {

			?>

            <div class="vi-ui blue message">
                <div class="header"><?php esc_html_e( 'Please follow these steps to get PayPal API Credentials', 'woo-orders-tracking' ) ?></div>
                <ul class="list">
                    <li>
						<?php printf( __( 'Go to %s and login with your PayPal account', 'woo-orders-tracking' ), '<strong><a href="https://developer.paypal.com/developer/applications/"
                           target="_blank">PayPal Developer</a></strong>' ); ?>
                    </li>
                    <li>
						<?php _e( 'Go to My Apps & Credentials and select the <strong>Live</strong> tab', 'woo-orders-tracking' ) ?>
                    </li>
                    <li>
						<?php esc_html_e( 'Click on Create App button', 'woo-orders-tracking' ) ?>
                    </li>
                    <li>
						<?php
						esc_html_e( 'Enter the name of your application and click Create App button', 'woo-orders-tracking' )
						?>
                    </li>
                    <li>
						<?php
						esc_html_e( 'Copy your Client ID and Secret and paste them to Client Id and Client Secret fields', 'woo-orders-tracking' )
						?>
                    </li>
                </ul>
            </div>
            <table class="form-table wot-paypal-app-table">
                <tbody>
                <tr class="wot-paypal-app-table-header" style="">
                    <th><?php esc_html_e( 'Payment Method', 'woo-orders-tracking' ) ?></th>
                    <th><?php esc_html_e( 'Client ID', 'woo-orders-tracking' ) ?></th>
                    <th><?php esc_html_e( 'Client Secret', 'woo-orders-tracking' ) ?></th>
                    <th><?php esc_html_e( 'PayPal sandbox', 'woo-orders-tracking' ) ?></th>
                    <th><?php esc_html_e( 'Actions', 'woo-orders-tracking' ) ?></th>
                </tr>
                </tbody>
                <tbody>
				<?php
				$paypal_method = $this->settings->get_params( 'paypal_method' );
				foreach ( $available_paypal_methods as $item ) {
					$i              = array_search( $item->id, $paypal_method );
					$live_client_id = $live_client_secret = $sandbox_client_id = $sandbox_client_secret = $sandbox_enable = '';
					if ( is_numeric( $i ) ) {
						$live_client_id        = $this->settings->get_params( 'paypal_client_id_live' )[ $i ];
						$live_client_secret    = $this->settings->get_params( 'paypal_secret_live' )[ $i ];
						$sandbox_client_id     = $this->settings->get_params( 'paypal_client_id_sandbox' )[ $i ];
						$sandbox_client_secret = $this->settings->get_params( 'paypal_secret_sandbox' )[ $i ];
						$sandbox_enable        = $this->settings->get_params( 'paypal_sandbox_enable' )[ $i ];
					}
					?>
                    <tr class="wot-paypal-app-content">
                        <td>
                            <input type="hidden"
                                   name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[paypal][paypal_method][]"
                                   value="<?php esc_attr_e( $item->id ) ?>"
                            >
                            <input type="text" title="<?php esc_attr_e( $item->id ) ?>"
                                   value="<?php esc_attr_e( $item->method_title ) ?>" readonly
                            >
                        </td>

                        <td>
                            <div class="field">

                                <div class="field  woo-orders-tracking-setting-paypal-live-wrap">
                                    <div class="vi-ui input"
                                         data-tooltip="<?php esc_attr_e( 'Live Client ID', 'sales-countdown-timer' ) ?>">
                                        <input type="text"
                                               name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[paypal][paypal_client_id_live][]"
                                               class="woo-orders-tracking-setting-paypal-client-id-live"
                                               value="<?php esc_attr_e( $live_client_id ) ?>">
                                    </div>
                                </div>
                                <div class="field woo-orders-tracking-setting-paypal-sandbox-wrap">
                                    <div class="vi-ui input"
                                         data-tooltip="<?php esc_attr_e( 'Sandbox Client ID', 'sales-countdown-timer' ) ?>">
                                        <input type="text"
                                               name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[paypal][paypal_client_id_sandbox][]"
                                               class="woo-orders-tracking-setting-paypal-client-id-sandbox"
                                               value="<?php esc_attr_e( $sandbox_client_id ) ?>">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="field ">
                                <div class="field  woo-orders-tracking-setting-paypal-live-wrap">
                                    <div class="vi-ui input"
                                         data-tooltip="<?php esc_attr_e( 'Live Client Secret', 'sales-countdown-timer' ) ?>">
                                        <input type="text"
                                               name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[paypal][paypal_secret_live][]"
                                               class="woo-orders-tracking-setting-paypal-secret-live"
                                               value="<?php esc_attr_e( $live_client_secret ) ?>">
                                    </div>
                                </div>
                                <div class="field woo-orders-tracking-setting-paypal-sandbox-wrap">
                                    <div class="vi-ui input"
                                         data-tooltip="<?php esc_attr_e( 'Sandbox Client Secret', 'sales-countdown-timer' ) ?>">
                                        <input type="text"
                                               name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[paypal][paypal_secret_sandbox][]"
                                               class="woo-orders-tracking-setting-paypal-secret-sandbox"
                                               value="<?php esc_attr_e( $sandbox_client_secret ) ?>">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="hidden"
                                   name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[paypal][paypal_sandbox_enable][]"
                                   class="<?php esc_attr_e( self::set( 'setting-paypal-sandbox-enable' ) ) ?>"
                                   value="<?php esc_attr_e( $sandbox_enable ); ?>"
                            >
                            <div class="vi-ui toggle checkbox">

                                <input type="checkbox"


                                       id="<?php esc_attr_e( self::set( 'setting-paypal-sandbox-enable' ) ) ?>"

                                       value="<?php esc_attr_e( $sandbox_enable ); ?>" <?php checked( $sandbox_enable, '1' ) ?> >


                            </div>
                        </td>
                        <td>
                            <div class="field">

                                <div class="field">
                                        <span class="wot-paypal-app-content-action-test-api wot-paypal-app-content-action-btn vi-ui button positive ">
                                    <?php esc_html_e( 'Test Connection', 'woo-orders-tracking' ) ?>
                                </span>


                                </div>
                                <div class="field">

                                    <div class="<?php esc_attr_e( self::set( 'setting-paypal-btn-check-api-text' ) ) ?>"

                                         style="padding-left: 5px; ">


                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
					<?php

				}

				?>
                </tbody>
            </table>
			<?php

		} else {
			?>
            <div class="vi-ui negative message">
                <div class="header">
					<?php esc_html_e( 'This option will only be available if a PayPal payment method is activated', 'woo-orders-tracking' ) ?>
                </div>
            </div>
			<?php
		}
	}

	private function tracking_service_settings() {
		?>
        <div class="vi-ui blue tiny message">
			<?php _e( 'TrackingMore tracking form shortcode <strong>[vi_wot_tracking_more_form]</strong>. You can still use this shortcode even if you do not use tracking service. More details about this at <a target="_blank" href="https://www.trackingmore.com/embed_box_float-en.html">Track Button</a>', 'woo-orders-tracking' ) ?>
        </div>
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="<?php esc_attr_e( self::set( 'setting-service-carrier-enable' ) ) ?>">
						<?php esc_html_e( 'Enable', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <div class="vi-ui toggle checkbox">
                        <input type="checkbox"
                               name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[service_carrier][service_carrier_enable]"
                               id="<?php esc_attr_e( self::set( 'setting-service-carrier-enable' ) ) ?>"
                               value="1" <?php checked( $this->settings->get_params( 'service_carrier_enable' ), '1' ) ?>>
                    </div>
                    <p class="description"><?php esc_html_e( 'Check it if you use the 3rd party service to track shipment info', 'woo-orders-tracking' ) ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php esc_attr_e( self::set( 'setting-service-carrier-type' ) ) ?>"><?php esc_html_e( 'Service', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[service_carrier][service_carrier_type]"
                            id="<?php esc_attr_e( self::set( 'setting-service-carrier-type' ) ) ?>"
                            class="vi-ui fluid dropdown <?php esc_attr_e( self::set( 'setting-service-carrier-type' ) ) ?>">
						<?php
						$service_carrier_type = $this->settings->get_params( 'service_carrier_type' );
						foreach ( $this->service_carriers_list as $item_slug => $item_name ) {
							if ( $item_slug === 'trackingmore' ) {
								?>
                                <option value="<?php esc_attr_e( $item_slug ) ?>"
                                        selected><?php echo $item_name; ?></option>
								<?php
							} else {
								?>
                                <option value="<?php esc_attr_e( $item_slug ) ?>"
                                        disabled><?php printf( __( "%s - PREMIUM FEATURE", 'woo-orders-tracking' ), $item_name ); ?></option>
								<?php
							}
						}
						?>
                    </select>
                    <p class="description"><?php esc_html_e( 'Select a 3rd party tracking service you use to track the shipment', 'woo-orders-tracking' ) ?></p>
                </td>
            </tr>
			<?php
			$api_key_class = array( 'tracking-service-api' );
			?>
            <tr class="<?php esc_attr_e( self::set( $api_key_class ) ) ?>">
                <th>
                    <label for="<?php esc_attr_e( self::set( 'setting-service-carrier-api-key' ) ) ?>">
						<?php
						esc_html_e( 'API key', 'woo-orders-tracking' );
						?>
                    </label>
                </th>
                <td>
                    <input type="text"
                           name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[service_carrier][service_carrier_api_key]"
                           id="<?php esc_attr_e( self::set( 'setting-service-carrier-api-key' ) ) ?>"
                           value="<?php esc_attr_e( $this->settings->get_params( 'service_carrier_api_key' ) ) ?>">
                    <p class="description <?php esc_attr_e( self::set( array(
						'setting-service-carrier-api-key-trackingmore',
						'setting-service-carrier-api-key',
					) ) ) ?>">
						<?php
						_e( 'Please enter your TrackingMore api key. If you don\'t have an account, <a href="https://my.trackingmore.com/get_apikey.php" target="_blank"><strong>click here</strong></a> to create account and generate api key', 'woo-orders-tracking' );
						?>
                    </p>
                </td>
            </tr>
			<?php
			$service_tracking_page = $this->settings->get_params( 'service_tracking_page' );
			?>
            <tr>
                <th>
                    <label for="<?php esc_attr_e( self::set( 'setting-service-tracking-page' ) ) ?>">
						<?php
						esc_html_e( 'Tracking page', 'woo-orders-tracking' );
						?>
                    </label>
                </th>
                <td>
                    <select name="<?php esc_attr_e( self::set( 'settings' ) ) ?>[service_carrier][service_tracking_page]"
                            id="<?php esc_attr_e( self::set( 'setting-service-tracking-page' ) ) ?>"
                            class="search-page <?php esc_attr_e( self::set( 'setting-service-tracking-page' ) ) ?>">
						<?php
						if ( $service_tracking_page ) {
							?>
                            <option value="<?php esc_attr_e( $service_tracking_page ) ?>"
                                    selected><?php esc_html_e( get_the_title( $service_tracking_page ) ) ?></option>
							<?php
						}
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label>
						<?php esc_html_e( 'Change Order Status', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                    <p class="description"><?php esc_html_e( 'Select order status to change to when Shipment status changes to Delivered. Leave it blank if you don\'t want to change order status', 'woo-orders-tracking' ) ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="setting-service-carrier-default">
						<?php esc_html_e( 'Customize Tracking page', 'woo-orders-tracking' ) ?>
                    </label>
                </th>
                <td>
                    <div>
						<?php
						if ( $service_tracking_page && $service_tracking_page_url = get_the_permalink( $service_tracking_page ) ) {
							$href = 'customize.php?url=' . urlencode( $service_tracking_page_url ) . '&autofocus[panel]=vi_wot_orders_tracking_design';
							?>
                            <a href="<?php esc_attr_e( esc_url( $href ) ) ?>"
                               target="_blank">
								<?php esc_html_e( 'Go to design now', 'woo-orders-tracking' ) ?>
                            </a>
							<?php
						} else {
							?>
                            <label for="<?php esc_attr_e( self::set( 'setting-service-tracking-page' ) ) ?>"><?php esc_html_e( 'Please select a Tracking page and save settings to use this feature', 'woo-orders-tracking' ); ?></label>
							<?php
						}
						?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="vi-ui blue small message">
            <div class="header">
				<?php esc_html_e( 'Google reCAPTCHA for tracking form', 'woo-orders-tracking' ) ?>
            </div>
            <ul class="list">
                <li><?php echo wp_kses_post( __( 'Visit <a target="_blank" href="http://www.google.com/recaptcha/admin">Google reCAPTCHA page</a> to sign up for an API key pair with your Gmail account', 'woo-orders-tracking' ) ) ?></li>
                <li><?php esc_html_e( 'Select the reCAPTCHA version that you want to use', 'woo-orders-tracking' ) ?></li>
                <li><?php esc_html_e( 'Fill in authorized domains', 'woo-orders-tracking' ) ?></li>
                <li><?php esc_html_e( 'Accept terms of service and click Register button', 'woo-orders-tracking' ) ?></li>
                <li><?php esc_html_e( 'Copy and paste the site key and secret key into respective fields', 'woo-orders-tracking' ) ?></li>
            </ul>
        </div>
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="<?php echo esc_attr( self::set( 'tracking_form_recaptcha_enable' ) ) ?>">
						<?php esc_html_e( 'Enable reCAPTCHA', 'woo-orders-tracking' ); ?>
                    </label>
                </th>
                <td>
                    <a class="vi-ui button" target="_blank"
                       href="https://1.envato.market/6ZPBE"><?php esc_html_e( 'Upgrade This Feature', 'woo-orders-tracking' ) ?></a>
                    <p class="description"><?php esc_html_e( 'Use Google reCAPTCHA for tracking form', 'woo-orders-tracking' ) ?></p>
                </td>
            </tr>
            </tbody>
        </table>
		<?php

	}

	public function wotv_admin_choose_default_shipping_carrier() {

		if ( ! current_user_can( 'manage_options' ) ) {

			return;

		}

		if ( ! isset( $_POST['action_nonce'] ) || ! wp_verify_nonce( $_POST['action_nonce'], 'vi_wot_setting_action_nonce' ) ) {

			return;

		}

		$carrier_slug = isset( $_POST['carrier_slug'] ) ? sanitize_text_field( $_POST['carrier_slug'] ) : '';

		if ( $carrier_slug ) {
			$args                             = $this->settings->get_params();
			$args['shipping_carrier_default'] = $carrier_slug;
			update_option( 'woo_orders_tracking_settings', $args );

			wp_send_json(

				array(

					'status' => 'success',

					'default' => $carrier_slug,

				)

			);

		} else {

			wp_send_json(

				array(

					'status' => 'error',

					'message' => 'not enough info',

					'details' => array( 'slug' => $carrier_slug )

				)

			);

		}
	}

	public function wotv_admin_delete_shipping_carrier() {

		if ( ! current_user_can( 'manage_options' ) ) {

			return;

		}


		if ( ! isset( $_POST['action_nonce'] ) || ! wp_verify_nonce( $_POST['action_nonce'], 'vi_wot_setting_action_nonce' ) ) {

			return;

		}

		$carrier_slug = isset( $_POST['carrier_slug'] ) ? sanitize_text_field( $_POST['carrier_slug'] ) : '';

		if ( $carrier_slug ) {

			$args = $this->settings->get_params();

			$position = '';

			$carriers = json_decode( $this->settings->get_params( 'custom_carriers_list' ), true );

			if ( count( $carriers ) ) {

				foreach ( $carriers as $shipping_carrier ) {

					if ( $shipping_carrier["slug"] === $carrier_slug ) {

						$position = array_search( $shipping_carrier, $carriers );

						break;

					} else {

						continue;

					}

				}

				array_splice( $carriers, $position, 1 );

				$args['custom_carriers_list'] = json_encode( $carriers );

				update_option( 'woo_orders_tracking_settings', $args );

				wp_send_json(

					array(

						'status' => 'success',

						'position' => $position,

					)

				);

			} else {

				wp_send_json(

					array(

						'status' => 'error',

						'message' => 'can\'t delete carrier',

						'details' => array( 'custom_carriers_list' => $carriers )

					)

				);

			}

		} else {

			wp_send_json(

				array(

					'status' => 'error',

					'message' => 'not enough info',

					'details' => array( 'slug' => $carrier_slug )

				)

			);

		}

	}

	public function wotv_admin_edit_shipping_carrier() {

		if ( ! current_user_can( 'manage_options' ) ) {

			return;

		}


		if ( ! isset( $_POST['action_nonce'] ) || ! wp_verify_nonce( $_POST['action_nonce'], 'vi_wot_setting_action_nonce' ) ) {

			return;

		}

		$carrier_slug = isset( $_POST['carrier_slug'] ) ? sanitize_text_field( $_POST['carrier_slug'] ) : '';

		$carrier_name = isset( $_POST['carrier_name'] ) ? sanitize_text_field( $_POST['carrier_name'] ) : '';

		$shipping_country = isset( $_POST['shipping_country'] ) ? sanitize_text_field( $_POST['shipping_country'] ) : '';

		$tracking_url     = isset( $_POST['tracking_url'] ) ? sanitize_text_field( $_POST['tracking_url'] ) : '';
		$digital_delivery = isset( $_POST['digital_delivery'] ) ? sanitize_text_field( $_POST['digital_delivery'] ) : '';

		if ( $carrier_slug && $carrier_name && $shipping_country && $tracking_url ) {

			$args = $carriers = $carrier_change = array();

			$position = '';

			$carriers = json_decode( $this->settings->get_params( 'custom_carriers_list' ), true );

			if ( count( $carriers ) ) {

				foreach ( $carriers as $shipping_carrier ) {

					if ( $shipping_carrier["slug"] === $carrier_slug ) {

						$position = array_search( $shipping_carrier, $carriers );

						$shipping_carrier['name'] = $carrier_name;

						$shipping_carrier['country'] = $shipping_country;

						$shipping_carrier['url'] = $tracking_url;

						$shipping_carrier['digital_delivery'] = $digital_delivery;

						$carrier_change = $shipping_carrier;

						break;

					}

				}

				$carriers[ $position ] = $carrier_change;

				$args['custom_carriers_list'] = json_encode( $carriers );

				$args = wp_parse_args( $args, $this->settings->get_params() );

				update_option( 'woo_orders_tracking_settings', $args );

				wp_send_json(

					array(

						'status' => 'success',

						'position' => $position,

						'carrier_name' => $carrier_name,

						'shipping_country' => $shipping_country,

						'tracking_url' => $tracking_url,

						'digital_delivery' => $digital_delivery,

					)

				);

			} else {

				wp_send_json(

					array(

						'status' => 'error',

						'message' => 'can\'t edit carrier',

						'details' => array( 'custom_carriers_list' => $carriers )

					)

				);

			}

		} else {

			wp_send_json(

				array(

					'status' => 'error',

					'message' => 'not enough info',

					'details' => array(

						'name' => $carrier_name,

						'slug' => $carrier_slug,

						'country' => $shipping_country,

						'url' => $tracking_url

					)

				)

			);

		}

	}

	public function wotv_admin_add_new_shipping_carrier() {

		if ( ! current_user_can( 'manage_options' ) ) {

			return;

		}


		if ( ! isset( $_POST['action_nonce'] ) || ! wp_verify_nonce( $_POST['action_nonce'], 'vi_wot_setting_action_nonce' ) ) {

			return;

		}


		$carrier_name = isset( $_POST['carrier_name'] ) ? sanitize_text_field( $_POST['carrier_name'] ) : '';

		$tracking_url = isset( $_POST['tracking_url'] ) ? sanitize_text_field( $_POST['tracking_url'] ) : '';

		$shipping_country = isset( $_POST['shipping_country'] ) ? sanitize_text_field( $_POST['shipping_country'] ) : '';

		$digital_delivery = isset( $_POST['digital_delivery'] ) ? sanitize_text_field( $_POST['digital_delivery'] ) : '';

		if ( $carrier_name && $tracking_url && $shipping_country ) {

			$args = $this->settings->get_params();

			$custom_carriers_list = json_decode( $this->settings->get_params( 'custom_carriers_list' ), true );

			$custom_carrier = array(

				'name' => $carrier_name,

				'slug' => 'custom_' . time(),

				'url' => $tracking_url,

				'country' => $shipping_country,

				'type'             => 'custom',
				'digital_delivery' => $digital_delivery,

			);

			$custom_carriers_list[] = $custom_carrier;

			$args['custom_carriers_list'] = json_encode( $custom_carriers_list );


			update_option( 'woo_orders_tracking_settings', $args );


			wp_send_json(

				array(

					'status' => 'success',

					'carrier' => $custom_carrier,

				)

			);

		} else {

			wp_send_json(

				array(

					'status' => 'error',

					'message' => 'not enough info',

					'details' => array(

						'carrier_name' => $carrier_name,

						'tracking_url' => $tracking_url,

						'shipping_country' => $shipping_country

					)

				)

			);

		}
	}

	public function preview_emails_button( $editor_id ) {

		if ( isset( $_REQUEST['page'] ) && sanitize_text_field( $_REQUEST['page'] ) == 'woo-orders-tracking' ) {

			$editor_ids = array( 'wot-email-content' );

			if ( in_array( $editor_id, $editor_ids ) ) {

				?>

                <span class="<?php echo self::set( 'preview-emails-button' ) ?> button"

                      data-wot_language="<?php echo str_replace( 'wot-email-content', '', $editor_id ) ?>"><?php esc_html_e( 'Preview emails', 'woo-orders-tracking' ) ?></span>

				<?php

			}

		}

	}


	public function wot_preview_emails() {

		$shortcodes = array(

			'order_number' => 2019,

			'order_status' => 'processing',

			'order_date' => date_i18n( 'F d, Y', strtotime( 'today' ) ),

			'order_total' => 999,

			'order_subtotal' => 990,

			'items_count' => 1,

			'payment_method' => 'Cash on delivery',


			'shipping_method' => 'Free shipping',

			'shipping_address' => 'Thainguyen City',

			'formatted_shipping_address' => 'Thainguyen City, Vietnam',


			'billing_address' => 'Thainguyen City',

			'formatted_billing_address' => 'Thainguyen City, Vietnam',

			'billing_country' => 'VN',

			'billing_city' => 'Thainguyen',


			'billing_first_name' => 'John',

			'billing_last_name' => 'Doe',

			'formatted_billing_full_name' => 'John Doe',

			'billing_email' => 'support@villatheme.com',


			'shop_title' => get_bloginfo(),

			'home_url' => home_url(),

			'shop_url' => get_option( 'woocommerce_shop_page_id', '' ) ? get_page_link( get_option( 'woocommerce_shop_page_id' ) ) : '',


		);

		$headers = "Content-Type: text/html\r\n";

		$content = isset( $_GET['content'] ) ? wp_kses_post( stripslashes( $_GET['content'] ) ) : '';

		$heading = isset( $_GET['heading'] ) ? ( stripslashes( $_GET['heading'] ) ) : '';

		$heading               = str_replace( array(

			'{order_id}',

			'{billing_first_name}',

			'{billing_last_name}'

		), array(

			$shortcodes['order_number'],

			$shortcodes['billing_first_name'],

			$shortcodes['billing_last_name']

		), $heading );
		$service_tracking_page = $this->settings->get_params( 'service_tracking_page' );
		$imported              = array(

			array(

				'order_item_name' => "T-shirt",

				'tracking_code' => "LTxxxxxxxxxCN",

				'carrier_name' => "ePacket",

				'tracking_url' => $service_tracking_page ? get_page_link( $service_tracking_page ) : home_url(),

			),

		);

		ob_start();

		?>

        <table cellspacing="0" cellpadding="6" border="1"

               style="border: 1px solid #e5e5e5;vertical-align: middle;width: 100%; ">

            <thead>

            <tr>

                <th style="border: 1px solid #e5e5e5;vertical-align: middle;padding: 12px;text-align: left; "><?php esc_html_e( 'Product title', 'woo-orders-tracking' ) ?></th>

                <th style="border: 1px solid #e5e5e5;vertical-align: middle;padding: 12px;text-align: left; "><?php esc_html_e( 'Tracking number', 'woo-orders-tracking' ) ?></th>

                <th style="border: 1px solid #e5e5e5;vertical-align: middle;padding: 12px;text-align: left; "><?php esc_html_e( 'Carrier name', 'woo-orders-tracking' ) ?></th>

                <th style="border: 1px solid #e5e5e5;vertical-align: middle;padding: 12px;text-align: left; "><?php esc_html_e( 'Tracking link', 'woo-orders-tracking' ) ?></th>

            </tr>

            </thead>

            <tbody>

			<?php

			foreach ( $imported as $item ) {

				?>

                <tr>

                    <td style="border: 1px solid #e5e5e5;vertical-align: middle;padding: 12px;text-align: left; "><?php esc_html_e( $item['order_item_name'] ); ?></td>

                    <td style="border: 1px solid #e5e5e5;vertical-align: middle;padding: 12px;text-align: left; "><?php esc_html_e( $item['tracking_code'] ); ?></td>

                    <td style="border: 1px solid #e5e5e5;vertical-align: middle;padding: 12px;text-align: left; "><?php esc_html_e( $item['carrier_name'] ); ?></td>

                    <td style="border: 1px solid #e5e5e5;vertical-align: middle;padding: 12px;text-align: left; ">

                        <a href="<?php esc_attr_e( esc_url( $item['tracking_url'] ) ) ?>" target="_blank"

                           style="text-decoration: none;"><?php esc_html_e( 'Track', 'woo-orders-tracking' ) ?></a>

                    </td>

                </tr>

				<?php

			}

			?>

            </tbody>

        </table>

		<?php

		$tracking_table = ob_get_clean();

		$content = str_replace( array(

			'{order_id}',

			'{billing_first_name}',

			'{billing_last_name}',

			'{tracking_table}'

		), array(

			$shortcodes['order_number'],

			$shortcodes['billing_first_name'],

			$shortcodes['billing_last_name'],

			$tracking_table

		), $content );

		$mailer = WC()->mailer();

		$email = new WC_Email();

		$content = $email->style_inline( $mailer->wrap_message( $heading, $content ) );

		wp_send_json(

			array(

				'html' => $content,

			)

		);

	}

	public function wot_test_connection_paypal() {

		if ( ! current_user_can( 'manage_options' ) ) {

			return;

		}

		$client_id = isset( $_POST['client_id'] ) ? sanitize_text_field( $_POST['client_id'] ) : '';

		$secret = isset( $_POST['secret'] ) ? sanitize_text_field( $_POST['secret'] ) : '';

		$sandbox = isset( $_POST['sandbox'] ) ? sanitize_text_field( $_POST['sandbox'] ) : '';

		if ( $secret && $sandbox && $client_id ) {

			if ( $sandbox === 'no' ) {

				$sandbox = false;

			}

			$url = VI_WOO_ORDERS_TRACKING_ADMIN_PAYPAL::get_request_url( $sandbox );

			$check_token = VI_WOO_ORDERS_TRACKING_ADMIN_PAYPAL::get_access_token( $client_id, $secret, $url );
			if ( $check_token['status'] === 'success' ) {
				$message = '<p style="color: green; font-weight: 600 ;">' . __( 'Successfully!', 'woo-orders-tracking' ) . '</p>';
			} else {
				$message = '<p style="color: red; font-weight: 600 ;">' . $check_token['data'] . '</p>';
			}

			wp_send_json(

				array(

					'message' => $message

				)

			);

		}

	}

	public function orders_tracking_admin_footer() {

		global $pagenow;

		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';

		$countries = new WC_Countries();

		$countries = $countries->get_countries();

		if ( $page === 'woo-orders-tracking' ) {

			?>

            <div class="preview-emails-html-container woo-orders-tracking-footer-container woo-orders-tracking-hidden">

                <div class="preview-emails-html-overlay woo-orders-tracking-overlay"></div>

                <div class="preview-emails-html woo-orders-tracking-footer-content"></div>

            </div>

            <div class="edit-shipping-carrier-html-container woo-orders-tracking-footer-container woo-orders-tracking-hidden">

                <div class="edit-shipping-carrier-html-overlay woo-orders-tracking-overlay"></div>

                <div class="edit-shipping-carrier-html-content woo-orders-tracking-footer-content">

                    <div class="edit-shipping-carrier-html-content-header">

                        <h2><?php esc_html_e( 'Edit custom shipping carrier', 'woo-orders-tracking' ) ?></h2>

                        <i class="close icon edit-shipping-carrier-html-content-close"></i>

                    </div>

                    <div class="edit-shipping-carrier-html-content-body">

                        <div class="edit-shipping-carrier-html-content-body-row">

                            <div class="edit-shipping-carrier-html-content-body-carrier-name-wrap">

                                <label for="edit-shipping-carrier-html-content-body-carrier-name"><?php esc_html_e( 'Carrier Name', 'woo-orders-tracking' ) ?></label>

                                <input type="text" id="edit-shipping-carrier-html-content-body-carrier-name">

                            </div>

                            <div class="edit-shipping-carrier-html-content-body-country-wrap">

                                <label for="edit-shipping-carrier-html-content-body-country"><?php esc_html_e( 'Shipping Country', 'woo-orders-tracking' ) ?></label>

                                <select name="" id="edit-shipping-carrier-html-content-body-country"

                                        class="select2-hidden-accessible edit-shipping-carrier-html-content-body-country"

                                        tabindex="-1" aria-hidden="true"

                                >

                                    <option value=""></option>
                                    <option value="Global"><?php esc_html_e( 'Global', 'woo-orders-tracking' ) ?></option>

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
                        <div class="edit-shipping-carrier-html-content-body-row">
                            <div>
                                <input type="checkbox"
                                       id="edit-shipping-carrier-is-digital-delivery"
                                       class="edit-shipping-carrier-is-digital-delivery">
                                <label for="edit-shipping-carrier-is-digital-delivery"><?php esc_html_e( 'Check if this is a Digital Delivery carrier. Tracking number is not required for this kind of carrier', 'woo-orders-tracking' ) ?></label>
                            </div>
                        </div>
                        <div class="edit-shipping-carrier-html-content-body-row">

                            <div class="edit-shipping-carrier-html-content-body-carrier-url-wrap">

                                <label for="edit-shipping-carrier-html-content-body-carrier-url"><?php esc_html_e( 'Carrier URL', 'woo-orders-tracking' ) ?></label>

                                <input type="text" id="edit-shipping-carrier-html-content-body-carrier-url"

                                       placeholder="http://yourcarrier.com/{tracking_number}">

                                <p class="description">
                                    <strong>{tracking_number}</strong>: <?php esc_html_e( 'The placeholder for tracking number of an item', 'woo-orders-tracking' ) ?>
                                </p>
                                <p class="description">
                                    <strong>{postal_code}</strong>:<?php esc_html_e( 'The placeholder for postal code of an order', 'woo-orders-tracking' ) ?>
                                </p>
                                <p class="description"><?php echo 'eg: https://www.dhl.com/en/express/tracking.html?AWB={tracking_number}&brand=DHL'; ?></p>
                                <p class="description wotv-error-tracking-url"
                                   style="color: red"><?php esc_html_e( 'The tracking url will not include tracking number if message does not include ', 'woo-orders-tracking' ) ?>
                                    {tracking_number}</p>
                            </div>

                        </div>

                    </div>


                    <div class="edit-shipping-carrier-html-content-footer">

                        <button

                                type="button"

                                class="vi-ui button primary edit-shipping-carrier-html-btn-save"

                        >

							<?php esc_html_e( 'Save', 'woo-orders-tracking' ) ?>

                        </button>

                        <button

                                type="button"

                                class="vi-ui button red edit-shipping-carrier-html-btn-cancel"

                        >

							<?php esc_html_e( 'Cancel', 'woo-orders-tracking' ) ?>

                        </button>

                    </div>

                </div>

            </div>


            <div class="add-new-shipping-carrier-html-container woo-orders-tracking-footer-container woo-orders-tracking-hidden">

                <div class="add-new-shipping-carrier-html-overlay woo-orders-tracking-overlay"></div>

                <div class="add-new-shipping-carrier-html-content woo-orders-tracking-footer-content">

                    <div class="add-new-shipping-carrier-html-content-header">

                        <h2><?php esc_html_e( 'Add custom shipping carrier', 'woo-orders-tracking' ) ?></h2>

                        <i class="close icon add-new-shipping-carrier-html-content-close"></i>

                    </div>

                    <div class="add-new-shipping-carrier-html-content-body">

                        <div class="add-new-shipping-carrier-html-content-body-row">

                            <div class="add-new-shipping-carrier-html-content-body-carrier-name-wrap">

                                <label for="add-new-shipping-carrier-html-content-body-carrier-name"><?php esc_html_e( 'Carrier Name', 'woo-orders-tracking' ) ?></label>

                                <input type="text" id="add-new-shipping-carrier-html-content-body-carrier-name">

                            </div>

                            <div class="add-new-shipping-carrier-html-content-body-country-wrap">

                                <label for="add-new-shipping-carrier-html-content-body-country"><?php esc_html_e( 'Shipping Country', 'woo-orders-tracking' ) ?></label>

                                <select name="" id="add-new-shipping-carrier-html-content-body-country"

                                        class="select2-hidden-accessible add-new-shipping-carrier-html-content-body-country"

                                        tabindex="-1" aria-hidden="true"

                                >

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

                        <div class="add-new-shipping-carrier-html-content-body-row">
                            <div>
                                <input type="checkbox"
                                       id="add-new-shipping-carrier-is-digital-delivery"
                                       class="add-new-shipping-carrier-is-digital-delivery">
                                <label for="add-new-shipping-carrier-is-digital-delivery"><?php esc_html_e( 'Check if this is a Digital Delivery carrier. Tracking number is not required for this kind of carrier', 'woo-orders-tracking' ) ?></label>
                            </div>
                        </div>
                        <div class="add-new-shipping-carrier-html-content-body-row">

                            <div class="add-new-shipping-carrier-html-content-body-carrier-url-wrap">

                                <label for="add-new-shipping-carrier-html-content-body-carrier-url"><?php esc_html_e( 'Tracking URL', 'woo-orders-tracking' ) ?></label>

                                <input type="text" id="add-new-shipping-carrier-html-content-body-carrier-url"

                                       placeholder="http://yourcarrier.com/{tracking_number}">

                                <p class="description">
                                    <strong>{tracking_number}</strong>: <?php esc_html_e( 'The placeholder for tracking number of an item', 'woo-orders-tracking' ) ?>
                                </p>
                                <p class="description">
                                    <strong>{postal_code}</strong>:<?php esc_html_e( 'The placeholder for postal code of an order', 'woo-orders-tracking' ) ?>
                                </p>
                                <p class="description"><?php echo 'eg: https://www.dhl.com/en/express/tracking.html?AWB={tracking_number}&brand=DHL'; ?></p>
                                <p class="description wotv-error-tracking-url"
                                   style="color: red"><?php esc_html_e( 'The tracking url will not include tracking number if message does not include ', 'woo-orders-tracking' ) ?>
                                    {tracking_number}</p>
                            </div>

                        </div>

                    </div>

                    <div class="add-new-shipping-carrier-html-content-footer">

                        <button

                                type="button"

                                class="vi-ui button primary add-new-shipping-carrier-html-btn-save"

                        >

							<?php esc_html_e( 'Add New', 'woo-orders-tracking' ) ?>

                        </button>

                        <button

                                type="button"

                                class="vi-ui button red add-new-shipping-carrier-html-btn-cancel"

                        >

							<?php esc_html_e( 'Cancel', 'woo-orders-tracking' ) ?>

                        </button>

                    </div>

                </div>

            </div>

			<?php

		}

	}

	public function admin_enqueue_script() {
		global $pagenow;
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		if ( $pagenow === 'admin.php' && $page === 'woo-orders-tracking' ) {
			$this->schedule_send_emails = wp_next_scheduled( 'vi_wot_send_mails_for_import_csv_function' );
			wp_enqueue_style( 'vi-wot-admin-setting-css', VI_WOO_ORDERS_TRACKING_CSS . 'admin-setting.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-setting-message', VI_WOO_ORDERS_TRACKING_CSS . 'message.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-setting-input', VI_WOO_ORDERS_TRACKING_CSS . 'input.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-setting-label', VI_WOO_ORDERS_TRACKING_CSS . 'label.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-setting-accordion', VI_WOO_ORDERS_TRACKING_CSS . 'accordion.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-button', VI_WOO_ORDERS_TRACKING_CSS . 'button.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-checkbox', VI_WOO_ORDERS_TRACKING_CSS . 'checkbox.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-dropdown', VI_WOO_ORDERS_TRACKING_CSS . 'dropdown.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-form', VI_WOO_ORDERS_TRACKING_CSS . 'form.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-setting-input', VI_WOO_ORDERS_TRACKING_CSS . 'input.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-setting-popup', VI_WOO_ORDERS_TRACKING_CSS . 'popup.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-icon', VI_WOO_ORDERS_TRACKING_CSS . 'icon.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-menu', VI_WOO_ORDERS_TRACKING_CSS . 'menu.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-segment', VI_WOO_ORDERS_TRACKING_CSS . 'segment.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-select2', VI_WOO_ORDERS_TRACKING_CSS . 'select2.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-support', VI_WOO_ORDERS_TRACKING_CSS . 'villatheme-support.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-tab', VI_WOO_ORDERS_TRACKING_CSS . 'tab.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-setting-table', VI_WOO_ORDERS_TRACKING_CSS . 'table.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-setting-transition', VI_WOO_ORDERS_TRACKING_CSS . 'transition.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );


			wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array(

				'jquery-ui-draggable',

				'jquery-ui-slider',

				'jquery-touch-punch'

			), false, 1 );

			wp_enqueue_script( 'vi-wot-admin-setting-accordion', VI_WOO_ORDERS_TRACKING_JS . 'accordion.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-setting-address', VI_WOO_ORDERS_TRACKING_JS . 'address.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-setting-checkbox', VI_WOO_ORDERS_TRACKING_JS . 'checkbox.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-setting-dropdown', VI_WOO_ORDERS_TRACKING_JS . 'dropdown.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-setting-form', VI_WOO_ORDERS_TRACKING_JS . 'form.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'vi-wot-admin-setting-carrier-functions-js', VI_WOO_ORDERS_TRACKING_JS . '/carrier-functions.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-setting-transition', VI_WOO_ORDERS_TRACKING_JS . 'transition.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-setting-tab', VI_WOO_ORDERS_TRACKING_JS . 'tab.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-setting-select2', VI_WOO_ORDERS_TRACKING_JS . 'select2.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-setting-js', VI_WOO_ORDERS_TRACKING_JS . 'admin-setting.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );


			$countries = new WC_Countries();

			$this->shipping_countries = $countries->get_countries();

			wp_localize_script(

				'vi-wot-admin-setting-js',

				'vi_wot_admin_settings',

				array(

					'ajax_url' => admin_url( 'admin-ajax.php' ),

					'shipping_carrier_default' => $this->settings->get_params( 'shipping_carrier_default' ),

					'custom_carriers_list' => $this->settings->get_params( 'custom_carriers_list' ),

					'shipping_carriers_define_list' => json_encode( VI_WOO_ORDERS_TRACKING_DATA::shipping_carriers() ),

					'shipping_countries' => $this->shipping_countries,

					'service_carrier_default' => $this->settings->get_params( 'service_carrier_default' ),

					'service_carriers_list' => array_keys( $this->service_carriers_list ),

					'select_default_carrier_text' => __( 'Set Default', 'woo-orders-tracking' ),

					'add_new_error_empty_field' => esc_html__( 'Please fill full information for carrier', 'woo-orders-tracking' ),

					'confirm_delete_carrier_custom' => esc_html__( 'Are you sure you want to delete this carrier?', 'woo-orders-tracking' ),

					'confirm_delete_string_replace' => esc_html__( 'Remove this item?', 'woo-orders-tracking' ),

				)

			);

		}

	}

	public function search_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );
		if ( ! $keyword ) {
			$keyword = filter_input( INPUT_POST, 'keyword', FILTER_SANITIZE_STRING );
		}
		if ( empty( $keyword ) ) {
			die();
		}
		$args      = array(
			'post_status'    => 'any',
			'post_type'      => 'page',
			'posts_per_page' => 50,
			's'              => $keyword
		);
		$the_query = new WP_Query( $args );
		$items     = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$items[] = array( 'id' => get_the_ID(), 'text' => get_the_title() );
			}
		}
		wp_reset_postdata();
		wp_send_json( $items );
	}
}