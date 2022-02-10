<?php

/**
 * Ajax Address Form Class
 */
class WPUF_Ajax_Address_Form {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'register_plugin_scripts' ] );
        add_action( 'wp_ajax_wpuf_address_ajax_action', [ $this, 'ajax_form_action' ], 10, 1 );
        add_action( 'wp_ajax_nopriv_wpuf_address_ajax_action', [ $this, 'ajax_form_action' ], 10, 1 );
    }

    /**
     * Enqueue scripts
     */
    public function register_plugin_scripts() {
        global $post;

        $pay_page = intval( wpuf_get_option( 'payment_page', 'wpuf_payment' ) );

        if ( wpuf_get_option( 'load_script', 'wpuf_general', 'on' ) == 'on' ) {
            $this->plugin_scripts();
        } elseif ( isset( $post->ID ) && ( $pay_page == $post->ID ) ) {
            $this->plugin_scripts();
        }
    }

    /**
     * Load billing scripts
     */
    public function plugin_scripts() {
        wp_enqueue_script( 'wpuf-ajax-script', plugins_url( 'assets/js/billing-address.js', __DIR__ ), [ 'jquery' ], false );
        wp_localize_script( 'wpuf-ajax-script', 'ajax_object', [ 'ajaxurl'     => admin_url( 'admin-ajax.php' ),
                                                                 'fill_notice' => __( 'Some Required Fields are not filled!', 'wp-user-frontend' )
        ] );
    }

    /**
     * Address Form
     */
    public static function wpuf_ajax_address_form() {
        $address_fields = wpuf_get_user_address();
        $show_address   = wpuf_get_option( 'show_address', 'wpuf_address_options', false );
        $show_country   = wpuf_get_option( 'country', 'wpuf_address_options', false );
        $show_state     = wpuf_get_option( 'state', 'wpuf_address_options', false );
        $show_add1      = wpuf_get_option( 'address_1', 'wpuf_address_options', false );
        $show_add2      = wpuf_get_option( 'address_2', 'wpuf_address_options', false );
        $show_city      = wpuf_get_option( 'city', 'wpuf_address_options', false );
        $show_zip       = wpuf_get_option( 'zip', 'wpuf_address_options', false );

        $required_class = 'bill_required';

        $country_req  = '';
        $country_hide = '';
        $state_req    = '';
        $state_hide   = '';
        $add1_req     = '';
        $add1_hide    = '';
        $add2_req     = '';
        $add2_hide    = '';
        $city_req     = '';
        $city_hide    = '';
        $zip_req      = '';
        $zip_hide     = '';
        $required     = '';

        if ( $show_country == 'hidden' ) {
            $show_state = 'hidden';
        }

        switch ( $show_country ) {
            case 'required':
                $country_required = true;
                break;

            case 'hidden':
                $country_hide = 'display: none;';
            // no break
            default:
                break;
        }

        switch ( $show_state ) {
            case 'required':
                $state_required = true;
                break;

            case 'hidden':
                $state_hide = 'display: none;';
            // no break
            default:
                break;
        }

        switch ( $show_add1 ) {
            case 'required':
                $address1_required = true;
                break;

            case 'hidden':
                $add1_hide = 'display: none;';
            // no break
            default:
                break;
        }

        switch ( $show_add2 ) {
            case 'required':
                $address2_required = true;
                break;

            case 'hidden':
                $add2_hide = 'display: none;';
            // no break
            default:
                break;
        }
        switch ( $show_city ) {
            case 'required':
                $city_required = true;
                break;

            case 'hidden':
                $city_hide = 'display: none;';
            // no break
            default:
                break;
        }
        switch ( $show_zip ) {
            case 'required':
                $zip_required = true;
                break;

            case 'hidden':
                $zip_hide = 'display: none;';
            // no break
            default:
                break;
        }

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
        } else if ( isset( $_GET['user_id'] ) ) {
            $user_id = absint( $_GET['user_id'] );
        } else {
            return;
        }

        if ( $show_address ) {
            ?>

            <form class="wpuf-form form-label-above" id="wpuf-ajax-address-form" action="" method="post">
                <?php wp_nonce_field( 'wpuf-ajax-address' ); ?>
                <input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">

                <table id="wpuf-address-country-state" class="wp-list-table widefat">
                    <tr>
                        <td class="<?php echo isset( $country_required ) ? esc_attr( $required_class ) : null; ?>"
                            style="display:inline-block;float:left;width:100%;margin:0px;padding:5px;<?php echo esc_attr( $country_hide ); ?>">
                            <label><?php esc_html_e( 'Country', 'wp-user-frontend' ); ?><?php echo isset( $country_required ) ? wp_kses( '<span class="required">*</span>', array( 'span' => array() ) ) : null; ?></label>
                            <br>
                            <?php
                            if ( function_exists( 'wpuf_get_tax_rates' ) ) {
                                $rates = wpuf_get_tax_rates();
                            }

                            $cs        = new CountryState();
                            $states    = [];
                            $selected  = [];
                            $base_addr = get_option( 'wpuf_base_country_state', false );

                            $selected['country'] = ! ( empty( $address_fields['country'] ) ) ? $address_fields['country'] : $base_addr['country'];

                            echo wp_kses( wpuf_select( [
                                    'options'          => $cs->countries(),
                                    'name'             => 'wpuf_biiling_country',
                                    'selected'         => $selected['country'],
                                    'show_option_all'  => false,
                                    'show_option_none' => false,
                                    'id'               => 'wpuf_biiling_country',
                                    'class'            => 'wpuf_biiling_country',
                                    'chosen'           => false,
                                    'placeholder'      => __( 'Choose a country', 'wp-user-frontend' ),
                                ]
                            ), [
                                'select' => [
                                    'class'            => [],
                                    'name'             => [],
                                    'id'               => [],
                                    'data-placeholder' => []
                                ],
                                'option' => [
                                    'value'    => [],
                                    'class'    => [],
                                    'id'       => [],
                                    'selected' => []
                                ],
                            ] ); ?>
                        </td>
                        <td class="<?php echo isset( $state_required ) ? esc_attr( $required_class ) : null; ?>"
                            style="display:inline-block;float:left;width:100%;margin:0px;padding:5px;<?php echo esc_attr( $state_hide ); ?>">
                            <label><?php esc_html_e( 'State/Province/Region', 'wp-user-frontend' ); ?><?php echo isset( $state_required ) ? wp_kses( '<span class="required">*</span>', array( 'span' => array() ) ) : null; ?></label>
                            <br>
                            <?php
                            $states            = $cs->getStates( $selected['country'] );
                            $selected['state'] = ! ( empty( $address_fields['state'] ) ) ? $address_fields['state'] : $base_addr['state'];
                            echo wp_kses( wpuf_select( [
                                    'options'          => $states,
                                    'name'             => 'wpuf_biiling_state',
                                    'selected'         => $selected['state'],
                                    'show_option_all'  => false,
                                    'show_option_none' => false,
                                    'id'               => 'wpuf_biiling_state',
                                    'class'            => 'wpuf_biiling_state',
                                    'chosen'           => false,
                                    'placeholder'      => __( 'Choose a state', 'wp-user-frontend' ),
                                ]
                            ), [
                                'select' => [
                                    'class'            => [],
                                    'name'             => [],
                                    'id'               => [],
                                    'data-placeholder' => []
                                ],
                                'option' => [
                                    'value'    => [],
                                    'class'    => [],
                                    'id'       => [],
                                    'selected' => []
                                ],
                            ] ); ?>
                        </td>
                        <td style="display:inline-block;float:left;width:100%;margin:0px;padding:5px;<?php echo esc_attr( $add1_hide ); ?>">
                            <div
                                class="wpuf-label"><?php esc_html_e( 'Address Line 1 ', 'wp-user-frontend' ); ?><?php echo isset( $address1_required ) ? wp_kses( '<span class="required">*</span>', array( 'span' => array() ) ) : null; ?></div>
                            <div class="wpuf-fields">
                                <input type="text"
                                       class="input <?php echo isset( $address1_required ) ? esc_attr( $required_class ) : null; ?>"
                                       name="wpuf_biiling_add_line_1"
                                       id="wpuf_biiling_add_line_1"
                                       value="<?php echo esc_attr( $address_fields['add_line_1'] ); ?>"/>
                            </div>
                        </td>
                        <td style="display:inline-block;float:left;width:100%;margin:0px;padding:5px;<?php echo esc_attr( $add2_hide ); ?>">
                            <div
                                class="wpuf-label"><?php esc_html_e( 'Address Line 2 ', 'wp-user-frontend' ); ?><?php echo isset( $address2_required ) ? wp_kses( '<span class="required">*</span>', array( 'span' => array() ) ) : null; ?></div>
                            <div class="wpuf-fields">
                                <input type="text"
                                       class="input <?php echo isset( $address2_required ) ? esc_attr( $required_class ) : null; ?>"
                                       name="wpuf_biiling_add_line_2"
                                       id="wpuf_biiling_add_line_2"
                                       value="<?php echo esc_attr( $address_fields['add_line_2'] ); ?>"/>
                            </div>
                        </td>
                        <td style="display:inline-block;float:left;width:100%;margin:0px;padding:5px;<?php echo esc_attr( $city_hide ); ?>">
                            <div
                                class="wpuf-label"><?php esc_html_e( 'City', 'wp-user-frontend' ); ?><?php echo isset( $city_required ) ? wp_kses( '<span class="required">*</span>', array( 'span' => array() ) ) : null; ?></div>
                            <div class="wpuf-fields">
                                <input type="text"
                                       class="input <?php echo isset( $city_required ) ? esc_attr( $required_class ) : null; ?>"
                                       name="wpuf_biiling_city" id="wpuf_biiling_city"
                                       value="<?php echo esc_attr( $address_fields['city'] ); ?>"/>
                            </div>
                        </td>
                        <td style="display:inline-block;float:left;width:100%;margin:0px;padding:5px;<?php echo esc_attr( $zip_hide ); ?>">
                            <div
                                class="wpuf-label"><?php esc_html_e( 'Postal Code/ZIP', 'wp-user-frontend' ); ?><?php echo isset( $zip_required ) ? wp_kses( '<span class="required">*</span>', array( 'span' => array() ) ) : null; ?></div>
                            <div class="wpuf-fields">
                                <input type="text"
                                       class="input <?php echo isset( $zip_required ) ? esc_attr( $required_class ) : null; ?>"
                                       name="wpuf_biiling_zip_code" id="wpuf_biiling_zip_code"
                                       value="<?php echo esc_attr( $address_fields['zip_code'] ); ?>"/>
                            </div>
                        </td>
                        <td class="<?php echo esc_attr( $required ); ?>" class="wpuf-submit" style="display:none;">
                            <input type="submit" class="wpuf-btn" name="submit" id="wpuf-account-update-billing_address"
                                   value="<?php esc_html_e( 'Update Billing Address', 'wp-user-frontend' ); ?>"/>
                        </td>
                    </tr>

                </table>
                <div class="clear"></div>
            </form>

            <?php
        }
    }

    /**
     * Ajax Form action
     */
    public function ajax_form_action() {
        check_ajax_referer( 'wpuf-ajax-address' );

        $post_data = wp_unslash( $_POST );

        $user_id = get_current_user_id();

        $address_fields = [];

        $add_line_1 = isset( $post_data['billing_add_line1'] ) ? sanitize_text_field( wp_unslash( $post_data['billing_add_line1'] ) ) : '';
        $add_line_2 = isset( $post_data['billing_add_line2'] ) ? sanitize_text_field( wp_unslash( $post_data['billing_add_line2'] ) ) : '';
        $city       = isset( $post_data['billing_city'] ) ? sanitize_text_field( wp_unslash( $post_data['billing_city'] ) ) : '';

        $state    = isset( $post_data['billing_state'] ) ? sanitize_text_field( wp_unslash( $post_data['billing_state'] ) ) : '';
        $zip_code = isset( $post_data['billing_zip'] ) ? sanitize_text_field( wp_unslash( $post_data['billing_zip'] ) ) : '';
        $country  = isset( $post_data['billing_country'] ) ? sanitize_text_field( wp_unslash( $post_data['billing_country'] ) ) : '';

        $address_fields = [
            'add_line_1' => $add_line_1,
            'add_line_2' => $add_line_2,
            'city'       => $city,
            'state'      => $state,
            'zip_code'   => $zip_code,
            'country'    => $country,
        ];

        update_user_meta( $user_id, 'wpuf_address_fields', $address_fields );

        $msg = '<div class="wpuf-success">' . __( 'Billing address is updated.', 'wp-user-frontend' ) . '</div>';

        echo wp_kses_post( $msg );
        exit();
    }
}
