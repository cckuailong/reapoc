<?php

// Numeric Field Class
class WPUF_Form_Field_Numeric extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Numeric Field', 'wp-user-frontend' );
        $this->input_type = 'numeric_text_field';
        $this->icon       = 'hashtag';
    }

    /**
     * Check if it's a pro feature
     *
     * @return bool
     */
    public function is_pro() {
        return true;
    }

    public function render( $field_settings, $form_id, $post_id=null, $user_id=null ) {
    }

    public function get_options_settings() {
    }

    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [];

        return array_merge( $defaults, $props );
    }
}
