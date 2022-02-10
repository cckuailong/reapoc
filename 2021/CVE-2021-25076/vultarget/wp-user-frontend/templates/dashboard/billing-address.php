<?php

$user_id = get_current_user_id();

$address_fields = [];
$countries      = [];
$cs             = new CountryState();


if ( isset( $_POST['update_billing_address'] ) ) {

    if ( ! isset( $_POST['wpuf_save_address_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wpuf_save_address_nonce'] ), 'wpuf_address_ajax_action' ) ) {
        return;
    }

    $add_line_1 = isset( $_POST['wpuf_biiling_add_line_1'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_biiling_add_line_1'] ) ) : '';
    $add_line_2 = isset( $_POST['wpuf_biiling_add_line_2'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_biiling_add_line_2'] ) ) : '';
    $city       = isset( $_POST['wpuf_biiling_city'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_biiling_city'] ) ) : '';
    $state      = isset( $_POST['wpuf_biiling_state'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_biiling_state'] ) ) : '';
    $zip_code   = isset( $_POST['wpuf_biiling_zip_code'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_biiling_zip_code'] ) ) : '';
    $country    = isset( $_POST['wpuf_biiling_country'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_biiling_country'] ) ) : '';

    $address_fields = [
        'add_line_1' => $add_line_1,
        'add_line_2' => $add_line_2,
        'city'       => $city,
        'state'      => strtolower( str_replace( ' ', '', $state ) ),
        'zip_code'   => $zip_code,
        'country'    => $country,
    ];
    update_user_meta( $user_id, 'wpuf_address_fields', $address_fields );
    echo '<div class="wpuf-success">' . esc_html( __( 'Billing address is updated.', 'wp-user-frontend' ) ) . '</div>';
} else {
    if ( metadata_exists( 'user', $user_id, 'wpuf_address_fields' ) ) {
        $address_fields = wpuf_get_user_address();
    } else {
        $address_fields = array_fill_keys(
            [ 'add_line_1', 'add_line_2', 'city', 'state', 'zip_code', 'country' ], '' );
    }
}
?>

<form class="wpuf-form form-label-above" action="" method="post" id="wpuf-payment-gateway">
    <div class="wpuf-fields">
        <?php
        wp_nonce_field( 'wpuf-ajax-address' );
        wp_nonce_field( 'wpuf_address_ajax_action', 'wpuf_save_address_nonce' );
        ?>
        <ul class="wpuf-form form-label-above">

            <li class="wpuf-el" data-label="<?php esc_attr_e( 'Country', 'wp-user-frontend' ); ?>">
                <label class="wpuf-fields wpuf-label"><?php esc_html_e( 'Country', 'wp-user-frontend' ); ?><span
                        class="required">*</span></label>
                <?php
                //Inconsistency with keys, remap keys, Back compat with keys
                if ( array_key_exists('billing_country', $address_fields ) ){
                    foreach ( $address_fields as $key => $val ) {
                        unset( $address_fields[$key] );
                        $address_fields[str_replace('billing_','',$key)] = $val;
                    }
                }

                $selected['country'] = ! ( empty( $address_fields['country'] ) ) ? $address_fields['country'] : 'US';

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
                        'data'             => [ 'required' => 'yes', 'type' => 'select' ],
                    ]
                ), [
                    'select' => [
                        'class'            => [],
                        'name'             => [],
                        'id'               => [],
                        'data-placeholder' => [],
                        'data-required'    => [],
                        'data-type'        => [],
                    ],
                    'option' => [
                        'value'    => [],
                        'class'    => [],
                        'id'       => [],
                        'selected' => []
                    ],
                ] ); ?>
            </li>

            <li class="wpuf-el" data-label="<?php esc_attr_e( 'State/Province/Region', 'wp-user-frontend' ); ?>">
                <div class="wpuf-label"><?php esc_html_e( 'State/Province/Region', 'wp-user-frontend' ); ?> <span
                        class="required">*</span></div>
                <div class="wpuf-fields">
                    <?php
                    $states            = $cs->getStates( $selected['country'] );
                    $selected['state'] = ! ( empty( $address_fields['state'] ) ) ? $address_fields['state'] : '';
                    $add_line_1        = isset( $address_fields['add_line_1'] ) ? esc_attr( $address_fields['add_line_1'] ) : '';
                    $add_line_2        = isset( $address_fields['add_line_2'] ) ? esc_attr( $address_fields['add_line_2'] ) : '';
                    $city              = isset( $address_fields['city'] ) ? esc_attr( $address_fields['city'] ) : '';
                    $zip_code          = isset( $address_fields['zip_code'] ) ? esc_attr( $address_fields['zip_code'] ) : '';

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
                            'data'             => [ 'required' => 'yes', 'type' => 'select' ],
                        ]
                    ), [
                        'select' => [
                            'class'            => [],
                            'name'             => [],
                            'id'               => [],
                            'data-placeholder' => [],
                            'data-required'    => [],
                            'data-type'        => [],
                        ],
                        'option' => [
                            'value'    => [],
                            'class'    => [],
                            'id'       => [],
                            'selected' => []
                        ],
                    ] ); ?>
                </div>
            </li>

            <li class="wpuf-el" data-label="<?php esc_attr_e( 'Address Line 1', 'wp-user-frontend' ); ?>">
                <div class="wpuf-label"><?php esc_html_e( 'Address Line 1 ', 'wp-user-frontend' ); ?><span
                        class="required">*</span></div>
                <div class="wpuf-fields">
                    <input data-required="yes" data-type="text" type="text" class="input" name="wpuf_biiling_add_line_1" id="wpuf_biiling_add_line_1" value="<?php echo $add_line_1; ?>"/>
                </div>
            </li>

            <li class="wpuf-el" data-label="<?php esc_attr_e( 'Address Line 2', 'wp-user-frontend' ); ?>">
                <div class="wpuf-label"><?php esc_html_e( 'Address Line 2 ', 'wp-user-frontend' ); ?></div>
                <div class="wpuf-fields">
                    <input data-required="no" type="text" class="input" name="wpuf_biiling_add_line_2" id="wpuf_biiling_add_line_2" data-type="text" value="<?php echo $add_line_2; ?>"/>
                </div>
            </li>

            <li class="wpuf-el" data-label="<?php esc_attr_e( 'City', 'wp-user-frontend' ); ?>">
                <div class="wpuf-label"><?php esc_html_e( 'City', 'wp-user-frontend' ); ?> <span
                        class="required">*</span></div>
                <div class="wpuf-fields">
                    <input data-required="yes" type="text" class="input" name="wpuf_biiling_city" id="wpuf_biiling_city" data-type="text" value="<?php echo $city; ?>"/>
                </div>
            </li>

            <li class="wpuf-el" data-label="<?php esc_attr_e( 'Postal/ZIP Code', 'wp-user-frontend' ); ?>">
                <div class="wpuf-label">
                    <?php esc_html_e( 'Postal/ZIP Code', 'wp-user-frontend' ); ?> <span
                        class="required">*</span></div>
                <div class="wpuf-fields">
                    <input data-required="yes" type="text" class="input" name="wpuf_biiling_zip_code" id="wpuf_biiling_zip_code" data-type="text" value="<?php echo $zip_code; ?>"/>
                </div>
            </li>

            <li class="wpuf-submit">
                <input type="submit" name="update_billing_address" id="wpuf-account-update-billing_address"
                       value="<?php esc_html_e( 'Update Billing Address', 'wp-user-frontend' ); ?>"/>
            </li>
        </ul>

        <div class="clear"></div>

    </div>
</form>
