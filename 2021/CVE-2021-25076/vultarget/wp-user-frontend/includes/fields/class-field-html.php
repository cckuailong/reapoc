<?php

/**
 * Html Field Class
 */
class WPUF_Form_Field_HTML extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Custom HTML', 'wp-user-frontend' );
        $this->input_type = 'custom_html';
        $this->icon       = 'code';
    }

    /**
     * Render the html field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        $field_settings['name'] = isset( $field_settings['name'] ) ? $field_settings['name'] : $this->input_type . '_' . $field_settings['id'];
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>

            <div class="wpuf-fields <?php echo 'html_' . esc_attr( $form_id ); ?><?php echo ' wpuf_' . esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>">
                <?php echo $field_settings['html']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
            </div>

        </li>

        <?php
    }

    /**
     * It's a full width block
     *
     * @return bool
     */
    public function is_full_width() {
        return true;
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $settings = [
            [
                'name'      => 'html',
                'title'     => __( 'Html Codes', 'wp-user-frontend' ),
                'type'      => 'textarea',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Paste your HTML codes, WordPress shortcodes will also work here', 'wp-user-frontend' ),
            ],
        ];

        return $settings;
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $props = [
            'input_type'        => 'html',
            'template'          => $this->get_type(),
            'label'             => $this->get_name(),
            'html'              => sprintf( '%s', __( 'HTML Section', 'wp-user-frontend' ) ),
            'id'                => 0,
            'is_new'            => true,
            'wpuf_cond'         => null,
        ];

        return $props;
    }
}
