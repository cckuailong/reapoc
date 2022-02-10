<?php

/**
 * User Subscription Class
 *
 * @since 2.6.0
 */
class WPUF_User_Subscription {

    /**
     * The user object
     *
     * @var \WPUF_User
     */
    private $user;

    /**
     * The current subscription package
     *
     * @var array
     */
    private $pack;

    /**
     * Constructor
     *
     * @param \WPUF_User $user
     */
    public function __construct( $user ) {
        $this->user = $user;

        $this->populate_data();
    }

    /**
     * Populate the subscription info into the class
     *
     * @return void
     */
    public function populate_data() {
        if ( ! $this->pack ) {
            $this->pack = get_user_meta( $this->user->id, '_wpuf_subscription_pack', true );
        }
    }

    /**
     * Get the current pack of the user
     *
     * @return array|WP_Error
     */
    public function current_pack() {
        $pack = $this->pack;

        if ( ! isset( $this->pack['pack_id'] ) ) {
            $pack_page = get_permalink( wpuf_get_option( 'subscription_page', 'wpuf_payment' ) );

            return new WP_Error( 'no-pack', sprintf( __( 'You must <a href="%s">purchase a subscription package</a> before posting', 'wp-user-frontend' ), $pack_page ) );
        }

        // seems like the user has a pack, now check expiration
        if ( $this->expired() ) {
            return new WP_Error( 'expired', __( 'The subscription pack has expired. Please buy a pack.', 'wp-user-frontend' ) );
        }

        return $pack;
    }

    /**
     * Check if the current pack is expired
     *
     * @return bool
     */
    public function expired() {

        // if no data found, take it as expired
        if ( ! isset( $this->pack['expire'] ) ) {
            return true;
        }

        $expire_date = isset( $this->pack['expire'] ) ? $this->pack['expire'] : 0;
        $expired     = true;

        if ( strtolower( $expire_date ) == 'unlimited' || empty( $expire_date ) ) {
            $expired = false;
        } elseif ( strtotime( date( 'Y-m-d', strtotime( $expire_date ) ) ) >= strtotime( date( 'Y-m-d', time() ) ) ) {
            $expired = false;
        } else {
            $expired = true;
        }

        return $expired;
    }

    /**
     * Check if a pack is recurring
     *
     * @return bool
     */
    public function recurring() {
        $current_pack = $this->current_pack();

        if ( is_wp_error( $current_pack ) ) {
            return false;
        }

        return 'yes' == $current_pack['recurring'];
    }

