<?php

/**
 * Radio Field Class
 */
class WPUF_Form_Field_Radio extends WPUF_Form_Field_Checkbox {

    public function __construct() {
        $this->name       = __( 'Radio', 'wp-user-frontend' );
        $this->input_type = 'radio_field';
        $this->icon       = 'dot-circle-o';
    }

    /**
     * Render the Radio field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        $selected = isset( $field_settings['selected'] ) ? $field_settings['selected'] : '';

        if ( isset( $post_id ) && $post_id != '0'  ) {
            if ( $this->is_meta( $field_settings ) ) {
                $selected = $this->get_meta( $post_id, $field_settings['name'], $type );
            }
        }
        // else {

        //     $selected = isset( $field_settings['selected'] ) ? $field_settings['selected'] : '';
        // }

        $this->field_print_label( $field_settings, $form_id );

        do_action( 'WPUF_radio_field_after_label', $field_settings ); ?>

            <div class="wpuf-fields" data-required="<?php echo esc_attr( $field_settings['required'] ); ?>" data-type="radio">

                <?php
                if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                    foreach ( $field_settings['options'] as $value => $option ) {
                        ?>

                        <label <?php echo $field_settings['inline'] == 'yes' ? 'class="wpuf-radio-inline"' : 'class="wpuf-radio-block"'; ?>>
                            <input
                                name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                                class="<?php echo esc_attr( 'wpuf_' . $field_settings['name'] . '_' . $form_id ); ?>"
                                type="radio"
                                value="<?php echo esc_attr( $value ); ?>"<?php checked( $selected, $value ); ?>
                                ondblclick="WP_User_Frontend.doUncheckRadioBtn(this)"
                            />
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
        $default_options  = $this->get_default_option_settings( true, [ 'width' ] );
        $dropdown_options = [
            $this->get_default_option_dropdown_settings(),
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
            'input_type'       => 'radio',
            'selected'         => '',
            'inline'           => 'no',
            'options'          => [ 'Option' => __( 'Option', 'wp-user-frontend' ) ],
            'id'               => 0,
            'is_new'           => true,
            'show_in_post'     => 'yes',
            'hide_field_label' => 'no',
            'is_meta'          => 'yes',
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

        $val   = isset( $_POST[$field['name']] ) ? sanitize_text_field( wp_unslash( $_POST[$field['name']] ) ) : '';

        return isset( $field['options'][$val] ) ? $field['options'][$val] : '';
    }

    /**
     * Render radio field data
     *
     * @since 3.3.0
     *
     * @param mixed $data
     * @param array $field
     *
     * @return string
     */
    public function render_field_data( $data, $field ) {
        $data       = is_array( $data ) ? array_pop( $data ) : $data;
        $hide_label = isset( $field['hide_field_label'] )
            ? wpuf_validate_boolean( $field['hide_field_label'] )
            : false;

        $container_classnames = [ 'wpuf-field-data', 'wpuf-field-data-' . $this->input_type ];

        if ( empty( $field['options'] ) || ! is_array( $field['options'] ) || ! isset( $field['options'][ $data ] ) ) {
            return;
        }

        ob_start();
        ?>
            <li class="<?php echo esc_attr( implode( ' ' , $container_classnames ) );  ?>">
                <?php if ( ! $hide_label ): ?>
                    <label><?php echo esc_html( $field['label'] ); ?>:</label>
                <?php endif; ?>
                <?php echo esc_html( $field['options'][ $data ] ); ?>
            </li>
        <?php
        return ob_get_clean();
    }
}
