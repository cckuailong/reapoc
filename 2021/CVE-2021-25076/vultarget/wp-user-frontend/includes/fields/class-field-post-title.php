<?php

class WPUF_Form_Field_Post_Title extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Post Title', 'wp-user-frontend' );
        $this->input_type = 'post_title';
        $this->icon       = 'header';
    }

    /**
     * Render the PostTitle field
     *
     * @param array $field_settings
     * @param int   $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        if ( isset( $post_id ) ) {
            $value = get_post_field( $field_settings['name'], $post_id );
        } else {
            $value = $field_settings['default'];
        } ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <div class="wpuf-fields">
                <input
                    class="textfield <?php echo esc_attr( 'wpuf_' . $field_settings['name'] . '_' . $form_id ); ?>"
                    id="<?php echo esc_attr( $field_settings['name'] . '_' . $form_id ); ?>"
                    type="text"
                    data-duplicate="<?php // echo $field_settings['duplicate'] ? $field_settings['duplicate'] : 'no'; ?>"
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
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings( false, [ 'dynamic' ] );
        $default_text_options = $this->get_default_text_option_settings( true );

        return array_merge( $default_options, $default_text_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'input_type'        => 'text',
            'is_meta'           => 'no',
            'name'              => 'post_title',
            'width'             => 'large',
            'size'              => 40,
            'id'                => 0,
            'is_new'            => true,
            'restriction_type' => 'character',
            'restriction_to'   => 'max',
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

        $field = isset( $_POST[ $field['name'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) ) : '';
        return $field;
    }
}
