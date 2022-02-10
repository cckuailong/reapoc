<?php

class WPUF_Pro_Upgrades {

    /**
     * Initialize
     */
    public function __construct() {
        if ( class_exists( 'WP_User_Frontend_Pro' ) ) {
            return;
        }

        // form fields
        add_filter( 'wpuf_field_get_js_settings', [ $this, 'add_conditional_field_prompt' ] );
        add_filter( 'wpuf-form-fields', [ $this, 'register_pro_fields' ], 10, 1 );
        // add_filter( 'wpuf-form-builder-field-settings', array( $this, 'register_pro_fields'), 10, 1 );
        add_filter( 'wpuf_field_groups_custom', [ $this, 'add_to_custom_fields' ] );
        add_filter( 'wpuf-form-fields-custom-fields', [ $this, 'add_to_custom_fields' ] );
        add_filter( 'wpuf_field_groups_others', [ $this, 'add_to_others_fields' ] );
        add_filter( 'wpuf-form-fields-others-fields', [ $this, 'add_to_others_fields' ] );
    }

    /**
     * Register pro fields
     *
     * @param array $fields
     *
     * @return array
     */
    public function register_pro_fields( $fields ) {
        if ( ! class_exists( 'WPUF_Form_Field_Pro' ) ) {
            if ( class_exists( 'WPUF_Field_Contract' ) ) {
                require_once WPUF_ROOT . '/includes/fields/class-field-pro.php';
            }
        }

        if ( class_exists( 'WPUF_Form_Field_Pro' ) ) {
            require_once WPUF_ROOT . '/includes/fields/class-pro-upgrade-fields.php';

            $fields['action_hook']             = new WPUF_Form_Field_Hook();
            $fields['address_field']           = new WPUF_Form_Field_Address();
            $fields['repeat_field']            = new WPUF_Form_Field_Repeat();
            $fields['country_list_field']      = new WPUF_Form_Field_Country();
            $fields['date_field']              = new WPUF_Form_Field_Date();
            $fields['embed']                   = new WPUF_Form_Field_Embed();
            $fields['file_upload']             = new WPUF_Form_Field_File();
            $fields['google_map']              = new WPUF_Form_Field_GMap();
            $fields['numeric_text_field']      = new WPUF_Form_Field_Numeric();
            $fields['ratings']                 = new WPUF_Form_Field_Rating();
            $fields['really_simple_captcha']   = new WPUF_Form_Field_Really_Simple_Captcha();
            $fields['shortcode']               = new WPUF_Form_Field_Shortcode();
            $fields['step_start']              = new WPUF_Form_Field_Step();
            $fields['toc']                     = new WPUF_Form_Field_Toc();
            $fields['math_captcha']            = new WPUF_Form_Field_Math_Captcha();
            $fields['qr_code']                 = new WPUF_Form_Field_QR_Code();
        }

        return $fields;
    }

    /**
     * Register fields to custom field section
     *
     * @param array $fields
     */
    public function add_to_custom_fields( $fields ) {
        $pro_fields = [
            'repeat_field',
            'date_field',
            'file_upload',
            'country_list_field',
            'numeric_text_field',
            'address_field',
            'google_map',
            'step_start',
        ];

        return array_merge( $fields, $pro_fields );
    }

    /**
     * Register fields to others field section
     *
     * @param array $fields
     */
    public function add_to_others_fields( $fields ) {
        $pro_fields = [
            'shortcode',
            'action_hook',
            'toc',
            'ratings',
            'embed',
            'really_simple_captcha',
            'math_captcha',
            'qr_code',
        ];

        return array_merge( $fields, $pro_fields );
    }

    /**
     * Add conditional logic prompt
     *
     * @param array $settings
     */
    public function add_conditional_field_prompt( $settings ) {
        $settings['settings'][] = [
            'name'           => 'wpuf_cond',
            'title'          => __( 'Conditional Logic', 'wp-user-frontend' ),
            'type'           => 'option-pro-feature-alert',
            'section'        => 'advanced',
            'priority'       => 30,
            'help_text'      => '',
            'is_pro_feature' => true,
        ];

        return $settings;
    }
}