    /**
     * Check if the user has posts left on a post type
     *
     * @param string $post_type
     *
     * @return bool
     */
    public function has_post_count( $post_type ) {
        if ( isset( $this->pack['posts'] ) && isset( $this->pack['posts'][ $post_type ] ) ) {
            $count = (int) $this->pack['posts'][ $post_type ];

            if ( $count > 0 || $count === -1 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a user has a subscription pack
     *
     * @param int $pack_id
     *
     * @return bool
     */
    public function has_pack( $pack_id ) {
        return $pack_id == $this->current_pack_id();
    }

    /**
     * update user meta
     *
     * @return string
     */
    public function update_meta( $user_meta, $key = '_wpuf_subscription_pack' ) {
        update_user_meta( $this->user->id, $key, $user_meta );
    }

    /**
     * Returns the current pack ID used by the user
     *
     * @return int|false
     */
    public function current_pack_id() {
        $pack = get_user_meta( $this->user->id, '_wpuf_subscription_pack', true );

        if ( isset( $pack['pack_id'] ) ) {
            return (int) $pack['pack_id'];
        }

        return false;
    }

    /**
     * Assign a pack to the user
     *
     * @param int $pack_id
     */
    public function add_pack( $pack_id, $profile_id = null, $recurring, $status = null ) {
        global $wpdb;
        $result       = '';
        $subscription = WPUF_Subscription::init()->get_subscription( $pack_id );

        if ( $this->user->id && $subscription ) {
            $user_meta = [
                'pack_id' => $pack_id,
                'posts'   => $subscription->meta_value['post_type_name'],
                'total_feature_item' => $subscription->meta_value['_total_feature_item'],
                'remove_feature_item' => $subscription->meta_value['_remove_feature_item'],
                'status'  => $status,
            ];

            // $recurring = get_post_meta( $pack_id, '_recurring_pay', true );

            if ( $recurring ) {
                $totla_date              = date( 'd-m-Y', strtotime( '+' . $subscription->meta_value['billing_cycle_number'] . $subscription->meta_value['cycle_period'] . 's' ) );
                $user_meta['expire']     = '';
                $user_meta['profile_id'] = $profile_id;
                $user_meta['recurring']  = 'yes';
            } else {
                $period_type            = $subscription->meta_value['expiration_period'];
                $period_number          = $subscription->meta_value['expiration_number'];
                $date                   = date( 'd-m-Y', strtotime( '+' . $period_number . $period_type . 's' ) );
                $expired                = ( empty( $period_number ) || ( $period_number == 0 ) ) ? 'unlimited' : wpuf_date2mysql( $date );
                $user_meta['expire']    = $expired;
                $user_meta['recurring'] = 'no';
            }

            $user_meta = apply_filters( 'wpuf_new_subscription', $user_meta, $this->user->id, $pack_id, $recurring );

            if ( $subscription->_enable_post_expiration ) {
                $user_meta['_enable_post_expiration']    = $subscription->_enable_post_expiration;
                $user_meta['_post_expiration_time']      = $subscription->_post_expiration_time;
                $user_meta['_expired_post_status']       = $subscription->_expired_post_status;
                $user_meta['_enable_mail_after_expired'] = $subscription->_enable_mail_after_expired;
                $user_meta['_post_expiration_message']   = $subscription->_post_expiration_message;
            }

            $this->update_meta( $user_meta );

            if ( ! $this->is_free_pack( $pack_id ) ) {
                $sql = $wpdb->prepare(
                    'SELECT * FROM ' . $wpdb->prefix . 'wpuf_transaction
                WHERE user_id = %d AND pack_id = %d ORDER BY id DESC LIMIT 1', $this->user->id, $pack_id
                );

                $result = $wpdb->get_row( $sql );
            }

            if ( $result ) {
                $table_data = [
                    'user_id'               => $this->user->id,
                    'name'                  => $this->user->user->data->display_name,
                    'subscribtion_id'       => $pack_id,
                    'subscribtion_status'   => $status,
                    'gateway'               => isset( $result->payment_type ) ? $result->payment_type : 'bank',
                    'transaction_id'        => isset( $result->transaction_id ) ? $result->transaction_id : 'NA',
                    'starts_from'           => date( 'd-m-Y' ),
                    'expire'                => $user_meta['expire'] == '' ? 'recurring' : $user_meta['expire'],
                ];

                $wpdb->insert( $wpdb->prefix . 'wpuf_subscribers', $table_data );
            }

            if ( self::is_free_pack( $pack_id ) ) {
                wpuf()->subscription->insert_free_pack_subscribers( $pack_id, $this->user );
            }
        }
    }

    /**
     * Delete subscription pack from the user
     *
     * @return void
     */
    public function delete_pack() {

        // cancel is it's a recurring payment
        if ( $this->recurring() ) {
            $wpuf_paypal = new WPUF_Paypal();
            $wpuf_paypal->recurring_change_status( $this->user->id, 'Cancel' );
        }

        delete_user_meta( $this->user->id, '_wpuf_subscription_pack' );
    }

    /**
     * Determine if the user has used a free pack before
     *
     * @param int $pack_id
     *
     * @return bool
     */
    public function used_free_pack( $pack_id ) {
        $has_used = get_user_meta( $this->user->id, 'wpuf_fp_used', true );

        if ( $has_used == '' ) {
            return false;
        }

        if ( is_array( $has_used ) && isset( $has_used[ $pack_id ] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Add a free used pack to the user account
     *
     * @param int $pack_id
     */
    public function add_free_pack( $user_id, $pack_id ) {
        $has_used = get_user_meta( $this->user->id, 'wpuf_fp_used', true );
        $has_used = is_array( $has_used ) ? $has_used : [];

        $has_used[ $pack_id ] = $pack_id;

        update_user_meta( $user_id, 'wpuf_fp_used', $has_used );
    }

    public function pack_info( $form_id ) {
        $form             = new WPUF_Form( $form_id );
        $payment_options  = $form->is_charging_enabled();

        if ( ! $payment_options || ! is_user_logged_in() ) {
            return;
        }

        ob_start();

        if ( $this->current_pack_id() ) {
            return;
        }

        $pack = WPUF_Subscription::get_subscription( $this->current_pack_id() );

        $details_meta = WPUF_Subscription::init()->get_details_meta_value();

        $billing_amount = ( intval( $pack->meta_value['billing_amount'] ) > 0 ) ? $details_meta['symbol'] . $pack->meta_value['billing_amount'] : __( 'Free', 'wp-user-frontend' );

        if ( $pack->meta_value['recurring_pay'] == 'yes' ) {
            $recurring_des = sprintf( 'For each %s %s', $pack->meta_value['billing_cycle_number'], WPUF_Subscription::get_cycle_label( $pack->meta_value['cycle_period'], $pack->meta_value['billing_cycle_number'] ), $pack->meta_value['trial_duration_type'] );
            $recurring_des .= ! empty( $pack->meta_value['billing_limit'] ) ? sprintf( ', for %s installments', $pack->meta_value['billing_limit'] ) : '';
            $recurring_des = $recurring_des;
        } else {
            $recurring_des = '';
        } ?>
        <div class="wpuf_sub_info">
            <h3><?php esc_html_e( 'Subscription Details', 'wp-user-frontend' ); ?></h3>
            <div class="wpuf-text">
                <div><strong><?php esc_html_e( 'Subcription Name: ', 'wp-user-frontend' ); ?></strong><?php echo esc_html( $pack->post_title ); ?></div>
                <div>
                    <strong><?php esc_html_e( 'Package & billing details: ', 'wp-user-frontend' ); ?></strong>

                    <div class="wpuf-pricing-wrap">
                        <div class="wpuf-sub-amount">
                            <?php echo esc_html( $billing_amount ); ?>
                            <?php echo esc_html( $recurring_des ); ?>
                        </div>
                    </div>

                </div>
                <div>
                    <strong><?php esc_html_e( 'Remaining post: ', 'wp-user-frontend' ); ?></strong>
                    <?php
                    foreach ( $this->pack['posts'] as $key => $value ) {
                        $value = intval( $value );

                        if ( $value === 0 ) {
                            continue;
                        }

                        $post_type_obj = get_post_type_object( $key );

                        if ( ! $post_type_obj ) {
                            continue;
                        }
                        $value = ( $value == '-1' ) ? __( 'Unlimited', 'wp-user-frontend' ) : $value;
                        ?>
                        <div><?php echo esc_html( $post_type_obj->labels->name . ': ' . $value ); ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                if ( $this->pack['recurring'] != 'yes' ) {
                    if ( ! empty( $this->pack['expire'] ) ) {
                        $expire = ( $this->pack['expire'] == 'unlimited' ) ? ucfirst( 'unlimited' ) : wpuf_date2mysql( $this->pack['expire'] );
                        ?>
                        <div class="wpuf-expire">
                            <strong><?php esc_html_e( 'Expire date:', 'wp-user-frontend' ); ?></strong> <?php echo esc_html( wpuf_get_date( $expire ) ); ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
            if ( $this->pack['recurring'] == 'yes' ) {
                $payment_page = get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) );
                ?>
                <form action="" method="post">
                    <?php wp_nonce_field( '_wpnonce', 'wpuf_payment_cancel' ); ?>
                    <input type="hidden" name="user_id" value="<?php echo esc_attr( $this->user->id ); ?>">
                    <input type="hidden" name="action" value="wpuf_cancel_pay">
                    <input type="hidden" name="gateway" value="paypal">
                    <input type="submit" name="wpuf_payment_cancel_submit" value="cancel">
                </form>
                <?php $subscription_page = wpuf_get_option( 'subscription_page', 'wpuf_payment' ); ?>
                <a href="<?php echo esc_attr( get_permalink( $subscription_page ) ); ?>"><?php esc_html_e( 'Change', 'wp-user-frontend' ); ?></a>
                <?php
            }
            echo wp_kses_post( '</div>' );

            $content = ob_get_clean();

            return apply_filters( 'wpuf_sub_info', $content, $this->user, $this->pack, $pack );
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
    public function reset_subscription_data( $post_id, $form_id, $form_settings, $form_vars ) {
        global $userdata;

        $sub_info  = $this->pack;
        $post_type = isset( $form_settings['post_type'] ) ? $form_settings['post_type'] : 'post';
        $count     = isset( $sub_info['posts'][ $post_type ] ) ? intval( $sub_info['posts'][ $post_type ] ) : 0;

        // decrease the post count, if not umlimited
        $wpuf_post_status = get_post_meta( $post_id, 'wpuf_post_status', true );

        if ( $wpuf_post_status != 'published' && $wpuf_post_status != 'new_draft' ) {
            if ( $count > 0 ) {
                $sub_info['posts'][ $post_type ] = $count - 1;
            }

            $sub_info = $this->handle_featured_item( $post_id, $sub_info );

            $this->update_meta( $sub_info );

            update_post_meta( $post_id, 'wpuf_post_status', 'new_draft' );
        }
    }

    /**
     * Checks against the user, if he is valid for posting new post
     *
     * @global object $userdata
     *
     * @return bool
     */
    public function has_error( $form_settings = null ) {
        if ( ! $this->current_pack_id() ) {
            return false;
        }

        $user_sub_meta         = $this->pack;
        $fallback_ppp_enable   = isset( $form_settings['fallback_ppp_enable'] ) ? $form_settings['fallback_ppp_enable'] : 'false';
        $form_post_type        = isset( $form_settings['post_type'] ) ? $form_settings['post_type'] : 'post';
        $post_count            = isset( $user_sub_meta['posts'][ $form_post_type ] ) ? $user_sub_meta['posts'][ $form_post_type ] : 0;

        if ( isset( $user_sub_meta['recurring'] ) && $user_sub_meta['recurring'] == 'yes' ) {

            // user has recurring subscription
            if ( $post_count > 0 || $post_count == '-1' ) {
                return false;
            } elseif ( $post_count <= 0 && $fallback_ppp_enable == 'true' ) {
                return true;
            } else {
                return true;
            }
        } else {
            $expire = isset( $user_sub_meta['expire'] ) ? $user_sub_meta['expire'] : 0;

            if ( strtolower( $expire ) == 'unlimited' || empty( $expire ) ) {
                $expire_status = false;
            } elseif ( ( strtotime( date( 'Y-m-d', strtotime( $expire ) ) ) >= strtotime( date( 'Y-m-d', time() ) ) ) && ( $post_count > 0 || $post_count == '-1' ) ) {
                $expire_status = false;
            } else {
                $expire_status = true;
            }

            if ( $post_count > 0 || $post_count == '-1' ) {
                $post_count_status = false;
            } elseif ( $post_count <= 0 && $fallback_ppp_enable == 'true' ) {
                $post_count_status = false;
            } else {
                $post_count_status = true;
            }

            if ( $expire_status || $post_count_status ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a pack is free
     *
     * @since 2.8.0
     *
     * @param $pack_id
     *
     * @return bool
     */
    public static function is_free_pack( $pack_id ) {
        $subs           = new WPUF_Subscription();
        $pack           = $subs->get_subscription( $pack_id );
        $billing_amount = ( $pack->meta_value['billing_amount'] >= 0 && ! empty( $pack->meta_value['billing_amount'] ) ) ? $pack->meta_value['billing_amount'] : false;

        if ( $billing_amount === false ) {
            return true;
        }

        return false;
    }

    /**
     * Get Expiration Message
     *
     * @since 2.8.8
     *
     * @param $pack_id
     *
     * @return string
     */
    public function get_subscription_exp_msg( $pack_id ) {
        $sub_pack  = WPUF_Subscription::get_subscription( $pack_id );
        $sub_info  = $this->pack;

        $exp_message = ! empty( $sub_pack->meta_value['_post_expiration_message'] ) ? $sub_pack->meta_value['_post_expiration_message'] : $sub_info['_post_expiration_message'];

        return $exp_message;
    }

    /**
     * Handle feature item count for add, edit
     *
     * @param $post_id
     * @param $sub_info
     *
     * @return array
     */
    public function handle_featured_item( $post_id, $sub_info ) {
        $featured_count = ! empty( $sub_info['total_feature_item'] ) ? intval( $sub_info['total_feature_item'] ) : 0;
        $stickies       = get_option( 'sticky_posts' );
        $is_featured    = in_array( intval( $post_id ), $stickies, true );

        if ( $featured_count > 0 && array_key_exists( 'is_featured_item', $_POST ) && ! $is_featured ) {
            stick_post( $post_id );
            $sub_info['total_feature_item'] = $featured_count - 1;
        }

        if ( $featured_count > 0 && ! array_key_exists( 'is_featured_item', $_POST ) && $is_featured ) {
            $sub_info['total_feature_item'] = $featured_count + 1;
            unstick_post( $post_id );
        }

        return $sub_info;
    }

}
