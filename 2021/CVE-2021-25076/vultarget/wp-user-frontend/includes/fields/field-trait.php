<?php

trait WPUF_Form_Field_Post_trait {

    /**
     * Prints HTML5 required attribute
     *
     * @param array $attr
     *
     * @return string
     */
    public function required_html5( $attr ) {
        if ( $attr['required'] == 'yes' ) {
            // echo ' required="required"';
        }
    }

    /**
     * Print required class name
     *
     * @param array $attr
     *
     * @return string
     */
    public function required_class( $attr ) {
        if ( $attr['required'] == 'yes' ) {
            echo ' required';
        }
    }

    /**
     * Prints required field asterisk
     *
     * @param array $attr
     *
     * @return string
     */
    public function required_mark( $attr ) {
        if ( isset( $attr['required'] ) && $attr['required'] == 'yes' ) {
            return ' <span class="required">*</span>';
        }
    }
}
