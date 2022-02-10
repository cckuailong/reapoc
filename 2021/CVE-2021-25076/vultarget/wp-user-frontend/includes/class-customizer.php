<?php
/**
 * WPUF_Customizer_Options class
 *
 * @since 2.8.9
 */
class WPUF_Customizer_Options {

    /**
     * Class constructor
     */
    public function __construct() {
        add_action( 'customize_register', [ $this, 'customizer_options' ] );
        add_action( 'wp_head', [ $this, 'save_customizer_options' ] );
    }

    public function save_customizer_options() {
        $address_options = [];

        $fields = [
            'show_address'  => __( 'Show Billing Address', 'wp-user-frontend' ),
            'country'       => __( 'Country', 'wp-user-frontend' ),
            'state'         => __( 'State/Province/Region', 'wp-user-frontend' ),
            'address_1'     => __( 'Address line 1', 'wp-user-frontend' ),
            'address_2'     => __( 'Address line 2', 'wp-user-frontend' ),
            'city'          => __( 'City', 'wp-user-frontend' ),
            'zip'           => __( 'Postal Code/ZIP', 'wp-user-frontend' ),
        ];

        foreach ( $fields as $field => $label ) {
            $settings_name           = 'wpuf_address_' . $field . '_settings';
            $address_options[$field] = get_theme_mod( $settings_name );
        }

        update_option( 'wpuf_address_options', $address_options ); ?>
        <style>

        </style>
        <?php
    }

    public function customizer_options( $wp_customize ) {

        /* Add WPUF Panel to Customizer */

        $wp_customize->add_panel( 'wpuf_panel', [
            'title'					    => __( 'WP User Frontend', 'wp-user-frontend' ),
            'description'			=> __( 'Customize WPUF Settings', 'wp-user-frontend' ),
            'priority'				  => 25,
        ] );

        /* WPUF Billing Address Customizer */
        $wp_customize->add_section(
            'wpuf_billing_address',
            [
                'title'       => __( 'Billing Address', 'wp-user-frontend' ),
                'priority'    => 20,
                'panel'       => 'wpuf_panel',
                'description' => __( 'These options let you change the appearance of the billing address.', 'wp-user-frontend' ),
            ]
         );

        // Billing Address field controls.
        $fields = [
            'show_address'  => __( 'Show Billing Address', 'wp-user-frontend' ),
            'country'       => __( 'Country', 'wp-user-frontend' ),
            'state'         => __( 'State/Province/Region', 'wp-user-frontend' ),
            'address_1'     => __( 'Address line 1', 'wp-user-frontend' ),
            'address_2'     => __( 'Address line 2', 'wp-user-frontend' ),
            'city'          => __( 'City', 'wp-user-frontend' ),
            'zip'           => __( 'Postal Code/ZIP', 'wp-user-frontend' ),
        ];

        foreach ( $fields as $field => $label ) {
            $wp_customize->add_setting(
                'wpuf_address_' . $field . '_settings',
                [
                    'type'       => 'theme_mod',
                    'section'    => 'wpuf_billing_address',
                ]
             );

            if ( $field == 'show_address' ) {
                $wp_customize->add_control(
                    'wpuf_address_' . $field . '_control',
                    [
                        /* Translators: %s field name. */
                        'label'    => sprintf( __( '%s field', 'wp-user-frontend' ), $label ),
                        'section'  => 'wpuf_billing_address',
                        'settings' => 'wpuf_address_' . $field . '_settings',
                        'type'     => 'checkbox',
                    ]
                 );
            } else {
                $wp_customize->add_control(
                    'wpuf_address_' . $field . '_control',
                    [
                        /* Translators: %s field name. */
                        'label'    => sprintf( __( '%s field', 'wp-user-frontend' ), $label ),
                        'section'  => 'wpuf_billing_address',
                        'settings' => 'wpuf_address_' . $field . '_settings',
                        'type'     => 'select',
                        'choices'  => [
                            'hidden'   => __( 'Hidden', 'wp-user-frontend' ),
                            'optional' => __( 'Optional', 'wp-user-frontend' ),
                            'required' => __( 'Required', 'wp-user-frontend' ),
                        ],
                    ]
                 );
            }
        }
    }
}
