<?php

/**
 * Manage Subscription packs
 */
class WPUF_Admin_Subscription {

    /**
     * The class instance holder
     *
     * @var \Object
     */
    private static $_instance;

    /**
     * The constructor
     */
    public function __construct() {
        add_filter( 'post_updated_messages', [ $this, 'form_updated_message' ] );

        add_action( 'show_user_profile', [ $this, 'profile_subscription_details' ], 30 );
        add_action( 'edit_user_profile', [ $this, 'profile_subscription_details' ], 30 );
        add_action( 'personal_options_update', [ $this, 'profile_subscription_update' ] );
        add_action( 'edit_user_profile_update', [ $this, 'profile_subscription_update' ] );
        add_action( 'wp_ajax_wpuf_delete_user_package', [ $this, 'delete_user_package' ] );

        add_filter( 'manage_wpuf_subscription_posts_columns', [ $this, 'subscription_columns_head' ] );
        add_action( 'manage_wpuf_subscription_posts_custom_column', [ $this, 'subscription_columns_content' ], 10, 2 );

        // display help link to docs
        add_action( 'admin_notices', [ $this, 'add_help_link' ] );

        // new subscription metabox hooks
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'admin_print_styles-post-new.php', [ $this, 'enqueue_scripts' ] );
        add_action( 'admin_print_styles-post.php', [ $this, 'enqueue_scripts' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_profile_script' ] );
    }

    /**
     * Get singleton instance
     *
     * @return [type] [description]
     */
    public static function getInstance() {
        if ( ! self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Add settings metaboxes
     */
    public function add_meta_boxes() {
        add_meta_box( 'wpuf-metabox-subscription', __( 'Pack Description', 'wp-user-frontend' ), [ $this, 'pack_description_metabox' ], 'wpuf_subscription', 'normal', 'high' );
        add_meta_box( 'wpuf_subs_metabox', 'Subscription Options', [ $this, 'subs_meta_box' ], 'wpuf_subscription' );
    }

    /**
     * Custom post update message
     *
     * @param array $messages
     *
     * @return array
     */
    public function form_updated_message( $messages ) {
        $message = [
            0  => '',
            1  => __( 'Subscription pack updated.', 'wp-user-frontend' ),
            2  => __( 'Custom field updated.', 'wp-user-frontend' ),
            3  => __( 'Custom field deleted.', 'wp-user-frontend' ),
            4  => __( 'Subscription pack updated.', 'wp-user-frontend' ),
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Subscription pack restored to revision from %s', 'wp-user-frontend' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Subscription pack published.', 'wp-user-frontend' ),
            7  => __( 'Subscription pack saved.', 'wp-user-frontend' ),
            8  => __( 'Subscription pack submitted.', 'wp-user-frontend' ),
            9  => '',
            10 => __( 'Subscription pack draft updated.', 'wp-user-frontend' ),
        ];

        $messages['wpuf_subscription'] = $message;

        return $messages;
    }

    /**
     * Update user profile lock
     *
     * @param int $user_id
     */
    public function profile_subscription_update( $user_id ) {
        if ( ! is_admin() && ! current_user_can( 'edit_users' ) ) {
            return;
        }
        $nonce = isset( $_REQUEST['wpuf-subscription-nonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['wpuf-subscription-nonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'update-profile_' . $user_id ) ) {
            return;
        }

        if ( ! isset( $_POST['pack_id'] ) ) {
            return;
        }

        if ( isset( $_POST['wpuf_profile_mail_noti'] ) ) {
            $wpuf_profile_mail_noti = sanitize_text_field( wp_unslash( $_POST['wpuf_profile_mail_noti'] ) );
            update_user_meta( $user_id, '_pack_assign_notification', $wpuf_profile_mail_noti );
        }

        $pack_id   = isset( $_POST['pack_id'] ) ? intval( wp_unslash( $_POST['pack_id'] ) ) : '';
        $u_id   = isset( $_POST['user_id'] ) ? intval( wp_unslash( $_POST['user_id'] ) ) : '';
        $pack      = WPUF_Subscription::get_subscription( $pack_id );
        $user_pack = WPUF_Subscription::get_user_pack( $u_id );

        if ( isset( $user_pack['pack_id'] ) && $pack_id == $user_pack['pack_id'] ) {
            //updating number of posts

            if ( isset( $user_pack['posts'] ) ) {
                $p_type = isset( $_POST[ $post_type ] ) ? sanitize_text_field( wp_unslash( $_POST[ $post_type ] ) ) : '';
                foreach ( $user_pack['posts'] as $post_type => $post_num ) {
                    $user_pack['posts'][ $post_type ] = $p_type;
                }
            }

            //post expiration enable or disable

            if ( isset( $_POST['is_post_expiration_enabled'] ) ) {
                $user_pack['_enable_post_expiration'] = sanitize_text_field( wp_unslash( $_POST['is_post_expiration_enabled'] ) );
            } else {
                unset( $user_pack['_enable_post_expiration'] );
            }

            //updating post time
            if ( isset( $_POST['post_expiration_settings'] ) ) {
                $post_expiration_settings = array_map( 'sanitize_text_field', wp_unslash( $_POST['post_expiration_settings'] ) );

                $user_pack['_post_expiration_time'] = $post_expiration_settings['expiration_time_value'] . ' ' . $post_expiration_settings['expiration_time_type'];

                echo esc_html( $user_pack['_post_expiration_time'] );
            }

            if ( isset( $user_pack['recurring'] ) && $user_pack['recurring'] == 'yes' ) {
                foreach ( $user_pack['posts'] as $type => $value ) {
                    $user_pack['posts'][ $type ] = isset( $_POST[ $type ] ) ? sanitize_text_field( wp_unslash( $_POST[ $type ] ) ) : 0;
                }
            } else {
                foreach ( $user_pack['posts'] as $type => $value ) {
                    $user_pack['posts'][ $type ] = isset( $_POST[ $type ] ) ? sanitize_text_field( wp_unslash( $_POST[ $type ] ) ) : 0;
                }
                $user_pack['expire'] = isset( $_POST['expire'] ) ? wpuf_date2mysql( sanitize_text_field( wp_unslash( $_POST['expire'] ) ) ) : $user_pack['expire'];
            }
            wpuf_get_user( $user_id )->subscription()->update_meta( $user_pack );
        } else {
            if ( $pack_id == '-1' ) {
                return;
            }

            $user_info      = get_userdata( $user_id );
            $cost           = $pack->meta_value['billing_amount'];
            $billing_amount = apply_filters( 'wpuf_payment_amount', $cost );
            $tax_amount     = $billing_amount - $cost;

            $data = [
                'user_id'          => $user_id,
                'status'           => 'completed',
                'subtotal'         => $cost,
                'tax'              => $tax_amount,
                'cost'             => $billing_amount,
                'post_id'          => 0,
                'pack_id'          => $pack_id,
                'payer_first_name' => $user_info->first_name,
                'payer_last_name'  => $user_info->last_name,
                'payer_email'      => $user_info->user_email,
                'payment_type'     => 'bank',
                'payer_address'    => null,
                'transaction_id'   => 0,
                'created'          => current_time( 'mysql' ),
                'profile_id'       => null,
            ];

            $is_recurring = false;

            if ( isset( $user_pack['recurring'] ) && $user_pack['recurring'] == 'yes' ) {
                $is_recurring = true;
            }

            WPUF_Payment::insert_payment( $data, 0, $is_recurring );
        }
    }

    /**
     * Subscription column headings
     *
     * @param array $head
     *
     * @return array
     */
    public function subscription_columns_head( $head ) {
        unset( $head['date'] );
        $head['title']          = __( 'Pack Name', 'wp-user-frontend' );
        $head['amount']         = __( 'Amount', 'wp-user-frontend' );
        $head['subscribers']    = __( 'Subscribers', 'wp-user-frontend' );
        $head['recurring']      = __( 'Recurring', 'wp-user-frontend' );
        $head['duration']       = __( 'Duration', 'wp-user-frontend' );

        return $head;
    }

    /**
     * Susbcription lists column content
     *
     * @param string $column_name
     * @param int    $post_ID
     *
     * @return void
     */
    public function subscription_columns_content( $column_name, $post_ID ) {
        switch ( $column_name ) {
            case 'amount':
                $amount = get_post_meta( $post_ID, '_billing_amount', true );

                if ( intval( $amount ) == 0 ) {
                    $amount = __( 'Free', 'wp-user-frontend' );
                } else {
                    $amount = wpuf_format_price( $amount );
                }
                echo esc_html( $amount );
                break;

            case 'subscribers':
                $users = WPUF_Subscription::init()->subscription_pack_users( $post_ID );

                echo wp_kses_post( '<a href="' . admin_url( 'edit.php?post_type=wpuf_subscription&page=wpuf_subscribers&post_ID=' . $post_ID ) . '" />' . count( $users ) . '</a>' );
                break;

            case 'recurring':
                $recurring = get_post_meta( $post_ID, '_recurring_pay', true );

                if ( $recurring == 'yes' ) {
                    esc_html_e( 'Yes', 'wp-user-frontend' );
                } else {
                    esc_html_e( 'No', 'wp-user-frontend' );
                }
                break;

            case 'duration':
                $recurring_pay        = get_post_meta( $post_ID, '_recurring_pay', true );
                $billing_cycle_number = get_post_meta( $post_ID, '_billing_cycle_number', true );
                $cycle_period         = get_post_meta( $post_ID, '_cycle_period', true );

                if ( $recurring_pay == 'yes' ) {
                    echo esc_attr( $billing_cycle_number . ' ' . $cycle_period ) . '\'s (cycle)';
                } else {
                    $expiration_number    = get_post_meta( $post_ID, '_expiration_number', true );
                    $expiration_period    = get_post_meta( $post_ID, '_expiration_period', true );
                    echo esc_attr( $expiration_number . ' ' . $expiration_period ) . '\'s';
                }
                break;
        }
    }

    public function get_post_types( $post_types = null ) {
        if ( ! $post_types ) {
            $post_types = WPUF_Subscription::init()->get_all_post_type();
        }

        ob_start();

        foreach ( $post_types as $key => $name ) {
            $post_type_object = get_post_type_object( $key );

            if ( $post_type_object ) { ?>
                <tr>
                    <th><label for="wpuf-<?php echo esc_attr( $key ); ?>"><?php printf( 'Number of %s', esc_html( $post_type_object->label ) ); ?></label></th>
                    <td>
                        <input type="text" size="20" style="" id="wpuf-<?php echo esc_attr( $key ); ?>" value="<?php echo intval( $name ); ?>" name="post_type_name[<?php echo esc_attr( $key ); ?>]" />
                        <div><span class="description"><span><?php printf( 'How many %s the user can list with this pack? Enter <strong>-1</strong> for unlimited.', esc_html( $key ) ); ?></span></span></div>
                    </td>
                </tr>
                <?php
            }
        }

        return ob_get_clean();
    }

    /**
     * Replaces default post editor with a simiple rich editor
     *
     * @param int $pack_id
     *
     * @return void
     */
    public function pack_description_metabox( $pack_id = null ) {
        global $post;

        wp_editor(
            $post->post_content, 'post_content', [
                'editor_height' => 100,
                'quicktags' => false,
                'media_buttons' => false,
            ]
        );
    }

    /**
     * Subscription settings metabox
     *
     * @return void
     */
    public function subs_meta_box() {
        global $post;

        $sub_meta = WPUF_Subscription::init()->get_subscription_meta( $post->ID, $post );

        $hidden_recurring_class       = ( $sub_meta['recurring_pay'] != 'yes' ) ? 'none' : '';
        $hidden_trial_class           = ( $sub_meta['trial_status'] != 'yes' ) ? 'none' : '';
        $hidden_expire                = ( $sub_meta['recurring_pay'] == 'yes' ) ? 'none' : '';
        $is_post_exp_selected         = isset( $sub_meta['_enable_post_expiration'] ) && $sub_meta['_enable_post_expiration'] == 'on' ? 'checked' : '';
        $_post_expiration_time        = explode( ' ', isset( $sub_meta['_post_expiration_time'] ) ? $sub_meta['_post_expiration_time'] : ' ' );
        $time_value                   = isset( $_post_expiration_time[0] ) ? $_post_expiration_time[0] : 1;
        $time_type                    = isset( $_post_expiration_time[1] ) ? $_post_expiration_time[1] : 'day';

        $expired_post_status          = isset( $sub_meta['_expired_post_status'] ) ? $sub_meta['_expired_post_status'] : '';
        $is_enable_mail_after_expired = isset( $sub_meta['_enable_mail_after_expired'] ) && $sub_meta['_enable_mail_after_expired'] == 'on' ? 'checked' : '';
        $post_expiration_message      = isset( $sub_meta['_post_expiration_message'] ) ? $sub_meta['_post_expiration_message'] : '';
        $featured_item                = ! empty( $sub_meta['_total_feature_item'] ) ? $sub_meta['_total_feature_item'] : 0;
        $remove_featured_item         = ! empty( $sub_meta['_remove_feature_item'] ) ? $sub_meta['_remove_feature_item'] : 0;
        ?>

        <div class="wpuf-subscription-pack-settings">
            <nav class="subscription-nav-tab">
                <ul>
                    <li class="tab-current">
                        <a href="#wpuf-payment-settings">
                            <span class="dashicons dashicons-cart"></span>
                            <?php esc_html_e( 'Payment Settings', 'wp-user-frontend' ); ?>
                        </a>
                    </li>

                    <li>
                        <a href="#wpuf-post-restriction">
                            <span class="dashicons dashicons-admin-post"></span>
                            <?php esc_html_e( 'Posting Restriction', 'wp-user-frontend' ); ?>
                        </a>
                    </li>

                    <?php do_action( 'wpuf_admin_subs_nav_tab', $post ); ?>
                </ul>
            </nav>

            <div class="subscription-nav-content">
                <section id="wpuf-payment-settings">
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th><label for="wpuf-billing-amount">
                                <span class="wpuf-biling-amount wpuf-subcription-expire" style="display: <?php echo esc_attr( $hidden_expire ); ?>;"><?php esc_html_e( 'Billing amount:', 'wp-user-frontend' ); ?></span>
                                <span class="wpuf-billing-cycle wpuf-recurring-child" style="display: <?php echo esc_attr( $hidden_recurring_class ); ?>;"><?php esc_html_e( 'Billing amount each cycle:', 'wp-user-frontend' ); ?></span></label></th>
                            <td>
                                <?php echo esc_attr( wpuf_get_currency( 'symbol' ) ); ?>
                                <input type="text" size="20" style="" id="wpuf-billing-amount" value="<?php echo esc_attr( $sub_meta['billing_amount'] ); ?>" name="billing_amount" />
                                <div><span class="description"></span></div>
                            </td>
                        </tr>
                        <tr class="wpuf-subcription-expire" style="display: <?php echo esc_attr( $hidden_expire ); ?>;">
                            <th><label for="wpuf-expiration-number"><?php esc_html_e( 'Expires In:', 'wp-user-frontend' ); ?></label></th>
                            <td>
                                <input type="text" size="20" style="" id="wpuf-expiration-number" value="<?php echo esc_attr( $sub_meta['expiration_number'] ); ?>" name="expiration_number" />

                                <select id="expiration-period" name="expiration_period">
                                    <?php echo esc_html( $this->option_field( $sub_meta['expiration_period'] ) ); ?>
                                </select>
                                <div><span class="description"></span></div>
                            </td>
                        </tr>

                        <?php do_action( 'wpuf_admin_subscription_detail', $sub_meta, $hidden_recurring_class, $hidden_trial_class, $this ); ?>
                        </tbody>
                    </table>
                </section>
                <section id="wpuf-post-restriction">
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th><label for="wpuf-sticky-item"><?php esc_html_e( 'Number of featured item', 'wp-user-frontend' ); ?></label></th>
                            <td>
                                <input type="text" size="20" style="" id="wpuf-sticky-item" value="<?php echo intval( $featured_item ); ?>" name="total_feature_item" />
                                <br>
                                <span class="description"><?php esc_html_e( 'How many items a user can set as featured, including all post types', 'wp-user-frontend' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wpuf-sticky-item"><?php esc_html_e( 'Remove featured item on subscription expiry', 'wp-user-frontend' ); ?></label></th>
                            <td>
                                <label for="">
                                    <input type="checkbox"  value="on" <?php echo esc_attr( 'on' === $remove_featured_item ? 'checked' : '' ); ?> name="remove_feature_item" />
                                    <?php esc_html_e( 'The featured item will be removed if the subscription expires', 'wp-user-frontend' ); ?>
                                </label>
                            </td>
                        </tr>
                            <?php
                                echo wp_kses(
                                    $this->get_post_types( $sub_meta['post_type_name'] ),
                                    [
                                        'div'    => [],
                                        'tr'     => [],
                                        'td'     => [],
                                        'th'     => [],
                                        'label'  => [
                                            'for' => [],
                                        ],
                                        'input' => [
                                            'type'  => [],
                                            'size'  => [],
                                            'style' => [],
                                            'id'    => [],
                                            'value' => [],
                                            'name'  => [],
                                        ],
                                        'span' => [
                                            'class' => [],
                                        ],
                                        'strong' => [],
                                    ]
                                );
                            ?>
                            <?php
                            // do_action( 'wpuf_admin_subscription_detail', $sub_meta, $hidden_recurring_class, $hidden_trial_class, $this );
                            ?>
                            <tr class="wpuf-metabox-post_expiration">

                                <th><?php esc_html_e( 'Post Expiration', 'wp-user-frontend' ); ?></th>

                                <td>
                                    <label>
                                        <input type="checkbox" id="wpuf-enable_post_expiration" name="post_expiration_settings[enable_post_expiration]" value="on" <?php echo esc_attr( $is_post_exp_selected ); ?> />
                                        <?php esc_html_e( 'Enable Post Expiration', 'wp-user-frontend' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr class="wpuf-metabox-post_expiration wpuf_subscription_expiration_field">
                                <?php
                                $timeType_array = [
                                    'year',
                                    'month',
                                    'day',
                                ];
                                ?>
                                <th class="wpuf-post-exp-time"> <?php esc_html_e( 'Post Expiration Time', 'wp-user-frontend' ); ?> </th>
                                <td class="wpuf-post-exp-time">
                                    <input type="number" name="post_expiration_settings[expiration_time_value]" id="wpuf-expiration_time_value" value="<?php echo $time_value; ?>" id="wpuf-expiration_time_value" min="1">
                                    <select name="post_expiration_settings[expiration_time_type]" id="wpuf-expiration_time_type">
                                        <?php
                                        foreach ( $timeType_array as $each_time_type ) {
                                            ?>
                                            <option value="<?php echo esc_attr( $each_time_type ); ?>" <?php echo $each_time_type == $time_type ? 'selected' : ''; ?>><?php echo esc_html( ucfirst( $each_time_type ) . '(s)' ); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>

                            </tr>
                            <tr class="wpuf_subscription_expiration_field">
                                <th>
                                    <?php esc_html_e( 'Post Status', 'wp-user-frontend' ); ?>
                                </th>
                                <td>
                                    <?php $post_statuses = get_post_statuses(); ?>
                                    <select name="post_expiration_settings[expired_post_status]" id="wpuf-expired_post_status">
                                        <?php
                                        foreach ( $post_statuses as $post_status => $text ) {
                                            ?>
                                            <option value="<?php echo esc_attr( $post_status ); ?>" <?php echo ( $expired_post_status == $post_status ) ? 'selected' : ''; ?>><?php echo esc_html( $text ); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Status of post after post expiration time is over ', 'wp-user-frontend' ); ?></p>
                                </td>
                            </tr>
                            <tr class="wpuf_subscription_expiration_field">
                                <th>
                                    <?php esc_html_e( 'Expiration Mail', 'wp-user-frontend' ); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="post_expiration_settings[enable_mail_after_expired]" value="on" <?php echo esc_attr( $is_enable_mail_after_expired ); ?> />
                                        <?php esc_html_e( 'Send Expiration Email to Post Author', 'wp-user-frontend' ); ?>
                                    </label>

                                    <p class="help">
                                        <?php esc_html_e( 'Send Mail to Author After Exceeding Post Expiration Time', 'wp-user-frontend' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr class="wpuf_subscription_expiration_field">
                                <th><?php esc_html_e( 'Expiration Message', 'wp-user-frontend' ); ?></th>
                                <td>
                                    <textarea name="post_expiration_settings[post_expiration_message]" id="wpuf-post_expiration_message" cols="50" rows="5"><?php echo esc_attr( $post_expiration_message ); ?></textarea>
                                    <p class="description"><strong><?php echo esc_html( __( 'You may use: {post_author} {post_url} {blogname} {post_title} {post_status}', 'wp-user-frontend' ) ); ?></strong></p>
                                </td>
                            </tr>

                            <?php
                                /**
                                 * @since 2.7.0
                                 */
                                do_action( 'wpuf_admin_subscription_post_restriction', $sub_meta, $post, $this );
                            ?>
                        </tbody>
                    </table>
                </section>

                <?php do_action( 'wpuf_admin_subs_nav_content', $post ); ?>
            </div>
            <?php wp_nonce_field( 'subs_meta_box_nonce', 'meta_box_nonce' ); ?>
        </div>

        <?php
    }

    /**
     * Enqueue script for subscription editor page
     *
     * @return void
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();

        if ( $screen->post_type != 'wpuf_subscription' ) {
            return;
        }

        wp_enqueue_script( 'wpuf-metabox-tabs', WPUF_ASSET_URI . '/js/metabox-tabs.js', [ 'jquery' ] );
    }

    /**
     * Enqueue script for profile
     *
     * @return void
     */
    public function enqueue_profile_script() {
        $screen = get_current_screen();

        if ( 'profile' != $screen->base ) {
            return;
        }

        wp_enqueue_script( 'wpuf-admin-profile-subs', WPUF_ASSET_URI . '/js/admin-profile-subs.js', [ 'jquery' ] );
    }

    /**
     * Option fields for date type
     *
     * @param string $selected
     *
     * @return void
     */
    public function option_field( $selected ) {
        ?>
        <option value="day" <?php selected( $selected, 'day' ); ?> ><?php esc_html_e( 'Day(s)', 'wp-user-frontend' ); ?></option>
        <option value="week" <?php selected( $selected, 'week' ); ?> ><?php esc_html_e( 'Week(s)', 'wp-user-frontend' ); ?></option>
        <option value="month" <?php selected( $selected, 'month' ); ?> ><?php esc_html_e( 'Month(s)', 'wp-user-frontend' ); ?></option>
        <option value="year" <?php selected( $selected, 'year' ); ?> ><?php esc_html_e( 'Year(s)', 'wp-user-frontend' ); ?></option>
        <?php
    }

    public function packdropdown_without_recurring( $packs, $selected = '' ) {
        $packs = isset( $packs ) ? $packs : [];

        foreach ( $packs as $key => $pack ) {
            $recurring = isset( $pack->meta_value['recurring_pay'] ) ? $pack->meta_value['recurring_pay'] : '';

            if ( $recurring == 'yes' ) {
                continue;
            }
            ?>
            <option value="<?php echo esc_attr( $pack->ID ); ?>" <?php selected( $selected, $pack->ID ); ?>><?php echo esc_attr( $pack->post_title ); ?></option>
            <?php
        }
    }

    /**
     * Adds the postlock form in users profile
     *
     * @param object $profileuser
     */
    public function profile_subscription_details( $profileuser ) {
        if ( ! current_user_can( 'edit_users' ) ) {
            return;
        }

        $current_user = wpuf_get_user();

        if ( ! $current_user->subscription()->current_pack_id() ) {
            // return;
        }

        $userdata = get_userdata( $profileuser->ID ); //wp 3.3 fix

        $packs    = WPUF_Subscription::init()->get_subscriptions();
        $user_sub = WPUF_Subscription::get_user_pack( $userdata->ID );
        $pack_id  = isset( $user_sub['pack_id'] ) ? $user_sub['pack_id'] : '';
        ?>
        <div class="wpuf-user-subscription" style="width: 640px;">
            <h3><?php esc_html_e( 'WPUF Subscription Information', 'wp-user-frontend' ); ?></h3>

            <?php

            if ( isset( $user_sub['pack_id'] ) ) {
                $pack         = WPUF_Subscription::get_subscription( $user_sub['pack_id'] );
                $details_meta = WPUF_Subscription::init()->get_details_meta_value();

                $billing_amount = ( isset( $pack->meta_value['billing_amount'] ) && intval( $pack->meta_value['billing_amount'] ) > 0 ) ? $details_meta['symbol'] . $pack->meta_value['billing_amount'] : __( 'Free', 'wp-user-frontend' );
                $recurring_pay  = ( isset( $pack->meta_value['recurring_pay'] ) && $pack->meta_value['recurring_pay'] == 'yes' ) ? true : false;

                if ( $billing_amount && $recurring_pay ) {
                    $recurring_des = sprintf( __( 'For each %1$s %2$s', 'wp-user-frontend' ), $pack->meta_value['billing_cycle_number'], $pack->meta_value['cycle_period'], $pack->meta_value['trial_duration_type'] );
                    $recurring_des .= ! empty( $pack->meta_value['billing_limit'] ) ? sprintf( __( ', for %s installments', 'wp-user-frontend' ), $pack->meta_value['billing_limit'] ) : '';
                    $recurring_des = $recurring_des;
                } else {
                    $recurring_des = '';
                }
                ?>
                <div class="wpuf-user-sub-info">

                    <div class="wpuf-sub-summary">
                        <div class="sub-name">
                            <span class="label">
                                <?php esc_html_e( 'Subcription Name', 'wp-user-frontend' ); ?>
                            </span>

                            <span class="value">
                                <?php echo isset( $pack->post_title ) ? esc_html( $pack->post_title ) : ''; ?>
                            </span>
                        </div>

                        <div class="sub-price">
                            <span class="label">
                                <?php esc_html_e( 'Billing Info', 'wp-user-frontend' ); ?>
                            </span>

                            <span class="value">
                                <?php echo esc_html( $billing_amount ); ?>

                                <?php if ( $recurring_des ) { ?>
                                    <p><?php echo esc_html( $recurring_des ); ?></p>
                                <?php } ?>
                            </span>
                        </div>

                        <?php if ( isset( $user_sub['recurring'] ) && $user_sub['recurring'] == 'yes' ) { ?>
                            <div class="info">
                                <p><?php esc_html_e( 'This user is using recurring subscription pack', 'wp-user-frontend' ); ?></p>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="wpuf-sub-section remaining-posts">
                        <h4><?php esc_html_e( 'Remaining Posting Count', 'wp-user-frontend' ); ?></h4>

                        <table class="form-table">
                            <?php if ( ! empty( $user_sub['total_feature_item'] ) ) { ?>
                            <tr>
                                <th><label><?php esc_html_e( 'Number of featured item', 'wp-user-frontend' ); ?></label></th>
                                <td><input type="text" value="<?php echo esc_attr( $user_sub['total_feature_item'] ); ?>" name="<?php echo esc_attr( $key ); ?>" ></td>
                            </tr>
                            <?php } ?>
                            <?php
                            foreach ( $user_sub['posts'] as $key => $value ) {
                                $post_type_object = get_post_type_object( $key );

                                if ( $post_type_object ) {
                                    ?>
                                     <tr>
                                         <th><label><?php echo esc_html( $post_type_object->labels->name ); ?></label></th>
                                         <td><input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $key ); ?>" ></td>
                                     </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                    </div>

                    <div class="wpuf-sub-section post-expiration">
                        <h4><?php esc_html_e( 'Subscription Expiration Info', 'wp-user-frontend' ); ?></h4>

                        <table class="form-table">
                            <?php
                            if ( $user_sub['recurring'] != 'yes' ) {
                                if ( ! empty( $user_sub['expire'] ) ) {
                                    $expire = ( $user_sub['expire'] == 'unlimited' ) ? ucfirst( 'unlimited' ) : wpuf_get_date( wpuf_date2mysql( $user_sub['expire'] ) );
                                    ?>
                                    <tr>
                                        <th><label><?php esc_html_e( 'Expire date:', 'wp-user-frontend' ); ?></label></th>
                                        <td><input type="text" class="wpuf-date-picker" name="expire" value="<?php echo esc_html( $expire ); ?>"></td>
                                    </tr>
                                    <?php
                                }
                            }

                            $is_post_exp_selected  = isset( $user_sub['_enable_post_expiration'] ) ? 'checked' : '';
                            $_post_expiration_time = explode( ' ', isset( $user_sub['_post_expiration_time'] ) ? $user_sub['_post_expiration_time'] : '' );
                            $time_value            = isset( $_post_expiration_time[0] ) && ! empty( $_post_expiration_time[0] ) ? $_post_expiration_time[0] : '1';
                            $time_type             = isset( $_post_expiration_time[1] ) && ! empty( $_post_expiration_time[1] ) ? $_post_expiration_time[1] : 'day';
                            ?>
                            <tr>
                                <th><label><?php esc_html_e( 'Post Expiration Enabled', 'wp-user-frontend' ); ?></label></th>
                                <td><input type="checkbox" class="wpuf-post-exp-enabled" name="is_post_expiration_enabled" value="on" <?php echo esc_attr( $is_post_exp_selected ); ?>></td>
                            </tr>
                            <tr class="wpuf-post-exp-time">
                                <?php
                                $timeType_array = [
                                    'year'  => 100,
                                    'month' => 12,
                                    'day'   => 30,
                                ];
                                ?>
                                <th><?php esc_html_e( 'Post Expiration Time', 'wp-user-frontend' ); ?></th>
                                <td>
                                    <select name="post_expiration_settings[expiration_time_value]" id="wpuf-expiration_time_value">
                                        <?php
                                        for ( $i = 1; $i <= $timeType_array[ $time_type ]; $i++ ) {
                                            ?>
                                            <option value="<?php echo esc_attr( $i ); ?>" <?php echo $i == $time_value ? 'selected' : ''; ?>><?php echo esc_attr( $i ); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <select name="post_expiration_settings[expiration_time_type]" id="wpuf-expiration_time_type">
                                        <?php
                                        foreach ( $timeType_array as $each_time_type => $each_time_type_val ) {
                                            ?>
                                            <option value="<?php echo esc_attr( $each_time_type ); ?>" <?php echo $each_time_type == $time_type ? 'selected' : ''; ?>><?php echo esc_html( ucfirst( $each_time_type ) ); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="wpuf-sub-section tax-restriction">
                        <h4><?php esc_html_e( 'Allowed Taxonomy Terms', 'wp-user-frontend' ); ?></h4>

                        <table class="form-table">
                            <tr>
                                <?php
                                    $allowed_tax_id_arr = [];
                                $allowed_tax_id_arr                     = get_post_meta( $pack_id, '_sub_allowed_term_ids', true );

                                if ( ! $allowed_tax_id_arr ) {
                                    $allowed_tax_id_arr = [];
                                }

                                $builtin_taxs = get_taxonomies(
                                    [
                                        '_builtin' => true,
                                    ], 'objects'
                                );

                                foreach ( $builtin_taxs as $builtin_tax ) {
                                    if ( is_taxonomy_hierarchical( $builtin_tax->name ) ) {
                                        $tax_terms = get_terms(
                                            [
                                                'taxonomy'   => $builtin_tax->name,
                                                'hide_empty' => false,
                                            ]
                                        );

                                        foreach ( $tax_terms as $tax_term ) {
                                            if ( in_array( $tax_term->term_id, $allowed_tax_id_arr ) ) {
                                                ?>
                                 <td> <?php echo esc_html( $tax_term->name ); ?> </td>
                                                <?php
                                            }
                                        }
                                    }
                                }

                                $custom_taxs = get_taxonomies( [ '_builtin' => false ], 'objects' );

                                foreach ( $custom_taxs as $custom_tax ) {
                                    if ( is_taxonomy_hierarchical( $custom_tax->name ) ) {
                                        $tax_terms = get_terms(
                                            [
                                                'taxonomy'   => $custom_tax->name,
                                                'hide_empty' => false,
                                            ]
                                        );

                                        foreach ( $tax_terms as $tax_term ) {
                                            if ( in_array( $tax_term->term_id, $allowed_tax_id_arr ) ) {
                                                ?>
                                 <td> <?php echo esc_html( $tax_term->name ); ?> </td>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php if ( ! isset( $user_sub['recurring'] ) || $user_sub['recurring'] != 'yes' ) { ?>

                <?php if ( empty( $user_sub ) ) { ?>
                    <div class="wpuf-sub-actions">
                        <a class="btn button-secondary wpuf-assing-pack-btn wpuf-add-pack" href="#"><?php esc_html_e( 'Assign Package', 'wp-user-frontend' ); ?></a>
                        <a class="btn button-secondary wpuf-assing-pack-btn wpuf-cancel-pack" style="display:none;" href="#"><?php esc_html_e( 'Cancel', 'wp-user-frontend' ); ?></a>
                    </div>
                <?php } ?>

                <table class="form-table wpuf-pack-dropdown" disabled="disabled" style="display: none;">
                    <tr>
                        <th><label for="wpuf_sub_pack"><?php esc_html_e( 'Select Package:', 'wp-user-frontend' ); ?> </label></th>
                        <td>
                            <select name="pack_id" id="wpuf_sub_pack">
                                <option value="-1"><?php esc_html_e( '&mdash; Select &mdash;', 'wp-user-frontend' ); ?></option>
                                <?php $this->packdropdown_without_recurring( $packs, $pack_id ); //WPUF_Subscription::init()->packdropdown( $packs, $selected = '' ); ?>
                            </select>
                            <br>
                            <span class="description"><?php esc_html_e( 'Only non-recurring pack can be assigned', 'wp-user-frontend' ); ?></span>
                        </td>
                    </tr>
                </table>
            <?php } ?>
            <?php
            wp_nonce_field( 'update-profile_' . $userdata->ID, 'wpuf-subscription-nonce' );
            do_action( 'wpuf_admin_subscription_content', $userdata->ID );
            ?>
            <?php if ( ! empty( $user_sub ) ) { ?>
                <div class="wpuf-sub-actions">
                    <a class="btn button-secondary wpuf-delete-pack-btn" href="javascript:" data-userid="<?php echo esc_attr( $userdata->ID ); ?>" data-packid="<?php echo isset( $user_sub['pack_id'] ) ? esc_attr( $user_sub['pack_id'] ) : ''; ?>"><?php esc_html_e( 'Delete Package', 'wp-user-frontend' ); ?></a>
                </div>
            <?php } ?>
        </div>
        <?php
    }

    public function lenght_type_option( $selected ) {
        for ( $i = 1; $i <= 30; $i++ ) {
            ?>
                <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $selected ); ?>><?php echo esc_html( $i ); ?></option>
            <?php
        }
    }

    /**
     * Ajax function. Delete user package
     *
     * @since 2.2.7
     */
    public function delete_user_package() {
        $nonce = isset( $_REQUEST['wpuf_subscription_delete_nonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['wpuf_subscription_delete_nonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf-subscription-delete-nonce' ) ) {
            return;
        }
        $userid = isset( $_POST['userid'] ) ? intval( wp_unslash( $_POST['userid'] ) ) : 0;

        echo esc_html( delete_user_meta( $userid, '_wpuf_subscription_pack' ) );
        $wpuf_paypal = new WPUF_Paypal();
        $wpuf_paypal->recurring_change_status( $userid, 'Cancel' );

        if ( isset( $_POST['packid'] ) ) {
            $pack_id = intval( wp_unslash( $_POST['packid'] ) );
            WPUF_Subscription::subscriber_cancel( $userid, $pack_id );
        }
        exit;
    }

    /**
     * Add help link to the subscriptions listing page
     *
     * @return void
     */
    public function add_help_link() {
        $screen = get_current_screen();

        if ( 'edit-wpuf_subscription' != $screen->id ) {
            return;
        }
        ?>
        <div class="wpuf-footer-help">
            <span class="wpuf-footer-help-content">
                <span class="dashicons dashicons-editor-help"></span>
                <?php printf( wp_kses_post( __( 'Learn more about <a href="%s" target="_blank">Subscription</a>', 'wp-user-frontend' ) ), 'https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/?utm_source=wpuf-footer-help&utm_medium=text-link&utm_campaign=learn-more-subscription' ); ?>
            </span>
        </div>

        <script type="text/javascript">
            jQuery(function($) {
                $('.wpuf-footer-help').appendTo('.wrap');
            });
        </script>
        <?php
    }
}

//$subscription = new WPUF_Admin_Subscription();
