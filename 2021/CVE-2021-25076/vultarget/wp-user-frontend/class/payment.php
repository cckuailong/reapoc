<?php

/**
 * WP User Frontend payment gateway handler
 *
 * @since 0.8
 */
class WPUF_Payment {

    public function __construct() {
        add_action( 'init', [ $this, 'send_to_gateway' ] );
        add_action( 'wpuf_payment_received', [ $this, 'payment_notify_admin' ] );
        add_filter( 'the_content', [ $this, 'payment_page' ] );
        add_action( 'init', [ $this, 'handle_cancel_payment' ] );
    }

    public static function get_payment_gateways() {

        // default, built-in gateways
        $gateways = [
            'paypal' => [
                'admin_label'    => __( 'PayPal', 'wp-user-frontend' ),
                'checkout_label' => __( 'PayPal', 'wp-user-frontend' ),
                'icon'           => apply_filters( 'wpuf_paypal_checkout_icon', WPUF_ASSET_URI . '/images/paypal.png' ),
            ],
            'bank' => [
                'admin_label'    => __( 'Bank Payment', 'wp-user-frontend' ),
                'checkout_label' => __( 'Bank Payment', 'wp-user-frontend' ),
            ],
        ];

        $gateways = apply_filters( 'wpuf_payment_gateways', $gateways );

        return $gateways;
    }

    /**
     * Get active payment gateways
     *
     * @return array
     */
    public function get_active_gateways() {
        $all_gateways    = wpuf_get_gateways( 'checkout' );
        $active_gateways = wpuf_get_option( 'active_gateways', 'wpuf_payment' );
        $active_gateways = is_array( $active_gateways ) ? $active_gateways : [];
        $gateways        = [];

        foreach ( $all_gateways as $id => $label ) {
            if ( array_key_exists( $id, $active_gateways ) ) {
                $gateways [ $id ] = $label;
            }
        }

        return $gateways;
    }

