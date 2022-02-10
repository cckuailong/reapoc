<?php

/**
 * Form field manager class
 *
 * @since 1.1.0
 */
class WPUF_Field_Manager {

    /**
     * The fields
     *
     * @var array
     */
    private $fields = [];

    /**
     * WP post types
     *
     * @var string
     */
    private $wp_post_types = [];

    /**
     * Get all the registered fields
     *
     * @return array
     */
    public function get_fields() {
        if ( !empty( $this->fields ) ) {
            return $this->fields;
        }

        $this->register_field_types();

        return $this->fields;
    }

    /**
     * Get field type class instance
     *
     * @since 3.2.0
     *
     * @param string $field_type
     *
     * @return \WPUF_Field_Contract
     */
    public function get_field( $field_type ) {
        $fields = $this->get_fields();

        if ( isset( $field_type, $fields ) ) {
            return $fields[ $field_type ];
        }
    }

    /**
     * Add Post Field Type
     *
     * @since 2.5
     *
     * @return void
     */
    public function add_field_settings( $field_settings ) {
        if ( class_exists( 'WPUF_Field_Contract' ) ) {
            require_once WPUF_ROOT . '/includes/fields/class-field-post-title.php';
            require_once WPUF_ROOT . '/includes/fields/class-field-post-content.php';
            require_once WPUF_ROOT . '/includes/fields/class-field-post-tags.php';
            require_once WPUF_ROOT . '/includes/fields/class-field-post-excerpt.php';
            require_once WPUF_ROOT . '/includes/fields/class-field-post-taxonomy.php';
            require_once WPUF_ROOT . '/includes/fields/class-field-featured-image.php';

            $field_settings['post_title']     = new WPUF_Form_Field_Post_Title();
            $field_settings['post_content']   = new WPUF_Form_Field_Post_Content();
            $field_settings['post_excerpt']   = new WPUF_Form_Field_Post_Excerpt();
            $field_settings['featured_image'] = new WPUF_Form_Field_Featured_Image();

            $taxonomy_templates = [];

            foreach ( $this->wp_post_types as $post_type => $taxonomies ) {
                if ( !empty( $taxonomies ) ) {
                    foreach ( $taxonomies as $tax_name => $taxonomy ) {
                        if ( 'post_tag' === $tax_name ) {
                            // $taxonomy_templates['post_tag'] = self::post_tags();
                            $taxonomy_templates['post_tags'] = new WPUF_Form_Field_Post_Tags();
                        } else {
                            // $taxonomy_templates[ $tax_name ] = self::taxonomy_template( $tax_name, $taxonomy );
                            // $taxonomy_templates[ 'taxonomy' ] = new WPUF_Form_Field_Post_Taxonomy($tax_name, $taxonomy);
                            $taxonomy_templates[ $tax_name ] = new WPUF_Form_Field_Post_Taxonomy( $tax_name, $taxonomy );
                        }
                    }
                }
            }

            $field_settings = array_merge( $field_settings, $taxonomy_templates );
        }

        return $field_settings;
    }

    /**
     * Register the field types
     *
     * @return void
     */
    private function register_field_types() {
        require_once __DIR__ . '/fields/class-abstract-fields.php';
        require_once __DIR__ . '/fields/class-field-post-title.php';
        require_once __DIR__ . '/fields/class-field-post-tags.php';
        require_once __DIR__ . '/fields/class-field-post-taxonomy.php';
        require_once __DIR__ . '/fields/class-field-post-content.php';
        require_once __DIR__ . '/fields/class-field-text.php';
        require_once __DIR__ . '/fields/class-field-email.php';
        require_once __DIR__ . '/fields/class-field-textarea.php';
        require_once __DIR__ . '/fields/class-field-checkbox.php';
        require_once __DIR__ . '/fields/class-field-radio.php';
        require_once __DIR__ . '/fields/class-field-dropdown.php';
        require_once __DIR__ . '/fields/class-field-multidropdown.php';
        require_once __DIR__ . '/fields/class-field-url.php';
        require_once __DIR__ . '/fields/class-field-column.php';
        require_once __DIR__ . '/fields/class-field-sectionbreak.php';
        require_once __DIR__ . '/fields/class-field-html.php';
        require_once __DIR__ . '/fields/class-field-hidden.php';
        require_once __DIR__ . '/fields/class-field-image.php';
        require_once __DIR__ . '/fields/class-field-recaptcha.php';
        require_once __DIR__ . '/fields/class-field-featured-image.php';

        $fields = [
            'post_title'          => new WPUF_Form_Field_Post_Title(),
            'post_content'        => new WPUF_Form_Field_Post_Content(),
            'post_tags'           => new WPUF_Form_Field_Post_Tags(),
            'taxonomy'            => new WPUF_Form_Field_Post_Taxonomy( 'category', 'category' ),
            'text_field'          => new WPUF_Form_Field_Text(),
            'email_address'       => new WPUF_Form_Field_Email(),
            'textarea_field'      => new WPUF_Form_Field_Textarea(),
            'radio_field'         => new WPUF_Form_Field_Radio(),
            'checkbox_field'      => new WPUF_Form_Field_Checkbox(),
            'dropdown_field'      => new WPUF_Form_Field_Dropdown(),
            'multiple_select'     => new WPUF_Form_Field_MultiDropdown(),
            'website_url'         => new WPUF_Form_Field_URL(),
            'column_field'        => new WPUF_Form_Field_Column(),
            'section_break'       => new WPUF_Form_Field_SectionBreak(),
            'custom_html'         => new WPUF_Form_Field_HTML(),
            'custom_hidden_field' => new WPUF_Form_Field_Hidden(),
            'image_upload'        => new WPUF_Form_Field_Image(),
            'recaptcha'           => new WPUF_Form_Field_reCaptcha(),
            'featured_image'      => new WPUF_Form_Field_Featured_Image(),
        ];

        $this->fields = apply_filters( 'wpuf-form-fields', $fields );
    }

