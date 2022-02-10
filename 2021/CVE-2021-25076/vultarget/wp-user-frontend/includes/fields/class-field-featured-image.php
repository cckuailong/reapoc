<?php

/**
 * Feature Image Field Class
 */
class WPUF_Form_Field_Featured_Image extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Featured Image', 'wp-user-frontend' );
        $this->input_type = 'featured_image';
        $this->icon       = 'file-image-o';
    }

    /**
     * Render the Feature Image field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        $has_featured_image = false;

        $unique_id = sprintf( '%s-%d', $field_settings['name'], $form_id );

        if ( isset( $post_id ) ) {

            // it's a featured image then
            $thumb_id = get_post_thumbnail_id( $post_id );

            if ( $thumb_id ) {
                $featured_image    = WPUF_Upload::attach_html( $thumb_id, 'featured_image' );
                $has_featured_image=true;
            }
        }

        $this->field_print_label( $field_settings, $form_id );

        // Check for the existance of 'button_label' and fallback to label if it is not found
        $label = array_key_exists( 'button_label', $field_settings ) ? $field_settings['button_label'] : $field_settings['label']; ?>

        <div class="wpuf-fields">
            <div id="wpuf-<?php echo esc_attr( $unique_id ); ?>-upload-container">
                <div class="wpuf-attachment-upload-filelist" data-type="file" data-required="<?php echo esc_attr( $field_settings['required'] ); ?>">
                    <a id="wpuf-<?php echo esc_attr( $unique_id ); ?>-pickfiles" data-form_id="<?php echo esc_attr( $form_id ); ?>" class="button file-selector <?php echo ' wpuf_' . esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>" href="#"><?php echo esc_html( $label ); ?></a>

                    <ul class="wpuf-attachment-list thumbnails">

                        <?php
                            if ( $has_featured_image ) {
                                echo wp_kses( $featured_image, [
                                    'li' => [
                                        'class' => []
                                    ],
                                    'div' => [
                                        'class' => []
                                    ],
                                    'img' => [
                                        'src' => [],
                                        'alt' => [],
                                    ],
                                    'input' => [
                                        'type' => [],
                                        'name' => [],
                                        'value' => [],
                                    ],
                                    'a' => [
                                        'href' => [],
                                        'class' => [],
                                        'data-attach-id' => [],
                                    ],
                                    'span' => [
                                        'class' => []
                                    ]
                                ]);
                            } ?>

                    </ul>
                </div>
            </div><!-- .container -->

            <?php $this->help_text( $field_settings ); ?>

        </div> <!-- .wpuf-fields -->

        <script type="text/javascript">
            ;(function($) {
                $(document).ready( function(){
                    var uploader = new WPUF_Uploader('wpuf-<?php echo esc_attr( $unique_id ); ?>-pickfiles', 'wpuf-<?php echo esc_attr( $unique_id ); ?>-upload-container', <?php echo esc_attr($field_settings['count'] ); ?>, '<?php echo esc_attr( $field_settings['name'] ); ?>', 'jpg,jpeg,gif,png,bmp', <?php echo esc_attr( $field_settings['max_size'] ); ?>);
                    wpuf_plupload_items.push(uploader);
                });
            })(jQuery);
        </script>

        <?php $this->after_field_print_label();
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings( false, ['dynamic'] ); // exclude dynamic

        $settings = [
            [
                'name'          => 'max_size',
                'title'         => __( 'Max. file size', 'wp-user-frontend' ),
                'type'          => 'text',
                'section'       => 'advanced',
                'priority'      => 20,
                'help_text'     => __( 'Enter maximum upload size limit in KB', 'wp-user-frontend' ),
            ],
            [
                'name'          => 'button_label',
                'title'         => __( 'Button Label', 'wp-user-frontend' ),
                'type'          => 'text',
                'default'       => __( 'Select Image', 'wp-user-frontend' ),
                'section'       => 'basic',
                'priority'      => 22,
                'help_text'     => __( 'Enter a label for the Select button', 'wp-user-frontend' ),
            ],
        ];

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
            'input_type'        => 'image_upload',
            'max_size'          => '1024',
            'button_label'      => __( 'Featured Image', 'wp-user-frontend' ),
            'is_meta'           => 'no',
            'name'              => 'featured_image',
            'count'             => '1',
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
     * @return @return mixed
     */
    public function prepare_entry( $field ) {
        check_ajax_referer( 'wpuf_form_add' );

        $wpuf_files = isset( $_POST['wpuf_files'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wpuf_files'] ) ) : [];

        return isset( $wpuf_files[$field['name']] ) ? $wpuf_files[$field['name']] : [];
    }
}