    /**
     * Show the payment page
     *
     * @param string $content
     *
     * @return string
     */
    public function payment_page( $content ) {
        global $post;

        $pay_page       = intval( wpuf_get_option( 'payment_page', 'wpuf_payment' ) );
        $billing_amount = 0;

        $action   = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';
        $get_type = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : '';
        $type     = ( $get_type === 'post' ) ? 'post' : 'pack';

        if ( ! is_user_logged_in() && $action === 'wpuf_pay' && $type !== 'post' ) {
            /* translators: %s: login url */
            printf( esc_html( __( 'This page is restricted. Please %s to view this page.', 'wp-user-frontend' ) ), wp_loginout( '', false ) );

            return;
        }

        if ( $action === 'wpuf_pay' && $pay_page === 0 ) {
            esc_html_e( 'Please select your payment page from admin panel', 'wp-user-frontend' );

            return;
        }

        if ( $post->ID === $pay_page && $action === 'wpuf_pay' ) {
            $post_id = isset( $_REQUEST['post_id'] ) ? intval( wp_unslash( $_REQUEST['post_id'] ) ) : 0;
            $pack_id = isset( $_REQUEST['pack_id'] ) ? intval( wp_unslash( $_REQUEST['pack_id'] ) ) : 0;
            $is_free = false;

            if ( $pack_id ) {
                $pack_detail = WPUF_Subscription::get_subscription( $pack_id );

                if ( ! $pack_detail ) {
                    ?>
                        <div class="wpuf-info"><?php esc_html_e( 'No subscription pack found.', 'wp-user-frontend' ); ?></div>
                    <?php
                    return;
                }

                $recurring_pay = isset( $pack_detail->meta_value['recurring_pay'] ) ? $pack_detail->meta_value['recurring_pay'] : 'no';

                if ( empty( $pack_detail->meta_value['billing_amount'] ) || $pack_detail->meta_value['billing_amount'] <= 0 ) {
                    $is_free = true;
                }
            }

            $gateways = $this->get_active_gateways();

            if ( isset( $_REQUEST['wpuf_payment_submit'] ) ) {
                $selected_gateway = isset( $_REQUEST['wpuf_payment_method'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpuf_payment_method'] ) ) : '';
            } else {
                $selected_gateway = 'paypal';
            }

            ob_start();

            if ( is_user_logged_in() ) {
                $current_user = wp_get_current_user();
            } else {
                $user_id      = isset( $_GET['user_id'] ) ? intval( wp_unslash( $_GET['user_id'] ) ) : 0;
                $current_user = get_userdata( $user_id );
            }

            if ( $pack_id && $is_free ) {
                $wpuf_subscription = WPUF_Subscription::init();
                $wpuf_user         = new WPUF_User( $current_user->ID );

                if ( ! $wpuf_user->subscription()->used_free_pack( $pack_id ) ) {
                    wpuf_get_user( $current_user->ID )->subscription()->add_pack( $pack_id, null, false, 'free' );
                    $wpuf_user->subscription()->add_free_pack( $current_user->ID, $pack_id );

                    $message = apply_filters( 'wpuf_fp_activated_msg', __( 'Your free package has been activated. Enjoy!', 'wp-user-frontend' ) );
                } else {
                    $message = apply_filters( 'wpuf_fp_activated_error', __( 'You already have activated a free package previously.', 'wp-user-frontend' ) );
                }
                ?>
                    <div class="wpuf-info"><?php echo esc_html( $message ); ?></div>
                <?php
            } else {
                ?>
                <?php
                if ( count( $gateways ) ) {
                    ?>
                    <div class="wpuf-payment-page-wrap wpuf-pay-row">
                    <?php $pay_page_style = ''; ?>
                        <div class="wpuf-bill-addr-wrap wpuf-pay-col">
                        <?php
                        if ( wpuf_get_option( 'show_address', 'wpuf_address_options', false ) && is_user_logged_in() ) {
                            $pay_page_style = 'vertical-align:top; margin-left: 20px; display: inline-block;';
                            ?>
                            <div class="wpuf-bill-addr-info">
                                <h3> <?php esc_html_e( 'Billing Address', 'wp-user-frontend' ); ?> </h3>
                                <div class="wpuf-bill_addr-inner">
                                    <?php
                                    $add_form = new WPUF_Ajax_Address_Form();
                                    $add_form->wpuf_ajax_address_form();
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        </div>
                        <div class="wpuf-payment-gateway-wrap" style="<?php echo esc_attr( $pay_page_style ); ?>">
                        <form id="wpuf-payment-gateway" action="" method="POST">

                            <?php
                            if ( $pack_id ) {
                                $pack         = WPUF_Subscription::init()->get_subscription( $pack_id );
                                $details_meta = WPUF_Subscription::init()->get_details_meta_value();
                                $currency     = wpuf_get_currency( 'symbol' );

                                if ( is_user_logged_in() ) {
                                    ?>
                                    <input type="hidden" name="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>">
                                    <?php
                                }
                                ?>

                                <div class="wpuf-coupon-info-wrap wpuf-pay-col">
                                    <div class="wpuf-coupon-info">
                                        <div class="wpuf-pack-info">
                                            <h3 class="wpuf-pay-col">
                                                <?php esc_html_e( 'Pricing & Plans', 'wp-user-frontend' ); ?>

                                                <a style="white-space: nowrap" href="<?php echo esc_attr( wpuf_get_subscription_page_url() ); ?>"><?php esc_html_e( 'Change Pack', 'wp-user-frontend' ); ?></a>
                                            </h3>
                                            <div class="wpuf-subscription-error"></div>
                                            <div class="wpuf-subscription-success"></div>

                                            <div class="wpuf-pack-inner">

                                                <?php
                                                if ( class_exists( 'WPUF_Coupons' ) ) {
                                                    echo wp_kses_post( WPUF_Coupons::init()->after_apply_coupon( $pack ) );
                                                } else {
                                                    $pack_cost      = $pack->meta_value['billing_amount'];
                                                    $billing_amount = apply_filters( 'wpuf_payment_amount', $pack->meta_value['billing_amount'] );
                                                    ?>

                                                    <div id="wpuf_type" style="display: none"><?php echo 'pack'; ?></div>
                                                    <div id="wpuf_id" style="display: none"><?php echo esc_attr( $pack_id ); ?></div>
                                                    <div><?php esc_html_e( 'Selected Pack ', 'wp-user-frontend' ); ?>: <strong><?php echo esc_attr( $pack->post_title ); ?></strong></div>
                                                    <div><?php esc_html_e( 'Pack Price ', 'wp-user-frontend' ); ?>: <strong><span id="wpuf_pay_page_cost"><?php echo esc_attr( wpuf_format_price( $pack_cost ) ); ?></strong></span></div>

                                                    <?php do_action( 'wpuf_before_pack_payment_total' ); ?>

                                                    <div><?php esc_html_e( 'Total', 'wp-user-frontend' ); ?>: <strong><span id="wpuf_pay_page_total"><?php echo esc_attr( wpuf_format_price( $billing_amount ) ); ?></strong></span></div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ( class_exists( 'WPUF_Coupons' ) ) { ?>
                                    <div class="wpuf-copon-wrap"  style="display:none;">
                                        <div class="wpuf-coupon-error" style="color: red;"></div>
                                        <input type="text" name="coupon_code" size="20" class="wpuf-coupon-field">
                                        <input type="hidden" name="coupon_id" size="20" class="wpuf-coupon-id-field">
                                        <div>
                                            <a href="#" data-pack_id="<?php echo esc_attr( $pack_id ); ?>" class="wpuf-apply-coupon"><?php esc_html_e( 'Apply Coupon', 'wp-user-frontend' ); ?></a>
                                            <a href="#" data-pack_id="<?php echo esc_attr( $pack_id ); ?>" class="wpuf-copon-cancel"><?php esc_html_e( 'Cancel', 'wp-user-frontend' ); ?></a>
                                        </div>
                                    </div>
                                    <a href="#" class="wpuf-copon-show"><?php esc_html_e( 'Have a discount code?', 'wp-user-frontend' ); ?></a>

                                    <?php } ?>
                                </div>

                                <?php
                            }

                            if ( $post_id ) {
                                $form              = new WPUF_Form( get_post_meta( $post_id, '_wpuf_form_id', true ) );
                                $force_pack        = $form->is_enabled_force_pack();
                                $pay_per_post      = $form->is_enabled_pay_per_post();
                                $fallback_enabled  = $form->is_enabled_fallback_cost();
                                $fallback_cost     = (float) $form->get_subs_fallback_cost();
                                $pay_per_post_cost = (float) $form->get_pay_per_post_cost();
                                $current_user      = wpuf_get_user();

                                $current_pack = $current_user->subscription()->current_pack();

                                if ( $force_pack && ! is_wp_error( $current_pack ) && $fallback_enabled ) {
                                    $post_cost      = $fallback_cost;
                                    $billing_amount = apply_filters( 'wpuf_payment_amount', $fallback_cost );
                                } else {
                                    $post_cost      = $pay_per_post_cost;
                                    $billing_amount = apply_filters( 'wpuf_payment_amount', $pay_per_post_cost );
                                }
                                ?>
                                        <div id="wpuf_type" style="display: none"><?php echo 'post'; ?></div>
                                        <div id="wpuf_id" style="display: none"><?php echo esc_attr( $post_id ); ?></div>
                                        <div><?php esc_html_e( 'Post cost', 'wp-user-frontend' ); ?>: <strong><span id="wpuf_pay_page_cost"><?php echo esc_attr( wpuf_format_price( $post_cost ) ); ?></strong></span></div>

                                        <?php do_action( 'wpuf_before_pack_payment_total' ); ?>

                                        <div><?php esc_html_e( 'Total', 'wp-user-frontend' ); ?>: <strong><span id="wpuf_pay_page_total"><?php echo esc_html( wpuf_format_price( $billing_amount ) ); ?></strong></span></div>
                                    <?php
                            }
                            ?>
                            <?php wp_nonce_field( 'wpuf_payment_gateway' ); ?>

                            <?php do_action( 'wpuf_before_payment_gateway' ); ?>

                            <p>
                                <label for="wpuf-payment-method"><?php esc_html_e( 'Choose Your Payment Method', 'wp-user-frontend' ); ?></label><br />

                                <ul class="wpuf-payment-gateways">
                                    <?php foreach ( $gateways as $gateway_id => $gateway ) { ?>
                                        <li class="wpuf-gateway-<?php echo esc_attr( $gateway_id ); ?>">
                                            <label>
                                                <input name="wpuf_payment_method" type="radio" value="<?php echo esc_attr( $gateway_id ); ?>" <?php checked( $selected_gateway, $gateway_id ); ?>>
                                                <?php
                                                echo esc_html( $gateway['label'] );

                                                if ( ! empty( $gateway['icon'] ) ) {
                                                    printf( ' <img src="%s" alt="image">', wp_kses_post( $gateway['icon'] ) );
                                                }
                                                ?>
                                            </label>

                                            <div class="wpuf-payment-instruction" style="display: none;">
                                                <div class="wpuf-instruction"><?php echo esc_html( wpuf_get_option( 'gate_instruct_' . esc_html( $gateway_id ), 'wpuf_payment' ) ); ?></div>

                                                <?php do_action( 'wpuf_gateway_form_' . $gateway_id, $type, $post_id, $pack_id ); ?>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </p>
                            <?php do_action( 'wpuf_after_payment_gateway' ); ?>
                            <p>
                                <input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>" />
                                <input type="hidden" name="action" value="wpuf_pay" />
                                <?php if ( $post_id ) { ?>
                                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
                                <?php } ?>

                                <?php if ( $pack_id ) { ?>
                                    <input type="hidden" name="pack_id" value="<?php echo esc_attr( $pack_id ); ?>" />
                                    <input type="hidden" name="recurring_pay" value="<?php echo esc_attr( $recurring_pay ); ?>" />
                                <?php } ?>
                                <input type="submit" name="wpuf_payment_submit" class="wpuf-btn" value="<?php esc_html_e( 'Proceed', 'wp-user-frontend' ); ?>"/>
                            </p>
                        </form>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <?php esc_html_e( 'No Payment gateway found', 'wp-user-frontend' ); ?>
                <?php } ?>

                <?php
            }

            return ob_get_clean();
        }

        return $content;
    }

    /**
     * Send payment handler to the gateway
     *
     * This function sends the payment handler mechanism to the selected
     * gateway. If 'paypal' is selected, then a particular action is being
     * called. A  listener function can be invoked for that gateway to handle
     * the request and send it to the gateway.
     *
     * Need to use `wpuf_gateway_{$gateway_name}
     */
    public function send_to_gateway() {
        $action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
        $nonce = isset( $_POST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ) : '';

        if ( $action === 'wpuf_pay' && isset( $nonce ) && wp_verify_nonce( $nonce, 'wpuf_payment_gateway' ) ) {
            $post_id       = isset( $_REQUEST['post_id'] ) ? intval( wp_unslash( $_REQUEST['post_id'] ) ) : 0;
            $pack_id       = isset( $_REQUEST['pack_id'] ) ? intval( wp_unslash( $_REQUEST['pack_id'] ) ) : 0;
            $gateway       = isset( $_POST['wpuf_payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_payment_method'] ) ) : '';
            $type          = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
            $current_user  = wpuf_get_user();
            $current_pack  = $current_user->subscription()->current_pack();
            $cost          = 0;

            if ( is_user_logged_in() ) {
                $userdata = wp_get_current_user();
            } else {
                $user_id = isset( $_REQUEST['user_id'] ) ? intval( wp_unslash( $_REQUEST['user_id'] ) ) : 0;

                if ( $user_id ) {
                    $userdata = get_userdata( $user_id );
                } elseif ( $type === 'post' && ! is_user_logged_in() ) {
                    $post      = get_post( $post_id );
                    $user_id   = $post->post_author;
                    $userdata  = get_userdata( $user_id );
                } else {
                    $userdata             = new stdClass();
                    $userdata->ID         = 0;
                    $userdata->user_email = '';
                    $userdata->first_name = '';
                    $userdata->last_name  = '';
                }
            }

            switch ( $type ) {
                case 'post':
                    $post          = get_post( $post_id );
                    $form_id       = get_post_meta( $post_id, '_wpuf_form_id', true );
                    $form          = new WPUF_Form( $form_id );
                    $form_settings = $form->get_settings();
                    $force_pack    = $form->is_enabled_force_pack();
                    $fallback_on   = $form->is_enabled_fallback_cost();
                    $post_count    = $current_user->subscription()->has_post_count( $form_settings['post_type'] );

                    if ( $force_pack && $fallback_on && ! is_wp_error( $current_pack ) && ! $post_count ) {
                        $amount = $form->get_subs_fallback_cost();
                    } else {
                        $amount = $form->get_pay_per_post_cost();
                    }
                    $item_number = $post->ID;
                    $item_name   = $post->post_title;
                    break;

                case 'pack':
                    $pack           = WPUF_Subscription::init()->get_subscription( $pack_id );
                    $custom         = $pack->meta_value;
                    $cost           = $pack->meta_value['billing_amount'];
                    $amount         = $cost;
                    $item_name      = $pack->post_title;
                    $item_number    = $pack->ID;
                    break;
            }

            $payment_vars = [
                'currency'    => wpuf_get_option( 'currency', 'wpuf_payment' ),
                'price'       => $amount,
                'item_number' => $item_number,
                'item_name'   => $item_name,
                'type'        => $type,
                'user_info'   => [
                    'id'         => $userdata->ID,
                    'email'      => $userdata->user_email,
                    'first_name' => $userdata->first_name,
                    'last_name'  => $userdata->last_name,
                ],
                'date'      => gmdate( 'Y-m-d H:i:s' ),
                'post_data' => $_POST,
                'custom'    => isset( $custom ) ? $custom : '',
            ];

            if ( isset( $_POST['billing_address'] ) ) {
                $address_fields = array_map( 'sanitize_text_field', wp_unslash( $_POST['billing_address'] ) );
            } else {
                $address_fields = wpuf_get_user_address();
            }

            if ( ! empty( $address_fields ) ) {
                update_user_meta( $userdata->ID, 'wpuf_address_fields', $address_fields );
            }

            /**
             * Filter: wpuf_payment_vars
             *
             * @since 3.1.13
             */
            $payment_vars = apply_filters( 'wpuf_payment_vars', $payment_vars );

            do_action( 'wpuf_gateway_' . $gateway, $payment_vars );
        }
    }

    /**
     * Insert payment info to database
     *
     * @global object $wpdb
     *
     * @param array $data           payment data to insert
     * @param int   $transaction_id the transaction id in case of update
     */
    public static function insert_payment( $data, $transaction_id = 0, $recurring = false ) {
        global $wpdb;

        $user_id = get_current_user_id();

        //check if it's already there
        $sql = $wpdb->prepare(
            'SELECT transaction_id
            FROM ' . $wpdb->prefix . 'wpuf_transaction
            WHERE transaction_id = %s LIMIT 1', $transaction_id
        );

        $result = $wpdb->get_row( $sql );

        if ( $recurring !== false ) {
            $profile_id = $data['profile_id'];
        }

        if ( isset( $data['profile_id'] ) || empty( $data['profile_id'] ) ) {
            unset( $data['profile_id'] );
        }

        if ( empty( $data['tax'] ) ) {
            $data['tax'] = floatval( $data['cost'] ) - floatval( $data['subtotal'] );
        }

        if ( wpuf_get_option( 'show_address', 'wpuf_address_options', false ) && ! empty( $data['user_id'] ) ) {
            $data['payer_address'] = wpuf_get_user_address( $data['user_id'] );
        }

        if ( ! empty( $data['payer_address'] ) ) {
            $data['payer_address'] = maybe_serialize( $data['payer_address'] );
        }

        if ( isset( $profile_id ) ) {
            $data['profile_id'] = $profile_id;
        }

        if ( ! $result ) {
            $wpdb->insert( $wpdb->prefix . 'wpuf_transaction', $data );

            do_action( 'wpuf_payment_received', $data, $recurring );
        } else {
            $wpdb->update( $wpdb->prefix . 'wpuf_transaction', $data, [ 'transaction_id' => $transaction_id ] );
        }

        //workaround for subscriptions can't be assigned from user profile regression
        if ( ! did_action( 'wpuf_payment_received' ) ) {
            do_action( 'wpuf_payment_received', $data, $recurring );
        }
    }

    /**
     * Send payment received mail
     *
     * @param array $info payment information
     */
    public function payment_notify_admin( $info ) {
        $headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>' . "\r\n\\";
        // translators: %s is site title name
        $subject = sprintf( __( '[%s] Payment Received', 'wp-user-frontend' ), get_bloginfo( 'name' ) );
        // translators: %s is site title name
        $msg = sprintf( __( 'New payment received at %s', 'wp-user-frontend' ), get_bloginfo( 'name' ) );

        $receiver = get_bloginfo( 'admin_email' );
        wp_mail( $receiver, $subject, $msg, $headers );
    }

    /**
     * Handle the cancel payment
     *
     * @return void
     *
     * @since  2.4.1
     */
    public function handle_cancel_payment() {
        $nonce = isset( $_POST['wpuf_payment_cancel'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_payment_cancel'] ) ) : '';
        $action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';

        if ( ! isset( $_POST['wpuf_payment_cancel_submit'] ) || $action !== 'wpuf_cancel_pay' || ! wp_verify_nonce( $nonce, '_wpnonce' ) ) {
            return;
        }

        $gateway = isset( $_POST['gateway'] ) ? sanitize_text_field( wp_unslash( $_POST['gateway'] ) ) : '';

        do_action( "wpuf_cancel_payment_{$gateway}", $_POST );
    }
}
