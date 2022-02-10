<?php

/**
 * WPUF subscription manager
 *
 * @since 0.2
 *
 * @author Tareq Hasan
 */
class WPUF_Subscription {

    private static $_instance;

    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_filter( 'wpuf_add_post_args', [ $this, 'set_pending' ], 10, 4 );
        add_filter( 'wpuf_add_post_redirect', [ $this, 'post_redirect' ], 10, 4 );

        add_filter( 'wpuf_addpost_notice', [ $this, 'force_pack_notice' ], 20, 3 );
        add_filter( 'wpuf_can_post', [ $this, 'force_pack_permission' ], 20, 3 );
        add_action( 'wpuf_add_post_form_top', [ $this, 'add_post_info' ], 10, 2 );

        add_action( 'wpuf_add_post_after_insert', [ $this, 'monitor_new_post' ], 10, 3 );
        add_action( 'wpuf_draft_post_after_insert', [ $this, 'monitor_new_draft_post' ], 10, 3 );
        add_action( 'wpuf_payment_received', [ $this, 'payment_received' ], 10, 2 );

        add_shortcode( 'wpuf_sub_info', [ $this, 'subscription_info' ] );
        add_shortcode( 'wpuf_sub_pack', [ $this, 'subscription_packs' ] );

        add_action( 'save_post', [ $this, 'save_form_meta' ], 10, 2 );
        add_filter( 'enter_title_here', [ $this, 'change_default_title' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'subscription_script' ] );

        add_action( 'user_register', [ $this, 'after_registration' ], 10, 1 );

        add_action( 'register_form', [ $this, 'register_form' ] );
        add_action( 'wpuf_add_post_form_top', [ $this, 'register_form' ] );
        add_filter( 'wpuf_user_register_redirect', [ $this, 'subs_redirect_pram' ], 10, 5 );

        add_filter( 'template_redirect', [ $this, 'user_subscription_cancel' ] );

        add_action( 'wpuf_draft_post_after_insert', [ $this, 'reset_user_subscription_data' ], 10, 4 );

