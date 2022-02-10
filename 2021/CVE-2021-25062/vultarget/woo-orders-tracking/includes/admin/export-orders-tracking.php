<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_ADMIN_EXPORT_ORDERS_TRACKING {
	private $settings;
	protected $error;

	public function __construct() {
		$this->settings = new VI_WOO_ORDERS_TRACKING_DATA();
		$this->error    = '';
		$this->enqueue_action();
	}
	public static function set( $name, $set_name = false ) {
		return VI_WOO_ORDERS_TRACKING_DATA::set( $name, $set_name );
	}
	public function enqueue_action() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ) );
		add_action( 'wp_ajax_vi_wot_export_preview', array( $this, 'vi_wot_export_preview' ) );
		add_action( 'admin_init', array( $this, 'orders_tracking_export_orders_tracking' ) );
	}

	public function admin_menu() {
		add_submenu_page(
			'woo-orders-tracking',
			esc_html__( 'Export Orders', 'woo-orders-tracking' ),
			esc_html__( 'Export Orders', 'woo-orders-tracking' ),
			'manage_options',
			'woo-orders-tracking-export',
			array( $this, 'settings_callback' )
		);

	}

	public function settings_callback() {
		$this->settings     = new VI_WOO_ORDERS_TRACKING_DATA();
		$all_order_statuses = wc_get_order_statuses();
		$billing_city       = $this->get_order_meta_values( '_billing_city' );
		$billing_country    = $this->get_countries( $this->get_order_meta_values( '_billing_country' ) );
		$shipping_city      = $this->get_order_meta_values( '_shipping_city' );
		$shipping_country   = $this->get_countries( $this->get_order_meta_values( '_shipping_country' ) );
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		$shipping_methods = $this->get_shipping_methods();
		?>
        <div class="wrap">
            <h2>
				<?php esc_html_e( 'Export orders tracking', 'woo-orders-tracking' ) ?>
            </h2>
            <form action="" method="post" id="vi_wot_export" class="vi-ui form">
				<?php
				wp_nonce_field( 'vi_wot_export_action_nonce', '_vi_wot_export_nonce' );
				if ( $this->error ) {
					echo '<div class="error" style="padding-top: 8px;padding-bottom: 8px;">';
					echo $this->error;
					echo '</div>';
				}
				?>
                <div class="vi-ui segment">
                    <h3><?php esc_html_e( 'Export filename', 'woo-orders-tracking' ) ?></h3>
                    <input type="text"
                           name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filename]"
                           id="<?php esc_attr_e( self::set( 'export-filename' ) ) ?>"
                           placeholder="orders-%y-%m-%d_%h-%i-%s"
                           value="<?php esc_attr_e( str_replace('.csv','',$this->settings->get_params( 'export_settings_filename' )) ) ?>">
                    <p>
                        <span><strong>%y</strong> : <?php esc_html_e( 'Year', 'woo-orders-tracking' ) ?></span>,
                        <span><strong>%m</strong> : <?php esc_html_e( 'Month', 'woo-orders-tracking' ) ?></span>,
                        <span><strong>%d</strong> : <?php esc_html_e( 'Day', 'woo-orders-tracking' ) ?></span>,
                        <span><strong>%h</strong> : <?php esc_html_e( 'Hour', 'woo-orders-tracking' ) ?></span>,
                        <span><strong>%i</strong> : <?php esc_html_e( 'Minute', 'woo-orders-tracking' ) ?></span>,
                        <span><strong>%s</strong> : <?php esc_html_e( 'Second', 'woo-orders-tracking' ) ?></span>
                    </p>
                </div>
                <div class="vi-ui accordion segment ">
                    <div class="title active">
                        <h2>
                            <i class="dropdown icon"></i><?php esc_html_e( 'Filter Orders', 'woo-orders-tracking' ) ?>
                        </h2>
                    </div>
                    <div class="content active">
                        <table class="form-table">
                            <tbody>
                            <tr valign="top">
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'export-filter-order-date' ) ) ?>"><?php esc_html_e( 'Date', 'woo-orders-tracking' ) ?></label>
                                </th>
                                <td>
                                    <div class="<?php esc_attr_e( self::set( 'export-filter-order-date-row' ) ) ?>"
                                         style="display: flex; justify-content: space-between;">
                                        <div style="width: 30%;">
                                            <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filter-order-date]"
                                                    id="<?php esc_attr_e( self::set( 'export-filter-order-date' ) ) ?>"
                                                    class="vi-ui fluid dropdown">
                                                <option value="date_created" <?php selected( $this->settings->get_params( 'export_settings_filter-order-date' ), 'date_created' ) ?> ><?php esc_html_e( 'Created date', 'woo-orders-tracking' ) ?></option>
                                                <option value="date_modified" <?php selected( $this->settings->get_params( 'export_settings_filter-order-date' ), 'date_modified' ) ?> ><?php esc_html_e( 'Modification date', 'woo-orders-tracking' ) ?></option>
                                                <option value="date_completed" <?php selected( $this->settings->get_params( 'export_settings_filter-order-date' ), 'date_completed' ) ?> ><?php esc_html_e( 'Completed Date', 'woo-orders-tracking' ) ?></option>
                                                <option value="date_paid" <?php selected( $this->settings->get_params( 'export_settings_filter-order-date' ), 'date_paid' ) ?> ><?php esc_html_e( 'Paid Date', 'woo-orders-tracking' ) ?></option>
                                            </select>
                                        </div>
                                        <div style="width: 30%">
                                            <input type="text"
                                                   class="<?php esc_attr_e( self::set( 'export-datepicker' ) ) ?>"
                                                   name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filter-order-date-from]"
                                                   id="<?php esc_attr_e( self::set( 'export-filter-order-date-range-from' ) ) ?>"
                                                   autocomplete="off"
                                                   value="<?php esc_attr_e( $this->settings->get_params( 'export_settings_filter-order-date-from' ) ) ?>">
                                            <p class="description <?php esc_attr_e( self::set( 'export-filter-order-date-range-from-error' ) ) ?>"
                                               style="color: red;"></p>
                                            <p class="description"><?php esc_html_e( 'Date From', 'woo-orders-tracking' ) ?></p>
                                        </div>
                                        <div style="width: 30%">
                                            <input type="text"
                                                   class="<?php esc_attr_e( self::set( 'export-datepicker' ) ) ?>"
                                                   name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filter-order-date-to]"
                                                   id="<?php esc_attr_e( self::set( 'export-filter-order-date-range-to' ) ) ?>"
                                                   autocomplete="off"
                                                   value="<?php esc_attr_e( $this->settings->get_params( 'export_settings_filter-order-date-to' ) ) ?>">
                                            <p class="description <?php esc_attr_e( self::set( 'export-filter-order-date-range-to-error' ) ) ?>"
                                               style="color: red;"></p>
                                            <p class="description"><?php esc_html_e( 'Date To', 'woo-orders-tracking' ) ?></p>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'export-filter-order-status' ) ) ?>"><?php esc_html_e( 'Statuses', 'woo-orders-tracking' ) ?></label>
                                </th>
                                <td>
                                    <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filter-order-status][]"
                                            id="<?php esc_attr_e( self::set( 'export-filter-order-status' ) ) ?>"
                                            class="<?php esc_attr_e( self::set( 'export-filter-order-status' ) ) ?>  vi-ui fluid dropdown"
                                            tabindex="-1" aria-hidden="true" multiple="">
										<?php
										if ( ! empty( $all_order_statuses ) ) {
											$export_settings_filter_order_status = $this->settings->get_params( 'export_settings_filter-order-status' ) ? $this->settings->get_params( 'export_settings_filter-order-status' ) : array();
											foreach ( $all_order_statuses as $status_id => $status_name ) {
												$selected = '';
												if ( is_array( $export_settings_filter_order_status ) && in_array( $status_id, $export_settings_filter_order_status ) ) {
													$selected = 'selected = "selected"';
												}
												?>
                                                <option value="<?php esc_attr_e( $status_id ) ?>" <?php echo $selected; ?> ><?php esc_attr_e( $status_name ) ?></option>
												<?php
											}
										}
										?>
                                    </select>
                                </td>
                            </tr>

                            <tr valign="top"
                                class="<?php esc_attr_e( self::set( 'export-filter-order-billing' ) ) ?>">
                                <th rowspan="2">
                                    <label for="<?php esc_attr_e( self::set( 'export-filter-order-billing-address' ) ) ?>"><?php esc_html_e( 'Billing address', 'woo-orders-tracking' ) ?></label>
                                </th>
                                <td>
                                    <div class="<?php esc_attr_e( self::set( 'export-filter-order-billing-container' ) ) ?>">
                                        <select name="<?php esc_attr_e( self::set( 'export-filter-order-billing-address' ) ) ?>"
                                                id="<?php esc_attr_e( self::set( 'export-filter-order-billing-address' ) ) ?>"
                                                class="vi-ui dropdown">
                                            <option value="_billing_country"><?php esc_html_e( 'Country', 'woo-orders-tracking' ) ?></option>
                                            <option value="_billing_city"><?php esc_html_e( 'City', 'woo-orders-tracking' ) ?></option>
                                        </select>
                                    </div>
                                    <div class="<?php esc_attr_e( self::set( 'export-filter-order-billing-container' ) ) ?>">
                                        <select name="<?php esc_attr_e( self::set( 'export-filter-order-billing-condition' ) ) ?>"
                                                id="<?php esc_attr_e( self::set( 'export-filter-order-billing-condition' ) ) ?>"
                                                class="vi-ui dropdown">
                                            <option value="="><?php esc_html_e( 'Equal', 'woo-orders-tracking' ) ?></option>
                                            <option value="<>"><?php esc_html_e( 'Not identical to', 'woo-orders-tracking' ) ?></option>
                                        </select>
                                    </div>
                                    <div class="<?php esc_attr_e( self::set( array(
										'export-filter-order-billing-container',
										'export-filter-order-billing-country-wrap',
										'export-hidden'
									) ) ) ?>">
                                        <select name="<?php esc_attr_e( self::set( 'export-filter-order-billing-country' ) ) ?>"
                                                id="<?php esc_attr_e( self::set( 'export-filter-order-billing-country' ) ) ?>"
                                                class=" <?php esc_attr_e( self::set( 'export-filter-order-billing-country' ) ) ?>   select2-hidden-accessible"
                                                tabindex="-1" aria-hidden="true">
                                            <option value=""></option>
											<?php
											if ( isset( $billing_country ) && is_array( $billing_country ) && count( $billing_country ) ) {
												foreach ( $billing_country as $country_id => $country_name ) {
													?>
                                                    <option value="<?php esc_attr_e( $country_id ) ?>"><?php esc_attr_e( $country_name ) ?></option>
													<?php
												}
											}
											?>
                                        </select>
                                    </div>

                                    <div class="<?php esc_attr_e( self::set( array(
										'export-filter-order-billing-container',
										'export-filter-order-billing-city-wrap',
										'export-hidden'
									) ) ) ?>">
                                        <select name="<?php esc_attr_e( self::set( 'export-filter-order-billing-city' ) ) ?>"
                                                id="<?php esc_attr_e( self::set( 'export-filter-order-billing-city' ) ) ?>"
                                                class=" <?php esc_attr_e( self::set( array(
											        'export-filter-order-billing-city',
											        'export-hidden'
										        ) ) ) ?>  select2-hidden-accessible"
                                                tabindex="-1" aria-hidden="true">
                                            <option value=""></option>
											<?php
											if ( isset( $billing_city ) && is_array( $billing_city ) && count( $billing_city ) ) {
												foreach ( $billing_city as $city ) {
													?>
                                                    <option value="<?php esc_attr_e( $city ) ?>"><?php esc_attr_e( $city ) ?></option>
													<?php
												}
											}
											?>
                                        </select>
                                    </div>

                                    <div class="<?php esc_attr_e( self::set( 'export-filter-order-billing-container' ) ) ?>">
                                        <button type="button"
                                                class="vi-ui button <?php esc_attr_e( self::set( 'export-filter-order-billing-addition' ) ) ?>">
											<?php esc_html_e( 'Add condition ', 'woo-orders-tracking' ) ?><i
                                                    class="plus icon"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr valign="top"
                                class="<?php esc_attr_e( self::set( 'export-filter-order-billing-list' ) ) ?>">
                                <td>
                                    <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filter-order-billing-address][]"
                                            id="<?php esc_attr_e( self::set( 'export-filter-order-billing-address-data' ) ) ?>"
                                            class=" select2-hidden-accessible <?php esc_attr_e( self::set( 'export-filter-order-billing-address-data' ) ) ?> "
                                            multiple=""
                                            tabindex="-1" aria-hidden="true">
										<?php
										$export_settings_filter_billing_address = $this->settings->get_params( 'export_settings_filter-order-billing-address' );
										if ( $export_settings_filter_billing_address && is_array( $export_settings_filter_billing_address ) && count( $export_settings_filter_billing_address ) ) {
											foreach ( $export_settings_filter_billing_address as $item ) {
												$t = '';
												if ( strpos( $item, '=' ) ) {
													$item_data = explode( '=', $item );
													$t         = '=';
												} elseif ( strpos( $item, '<>' ) ) {
													$item_data = explode( '<>', $item );
													$t         = '<>';
												}
												if ( isset( $item_data ) && is_array( $item_data ) && count( $item_data ) >= 2 ) {
													list( $item_data_id, $item_data_name ) = array_map( 'trim', $item_data );
													if ( $item_data_id === '_billing_country' && isset( $billing_country ) && is_array( $billing_country ) && count( $billing_country ) ) {
														echo '<option value="' . $item . '" selected > Country  ' . $t . ' ' . ( isset( $billing_country[ $item_data_name ] ) ? $billing_country[ $item_data_name ] : $item_data_name ) . '</option>';
													} elseif ( $item_data_id === '_billing_city' && isset( $billing_city ) && is_array( $billing_city ) && count( $billing_city ) ) {
														echo '<option value="' . $item . '" selected > City  ' . $t . ' ' . $item_data_name . '</option>';
													}
												}
											}
										}
										?>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Filter orders by Billing country/city', 'woo-orders-tracking' ) ?></p>
                                </td>
                            </tr>

                            <tr valign="top"
                                class="<?php esc_attr_e( self::set( 'export-filter-order-shipping' ) ) ?>">
                                <th rowspan="2">
                                    <label for="<?php esc_attr_e( self::set( 'export-filter-order-shipping-address' ) ) ?>"><?php esc_html_e( 'Shipping address', 'woo-orders-tracking' ) ?></label>
                                </th>
                                <td>
                                    <div class="<?php esc_attr_e( self::set( 'export-filter-order-shipping-container' ) ) ?>">
                                        <select name="<?php esc_attr_e( self::set( 'export-filter-order-shipping-address' ) ) ?>"
                                                id="<?php esc_attr_e( self::set( 'export-filter-order-shipping-address' ) ) ?>"
                                                class="vi-ui dropdown">
                                            <option value="_shipping_country"><?php esc_html_e( 'Country', 'woo-orders-tracking' ) ?></option>
                                            <option value="_shipping_city"><?php esc_html_e( 'City', 'woo-orders-tracking' ) ?></option>
                                        </select>
                                    </div>
                                    <div class="<?php esc_attr_e( self::set( 'export-filter-order-shipping-container' ) ) ?>">
                                        <select name="<?php esc_attr_e( self::set( 'export-filter-order-shipping-condition' ) ) ?>"
                                                id="<?php esc_attr_e( self::set( 'export-filter-order-shipping-condition' ) ) ?>"
                                                class="vi-ui dropdown">
                                            <option value="="><?php esc_html_e( 'Identical', 'woo-orders-tracking' ) ?></option>
                                            <option value="<>"><?php esc_html_e( 'Not identical to', 'woo-orders-tracking' ) ?></option>
                                        </select>
                                    </div>
                                    <div class="<?php esc_attr_e( self::set( array(
										'export-filter-order-shipping-container',
										'export-filter-order-shipping-country-wrap',
										'export-hidden'
									) ) ) ?>">
                                        <select name="<?php esc_attr_e( self::set( 'export-filter-order-shipping-country' ) ) ?>"
                                                id="<?php esc_attr_e( self::set( 'export-filter-order-shipping-country' ) ) ?>"
                                                class=" <?php esc_attr_e( self::set( 'export-filter-order-shipping-country' ) ) ?>   select2-hidden-accessible"
                                                tabindex="-1" aria-hidden="true">
                                            <option value=""></option>
											<?php
											if ( ! empty( $shipping_country ) ) {
												foreach ( $shipping_country as $country_id => $country_name ) {
													?>
                                                    <option value="<?php esc_attr_e( $country_id ) ?>"><?php esc_attr_e( $country_name ) ?></option>
													<?php
												}
											}
											?>
                                        </select>
                                    </div>

                                    <div class="<?php esc_attr_e( self::set( array(
										'export-filter-order-shipping-container',
										'export-filter-order-shipping-city-wrap',
										'export-hidden'
									) ) ) ?>">
                                        <select name="<?php esc_attr_e( self::set( 'export-filter-order-shipping-city' ) ) ?>"
                                                id="<?php esc_attr_e( self::set( 'export-filter-order-shipping-city' ) ) ?>"
                                                class=" <?php esc_attr_e( self::set( 'export-filter-order-shipping-city', 'export-hidden' ) ) ?>  select2-hidden-accessible"
                                                tabindex="-1" aria-hidden="true">
                                            <option value=""></option>
											<?php
											if ( ! empty( $shipping_city ) ) {
												foreach ( $shipping_city as $city ) {
													?>
                                                    <option value="<?php esc_attr_e( $city ) ?>"><?php esc_attr_e( $city ) ?></option>
													<?php
												}
											}
											?>
                                        </select>
                                    </div>

                                    <div class="<?php esc_attr_e( self::set( 'export-filter-order-shipping-container' ) ) ?>">
                                        <button type="button"
                                                class="vi-ui button <?php esc_attr_e( self::set( 'export-filter-order-shipping-addition' ) ) ?>"
                                                style="max-height: 38px; min-height: 30px;">
											<?php esc_html_e( 'Add condition ', 'woo-orders-tracking' ) ?><i
                                                    class="plus icon"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr valign="top"
                                class="<?php esc_attr_e( self::set( 'export-filter-order-shipping-list' ) ) ?>">
                                <td>
                                    <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filter-order-shipping-address][]"
                                            id="<?php esc_attr_e( self::set( 'export-filter-order-shipping-address-data' ) ) ?>"
                                            class=" <?php esc_attr_e( self::set( 'export-filter-order-shipping-address-data' ) ) ?>"
                                            tabindex="-1" aria-hidden="true" multiple="">
										<?php
										$export_settings_filter_shipping_address = $this->settings->get_params( 'export_settings_filter-order-shipping-address' );
										if ( $export_settings_filter_shipping_address && is_array( $export_settings_filter_shipping_address ) && count( $export_settings_filter_shipping_address ) ) {
											foreach ( $export_settings_filter_shipping_address as $item ) {
												$t = '';
												if ( strpos( $item, '=' ) ) {
													$item_data = explode( '=', $item );
													$t         = '=';
												} elseif ( strpos( $item, '<>' ) ) {
													$item_data = explode( '<>', $item );
													$t         = '<>';
												}
												if ( isset( $item_data ) && is_array( $item_data ) && count( $item_data ) >= 2 ) {
													list( $item_data_id, $item_data_name ) = array_map( 'trim', $item_data );
													if ( $item_data_id === '_shipping_country' && isset( $shipping_country ) && is_array( $shipping_country ) && count( $shipping_country ) ) {
														echo '<option value="' . $item . '" selected > Country  ' . $t . ' ' . ( isset( $shipping_country[ $item_data_name ] ) ? $shipping_country[ $item_data_name ] : $item_data_name ) . '</option>';
													} elseif ( $item_data_id === '_shipping_city' && isset( $shipping_city ) && is_array( $shipping_city ) && count( $shipping_city ) ) {
														echo '<option value="' . $item . '" selected > City  ' . $t . ' ' . $item_data_name . '</option>';
													}
												}
											}
										}
										?>
                                    </select>
                                    <p class="description">
										<?php esc_html_e( 'Filter orders by Shipping country/city', 'woo-orders-tracking' ) ?>
                                    </p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'export-filter-order-payment-method' ) ) ?>">
										<?php esc_html_e( 'Payment methods', 'woo-orders-tracking' ) ?>
                                    </label>
                                </th>
                                <td>
                                    <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filter-order-payment-method][]"
                                            id="<?php esc_attr_e( self::set( 'export-filter-order-payment-method' ) ) ?>"
                                            class="<?php esc_attr_e( self::set( 'export-filter-order-payment-method' ) ) ?> vi-ui fluid dropdown"
                                            tabindex="-1" aria-hidden="true" multiple="">
										<?php
										$export_settings_filter_order_payment_method = $this->settings->get_params( 'export_settings_filter-order-payment-method' );
										if ( ! empty( $available_gateways ) ) {
											foreach ( $available_gateways as $method ) {
												$selected = '';
												if ( $export_settings_filter_order_payment_method && is_array( $export_settings_filter_order_payment_method ) && in_array( $method->id, $export_settings_filter_order_payment_method ) ) {
													$selected = 'selected="selected"';
												}
												?>
                                                <option value="<?php esc_attr_e( $method->id ) ?>" <?php echo $selected; ?> ><?php echo  $method->method_title; ?></option>
												<?php
											}
										}
										?>

                                    </select>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'export-filter-order-shipping-method' ) ) ?>">
										<?php esc_html_e( 'Shipping methods', 'woo-orders-tracking' ) ?>
                                    </label>
                                </th>
                                <td>
                                    <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[filter-order-shipping-method][]"
                                            id="<?php esc_attr_e( self::set( 'export-filter-order-shipping-method' ) ) ?>"
                                            class="<?php esc_attr_e( self::set( 'export-filter-order-shipping-method' ) ) ?> vi-ui fluid dropdown"
                                            tabindex="-1" aria-hidden="true" multiple="">
										<?php
										$export_settings_filter_order_shipping_method = $this->settings->get_params( 'export_settings_filter-order-shipping-method' );
										if ( ! empty( $shipping_methods ) ) {
											foreach ( $shipping_methods as $method_id => $method_name ) {
												$selected = '';
												if ( $export_settings_filter_order_shipping_method && is_array( $export_settings_filter_order_shipping_method ) && count( $export_settings_filter_order_shipping_method ) && in_array( $method_id, $export_settings_filter_order_shipping_method ) ) {
													$selected = 'selected="selected"';
												}
												?>
                                                <option value="<?php esc_attr_e( $method_id ) ?>" <?php echo $selected; ?> ><?php esc_attr_e( $method_name ) ?></option>
												<?php
											}
										}
										?>

                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="vi-ui accordion segment">
                    <div class="title active">
                        <h2>
                            <i class="dropdown icon"></i><?php esc_html_e( 'Export Orders', 'woo-orders-tracking' ) ?>
                        </h2>
                    </div>
                    <div class="content active">
                        <table class="form-table">
                            <tbody>

                            <tr valign="top">
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'export-set-fields' ) ) ?>">
										<?php esc_html_e( 'Fields', 'woo-orders-tracking' ) ?>
                                    </label>
                                </th>
                                <td>
                                    <div class="field">
                                        <div class="equal width fields">
                                            <div class="field">
                                                <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[set-fields][]"
                                                        id="<?php esc_attr_e( self::set( 'export-select-fields' ) ) ?>"
                                                        class="<?php esc_attr_e( self::set( 'export-select-fields' ) ) ?> vi-ui fluid search dropdown" multiple="">
													<?php
													$selected_fields  = $this->settings->get_params( 'export_settings_filter-order-export-set-fields' );
													$fields_to_select = VI_WOO_ORDERS_TRACKING_ADMIN_EXPORT_ORDER_MANAGE::get_fields_to_select();
													if ( $fields_to_select && is_array( $fields_to_select ) && count( $fields_to_select ) ) {
														foreach ( $fields_to_select as $field ) {
															$selected = '';
															if ( in_array( $field['key'], $selected_fields ) ) {
																$selected = 'selected = "selected"';
															}
															?>
                                                            <option value="<?php echo esc_attr( "{$field['type']}{wotv}{$field['key']}" ) ?>" <?php echo esc_attr( $selected ) ?>><?php echo esc_html( $field['title'] ) ?></option>
															<?php
														}
													}
													?>
                                                </select>
                                                <p class="description"><?php esc_html_e( 'Choose fields to export. Leave blank to export all available fields.', 'woo-orders-tracking' ) ?></p>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th>
                                    <label for="">
										<?php esc_html_e( 'Sort orders by', 'woo-orders-tracking' ) ?>
                                    </label>
                                </th>
                                <td style="display: flex;">
                                    <div style="margin-right: 10px;">
                                        <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[sort-order]"
                                                id="<?php esc_attr_e( self::set( 'export-sort-order' ) ) ?>"
                                                class="vi-ui dropdown">
                                            <option value="order_id"><?php esc_html_e( 'Order id', 'woo-orders-tracking' ) ?></option>
                                            <option value="order_created"><?php esc_html_e( 'Created date', 'woo-orders-tracking' ) ?></option>
                                            <option value="order_modification"><?php esc_html_e( 'Modification date', 'woo-orders-tracking' ) ?></option>
                                        </select>
                                        <p class="description"><?php esc_html_e( 'Select field to sort orders', 'woo-orders-tracking' ) ?></p>
                                    </div>
                                    <div>
                                        <select name="<?php esc_attr_e( self::set( 'export' ) ) ?>[sort-order-in]"
                                                id="<?php esc_attr_e( self::set( 'export-sort-order-in' ) ) ?>"
                                                class="vi-ui dropdown">
                                            <option value="ASC"><?php esc_html_e( 'Ascending', 'woo-orders-tracking' ) ?></option>
                                            <option value="DESC"
                                                    selected><?php esc_html_e( 'Descending', 'woo-orders-tracking' ) ?></option>
                                        </select>
                                        <p class="description"><?php esc_html_e( 'Select type to sort orders', 'woo-orders-tracking' ) ?></p>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <p>
                    <button type="button"
                            class="button vi-ui  <?php esc_attr_e( self::set( 'export-order-button-preview' ) ) ?>">
						<?php esc_html_e( 'Preview', 'woo-orders-tracking' ) ?>
                    </button>
                    <button type="submit"
                            class="button vi-ui  <?php esc_attr_e( self::set( 'export-order-button-save-settings' ) ) ?>"
                            name="<?php esc_attr_e( self::set( 'export-order-button-save-settings' ) ) ?>">
						<?php esc_html_e( 'Save Filter Settings', 'woo-orders-tracking' ) ?>
                    </button>

                    <button type="submit"
                            class="button vi-ui primary <?php esc_attr_e( self::set( 'export-order-button-export' ) ) ?>"
                            name="<?php esc_attr_e( self::set( 'export-order-button-export' ) ) ?>">
						<?php esc_html_e( 'Export Orders', 'woo-orders-tracking' ) ?>
                    </button>

                    <button type="button"
                            class="button vi-ui  <?php esc_attr_e( self::set( 'export-order-button-reset-settings' ) ) ?>"
                            name="<?php esc_attr_e( self::set( 'export-order-button-reset-settings' ) ) ?>">
						<?php esc_html_e( 'Reset Filter Settings', 'woo-orders-tracking' ) ?>
                    </button>
                </p>
                <div class="vi-ui  <?php esc_attr_e( self::set( 'export-preview' ) ) ?>"
                     style="overflow: auto;">

                </div>
            </form>
        </div>
		<?php
	}

	private function get_order_meta_values( $key ) {
		global $wpdb;
		$query   = $wpdb->prepare( 'SELECT DISTINCT meta_value FROM ' . $wpdb->postmeta . ' WHERE meta_key = %s', strtolower( $key ) );
		$results = $wpdb->get_col( $query );
		sort( $results );

		return $results;
	}

	private function get_countries( $list ) {
		$countries = array();
		if ( ! empty( $list ) ) {
			$list_countries = new WC_Countries();
			$list_countries = $list_countries->__get( 'countries' );
			foreach ( $list as $country ) {
				if ( array_key_exists( $country, $list_countries ) ) {
					$countries[ $country ] = $list_countries[ $country ];
				}
			}
		}

		return $countries;
	}

	private function get_shipping_methods() {
		global $wpdb;
		$shipping_methods = array();
		// try get  methods for zones
		if ( class_exists( "WC_Shipping_Zone" ) && method_exists( 'WC_Shipping_Zone', 'get_shipping_methods' ) ) {
			$zone    = new WC_Shipping_Zone( 0 );
			$methods = $zone->get_shipping_methods();
			foreach ( $methods as $method ) {
				$shipping_methods[ $method->get_rate_id() ] = __( '[Rest of the World]', 'woo-orders-tracking' ) . ' ' . $method->get_title();
			}
		}

		if ( class_exists( 'WC_Shipping_Zones' ) ) {

			foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
				foreach ( $zone['shipping_methods'] as $method ) {
					$shipping_methods[ $method->get_rate_id() ] = '[' . $zone['zone_name'] . '] ' . $method->get_title();
				}
			}
		}


		return $shipping_methods;
	}

	private function stripslashes_deep( $value ) {
		if ( is_array( $value ) ) {
			$value = array_map( 'stripslashes_deep', $value );
		} else {
			$value = wp_kses_post( stripslashes( $value ) );
		}

		return $value;
	}


	public function vi_wot_export_preview() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$data     = array();
		$settings = isset( $_POST ) ? stripslashes_deep( $_POST ) : array();
		if ( empty( $settings ) || empty( $settings['export_settings'] ) ) {
			return;
		}

		$settings = json_decode( $settings['export_settings'], true );
		if ( ! isset( $settings['_vi_wot_export_nonce'] ) || ! wp_verify_nonce( $settings['_vi_wot_export_nonce'], 'vi_wot_export_action_nonce' ) ) {
			return;
		}


		$export_settings = $settings['woo-orders-tracking-export'];
		$data            = VI_WOO_ORDERS_TRACKING_ADMIN_EXPORT_ORDER_MANAGE::get_data_export( $export_settings, 5 );
		if ( empty( $data ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'No order found', 'woo-orders-tracking' )
				)
			);
			die();
		}
		$header_row = $data_rows = array();
		if ( ! empty( $data['header_row'] ) ) {
			foreach ( $data['header_row'] as $item_id => $item_value ) {
				$header_row[] = $item_value['title'];
			}
		}
		if ( ! empty( $data['content'] ) ) {
			foreach ( $data['content'] as $item ) {
				$data_rows[] = $item;
			}
		}

		ob_start();
		?>
        <table class="vi-ui  striped table " style="width: 100%; ">
            <thead>
            <tr>
				<?php
				foreach ( $header_row as $column ) {
					?>
                    <th style="min-width: 100px;">
						<?php echo esc_html__( $column ) ?>
                    </th>
					<?php
				}
				?>
            </tr>
            </thead>
            <tbody>
			<?php
			foreach ( $data_rows as $row ) {
				echo '<tr >';
				foreach ( $row as $column ) {
					echo '<td >' . $column . '</td>';
				}
				echo '</tr>';
			}
			?>
            </tbody>
        </table>
		<?php
		$html = ob_get_clean();
		wp_send_json(
			array(
				'status'  => 'success',
				'preview' => $html,
			)
		);
		die();
	}


	public function orders_tracking_export_orders_tracking() {
		global $pagenow;
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( $pagenow === 'admin.php' && isset( $_REQUEST['page'] ) && sanitize_text_field( $_REQUEST['page'] ) === 'woo-orders-tracking-export' ) {
			if ( ! isset( $_POST['_vi_wot_export_nonce'] ) || ! wp_verify_nonce( $_POST['_vi_wot_export_nonce'], 'vi_wot_export_action_nonce' ) ) {
				return;
			}
			if ( isset( $_POST['woo-orders-tracking-export-order-button-save-settings'] ) ) {
				global $woo_orders_tracking_settings;
				$args                                                  = array();
				$args['export_settings_filename']                      = isset( $_POST['woo-orders-tracking-export']['filename'] ) ? sanitize_text_field( stripslashes( $_POST['woo-orders-tracking-export']['filename'] ) ) : '';
				$args['export_settings_filter-order-date']             = isset( $_POST['woo-orders-tracking-export']['filter-order-date'] ) ? sanitize_text_field( stripslashes( $_POST['woo-orders-tracking-export']['filter-order-date'] ) ) : '';
				$args['export_settings_filter-order-date-from']        = isset( $_POST['woo-orders-tracking-export']['filter-order-date-from'] ) ? sanitize_text_field( stripslashes( $_POST['woo-orders-tracking-export']['filter-order-date-from'] ) ) : '';
				$args['export_settings_filter-order-date-to']          = isset( $_POST['woo-orders-tracking-export']['filter-order-date-to'] ) ? sanitize_text_field( stripslashes( $_POST['woo-orders-tracking-export']['filter-order-date-to'] ) ) : '';
				$args['export_settings_filter-order-status']           = isset( $_POST['woo-orders-tracking-export']['filter-order-status'] ) ? $this->stripslashes_deep( $_POST['woo-orders-tracking-export']['filter-order-status'] ) : array();
				$args['export_settings_filter-order-billing-address']  = isset( $_POST['woo-orders-tracking-export']['filter-order-billing-address'] ) ? $this->stripslashes_deep( $_POST['woo-orders-tracking-export']['filter-order-billing-address'] ) : array();
				$args['export_settings_filter-order-shipping-address'] = isset( $_POST['woo-orders-tracking-export']['filter-order-shipping-address'] ) ? $this->stripslashes_deep( $_POST['woo-orders-tracking-export']['filter-order-shipping-address'] ) : array();
				$args['export_settings_filter-order-payment-method']   = isset( $_POST['woo-orders-tracking-export']['filter-order-payment-method'] ) ? $this->stripslashes_deep( $_POST['woo-orders-tracking-export']['filter-order-payment-method'] ) : array();
				$args['export_settings_filter-order-shipping-method']  = isset( $_POST['woo-orders-tracking-export']['filter-order-shipping-method'] ) ? $this->stripslashes_deep( $_POST['woo-orders-tracking-export']['filter-order-shipping-method'] ) : array();
				$args                                                  = wp_parse_args( $args, get_option( 'woo_orders_tracking_settings', $woo_orders_tracking_settings ) );
				if ( isset( $_POST['woo-orders-tracking-export']['set-fields'] ) && is_array( $_POST['woo-orders-tracking-export']['set-fields'] ) ) {
					$set_fields = array();
					foreach ( $_POST['woo-orders-tracking-export']['set-fields'] as $set_field ) {
						$set_fields[] = str_replace( array('wotv_field{wotv}','post_meta{wotv}','order_item_meta{wotv}'), '', sanitize_text_field( $set_field ) );
					}
					$args['export_settings_filter-order-export-set-fields'] = $set_fields;
				}
				update_option( 'woo_orders_tracking_settings', $args );

				$woo_orders_tracking_settings = $args;
			}
			if ( isset( $_POST['woo-orders-tracking-export-order-button-export'] ) ) {
				$data            = array();
				$export_settings = isset( $_POST['woo-orders-tracking-export'] ) ? $this->stripslashes_deep( $_POST['woo-orders-tracking-export'] ) : array();
				$data            = VI_WOO_ORDERS_TRACKING_ADMIN_EXPORT_ORDER_MANAGE::get_data_export( $export_settings );
				if ( empty( $data ) ) {
					$this->error = esc_html__( 'No order found', 'woo-orders-tracking' );
				} else {
					$filename   = $data['filename'];
					$header_row = $data_rows = array();
					if ( ! empty( $data['header_row'] ) ) {
						foreach ( $data['header_row'] as $item_id => $item_value ) {
							$header_row[] = $item_value['title'];
						}
					}
					if ( ! empty( $data['content'] ) ) {
						foreach ( $data['content'] as $item ) {
							$data_rows[] = $item;
						}
					}
					$fh = @fopen( 'php://output', 'w' );
					fprintf( $fh, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
					header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
					header( 'Content-Description: File Transfer' );
					header( 'Content-type: text/csv' );
					header( 'Content-Disposition: attachment; filename=' . $filename );
					header( 'Expires: 0' );
					header( 'Pragma: public' );
					fputcsv( $fh, $header_row );
					foreach ( $data_rows as $data_row ) {
						fputcsv( $fh, $data_row );

					}
					$csvFile = stream_get_contents( $fh );
					fclose( $fh );
					die();
				}
			}


		}
	}

	public function admin_enqueue_script() {

		global $pagenow;

		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		if ( $pagenow === 'admin.php' && $page === 'woo-orders-tracking-export' ) {
			wp_enqueue_style( 'vi-wot-admin-export-css', VI_WOO_ORDERS_TRACKING_CSS . 'admin-export.css', '', VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_style( 'vi-wot-admin-export-accordion', VI_WOO_ORDERS_TRACKING_CSS . 'accordion.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-export-button', VI_WOO_ORDERS_TRACKING_CSS . 'button.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-export-dropdown', VI_WOO_ORDERS_TRACKING_CSS . 'dropdown.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-export-form', VI_WOO_ORDERS_TRACKING_CSS . 'form.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-export-icon', VI_WOO_ORDERS_TRACKING_CSS . 'icon.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-export-segment', VI_WOO_ORDERS_TRACKING_CSS . 'segment.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-export-select2', VI_WOO_ORDERS_TRACKING_CSS . 'select2.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_style( 'vi-wot-admin-export-transition', VI_WOO_ORDERS_TRACKING_CSS . 'transition.min.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			$wp_scripts = wp_scripts();
			wp_enqueue_style( 'woo-orders-tracking-admin-ui-css',
				'//ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css',
				false,
				VI_WOO_ORDERS_TRACKING_VERSION,
				false );
			wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array(
				'jquery-ui-draggable',
				'jquery-ui-slider',
				'jquery-touch-punch'
			), false, 1 );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'vi-wot-admin-export-accordion', VI_WOO_ORDERS_TRACKING_JS . 'accordion.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'vi-wot-admin-export-address', VI_WOO_ORDERS_TRACKING_JS . 'address.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'vi-wot-admin-export-dropdown', VI_WOO_ORDERS_TRACKING_JS . 'dropdown.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'vi-wot-admin-export-form', VI_WOO_ORDERS_TRACKING_JS . 'form.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'vi-wot-admin-export-select2', VI_WOO_ORDERS_TRACKING_JS . 'select2.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'vi-wot-admin-export-serializejson', VI_WOO_ORDERS_TRACKING_JS . 'serializejson.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'vi-wot-admin-export-transition', VI_WOO_ORDERS_TRACKING_JS . 'transition.min.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_enqueue_script( 'vi-wot-admin-export-js', VI_WOO_ORDERS_TRACKING_JS . 'admin-export.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );

			wp_localize_script(
				'vi-wot-admin-export-js',
				'vi_wot_admin_export',
				array(
					'ajax_url'         => admin_url( 'admin-ajax.php' ),
					'date_range_error' => __( 'Date To must be greater than Date From', 'woo-orders-tracking' ),
					'date_from_error'  => __( 'Date Form mustn\'t be greater than Today', 'woo-orders-tracking' ),
				)
			);
		}
	}
}