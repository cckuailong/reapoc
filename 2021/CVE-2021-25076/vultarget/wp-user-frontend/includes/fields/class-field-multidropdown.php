<?php

/**
 * MultiDropdown Field Class
 */
class WPUF_Form_Field_MultiDropdown extends WPUF_Form_Field_Dropdown {

    public function __construct() {
        $this->name       = __( 'Multi Select', 'wp-user-frontend' );
        $this->input_type = 'multiple_select';
        $this->icon       = 'list-ul';
    }

    /**
     * Render the MultiDropDown field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        if ( isset( $post_id ) && $post_id != '0' ) {
            $selected = $this->get_meta( $post_id, $field_settings['name'], $type );

            if ( is_serialized( $selected ) ) {
                $selected = maybe_unserialize( $selected );
            } elseif ( is_array( $selected ) ) {
                $selected = $selected;
            } else {
                $selected = explode( ' | ', $selected );
            }
        } else {
            $selected = isset( $field_settings['selected'] ) ? $field_settings['selected'] : '';

            $selected = is_array( $selected ) ? $selected : (array) $selected;
        }

        $name     = $field_settings['name'] . '[]';

        $this->field_print_label( $field_settings, $form_id ); ?>

            <?php do_action( 'WPUF_multidropdown_field_after_label', $field_settings ); ?>

            <div class="wpuf-fields">
                <select multiple="multiple" class="multiselect <?php echo 'wpuf_' . esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>" id="<?php echo esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>" name="<?php echo esc_attr( $name ); ?>" mulitple="multiple" data-required="<?php echo esc_attr( $field_settings['required'] ); ?>" data-type="multiselect">

                    <?php if ( !empty( $field_settings['first'] ) ) { ?>
                        <option value="-1"><?php echo esc_attr( $field_settings['first'] ); ?></option>
                    <?php } ?>

                    <?php
                    if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                        foreach ( $field_settings['options'] as $value => $option ) {
                            $current_select = selected( in_array( $value, $selected ), true, false ); ?>
                            <option value="<?php echo esc_attr( $value ); ?>"<?php echo esc_attr( $current_select ); ?>><?php echo esc_html( $option ); ?></option>
                            <?php
                        }
                    } ?>
                </select>
                <?php $this->help_text( $field_settings ); ?>
            </div>


        <?php $this->after_field_print_label();
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'input_type'       => 'multiselect',
            'label'            => __( 'Multi Select', 'wp-user-frontend' ),
            'is_meta'          => 'yes',
            'selected'         => [],
            'options'          => [ 'Option' => __( 'Option', 'wp-user-frontend' ) ],
            'first'            => __( '- select -', 'wp-user-frontend' ),
            'id'               => 0,
            'is_new'           => true,
            'show_in_post'     => 'yes',
            'hide_field_label' => 'no',
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
            $entry_value = implode( WP_User_Frontend::$field_separator, $new_val );
        } else {
            $entry_value = '';
        }

        return $entry_value;
    }

    /**
     * Render multiselect field data
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
