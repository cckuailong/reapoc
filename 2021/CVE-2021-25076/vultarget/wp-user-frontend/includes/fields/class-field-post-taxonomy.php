<?php
// Post Taxonomy Class
class WPUF_Form_Field_Post_Taxonomy extends WPUF_Field_Contract {
    use WPUF_Form_Field_Post_trait;

    protected $tax_name;

    protected $taxonomy;

    protected $terms = [];

    protected $class;

    protected $field_settings;

    protected $form_id;

    public function __construct( $tax_name, $taxonomy, $post_id = null, $user_id = null ) {
        //phpcs:ignore
        $this->name       = __( $tax_name, 'wp-user-frontend' );
        $this->input_type = 'taxonomy';
        $this->tax_name   = $tax_name;
        // $this->taxonomy=$taxonomy;
        // $this->icon       = 'caret-square-o-down';
    }

    /**
     * Render the Post Taxonomy field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        $this->field_settings = $field_settings;
        $this->form_id = $form_id; ?>

        <li <?php $this->print_list_attributes( $this->field_settings ); ?>>

        <?php

        $this->print_label( $this->field_settings, $this->form_id );

        $this->exclude_type       = isset( $this->field_settings['exclude_type'] ) ? $this->field_settings['exclude_type'] : 'exclude';
        $this->exclude            = $this->field_settings['exclude'];

        if ( $this->exclude_type === 'child_of' && ! empty( $this->exclude ) ) {
            $this->exclude = $this->exclude[0];
        }

        $this->taxonomy           = $this->field_settings['name'];
        $this->class              = ' wpuf_' . $this->field_settings['name'] . '_' . $form_id;

        $current_user = get_current_user_id();

        if ( $post_id && $this->field_settings['type'] === 'text' ) {
            $this->terms = wp_get_post_terms( $post_id, $this->taxonomy, [ 'fields' => 'names' ] );
        } elseif ( $post_id ) {
            $this->terms = wp_get_post_terms( $post_id, $this->taxonomy, [ 'fields' => 'ids' ] );
        }

        if ( ! taxonomy_exists( $this->taxonomy ) ) {
            echo wp_kses_post( '<br><div class="wpuf-message">' . __( 'This field is no longer available.', 'wp-user-frontend' ) . '</div>' );

            return;
        }

        $div_class = 'wpuf_' . $this->field_settings['name'] . '_' . $this->field_settings['type'] . '_' . $field_settings['id'] . '_' . $form_id;

        if ( $this->field_settings['type'] === 'checkbox' ) {
            ?>
            <div class="wpuf-fields <?php echo wp_kses_post( $div_class ); ?>" data-required="<?php echo esc_attr( $field_settings['required'] ); ?>" data-type="tax-checkbox">
        <?php } else { ?>
            <div class="wpuf-fields <?php echo esc_attr( $div_class ); ?>">
            <?php
        }

        switch ( $this->field_settings['type'] ) {
            case 'ajax':
                $this->tax_ajax( $post_id );
                break;

            case 'select':
                $post_id = null;
                $this->tax_select( $post_id );
                break;

            case 'multiselect':
                $post_id = null;
                $this->tax_multiselect( $post_id );
                break;

            case 'checkbox':
                wpuf_category_checklist( $post_id, false, $this->field_settings, $this->class );
                break;

            case 'text':
                $post_id = null;
                $this->tax_input( $post_id );
                break;
            default:
                // code...
                break;
        }
        ?>
        <span class="wpuf-wordlimit-message wpuf-help"></span>
        <?php
        $this->help_text( $field_settings );
    }

    public function taxnomy_select( $terms ) {
        $attr = $this->field_settings;

        $selected = $terms ? $terms : '';
        $dataset  = sprintf(
            'data-required="%s" data-type="select" data-form-id="%d"',
            $attr['required'],
            trim( $this->form_id )
        );

        $exclude = wpuf_get_field_settings_excludes( $this->field_settings, $this->exclude_type );

        $tax_args = [
            'show_option_none' => __( '-- Select --', 'wp-user-frontend' ),
            'hierarchical'     => 1,
            'hide_empty'       => 0,
            'orderby'          => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
            'order'            => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
            'name'             => $this->taxonomy . '[]',
            'taxonomy'         => $this->taxonomy,
            'echo'             => 0,
            'title_li'         => '',
            'class'            => 'cat-ajax ' . $this->taxonomy . $this->class,
            $exclude['type']   => ( $this->exclude_type === 'child_of' ) ? $exclude['childs'] : $this->exclude,
            'selected'         => $selected,
            'child_of'         => isset( $attr['parent_cat'] ) ? $attr['parent_cat'] : '',
        ];

        $tax_args = apply_filters( 'wpuf_taxonomy_checklist_args', $tax_args );
        $select = wp_dropdown_categories( $tax_args );

        echo str_replace( '<select', '<select ' . $dataset, $select ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        $attr = [
            'required'      => $attr['required'],
            'name'          => $attr['name'],
            'exclude_type' => isset( $attr['exclude_type'] ) ? $attr['exclude_type'] : 'exclude',
            // 'exclude_type' => $attr['exclude_type'],
            'exclude'      => $attr['exclude'],
            'orderby'      => $attr['orderby'],
            'order'        => $attr['order'],
           // 'last_term_id' => isset( $attr['parent_cat'] ) ? $attr['parent_cat'] : '',
            //'term_id'      => $selected
        ];
        $attr = apply_filters( 'wpuf_taxonomy_checklist_args', $attr );
        ?>
        <span data-taxonomy=<?php echo esc_attr( json_encode( $attr ) ); ?>></span>
        <?php
    }

    public function catbuildTree( $items ) {
        $childs = [];

        foreach ( $items as &$item ) {
            $childs[ $item->parent ][] = &$item;
        }

        unset( $item );

        foreach ( $items as &$item ) {
            if ( isset( $childs[ $item->term_id ] ) ) {
                $item->childs = $childs[ $item->term_id ];
            }
        }

        return reset( $childs );
    }

    public function RecursiveCatWrite( $tree ) {
        foreach ( $tree as $vals ) {
            $level = 0;
            ?>
             <div id="lvl<?php echo esc_attr( $level ); ?>" level="<?php echo esc_attr( $level ); ?>" >
                <?php $this->taxnomy_select( $vals->term_id ); ?>
            </div>

            <?php
            $this->field_settings['parent_cat'] = $vals->term_id;

            if ( isset( $vals->childs ) ) {
                $this->RecursiveCatWrite( $vals->childs );
            }
        }
    }


    public function tax_ajax( $post_id = null ) {
        $taxonomy = $this->field_settings['name'];
        if ( isset( $post_id ) ) {
            $this->terms = wp_get_post_terms( $post_id, $this->taxonomy, [ 'fields' => 'all' ] );
            asort( $this->terms );

            if ( count( $this->terms ) > 1 ) {
                $first_item = array_shift( $this->terms );
            }

            $include = [];
            foreach ( $this->field_settings['exclude'] as $parent ) {
                array_map(
                    function ( $term ) use ( &$include ) {
                        $include[] = $term->term_id;
                    }, get_terms(
                        [
                            'taxonomy' => $taxonomy,
                            'parent' => $parent,
                            'hide_empty' => false,
                        ]
                    )
                );
            }
            $this->field_settings['exclude'] = $include;
            if ( isset( $first_item ) ) {
                array_push( $this->field_settings['exclude'], $first_item->term_id );
            }
        }
        ?>

        <div class="category-wrap <?php echo esc_attr( $this->class ); ?>">

            <?php

            if ( ! count( $this->terms ) ) {
                ?>
                <div id="lvl0" level="0">
                    <?php $this->taxnomy_select( null ); ?>
                </div>
                <?php
            } else {
                if ( $this->field_settings['type'] === 'ajax' ) {
                    $level = 0;
                    ?>
                                <div id="lvl<?php echo esc_attr( $level ); ?>" level="<?php echo esc_attr( $level ); ?>" >

                    <?php

                    $attr = $this->field_settings;
                    $dataset  = sprintf(
                        'data-required="%s" data-type="select" data-form-id="%d"',
                        $attr['required'],
                        trim( $this->form_id )
                    );

                    $child_of = $this->field_settings['exclude'];
                    $selected = count( $this->terms ) !== 1 ? $child_of[ array_search( $this->terms[0]->term_id, $child_of, true ) ] : $child_of[ count( $child_of ) - 1 ];
                    $tax_args = [
                        'show_option_none' => __( '-- Select --', 'wp-user-frontend' ),
                        'hierarchical'     => 1,
                        'hide_empty'       => 0,
                        'orderby'          => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
                        'order'            => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
                        'name'             => $this->taxonomy . '[]',
                        'taxonomy'         => $this->taxonomy,
                        'echo'             => 0,
                        'title_li'         => '',
                        'class'            => 'cat-ajax ' . $this->taxonomy . $this->class,
                        'include'          => $this->field_settings['exclude'],
                        'selected'         => $selected,
                    ];

                      $select_result = wp_dropdown_categories( $tax_args );
                       echo str_replace( '<select', '<select ' . $dataset, $select_result );
                        $attr = [
                            'required'      => $attr['required'],
                            'name'          => $attr['name'],
                            'exclude_type' => isset( $attr['exclude_type'] ) ? $attr['exclude_type'] : 'exclude',
                            'exclude'      => $attr['exclude'],
                            'orderby'      => $attr['orderby'],
                            'order'        => $attr['order'],
                        ];
                        $attr = apply_filters( 'wpuf_taxonomy_checklist_args', $attr );
                        ?>
           <span data-taxonomy=<?php echo esc_attr( json_encode( $attr ) ); ?>></span>
                               </div>
                    <?php
                    $found = in_array( $this->terms[0]->parent, $this->field_settings['exclude'], true );
                    if ( count( $this->terms ) > 1 || $found === true ) {
                        foreach ( $this->terms as $term ) {
                            $include = array_map(
                                function ( $term ) use ( &$include ) {
                                    return $term->term_id;
                                }, get_terms(
                                    [
                                        'taxonomy' => $taxonomy,
                                        'parent' => $term->parent,
                                        'hide_empty' => false,
                                    ]
                                )
                            );

                             $tax_args = [

                                 'hide_empty'       => 0,
                                 'orderby'          => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
                                 'order'            => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
                                 'name'             => $this->taxonomy . '[]',
                                 'taxonomy'         => $this->taxonomy,
                                 'title_li'         => '',
                                 'class'            => 'cat-ajax ' . $this->taxonomy . $this->class,
                                 'include'          => $include,
                                 'selected'         => $term->term_id,
                             ];

                               wp_dropdown_categories( $tax_args );
                        }
                    }
                }
            }
            ?>
        </div>
        <span class="loading"></span>
        <?php
    }

    public function tax_select( $post_id = null ) {
        $attr     = $this->field_settings;
        $selected = $this->terms ? $this->terms[0] : '';
        $required = sprintf( 'data-required="%s" data-type="select"', $attr['required'] );

        $exclude = wpuf_get_field_settings_excludes( $this->field_settings, $this->exclude_type );

        $tax_args = [
            'show_option_none' => isset( $attr['first'] ) ? $attr['first'] : '--select--',
            'hierarchical'     => 1,
            'hide_empty'       => 0,
            'orderby'          => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
            'order'            => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
            'name'             => $this->taxonomy,
            'taxonomy'         => $this->taxonomy,
            'echo'             => 0,
            'title_li'         => '',
            'class'            => $this->taxonomy . $this->class,
            $exclude['type']   => ( $this->exclude_type === 'child_of' ) ? $exclude['childs'] : $this->exclude,
            'selected'         => $selected,
        ];

        $tax_args = apply_filters( 'wpuf_taxonomy_checklist_args', $tax_args );

        $select = wp_dropdown_categories( $tax_args );

        $allowed_html = [
            'option'    => [
                'value' => [],
                'selected' => [],
            ],
        ];

        echo str_replace( '<select', '<select ' . $required, $select ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

        // echo wp_kses( str_replace( '<select', '<select ' . $required, $select ), [
        //     'select' => [],
        //     'option' => [
        //         'value' => [],
        //         'selected' => []
        //     ]
        // ] );

    }

    public function tax_multiselect( $post_id = null ) {
        $attr = $this->field_settings;

        $selected = $this->terms ? $this->terms : [];

        $required = sprintf( 'data-required="%s" data-type="multiselect"', $attr['required'] );

        $walker = new WPUF_Walker_Category_Multi();

        $exclude = wpuf_get_field_settings_excludes( $this->field_settings, $this->exclude_type );

        $tax_args = [
            // 'show_option_none' => __( '-- Select --', 'wpuf' ),
            'hierarchical'   => 1,
            'hide_empty'     => 0,
            'orderby'        => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
            'order'          => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
            'name'           => $this->taxonomy . '[]',
            'id'             => 'cat-ajax',
            'taxonomy'       => $this->taxonomy,
            'echo'           => 0,
            'title_li'       => '',
            'class'          => $this->taxonomy . ' multiselect' . $this->class,
            $exclude['type'] => ( $this->exclude_type === 'child_of' ) ? $exclude['childs'] : $this->exclude,
            'selected'       => $selected,
            'walker'         => $walker,
        ];

        $tax_args = apply_filters( 'wpuf_taxonomy_checklist_args', $tax_args );
        $select   = wp_dropdown_categories( $tax_args );

        echo str_replace( '<select', '<select multiple="multiple" ' . $required, $select );  // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    public function tax_input( $post_id = null ) {
        $attr = $this->field_settings;
        $query_string = '?action=wpuf-ajax-tag-search&tax=' . $attr['name'];

        if ( 'child_of' === $this->exclude_type ) {
            $exclude = wpuf_get_field_settings_excludes( $this->field_settings, $this->exclude_type );

            $query_string .= '&term_ids=' . implode( ',', $exclude['childs'] );
        }

        ?>

        <input class="textfield<?php echo esc_attr( $this->required_class( $attr ) ); ?>" id="<?php echo esc_attr( $attr['name'] ); ?>" type="text" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>" value="<?php echo esc_attr( implode( ', ', $this->terms ) ); ?>" size="40" />

        <script type="text/javascript">
            ;(function($) {
                $(document).ready( function(){
                        $('#<?php echo esc_attr( $attr['name'] ); ?>').suggest( wpuf_frontend.ajaxurl + '<?php echo $query_string; ?>', { delay: 500, minchars: 2, multiple: true, multipleSep: ', ' } );
                });
            })(jQuery);
        </script>
        <?php
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings( false, [ 'dynamic' ] );
        $default_text_options = $this->get_default_taxonomy_option_setttings( false, $this->tax_name );

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
            'input_type'        => 'taxonomy',
            'label'             => $this->tax_name,
            'name'              => $this->tax_name,
            'is_meta'           => 'no',
            'width'             => 'small',
            'type'              => 'select',
            'first'             => __( '- select -', 'wp-user-frontend' ),
            'show_inline'       => 'inline',
            'orderby'           => 'name',
            'order'             => 'ASC',
            'exclude'           => [],
            'id'                => 0,
            'is_new'            => true,
            'restriction_type'  => 'character',
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
        // $val   = $_POST[$field['name']];
        // return isset( $field['options'][$val] ) ? $field['options'][$val] : '';
        // return sanitize_text_field($_POST[$field['name']]);
        check_ajax_referer( 'wpuf_form_add' );

        $val = isset( $_POST[ $field['name'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) ) : '';

        return isset( $field['options'][ $val ] ) ? $field['options'][ $val ] : '';
    }
}