    /**
     * Get field groups
     *
     * @return array
     */
    public function get_field_groups() {
        $before_custom_fields = apply_filters( 'wpuf-form-fields-section-before', [] );
        $groups               = array_merge( $before_custom_fields, $this->get_custom_fields() );
        $groups               = array_merge( $groups, $this->get_others_fields() );
        $after_custom_fields  = apply_filters( 'wpuf-form-fields-section-after', [] );
        $groups               = array_merge( $groups, $after_custom_fields );

        return $groups;
    }

    /**
     * Custom field section
     *
     * @since 2.5
     *
     * @return array
     */
    private function get_custom_fields() {

        // $fields = apply_filters( 'wpuf-form-builder-fields-custom-fields', array(
        $fields = apply_filters( 'wpuf-form-fields-custom-fields', [
            'text_field', 'textarea_field', 'dropdown_field', 'multiple_select',
            'radio_field', 'checkbox_field', 'website_url', 'email_address',
            'custom_hidden_field', 'image_upload',
        ] );

        return [
            [
                'title'     => __( 'Custom Fields', 'wp-user-frontend' ),
                'id'        => 'custom-fields',
                'fields'    => $fields,
            ],
        ];
    }

    /**
     * Others field section
     *
     * @since 2.5
     *
     * @return array
     */
    private function get_others_fields() {
        $fields = apply_filters( 'wpuf-form-fields-others-fields', [
            'column_field', 'section_break', 'custom_html', 'recaptcha',
        ] );

        return [
            [
                'title'     => __( 'Others', 'wp-user-frontend' ),
                'id'        => 'others',
                'fields'    => $fields,
            ],
        ];
    }

    /**
     * Get fields JS setting for the form builder
     *
     * @return array
     */
    public function get_js_settings() {
        $fields   = $this->get_fields();

        $js_array = [];

        if ( $fields ) {
            foreach ( $fields as $type => $object ) {
                if ( is_object( $object ) ) {
                    $js_array[ $type ] = $object->get_js_settings();
                }
            }
        }

        return $js_array;
    }

    /**
     * Render the form fields
     *
     * @param array  $fields
     * @param int    $form_id
     * @param array  $atts
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render_fields( $fields, $form_id, $atts = [], $type = 'post', $post_id = null ) {
        if ( !$fields ) {
            return;
        }

        $fields = apply_filters( 'wpuf_render_fields', $fields, $form_id );

        foreach ( $fields as $field ) {
            if ( !$field_object = $this->field_exists( $field['template'] ) ) {
                if ( defined( 'WP_DEBUG' && WP_DEBUG ) ) {
                    echo wp_kses_post( '<h4 style="color: red;"><em>' . $field['template'] . '</em> field not found.</h4>' );
                }

                continue;
            }

            if ( $this->check_field_visibility( $field ) ) {
                if ( is_object( $field_object ) ) {
                    $field_object->render( $field, $form_id, $type, $post_id );
                    $field_object->conditional_logic( $field, $form_id );
                }
            }
        }
    }

    /**
     * Check field  Visibility
     *
     * @param array $form_field
     *
     * @return bool
     **/
    public function check_field_visibility( &$form_field ) {
        $show_field = true;

        // check field visibility options
        if ( array_key_exists( 'wpuf_visibility', $form_field ) ) {
            $visibility_selected = $form_field['wpuf_visibility']['selected'];
            $visibility_choices  = $form_field['wpuf_visibility']['choices'];
            $show_field          = false;

            if ( $visibility_selected == 'everyone' ) {
                $show_field = true;
            }

            if ( $visibility_selected == 'hidden' ) {
                $form_field['css'] .= 'wpuf_hidden_field';
                $show_field = true;
            }

            if ( $visibility_selected == 'logged_in' && is_user_logged_in() ) {
                if ( empty( $visibility_choices ) ) {
                    $show_field = true;
                } else {
                    foreach ( $visibility_choices as $key => $choice ) {
                        if ( current_user_can( $choice ) ) {
                            $show_field = true;
                            break;
                        }
                        continue;
                    }
                }
            }

            if ( $visibility_selected == 'subscribed_users' && is_user_logged_in() ) {
                $user_pack  = WPUF_Subscription::init()->get_user_pack( get_current_user_id() );

                if ( empty( $visibility_choices ) && !empty( $user_pack ) ) {
                    $show_field = true;
                } elseif ( !empty( $user_pack ) && !empty( $visibility_choices ) ) {
                    foreach ( $visibility_choices as $pack => $id ) {
                        if ( $user_pack['pack_id'] == $id ) {
                            $show_field = true;
                            break;
                        }
                        continue;
                    }
                }
            }
        }

        return $show_field;
    }

    /**
     * Check if a field exists
     *
     * @param string $field_type
     *
     * @return bool
     */
    public function field_exists( $field_type ) {
        if ( array_key_exists( $field_type, $this->get_fields() ) ) {
            return $this->fields[ $field_type ];
        }

        return false;
    }
}
