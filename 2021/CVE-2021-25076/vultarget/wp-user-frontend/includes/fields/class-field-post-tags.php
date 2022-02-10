<?php
//Post Tags Class
class WPUF_Form_Field_Post_Tags extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Tags', 'wp-user-frontend' );
        $this->input_type = 'post_tags';
        $this->icon       = 'text-width';
    }

    /**
     * Render the Post Tags field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        if ( isset( $post_id ) ) {
            $post_tags = wp_get_post_tags( $post_id );
            $tagsarray = [];

            foreach ( $post_tags as $tag ) {
                $tagsarray[] = $tag->name;
            }
            $value = implode( ', ', $tagsarray );
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
                    data-required="<?php echo esc_attr( $field_settings['required'] ); ?>"
                    data-type="text" name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                    placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"
                    size="<?php echo esc_attr( $field_settings['size'] ); ?>"
                />

                <span class="wpuf-wordlimit-message wpuf-help"></span>
                <?php  $this->help_text( $field_settings ); ?>
            </div>

              <script type="text/javascript">
                ;(function($) {
                    $(document).ready( function(){
                        $('li.tags input[name=tags]').suggest( wpuf_frontend.ajaxurl + '?action=wpuf-ajax-tag-search&tax=post_tag', { delay: 500, minchars: 2, multiple: true, multipleSep: ', ' } );
                    });
                })(jQuery);
            </script>



        </li>
        <?php
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings( false, ['dynamic'] );
        $settings             = $this->get_default_text_option_settings();

        return array_merge( $default_options, $settings );
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
            'name'              => 'tags',
            'width'             => 'large',
            'size'              => 40,
            'id'                => 0,
            'is_new'            => true,
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

        $field = isset( $_POST[$field['name']] ) ? sanitize_text_field( wp_unslash( $_POST[$field['name']] ) ) : '';
        return $field;
    }
}
