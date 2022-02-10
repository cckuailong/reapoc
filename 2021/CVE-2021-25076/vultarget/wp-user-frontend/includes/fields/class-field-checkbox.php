<?php

/**
 * Checkbox Field Class
 */
class WPUF_Form_Field_Checkbox extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Checkbox', 'wp-user-frontend' );
        $this->input_type = 'checkbox_field';
        $this->icon       = 'check-square-o';
    }

    /**
     * Render the Text field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        $selected = !empty( $field_settings['selected'] ) ? $field_settings['selected'] : [];

        if ( isset( $post_id ) && $post_id != '0'  ) {
            if ( $this->is_meta( $field_settings ) ) {
                if ( $value = $this->get_meta( $post_id, $field_settings['name'], $type, true ) ) {
                    $selected = $this->get_formatted_value( $value );
                } else {
                }
            }
        }
        $this->field_print_label( $field_settings, $form_id ); ?>

        <div class="wpuf-fields" data-required="<?php echo esc_attr( $field_settings['required'] ); ?>" data-type="radio">

            <?php
            if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                foreach ( $field_settings['options'] as $value => $option ) {
                    ?>
                    <label <?php echo esc_attr( $field_settings['inline'] ) == 'yes' ? 'class="wpuf-checkbox-inline"' : 'class="wpuf-checkbox-block"'; ?>>
                        <input type="checkbox" class="<?php echo 'wpuf_' . esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>" name="<?php echo esc_attr( $field_settings['name'] ); ?>[]" value="<?php echo esc_attr( $value ); ?>"<?php echo in_array( $value, $selected ) ? ' checked="checked"' : ''; ?> />
                        <?php echo $option; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </label>
                    <?php
                }
            } ?>

            <?php $this->help_text( $field_settings ); ?>

        </div>

        <?php $this->after_field_print_label();
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options  = $this->get_default_option_settings();

        $dropdown_options = [
            $this->get_default_option_dropdown_settings( true ),

            [
                'name'          => 'inline',
                'title'         => __( 'Show in inline list', 'wp-user-frontend' ),
                'type'          => 'radio',
                'options'       => [
                    'yes'   => __( 'Yes', 'wp-user-frontend' ),
                    'no'    => __( 'No', 'wp-user-frontend' ),
                ],
                'default'       => 'no',
                'inline'        => true,
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'Show this option in an inline list', 'wp-user-frontend' ),
            ],
        ];

        return array_merge( $default_options, $dropdown_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();

        $props    = [
            'input_type'        => 'checkbox',
            'is_meta'           => 'yes',
            'selected'          => [],
            'inline'            => 'no',
            'options'           => [ 'Option' => __( 'Option', 'wp-user-frontend' ) ],
            'id'                => 0,
            'is_new'            => true,
            'show_in_post'      => 'yes',
            'hide_field_label'  => 'no',
        ];

        return array_merge( $defaults, $props );
    }

    /**
     * Prepare entry
     *
     * @param $field
     *
     * @return mixed
     */
    public function prepare_entry( $field ) {
        check_ajax_referer( 'wpuf_form_add' );

        $field_name = isset( $_POST[$field['name']] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[$field['name']] ) ) : [];

        $entry_value  = ( is_array( $field_name ) && $field_name ) ? $field_name : [];

        if ( $entry_value ) {
            $new_val = [];

            foreach ( $entry_value as $option_key ) {
                $new_val[] = isset( $field['options'][$option_key] ) ? $field['options'][$option_key] : '';
            }

            $entry_value = implode( '|', $new_val );
        } else {
            $entry_value = '';
        }

        return $entry_value;
    }

    /**
     * Get field formatted value
     *
     * @since 3.2.0
     *
     * @param mixed $value
     *
     * @return array
     */
    public function get_formatted_value( $value ) {
        $formatted_value = [];

        if ( is_serialized( $value ) ) {
            $formatted_value = maybe_unserialize( $value );
        } elseif ( is_array( $value ) ) {
            $formatted_value = $value;
        } else {
            $formatted_value = [];
            $options         = explode( '|', $value );

            foreach ( $options as $option ) {
                array_push( $formatted_value, trim( $option ) );
            }
        }

        return $formatted_value;
    }

    /**
     * Render checkbox field data
     *
     * @since 3.3.0
     *
     * @param mixed $data
     * @param array $field
     *
     * @return string
     */
    public function render_field_data( $data, $field ) {
        if ( empty( $field['options'] ) || ! is_array( $field['options'] ) ) {
            return;
        }

        $data     = is_array( $data ) ? array_pop( $data ) : $data;
        $data     = explode( '|' , $data );
        $selected = [];

        foreach ( $data as $item ) {
            $item = trim( $item );

            if ( isset( $field['options'][ $item ] ) ) {
                $selected[] = $field['options'][ $item ];
            }
        }

        $hide_label = isset( $field['hide_field_label'] )
            ? wpuf_validate_boolean( $field['hide_field_label'] )
            : false;

        $data = implode( ' | ' , $selected );

        if ( empty( $data ) ) {
            return '';
        }

        $container_classnames = [ 'wpuf-field-data', 'wpuf-field-data-' . $this->input_type ];

        ob_start();
        ?>
            <li class="<?php echo esc_attr( implode( ' ' , $container_classnames ) );  ?>">
                <?php if ( ! $hide_label ): ?>
                    <label><?php echo esc_html( $field['label'] ); ?>:</label>
                <?php endif; ?>
                <?php echo esc_html( $data ); ?>
            </li>
        <?php
        return ob_get_clean();
    }
}
