<?php

class WPUF_Form_Field_Pro extends WPUF_Field_Contract {

    /**
     * Render the text field
     *
     * @param array $field_settings
     * @param int   $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $post_id = null, $user_id = null ) {
        if ( current_user_can( 'manage_options' ) ) {
            esc_html( __( $field_settings['template'] . '  is a premium field. To use this field you need to upgrade to the premium version.', 'wp-user-frontend' ) );
            echo wp_kses_post( '<br/>' );
        }
    }

    /**
     * Check if it's a pro feature
     *
     * @return bool
     */
    public function is_pro() {
        return true;
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        return __return_empty_array();
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        return __return_empty_array();
    }
}
