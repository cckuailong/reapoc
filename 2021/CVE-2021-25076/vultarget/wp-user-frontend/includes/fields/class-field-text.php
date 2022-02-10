<?php

/**
 * Text Field Class
 */
class WPUF_Form_Field_Text extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Text', 'wp-user-frontend' );
        $this->input_type = 'text_field';
        $this->icon       = 'text-width';
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
        if ( isset( $post_id ) && $post_id != '0' ) {
            if ( $this->is_meta( $field_settings ) ) {
                $value = $this->get_meta( $post_id, $field_settings['name'], $type );
            }
        } else {
            $value = $field_settings['default'];
        }

        $this->field_print_label( $field_settings, $form_id ); ?>

            <div class="wpuf-fields">
                <input
                    class="textfield <?php echo esc_attr( 'wpuf_' . $field_settings['name'] . '_' . $form_id ); ?>"
                    id="<?php echo esc_attr( $field_settings['name'] . '_' . $form_id ); ?>"
                    type="text"
                    data-required="<?php echo esc_attr( $field_settings['required'] ); ?>"
                    data-type="text" name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                    placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"
                    size="<?php echo esc_attr( $field_settings['size'] ); ?>"
                />

                <span class="wpuf-wordlimit-message wpuf-help"></span>
                <?php $this->help_text( $field_settings ); ?>
            </div>

            <?php
            if ( isset( $field_settings['content_restriction'] ) && $field_settings['content_restriction'] ) {
                $this->check_content_restriction_func(
                    $field_settings['content_restriction'],
                    'no',
                    $field_settings['name'] . '_' . $form_id,
                    $field_settings['restriction_type'],
                    $field_settings['restriction_to']
                );
            }

            $mask_option = isset( $field_settings['mask_options'] ) ? $field_settings['mask_options'] : '';

            if ( $mask_option ) {
                ?>
                <script>
                    jQuery(document).ready(function($) {
                        var text_field = $( "input[name*=<?php echo esc_attr( $field_settings['name'] ); ?>]" );
                        switch ( '<?php echo esc_attr( $mask_option ); ?>' ) {
                            case 'us_phone':
                                text_field.mask('(999) 999-9999');
                                break;
                            case 'date':
                                text_field.mask('99/99/9999');
                                break;
                            case 'tax_id':
                                text_field.mask('99-9999999');
                                break;
                            case 'ssn':
                                text_field.mask('999-99-9999');
                                break;
                            case 'zip':
                                text_field.mask('99999');
                                break;
                            default:
                                break;
                        }
                    });
                </script>

                <?php
            }
            ?>

        </li>

        <?php
        $this->after_field_print_label();
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options = $this->get_default_option_settings();

        $default_text_options = $this->get_default_text_option_settings( true );

        $text_options = array_merge( $default_options, $default_text_options );

        return apply_filters( 'wpuf_text_field_option_settings', $text_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();

        $props = [
            'input_type'        => 'text',
            'label'             => __( 'Text', 'wp-user-frontend' ),
            'is_meta'           => 'yes',
            'size'              => 40,
            'id'                => 0,
            'is_new'            => true,
            'show_in_post'      => 'yes',
            'hide_field_label'  => 'no',
            'restriction_type'  => 'character',
            'restriction_to'    => 'max',
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

        $value = isset( $_POST[ $field['name'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) ) : '';
        // return sanitize_text_field( trim( $_POST[$field['name']] ) );
        return $value;
    }

    /**
     * Render text field data
     *
     * @since 3.3.0
     *
     * @param mixed $data
     * @param array $field
     *
     * @return string
     */
    public function render_field_data( $data, $field ) {
        $data      = implode( ',', $data );
        $hide_label = isset( $field['hide_field_label'] )
            ? wpuf_validate_boolean( $field['hide_field_label'] )
            : false;

        if ( empty( $data ) ) {
            return '';
        }

        $container_classnames = [ 'wpuf-field-data', 'wpuf-field-data-' . $this->input_type ];

        ob_start();
        ?>
            <li class="<?php echo esc_attr( implode( ' ', $container_classnames ) ); ?>">
                <?php if ( ! $hide_label ) : ?>
                    <label><?php echo esc_html( $field['label'] ); ?>:</label>
                <?php endif; ?>
                <?php echo esc_html( $data ); ?>
            </li>
        <?php
        return ob_get_clean();
    }
}