        add_filter( 'wpuf_get_subscription_meta', [ $this, 'reset_trial' ] );
        //Handle non recurring subscription when expired
        add_action( 'wp', [ $this, 'handle_non_recur_subs' ] );
        add_action( 'non_recur_subs_daily', [ $this, 'cancel_non_recurring_subscription' ] );
    }

    /**
     * Handle subscription cancel request from the user
     *
     * @return WPUF_Subscription
     */
    public static function subscriber_cancel( $user_id, $pack_id ) {
        global $wpdb;

        $sql = $wpdb->prepare(
            'SELECT transaction_id FROM ' . $wpdb->prefix . 'wpuf_transaction
            WHERE user_id = %d AND pack_id = %d LIMIT 1', $user_id, $pack_id
        );
        $result = $wpdb->get_row( $sql );

        $transaction_id = $result ? $result->transaction_id : 0;

        $wpdb->update(
            $wpdb->prefix . 'wpuf_subscribers', [ 'subscribtion_status' => 'cancel' ], [
                'user_id' => $user_id,
                'subscribtion_id' => $pack_id,
                'transaction_id' => $transaction_id,
            ]
        );
    }

    /**
     * Handle subscription cancel request from the user
     *
     * @return WPUF_Subscription
     */
    public function user_subscription_cancel() {
        if ( isset( $_POST['wpuf_cancel_subscription'] ) ) {
            $nonce       = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';
            $user_id     = isset( $_POST['user_id'] ) ? intval( wp_unslash( $_POST['user_id'] ) ) : 0;
            $gateway     = isset( $_POST['gateway'] ) ? sanitize_text_field( wp_unslash( $_POST['gateway'] ) ) : 0;
            $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

            if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf-sub-cancel' ) ) {
                return;
            }

            $current_pack = self::get_user_pack( $user_id );

            $gateway = ( $gateway === 'bank/manual' ) ? 'bank' : $gateway;

            if ( 'bank' === $gateway ) {
                $this->update_user_subscription_meta( $user_id, 'Cancel' );
            } else {
                do_action( "wpuf_cancel_subscription_{$gateway}", $_POST );
            }

            $this::subscriber_cancel( $user_id, $current_pack['pack_id'] );

            wp_redirect( $request_uri );
            exit;
        }
    }

    public static function init() {
        if ( ! self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Redirect a user to subscription page after signup
     *
     * @since 2.2
     *
     * @param array $response
     * @param int   $user_id
     * @param array $userdata
     * @param int   $form_id
     * @param array $form_settings
     *
     * @return array
     */
    public function subs_redirect_pram( $response, $user_id, $userdata, $form_id, $form_settings ) {
        if ( ! isset( $_POST['_wpnonce'] ) || ! isset( $_POST['action'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'wpuf_form_add' ) ) {
            return;
        }

        $wpuf_sub = isset( $_POST['wpuf_sub'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_sub'] ) ) : '';
        $pack_id = isset( $_POST['pack_id'] ) ? sanitize_text_field( wp_unslash( $_POST['pack_id'] ) ) : '';

        if ( $wpuf_sub !== 'yes' ) {
            return $response;
        }

        if ( empty( $pack_id ) ) {
            return $response;
        }

        $pack           = $this->get_subscription( $pack_id );
        $billing_amount = ( $pack->meta_value['billing_amount'] >= 0 && ! empty( $pack->meta_value['billing_amount'] ) ) ? $pack->meta_value['billing_amount'] : false;

        if ( $billing_amount !== false ) {
            $pay_page = intval( wpuf_get_option( 'payment_page', 'wpuf_payment' ) );
            $redirect = add_query_arg(
                [
                    'action'  => 'wpuf_pay',
                    'user_id' => $user_id,
                    'type'    => 'pack',
                    'pack_id' => (int) $pack_id,
                ], get_permalink( $pay_page )
            );

            $response['redirect_to']  = $redirect;
            $response['show_message'] = false;
        }

        return $response;
    }

    /**
     * Insert hidden field on the register form based on selected package
     *
     * @since 2.2
     *
     * @return void
     */
    public function register_form() {
        $type    = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';
        $pack_id = isset( $_GET['pack_id'] ) ? intval( wp_unslash( $_GET['pack_id'] ) ) : 0;

        if ( $type !== 'wpuf_sub' ) {
            return;
        }

        if ( empty( $pack_id ) ) {
            return;
        }
        ?>
        <input type="hidden" name="wpuf_sub" value="yes" />
        <input type="hidden" name="pack_id"  value="<?php echo esc_attr( $pack_id ); ?>" />

        <?php
    }

    /**
     * Redirect to payment page or add free subscription after user registration
     *
     * @since 2.2
     *
     * @param int $user_id
     *
     * @return void
     */
    public function after_registration( $user_id ) {
        if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( '_wpnonce' ) ) {
            return;
        }

        $wpuf_sub = isset( $_POST['wpuf_sub'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_sub'] ) ) : '';
        $pack_id  = isset( $_POST['pack_id'] ) ? intval( wp_unslash( $_POST['pack_id'] ) ) : 0;

        if ( $wpuf_sub !== 'yes' ) {
            return $user_id;
        }

        if ( empty( $pack_id ) ) {
            return $user_id;
        }

        $pack           = $this->get_subscription( $pack_id );
        $billing_amount = ( $pack->meta_value['billing_amount'] >= 0 && ! empty( $pack->meta_value['billing_amount'] ) ) ? $pack->meta_value['billing_amount'] : false;

        if ( $billing_amount === false ) {
            wpuf_get_user( $user_id )->subscription()->add_pack( $pack_id, null, false, 'free' );
            wpuf_get_user( $user_id )->subscription()->add_free_pack( $user_id, $pack_id );
        } else {
            $pay_page = intval( wpuf_get_option( 'payment_page', 'wpuf_payment' ) );
            $redirect = add_query_arg(
                [
                    'action'  => 'wpuf_pay',
                    'type'    => 'pack',
                    'pack_id' => (int) $pack_id,
                ], get_permalink( $pay_page )
            );
        }
    }

    /**
     * Enqueue scripts and styles
     *
     * @since 2.2
     */
    public function subscription_script() {
        wp_enqueue_script( 'wpuf-subscriptions', WPUF_ASSET_URI . '/js/subscriptions.js', [ 'jquery' ], WPUF_VERSION, true );
        wp_localize_script(
            'wpuf-subscriptions', 'wpuf_subs_vars', array(
                'wpuf_subscription_delete_nonce' => wp_create_nonce( 'wpuf-subscription-delete-nonce' ),
            )
        );
    }

    /**
     * Get all subscription packs
     *
     * @return array
     */
    public function get_subscriptions( $args = [] ) {
        $defaults = [
            'post_type'      => 'wpuf_subscription',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];

        $args  = wp_parse_args( $args, $defaults );
        $posts = get_posts( $args );

        if ( $posts ) {
            foreach ( $posts as $key => $post ) {
                $post->meta_value = $this->get_subscription_meta( $post->ID, $posts );
            }
        }

        return $posts;
    }

    /**
     * Set meta fields on a subscription pack
     *
     * @since 2.2
     *
     * @param int      $subscription_id
     * @param \WP_Post $pack_post
     *
     * @return array
     */
    public static function get_subscription_meta( $subscription_id, $pack_post = null ) {
        $meta['post_content']               = isset( $pack_post->post_content ) ? $pack_post->post_content : '';
        $meta['post_title']                 = isset( $pack_post->post_title ) ? $pack_post->post_title : '';
        $meta['billing_amount']             = get_post_meta( $subscription_id, '_billing_amount', true );
        $meta['expiration_number']          = get_post_meta( $subscription_id, '_expiration_number', true );
        $meta['expiration_period']          = get_post_meta( $subscription_id, '_expiration_period', true );
        $meta['recurring_pay']              = get_post_meta( $subscription_id, '_recurring_pay', true );
        $meta['billing_cycle_number']       = get_post_meta( $subscription_id, '_billing_cycle_number', true );
        $meta['cycle_period']               = get_post_meta( $subscription_id, '_cycle_period', true );
        $meta['billing_limit']              = get_post_meta( $subscription_id, '_billing_limit', true );
        $meta['trial_status']               = get_post_meta( $subscription_id, '_trial_status', true );
        $meta['trial_duration']             = get_post_meta( $subscription_id, '_trial_duration', true );
        $meta['trial_duration_type']        = get_post_meta( $subscription_id, '_trial_duration_type', true );
        $meta['post_type_name']             = get_post_meta( $subscription_id, '_post_type_name', true );
        $meta['_enable_post_expiration']    = get_post_meta( $subscription_id, '_enable_post_expiration', true );
        $meta['_post_expiration_time']      = get_post_meta( $subscription_id, '_post_expiration_time', true );
        $meta['_expired_post_status']       = get_post_meta( $subscription_id, '_expired_post_status', true );
        $meta['_enable_mail_after_expired'] = get_post_meta( $subscription_id, '_enable_mail_after_expired', true );
        $meta['_post_expiration_message']   = get_post_meta( $subscription_id, '_post_expiration_message', true );
        $meta['_total_feature_item']        = get_post_meta( $subscription_id, '_total_feature_item', true );
        $meta['_remove_feature_item']       = get_post_meta( $subscription_id, '_remove_feature_item', true );

        $meta = apply_filters( 'wpuf_get_subscription_meta', $meta, $subscription_id );

        return $meta;
    }

    /**
     * Get all post types
     *
     * @since 2.2
     *
     * @return array
     */
    public function get_all_post_type() {
        $post_types = get_post_types();

        unset(
            $post_types['attachment'],
            $post_types['revision'],
            $post_types['nav_menu_item'],
            $post_types['wpuf_forms'],
            $post_types['wpuf_profile'],
            $post_types['wpuf_subscription'],
            $post_types['wpuf_coupon'],
            $post_types['wpuf_input'],
            $post_types['custom_css'],
            $post_types['customize_changeset'],
            $post_types['oembed_cache']
        );

        return apply_filters( 'wpuf_posts_type', $post_types );
    }

    /**
     * Post type name placeholder text
     *
     * @param string $title
     *
     * @return string
     */
    public function change_default_title( $title ) {
        $screen = get_current_screen();

        if ( 'wpuf_subscription' === $screen->post_type ) {
            $title = __( 'Pack Name', 'wp-user-frontend' );
        }

        return $title;
    }

    /**
     * Save form data
     *
     * @param int      $post_ID
     * @param \WP_Post $post
     *
     * @return void
     */
    public function save_form_meta( $subscription_id, $post ) {
        $nonce = isset( $_POST['meta_box_nonce'] ) ? sanitize_key( wp_unslash( $_POST['meta_box_nonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'subs_meta_box_nonce' ) ) {
            return;
        }

        // Is the user allowed to edit the post or page?
        if ( ! current_user_can( 'edit_post', $post->ID ) ) {
            return;
        }

        $post_data = wp_unslash( $_POST );

        if ( ! isset( $post_data['billing_amount'] ) ) {
            return;
        }

        update_post_meta( $subscription_id, '_billing_amount', $post_data['billing_amount'] );
        update_post_meta( $subscription_id, '_expiration_number', $post_data['expiration_number'] );
        update_post_meta( $subscription_id, '_expiration_period', $post_data['expiration_period'] );
        update_post_meta( $subscription_id, '_recurring_pay', isset( $post_data['recurring_pay'] ) ? $post_data['recurring_pay'] : 'no' );
        update_post_meta( $subscription_id, '_billing_cycle_number', $post_data['billing_cycle_number'] );
        update_post_meta( $subscription_id, '_cycle_period', $post_data['cycle_period'] );
        update_post_meta( $subscription_id, '_billing_limit', $post_data['billing_limit'] );
        update_post_meta( $subscription_id, '_trial_status', isset( $post_data['trial_status'] ) ? $post_data['trial_status'] : 'no' );
        update_post_meta( $subscription_id, '_trial_duration', $post_data['trial_duration'] );
        update_post_meta( $subscription_id, '_trial_duration_type', $post_data['trial_duration_type'] );
        update_post_meta( $subscription_id, '_post_type_name', $post_data['post_type_name'] );
        update_post_meta( $subscription_id, '_enable_post_expiration', ( isset( $post_data['post_expiration_settings']['enable_post_expiration'] ) ? $post_data['post_expiration_settings']['enable_post_expiration'] : '' ) );
        update_post_meta( $subscription_id, '_post_expiration_time', $post_data['post_expiration_settings']['expiration_time_value'] . ' ' . $post_data['post_expiration_settings']['expiration_time_type'] );
        update_post_meta( $subscription_id, '_expired_post_status', ( isset( $post_data['post_expiration_settings']['expired_post_status'] ) ? $post_data['post_expiration_settings']['expired_post_status'] : '' ) );
        update_post_meta( $subscription_id, '_enable_mail_after_expired', ( isset( $post_data['post_expiration_settings']['enable_mail_after_expired'] ) ? $post_data['post_expiration_settings']['enable_mail_after_expired'] : '' ) );
        update_post_meta( $subscription_id, '_post_expiration_message', ( isset( $post_data['post_expiration_settings']['post_expiration_message'] ) ? $post_data['post_expiration_settings']['post_expiration_message'] : '' ) );
        update_post_meta( $subscription_id, '_total_feature_item', ( isset( $post_data['total_feature_item'] ) ? $post_data['total_feature_item'] : '' ) );
        update_post_meta( $subscription_id, '_remove_feature_item', ( isset( $post_data['remove_feature_item'] ) ? $post_data['remove_feature_item'] : '' ) );
        do_action( 'wpuf_update_subscription_pack', $subscription_id, $post_data );
    }

    /**
     * Subscription post types
     *
     * @return void
     */
    public function register_post_type() {
        $capability = wpuf_admin_role();

        register_post_type(
            'wpuf_subscription', [
                'label'           => __( 'Subscription', 'wp-user-frontend' ),
                'public'          => false,
                'show_ui'         => true,
                'show_in_menu'    => false,
                'hierarchical'    => false,
                'query_var'       => false,
                'supports'        => [ 'title' ],
                'capability_type' => 'post',
                'capabilities'    => [
                    'publish_posts'       => $capability,
                    'edit_posts'          => $capability,
                    'edit_others_posts'   => $capability,
                    'delete_posts'        => $capability,
                    'delete_others_posts' => $capability,
                    'read_private_posts'  => $capability,
                    'edit_post'           => $capability,
                    'delete_post'         => $capability,
                    'read_post'           => $capability,
                ],
                'labels' => [
                    'name'               => __( 'Subscription', 'wp-user-frontend' ),
                    'singular_name'      => __( 'Subscription', 'wp-user-frontend' ),
                    'menu_name'          => __( 'Subscription', 'wp-user-frontend' ),
                    'add_new'            => __( 'Add Subscription', 'wp-user-frontend' ),
                    'add_new_item'       => __( 'Add New Subscription', 'wp-user-frontend' ),
                    'edit'               => __( 'Edit', 'wp-user-frontend' ),
                    'edit_item'          => __( 'Edit Subscription', 'wp-user-frontend' ),
                    'new_item'           => __( 'New Subscription', 'wp-user-frontend' ),
                    'view'               => __( 'View Subscription', 'wp-user-frontend' ),
                    'view_item'          => __( 'View Subscription', 'wp-user-frontend' ),
                    'search_items'       => __( 'Search Subscription', 'wp-user-frontend' ),
                    'not_found'          => __( 'No Subscription Found', 'wp-user-frontend' ),
                    'not_found_in_trash' => __( 'No Subscription Found in Trash', 'wp-user-frontend' ),
                    'parent'             => __( 'Parent Subscription', 'wp-user-frontend' ),
                ],
            ]
        );
    }


    /**
     * Get a subscription row from database
     *
     * @global object $wpdb
     *
     * @param int $sub_id subscription pack id
     *
     * @return object|bool
     */
    public static function get_subscription( $sub_id ) {
        $pack = get_post( $sub_id );

        if ( ! $pack ) {
            return false;
        }

        $pack->meta_value = self::get_subscription_meta( $sub_id, $pack );

        return $pack;
    }

    /**
     * Set the new post status if charging is active
     *
     * @param string $postdata
     *
     * @return string
     */
    public function set_pending( $postdata, $form_id, $form_settings, $form_vars ) {
        $form             = new WPUF_Form( $form_id );
        $payment_options  = $form->is_charging_enabled();
        $force_pack       = $form->is_enabled_force_pack();
        $pay_per_post     = $form->is_enabled_pay_per_post();
        $fallback_cost    = $form->is_enabled_fallback_cost();
        $current_user     = wpuf_get_user();
        $current_pack     = $current_user->subscription()->current_pack();
        $has_post         = $current_user->subscription()->has_post_count( $form_settings['post_type'] );

        if ( $payment_options && $force_pack && ! is_wp_error( $current_pack ) && $fallback_cost && ! $has_post ) {
            $postdata['post_status'] = 'pending';
        }

        if ( $payment_options && ! $force_pack && ( $pay_per_post || ( $fallback_cost && ! $has_post ) ) ) {
            $postdata['post_status'] = 'pending';
        }

        return $postdata;
    }

    /**
     * Checks the posting validity after a new post
     *
     * @global object $userdata
     * @global object $wpdb
     *
     * @param int $post_id
     */
    public function monitor_new_post( $post_id, $form_id, $form_settings ) {
        global $wpdb, $userdata;
        $post = get_post( $post_id );

        // bail out if charging is not enabled
        $form = new WPUF_Form( $form_id );

        if ( ! $form->is_charging_enabled() ) {
            return;
        }

        $force_pack    = $form->is_enabled_force_pack();
        $pay_per_post  = $form->is_enabled_pay_per_post();
        $fallback_cost = $form->is_enabled_fallback_cost();
        $current_user  = wpuf_get_user();
        $current_pack  = $current_user->subscription()->current_pack();
        $has_post      = $current_user->subscription()->has_post_count( $form_settings['post_type'] );

        if ( $force_pack && ! is_wp_error( $current_pack ) && $has_post ) {
            $sub_info    = self::get_user_pack( $userdata->ID );
            $post_type   = isset( $form_settings['post_type'] ) ? $form_settings['post_type'] : 'post';
            $count       = isset( $sub_info['posts'][ $post_type ] ) ? intval( $sub_info['posts'][ $post_type ] ) : 0;
            $post_status = isset( $form_settings['post_status'] ) ? $form_settings['post_status'] : 'publish';
            $featured_count = ! empty( $sub_info['total_feature_item'] ) ? intval( $sub_info['total_feature_item'] ) : 0;

            $old_status = $post->post_status;
            wp_transition_post_status( $post_status, $old_status, $post );

            // decrease the post count, if not unlimited
            $wpuf_post_status = get_post_meta( $post_id, 'wpuf_post_status', true );

            if ( $wpuf_post_status !== 'new_draft' ) {
                if ( $count > 0 ) {
                    $sub_info['posts'][ $post_type ] = $count - 1;
                }

                $user_subscription = new WPUF_User_Subscription( $current_user );
                $sub_info          = $user_subscription->handle_featured_item( $post_id, $sub_info );
                $this->update_user_subscription_meta( $userdata->ID, $sub_info );
            }

            //meta added to make post have flag if post is published
            update_post_meta( $post_id, 'wpuf_post_status', 'published' );
        } elseif ( $pay_per_post || ( $force_pack && $fallback_cost && ! $has_post ) ) {
            //there is some error and it needs payment
            //add a uniqid to track the post easily
            $order_id = uniqid( rand( 10, 1000 ), false );
            update_post_meta( $post_id, '_wpuf_order_id', $order_id, true );
            update_post_meta( $post_id, '_wpuf_payment_status', 'pending' );
        }
    }

    /**
     * Check if the post is draft and charging is enabled
     *
     * @global object $userdata
     * @global object $wpdb
     *
     * @param int $post_id
     */
    public function monitor_new_draft_post( $post_id, $form_id, $form_settings ) {
        global $wpdb, $userdata;

        // bail out if charging is not enabled
        $charging_enabled = '';
        $form             = new WPUF_Form( $form_id );
        $payment_options  = $form->is_charging_enabled();

        if ( ! $payment_options || ! is_user_logged_in() ) {
            $charging_enabled = 'no';
        } else {
            $charging_enabled = 'yes';
        }
        //phpcs:ignore
        $userdata = get_userdata( get_current_user_id() );
        $order_id = uniqid( rand( 10, 1000 ), false );

        if ( self::has_user_error( $form_settings ) ) {
            update_post_meta( $post_id, '_wpuf_order_id', $order_id, true );
        }

        if ( $form->is_enabled_pay_per_post() || ( $form->is_enabled_force_pack() && $form->is_enabled_fallback_cost() && ! wpuf_get_user()->subscription()->has_post_count( $form_settings['post_type'] ) ) ) {
            update_post_meta( $post_id, '_wpuf_order_id', $order_id, true );
            update_post_meta( $post_id, '_wpuf_payment_status', 'pending' );
        }
    }

    /**
     * Redirect to payment page after new post
     *
     * @param string $str
     * @param type   $post_id
     *
     * @return string
     */
    public function post_redirect( $response, $post_id, $form_id, $form_settings ) {
        $form             = new WPUF_Form( $form_id );
        $payment_options  = $form->is_charging_enabled();
        $force_pack       = $form->is_enabled_force_pack();
        $fallback_cost    = $form->is_enabled_fallback_cost();
        $current_user     = wpuf_get_user();
        $current_pack     = $current_user->subscription()->current_pack();
        $has_pack         = $current_user->subscription()->has_post_count( $form_settings['post_type'] );
        $ppp_cost_enabled = $form->is_enabled_pay_per_post();
        $sub_expired      = $current_user->subscription()->expired();

        if ( ( $payment_options && ! $has_pack ) || ( $payment_options && $sub_expired ) ) {
            $order_id = get_post_meta( $post_id, '_wpuf_order_id', true );

            // check if there is a order ID
            if ( $order_id || ( $payment_options && $fallback_cost ) ) {
                $response['show_message'] = false;
                $response['redirect_to']  = add_query_arg(
                    [
                        'action'  => 'wpuf_pay',
                        'type'    => 'post',
                        'post_id' => $post_id,
                    ], get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) )
                );
            }

            if ( ! $force_pack && $ppp_cost_enabled ) {
                $response['show_message'] = false;
                $response['redirect_to']  = add_query_arg(
                    [
                        'action'  => 'wpuf_pay',
                        'type'    => 'post',
                        'post_id' => $post_id,
                    ], get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) )
                );
            }
        }

        return $response;
    }

    /**
     * Perform actions when a new payment is made
     *
     * @param array $info payment info
     */
    public function payment_received( $info, $recurring ) {
        if ( $info['post_id'] ) {
            $order_id = get_post_meta( $info['post_id'], '_wpuf_order_id', true );

            $this->handle_post_publish( $order_id );
        } elseif ( $info['pack_id'] ) {
            if ( $recurring ) {
                $profile_id = $info['profile_id'];
            } else {
                $profile_id = isset( $info['user_id'] ) ? $info['user_id'] : null;
            }

            wpuf_get_user( $info['user_id'] )->subscription()->add_pack( $info['pack_id'], $profile_id, $recurring, $info['status'] );

            if ( false === $recurring ) {
                update_user_meta( $profile_id, 'wpuf_pre_sub_exp', '' );
                update_user_meta( $profile_id, 'wpuf_post_sub_exp', '' );
            }
        }
    }

    /**
     * Store new subscription info on user profile
     *
     * If data = 0, means 'unlimited'
     *
     * @param int $user_id
     * @param int $pack_id subscription pack id
     */
    public function new_subscription( $user_id, $pack_id, $profile_id = null, $recurring, $status = null ) {
        // _deprecated_function( __FUNCTION__, '2.6.0', 'wpuf_get_user( $user_id )->subscription()->add_pack( $pack_id, $profile_id = null, $recurring, $status = null );' );

        wpuf_get_user( $user_id )->subscription()->add_pack( $pack_id, $profile_id = null, $recurring, $status = null );
    }

    /**
     * Update user meta
     *
     * If data = 0, means 'unlimited'
     *
     * @param int   $user_id
     * @param array $data
     */
    public static function update_user_subscription_meta( $user_id, $user_meta ) {
        // _deprecated_function( __FUNCTION__, '2.6.0', 'wpuf_get_user( $user_id )->subscription()->update_meta( $user_meta );' );

        wpuf_get_user( $user_id )->subscription()->update_meta( $user_meta );
    }

    public static function post_by_orderid( $order_id ) {
        global $wpdb;

        //$post = get_post( $post_id );
        $sql = $wpdb->prepare(
            "SELECT p.ID, p.post_status
            FROM $wpdb->posts p, $wpdb->postmeta m
            WHERE p.ID = m.post_id AND p.post_status <> 'publish' AND m.meta_key = '_wpuf_order_id' AND m.meta_value = %s", $order_id
        );

        return $wpdb->get_row( $sql );
    }

    /**
     * Publish the post if payment is made
     *
     * @param int $post_id
     */
    public function handle_post_publish( $order_id ) {
        $post = self::post_by_orderid( $order_id );

        if ( $post ) {
            // set the payment status
            update_post_meta( $post->ID, '_wpuf_payment_status', 'completed' );

            if ( $post->post_status !== 'publish' ) {
                $this->set_post_status( $post->ID );
            }
        }
    }

    /**
     * Maintain post status from the form settings
     *
     * @since 2.1.9
     *
     * @param int $post_id
     */
    public function set_post_status( $post_id ) {
        $post_status = 'publish';
        $form_id     = get_post_meta( $post_id, '_wpuf_form_id', true );

        if ( $form_id ) {
            $form_settings = wpuf_get_form_settings( $form_id );
            $post_status   = $form_settings['post_status'];
        }

        $update_post = [
            'ID'          => $post_id,
            'post_status' => $post_status,
        ];

        wp_update_post( $update_post );
    }

    /**
     * Generate users subscription info with a shortcode
     *
     * @global type $userdata
     */
    public function subscription_info() {
        // _deprecated_function( __FUNCTION__, '2.6.0', 'wpuf_get_user()->subscription()->pack_info( $form_id );' );
        // wpuf_get_user()->subscription()->pack_info( $form_id );
        $sections = wpuf_get_account_sections();
        do_action( 'wpuf_account_content_subscription', $sections, 'subscription' );
    }

    /**
     * Show the subscription packs that are built
     * from admin Panel
     */
    public function subscription_packs( $atts = null ) {
        //$cost_per_post = isset( $form_settings['pay_per_post_cost'] ) ? $form_settings['pay_per_post_cost'] : 0;

        $action   = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
        $pack_msg = isset( $_GET['pack_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['pack_msg'] ) ) : '';
        $ppp_msg  = isset( $_GET['ppp_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['ppp_msg'] ) ) : '';

        $defaults = [
            'include' => '',
            'exclude' => '',
            'order'   => '',
            'orderby' => '',
        ];

        $arranged = [];
        $args     = wp_parse_args( $atts, $defaults );

        if ( $args['include'] !== '' ) {
            $pack_order = explode( ',', $args['include'] );
        } else {
            $args['order'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
        }

        $packs = $this->get_subscriptions( $args );

        $details_meta = $this->get_details_meta_value();

        ob_start();

        if ( $action === 'wpuf_paypal_success' ) {
            printf( '<h1>%1$s</h1><p>%2$s</p>', esc_html( __( 'Payment is complete', 'wp-user-frontend' ) ), esc_html( __( 'Congratulations, your payment has been completed!', 'wp-user-frontend' ) ) );
        }

        if ( $pack_msg === 'buy_pack' ) {
            esc_html_e( 'Please buy a subscription pack to post', 'wp-user-frontend' );
        }

        if ( $ppp_msg === 'pay_per_post' ) {
            esc_html_e( 'Please buy a subscription pack to post', 'wp-user-frontend' );
        }

        $current_pack = self::get_user_pack( get_current_user_id() );

        if (
            isset( $current_pack['pack_id'] ) &&
            ! empty( $current_pack['pack_id'] ) &&
            isset( $current_pack['status'] ) &&
            $current_pack['status'] === 'completed'
         ) {
            global $wpdb;

            $user_id         = get_current_user_id();
            $payment_gateway = $wpdb->get_var( $wpdb->prepare( "SELECT payment_type FROM {$wpdb->prefix}wpuf_transaction WHERE user_id = %s AND status = 'completed' ORDER BY created DESC", $user_id ) );

            $payment_gateway = strtolower( $payment_gateway );
            ?>

            <?php echo wp_kses_post( __( '<p><i>You have a subscription pack activated. </i></p>', 'wp-user-frontend' ) ); ?>
            <?php /* translators: %s: pack title */ ?>
            <?php echo sprintf( wp_kses_post( __( '<p><i>Pack name: %s </i></p>', 'wp-user-frontend' ) ), esc_html( get_the_title( $current_pack['pack_id'] ) ) ); ?>

            <?php echo '<p><i>' . esc_html__( 'To cancel the pack, press the following cancel button', 'wp-user-frontend' ) . '</i></p>'; ?>

            <form action="" id="wpuf_cancel_subscription" method="post">
                <?php wp_nonce_field( 'wpuf-sub-cancel' ); ?>
                <input type="hidden" name="user_id" value="<?php echo esc_attr( get_current_user_id() ); ?>">
                <input type="hidden" name="gateway" value="<?php echo esc_attr( $payment_gateway ); ?>">
                <input type="hidden" name="wpuf_cancel_subscription" value="Cancel">
                <input type="submit" name="wpuf_user_subscription_cancel" class="btn btn-sm btn-danger" value="<?php esc_html_e( 'Cancel', 'wp-user-frontend' ); ?>">
            </form>
            <?php
        }

        wpuf_load_template(
            'subscriptions/listing.php', apply_filters(
                'wpuf_subscription_listing_args', [
                    'subscription' => $this,
                    'args'         => $args,
                    'packs'        => $packs,
                    'pack_order'   => isset( $pack_order ) ? $pack_order : '',
                    'details_meta' => $details_meta,
                    'current_pack' => $current_pack,
                ]
            )
        );

        $contents = ob_get_clean();

        return apply_filters( 'wpuf_subscription_packs', $contents, $packs );
    }

    public function get_details_meta_value() {
        $meta['payment_page'] = get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) );
        $meta['onclick']      = '';
        $meta['symbol']       = wpuf_get_currency( 'symbol' );

        return $meta;
    }

    /**
     * Get cycle label
     *
     *@since 2.8.10
     *
     *@return string $labels[$cycle_period]
     */
    public static function get_cycle_label( $cycle_period, $cycle_number ) {
        $labels = [
            'day'   => _n( 'Day', 'Days', $cycle_number, 'wp-user-frontend' ),
            'week'  => _n( 'Week', 'Weeks', $cycle_number, 'wp-user-frontend' ),
            'month' => _n( 'Month', 'Months', $cycle_number, 'wp-user-frontend' ),
            'year'  => _n( 'Year', 'Years', $cycle_number, 'wp-user-frontend' ),
        ];

        return apply_filters( 'wpuf_subscription_cycle_label', $labels[ $cycle_period ] );
    }

    /**
     * Render Subscription Pack details
     *
     * @param $pack
     * @param $details_meta
     * @param string $current_pack_id
     * @param bool   $coupon_status
     */
    public function pack_details( $pack, $details_meta, $current_pack_id = '', $coupon_status = false ) {
        if ( function_exists( 'wpuf_prices_include_tax' ) ) {
            $price_with_tax = wpuf_prices_include_tax();
        }

        $user_id = get_current_user_id();

        if ( $user_id !== 0 ) {
            $user_pack_meta      = '_wpuf_subscription_pack';
            $pack_details        = get_user_meta( $user_id, $user_pack_meta, true );

            if ( ! empty( $pack_details ) ) {
                $current_pack_status = isset( $pack_details['status'] ) ? $pack_details['status'] : '';
            }
        }

        $billing_amount = ( $pack->meta_value['billing_amount'] >= 0 && ! empty( $pack->meta_value['billing_amount'] ) ) ? $pack->meta_value['billing_amount'] : '0.00';
        $trial_des      = '';
        $recurring_des  = '<div class="wpuf-pack-cycle wpuf-nullamount-hide">' . __( 'One time payment', 'wp-user-frontend' ) . '</div>';

        if ( isset( $price_with_tax ) && $price_with_tax ) {
            $billing_amount = apply_filters( 'wpuf_payment_amount', $billing_amount );
        }

        if ( $billing_amount && $pack->meta_value['recurring_pay'] === 'yes' ) {
            $recurring_des = sprintf( __( 'Every', 'wp-user-frontend' ) . ' %s %s', $pack->meta_value['billing_cycle_number'], self::get_cycle_label( $pack->meta_value['cycle_period'], $pack->meta_value['billing_cycle_number'] ), $pack->meta_value['trial_duration_type'] );
            $recurring_des .= ! empty( $pack->meta_value['billing_limit'] ) ? sprintf( ', ' . __( 'for', 'wp-user-frontend' ) . ' %s ' . __( 'installments', 'wp-user-frontend' ), $pack->meta_value['billing_limit'] ) : '';
            $recurring_des = '<div class="wpuf-pack-cycle wpuf-nullamount-hide">' . $recurring_des . '</div>';
        }

        if ( $billing_amount && $pack->meta_value['recurring_pay'] === 'yes' && $pack->meta_value['trial_status'] === 'yes' ) {
            //phpcs:ignore
            $duration = _n( $pack->meta_value['trial_duration_type'], $pack->meta_value['trial_duration_type'] . 's', $pack->meta_value['trial_duration'], 'wp-user-frontend' );
            /* translators: %s: trial days */
            $trial_des = sprintf( __( 'Trial available for first %1$s %2$s', 'wp-user-frontend' ), $pack->meta_value['trial_duration'], $duration );
        }

        $label       = wpuf_get_option( 'logged_in_label', 'wpuf_subscription_settings', false );
        $button_name = $label ? $label : __( 'Buy Now', 'wp-user-frontend' );

        if ( ! is_user_logged_in() ) {
            $label       = wpuf_get_option( 'logged_out_label', 'wpuf_subscription_settings', false );
            $button_name = $label ? $label : __( 'Sign Up', 'wp-user-frontend' );
        } elseif ( $billing_amount === '0.00' ) {
            $label       = wpuf_get_option( 'free_label', 'wpuf_subscription_settings', false );
            $button_name = $label ? $label : __( 'Free', 'wp-user-frontend' );
        }

        $query_args = [
            'action'  => 'register',
            'type'    => 'wpuf_sub',
            'pack_id' => $pack->ID,
        ];
        $query_url = wp_registration_url();

        if ( $coupon_status === false && is_user_logged_in() ) {
            $query_args = [
                'action'  => 'wpuf_pay',
                'type'    => 'pack',
                'pack_id' => $pack->ID,
            ];
            $query_url = $details_meta['payment_page'];
        }

        wpuf_load_template(
            'subscriptions/pack-details.php', apply_filters(
                'wpuf_subscription_pack_details_args', [
                    'pack'                  => $pack,
                    'billing_amount'        => $billing_amount,
                    'details_meta'          => $details_meta,
                    'recurring_des'         => $recurring_des,
                    'trial_des'             => $trial_des,
                    'coupon_status'         => $coupon_status,
                    'current_pack_id'       => $current_pack_id,
                    'current_pack_status'   => isset( $current_pack_status ) ? $current_pack_status : '',
                    'button_name'           => $button_name,
                    'query_args'            => $query_args,
                    'query_url'             => $query_url,
                ]
            )
        );
    }

    /**
     * Show a info message when posting if payment is enabled
     */
    public function add_post_info( $form_id, $form_settings ) {
        $form              = new WPUF_Form( $form_id );
        $pay_per_post      = $form->is_enabled_pay_per_post();
        $pay_per_post_cost = (float) $form->get_pay_per_post_cost();
        $force_pack        = $form->is_enabled_force_pack();
        $current_user      = wpuf_get_user();
        $current_pack      = $current_user->subscription()->current_pack();
        $payment_enabled   = $form->is_charging_enabled();

        if ( function_exists( 'wpuf_prices_include_tax' ) ) {
            $price_with_tax = wpuf_prices_include_tax();
        }

        if ( self::has_user_error( $form_settings ) || ( $payment_enabled && $pay_per_post && ! $force_pack ) ) {
            ?>
            <div class="wpuf-info">
                <?php
                $form              = new WPUF_Form( $form_id );
                $pay_per_post_cost = (float) $form->get_pay_per_post_cost();

                if ( isset( $price_with_tax ) && $price_with_tax ) {
                    $pay_per_post_cost = apply_filters( 'wpuf_payment_amount', $pay_per_post_cost );
                }
                /* translators: %s: amount */
                $text = sprintf( __( 'There is a <strong>%s</strong> charge to add a new post.', 'wp-user-frontend' ), wpuf_format_price( $pay_per_post_cost ) );

                echo wp_kses_post( apply_filters( 'wpuf_ppp_notice', $text, $form_id, $form_settings ) );
                ?>
            </div>
            <?php
        } elseif ( self::has_user_error( $form_settings ) || ( $payment_enabled && $force_pack && ! is_wp_error( $current_pack ) && ! $current_user->subscription()->has_post_count( $form_settings['post_type'] ) ) ) {
            ?>
            <div class="wpuf-info">
                <?php
                $form          = new WPUF_Form( $form_id );
                $fallback_cost = (int) $form->get_subs_fallback_cost();

                if ( isset( $price_with_tax ) && $price_with_tax ) {
                    $fallback_cost = apply_filters( 'wpuf_payment_amount', $fallback_cost );
                }
                /* translators: %s: amount */
                $text = sprintf( __( 'Your Subscription pack is exhausted. There is a <strong>%s</strong> charge to add a new post.', 'wp-user-frontend' ), wpuf_format_price( $fallback_cost ) );

                echo wp_kses_post( apply_filters( 'wpuf_ppp_notice', wp_kses_post( $text ), esc_html( $form_id ), $form_settings ) );
                ?>
            </div>
            <?php
        }
    }

    public static function get_user_pack( $user_id, $status = true ) {
        return get_user_meta( $user_id, '_wpuf_subscription_pack', $status );
    }

    public function subscription_pack_users( $pack_id = '', $status = '' ) {
        global $wpdb;
        $sql  = 'SELECT user_id FROM ' . $wpdb->prefix . 'wpuf_subscribers';
        $sql .= $pack_id ? ' WHERE subscribtion_id  = ' . $pack_id : '';
        $sql .= $status ? ' AND subscribtion_status = ' . $status : '';

        $rows = $wpdb->get_results( $sql );

        if ( empty( $rows ) ) {
            return $rows;
        }

        $results = [];

        foreach ( $rows as $row ) {
            if ( ! in_array( (int) $row->user_id, $results, true ) ) {
                $results[] = $row->user_id;
            }
        }

        $users = get_users( [ 'include' => $results ] );

        return $users;
    }

    public function force_pack_notice( $text, $id, $form_settings ) {
        $form = new WPUF_Form( $id );

        $force_pack = $form->is_enabled_force_pack();

        if ( $force_pack && self::has_user_error( $form_settings ) ) {
            $pack_page = get_permalink( wpuf_get_option( 'subscription_page', 'wpuf_payment' ) );
            /* translators: %s: subscription link */
            $text = sprintf( __( 'You must <a href="%s">purchase a pack</a> before posting', 'wp-user-frontend' ), $pack_page );
        }

        return apply_filters( 'wpuf_pack_notice', $text, $id, $form_settings );
    }

    public function force_pack_permission( $perm, $id, $form_settings ) {
        $form              = new WPUF_Form( $id );
        $force_pack        = $form->is_enabled_force_pack();
        $pay_per_post      = $form->is_enabled_pay_per_post();
        $fallback_enabled  = $form->is_enabled_fallback_cost();
        $fallback_cost     = $form->get_subs_fallback_cost();

        $current_user   = wpuf_get_user();
        $current_pack   = $current_user->subscription()->current_pack();
        $has_post_count = isset( $form_settings['post_type'] ) ? $current_user->subscription()->has_post_count( $form_settings['post_type'] ) : false;

        if ( is_user_logged_in() ) {
            if ( wpuf_get_user()->post_locked() ) {
                return 'no';
            } else {

                // if post locking not enabled
                if ( ! $form->is_charging_enabled() ) {
                    return 'yes';
                } else {
                    //if charging is enabled
                    if ( $force_pack ) {
                        if ( ! is_wp_error( $current_pack ) ) {
                            // current pack has no error
                            if ( ! $fallback_enabled ) {
                                //fallback cost enabled
                                if ( ! $current_user->subscription()->current_pack_id() ) {
                                    return 'no';
                                } elseif ( $current_user->subscription()->has_post_count( $form_settings['post_type'] ) ) {
                                    return 'yes';
                                }
                            } else {
                                //fallback cost disabled
                                if ( ! $current_user->subscription()->current_pack_id() ) {
                                    return 'no';
                                } elseif ( $has_post_count ) {
                                    return 'yes';
                                } elseif ( $current_user->subscription()->current_pack_id() && ! $has_post_count ) {
                                    return 'yes';
                                }
                            }
                        } else {
                            return 'no';
                        }
                    }

                    if ( ! $force_pack && $pay_per_post ) {
                        return 'yes';
                    }
                }
            }
        }

        if ( ! is_user_logged_in() && isset( $form_settings['guest_post'] ) && $form_settings['guest_post'] === 'true' ) {
            if ( $form->is_charging_enabled() ) {
                if ( $force_pack ) {
                    return 'no';
                }

                if ( ! $force_pack && $pay_per_post ) {
                    return 'yes';
                } elseif ( ! $force_pack && ! $pay_per_post ) {
                    return 'no';
                }
            } else {
                return 'yes';
            }
        }

        return $perm;
    }

    /**
     * Checks against the user, if he is valid for posting new post
     *
     * @global object $userdata
     *
     * @return bool
     */
    public static function has_user_error( $form_settings = null ) {

        // _deprecated_function( __FUNCTION__, '2.6.0', 'wpuf_get_user()->subscription()->has_error( $form_settings = null );' );

        wpuf_get_user()->subscription()->has_error( $form_settings );
    }

    /**
     * Determine if the user has used a free pack before
     *
     * @since 2.1.8
     *
     * @param int $user_id
     * @param int $pack_id
     *
     * @return bool
     */
    public static function has_used_free_pack( $user_id, $pack_id ) {
        // _deprecated_function( __FUNCTION__, '2.6.0', 'wpuf_get_user( $user_id )->subscription()->used_free_pack( $pack_id );' );

        wpuf_get_user( $user_id )->subscription()->used_free_pack( $pack_id );
    }

    /**
     * Add a free used pack to the user account
     *
     * @since 2.1.8
     *
     * @param int $user_id
     * @param int $pack_id
     */
    public static function add_free_pack( $user_id, $pack_id ) {
        // _deprecated_function( __FUNCTION__, '2.6.0', 'wpuf_get_user( $user_id )->subscription()->add_free_pack( $pack_id );' );

        wpuf_get_user( $user_id )->subscription()->add_free_pack( $user_id, $pack_id );
    }

    public function packdropdown( $packs, $selected = '' ) {
        $packs = isset( $packs ) ? $packs : [];

        foreach ( $packs as $key => $pack ) {
            ?>
            <option value="<?php echo esc_attr( $pack->ID ); ?>" <?php selected( $selected, $pack->ID ); ?>><?php echo esc_attr( $pack->post_title ); ?></option>
            <?php
        }
    }

    /**
     * Reset the post count of a subscription of a user
     *
     * @since 2.3.11
     *
     * @param $post_id
     * @param $form_id
     * @param $form_settings
     * @param $form_vars
     */
    public function reset_user_subscription_data( $post_id, $form_id, $form_settings, $form_vars ) {
        // _deprecated_function( __FUNCTION__, '2.6.0', 'wpuf_get_user()->subscription()->reset_subscription_data( $post_id, $form_id, $form_settings, $form_vars );' );

        wpuf_get_user()->subscription()->reset_subscription_data( $post_id, $form_id, $form_settings, $form_vars );
    }

    /**
     * Returns the payment status of a post
     *
     * @since 2.5.9
     *
     * @param $post_id
     *
     * @return string
     */
    public function get_payment_status( $post_id ) {
        return get_post_meta( $post_id, '_wpuf_payment_status', true );
    }

    /**
     * Insert Free pack users to subscribers list
     *
     * @since 2.8.8
     *
     * @param $post_id
     *
     * @return void
     */
    public function insert_free_pack_subscribers( $pack_id, $userdata ) {
        global $wpdb;

        $subscription = wpuf()->subscription->get_subscription( $pack_id );

        if ( $userdata->id && $subscription ) {
            $user_sub             = self::get_user_pack( $userdata->id );
            $post_expiration_time = wpuf_date2mysql( $user_sub['expire'] );

            $table_data = [
                'user_id'             => $userdata->id,
                'name'                => $userdata->user->data->display_name,
                'subscribtion_id'     => $pack_id,
                'subscribtion_status' => 'free',
                'gateway'             => 'free',
                'transaction_id'      => 'free',
                'starts_from'         => gmdate( 'd-m-Y' ),
                'expire'              => empty( $post_expiration_time ) ? 'recurring' : $post_expiration_time,
            ];

            $wpdb->insert( $wpdb->prefix . 'wpuf_subscribers', $table_data );
        }
    }

    /**
     * Reset trials data if used once
     *
     * @since 3.5.14
     *
     * @param $sub_meta
     *
     * @return mixed
     */
    public function reset_trial( $sub_meta ) {
        $used_trial = get_user_meta( get_current_user_id(), '_wpuf_used_trial', true );
        if ( 'yes' === $used_trial ) {
            unset( $sub_meta['trial_status'] );
            unset( $sub_meta['trial_duration'] );
            unset( $sub_meta['trial_duration_type'] );
        }
        return $sub_meta;
    }

    /**
     * Add daily cron for non recur subscritpion
     *
     * @since 3.5.14
     *
     * @return void
     */
    public function handle_non_recur_subs() {
        if ( ! wp_next_scheduled( 'non_recur_subs_daily' ) ) {
            wp_schedule_event( time(), 'daily', 'non_recur_subs_daily' );
        }
    }

    /**
     * Cancel non recurring subs if expired
     *
     * @since 3.5.14
     *
     * @return void
     */
    public function cancel_non_recurring_subscription() {
        global $wpdb;

        $key = '_wpuf_subscription_pack';

        $all_subscription = $wpdb->get_results(
            $wpdb->prepare(
                "
        SELECT um.meta_value,um.user_id FROM {$wpdb->usermeta} um
        LEFT JOIN {$wpdb->users} u ON u.ID = um.user_id
        WHERE um.meta_key = %s
    ", $key
            )
        );

        $current_time  = current_time( 'mysql' );
        $non_recurrent = array_filter(
            $all_subscription, function ( $pack ) use ( $current_time ) {
                $pack = maybe_unserialize( $pack->meta_value );
                return $pack['recurring'] === 'no' && $current_time >= $pack['expire'];
            }
        );

        $remove_feature_item_by_author = [];

        foreach ( $non_recurrent as $ns ) {
            $user_id  = $ns->user_id;
            $sub_meta = 'cancel';
            $meta     = maybe_unserialize( $ns->meta_value );

            self::update_user_subscription_meta( $user_id, $sub_meta );
            // remove feature item if sub expire
            if ( ! empty( $meta['remove_feature_item'] ) && 'on' === $meta['remove_feature_item'] ) {
                array_push( $remove_feature_item_by_author, $user_id );
            }
        }

        if ( ! empty( $remove_feature_item_by_author ) ) {
            $stickies = get_option( 'sticky_posts' );

            $post_ids = get_posts(
                [
                    'author__in'  => $remove_feature_item_by_author,
                    'numberposts' => -1,
                    'post_status' => [ 'draft', 'pending', 'private', 'publish' ],
                    'fields'      => 'ids',
                ]
            );

            foreach ( $post_ids as $post_id ) {
                if ( in_array( $post_id, $stickies, true ) ) {
                    unstick_post( $post_id );
                }
            }
        }
    }
}
