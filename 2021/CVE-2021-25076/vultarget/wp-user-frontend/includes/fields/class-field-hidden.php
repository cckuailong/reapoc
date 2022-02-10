<?php

/**
 * Hidden Field Class
 */
class WPUF_Form_Field_Hidden extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Hidden Field', 'wp-user-frontend' );
        $this->input_type = 'custom_hidden_field';
        $this->icon       = 'eye-slash';
    }

    /**
     * Render the Hidden field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        ?>
        <input type="hidden" name="<?php echo esc_attr( $field_settings['name'] ); ?>" value="<?php echo esc_attr( $field_settings['meta_value'] ); ?>">
        <?php
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $settings = [
            [
                'name'      => 'name',
                'title'     => __( 'Meta Key', 'wp-user-frontend' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 10,
                'help_text' => __( 'Name of the meta key this field will save to', 'wp-user-frontend' ),
            ],

            [
                'name'      => 'meta_value',
                'title'     => __( 'Meta Value', 'wp-user-frontend' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Enter the meta value', 'wp-user-frontend' ),
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
        $defaults = $this->default_attributes();

        $props = [
            'input_type'        => 'hidden',
            'template'          => $this->get_type(),
            'name'              => '',
            'meta_value'        => '',
            'is_meta'           => 'yes',
            'id'                => 0,
            'is_new'            => true,
            'wpuf_cond'         => null,
        ];

        return array_merge( $defaults, $props );
    }
}
