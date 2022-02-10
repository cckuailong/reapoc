<?php

class WPUF_Frontend_Render_Form {
    private static $_instance;

    public static $meta_key = 'wpuf_form';

    public static $separator = ' | ';

    public static $config_id = '_wpuf_form_id';

    private $form_condition_key = 'wpuf_cond';

    private $field_count = 0;

    public $multiform_start = 0;

    public $wp_post_types = [];

    public $form_fields = [];

    public $form_settings = [];

    /**
     * Send json error message
     *
     * @param string $error
     */
    public function send_error( $error ) {
        echo json_encode(
            [
                'success' => false,
                'error'   => $error,
            ]
        );

        die();
    }

    /**
     * Search on multi dimentional array
     *
     * @param array  $array
     * @param string $key   name of key
     * @param string $value the value to search
     *
     * @return array
     */
    public function search( $array, $key, $value ) {
        $results = [];

        if ( is_array( $array ) ) {
            if ( isset( $array[ $key ] ) && $array[ $key ] == $value ) {
                $results[] = $array;
            }

            foreach ( $array as $subarray ) {
                $results = array_merge( $results, $this->search( $subarray, $key, $value ) );
            }
        }

        return $results;
    }

    /**
     * Really simple captcha validation
     *
     * @return void
     */
    public function validate_rs_captcha() {
        $nonce = isset( $_REQUEST['wpuf-login-nonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['wpuf-login-nonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf_login_action' ) ) {
            return;
        }

        $rs_captcha_input = isset( $_POST['rs_captcha'] ) ? sanitize_text_field( wp_unslash( $_POST['rs_captcha'] ) ) : '';
        $rs_captcha_file  = isset( $_POST['rs_captcha_val'] ) ? sanitize_text_field( wp_unslash( $_POST['rs_captcha_val'] ) ) : '';

        if ( class_exists( 'ReallySimpleCaptcha' ) ) {
            $captcha_instance = new ReallySimpleCaptcha();

            if ( ! $captcha_instance->check( $rs_captcha_file, $rs_captcha_input ) ) {
                $this->send_error( __( 'Really Simple Captcha validation failed', 'wp-user-frontend' ) );
            } else {
                // validation success, remove the files
                $captcha_instance->remove( $rs_captcha_file );
            }
        }
    }

    /**
     * reCaptcha Validation
     *
     * @return void
     */
    public function validate_re_captcha( $no_captcha = '', $invisible = '' ) {
        // need to check if invisible reCaptcha need library or we can do it here.
        // ref: https://shareurcodes.com/blog/google%20invisible%20recaptcha%20integration%20with%20php
        check_ajax_referer( 'wpuf_form_add' );

        $site_key             = wpuf_get_option( 'recaptcha_public', 'wpuf_general' );
        $private_key          = wpuf_get_option( 'recaptcha_private', 'wpuf_general' );
        $remote_addr          = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $g_recaptcha_response = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

        if ( $no_captcha == 1 && 0 == $invisible ) {
            if ( ! class_exists( 'WPUF_ReCaptcha' ) ) {
                require_once WPUF_ROOT . '/lib/recaptchalib_noCaptcha.php';
            }

            $response  = null;
            $reCaptcha = new WPUF_ReCaptcha( $private_key );

            $resp = $reCaptcha->verifyResponse(
                $remote_addr,
                $g_recaptcha_response
            );

            if ( ! $resp->success ) {
                $this->send_error( __( 'noCaptcha reCAPTCHA validation failed', 'wp-user-frontend' ) );
            }
        } elseif ( $no_captcha == 0 && 0 == $invisible ) {
            $recap_challenge = isset( $_POST['recaptcha_challenge_field'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_challenge_field'] ) ) : '';
            $recap_response  = isset( $_POST['recaptcha_response_field'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_response_field'] ) ) : '';

            $resp = recaptcha_check_answer( $private_key, $remote_addr, $recap_challenge, $recap_response );

            if ( ! $resp->is_valid ) {
                $this->send_error( __( 'reCAPTCHA validation failed', 'wp-user-frontend' ) );
            }
        } elseif ( $no_captcha == 0 && 1 == $invisible ) {
            $response  = null;
            $recaptcha = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
            $object    = new Invisible_Recaptcha( $site_key, $private_key );

            $response = $object->verifyResponse( $recaptcha );

            if ( isset( $response['success'] ) and $response['success'] != true ) {
                $this->send_error( __( 'Invisible reCAPTCHA validation failed', 'wp-user-frontend' ) );
            }
        }
    }

    /**
     * render submit button
     *
     * @param [type] $form_id       [description]
     * @param [type] $form_settings [description]
     * @param [type] $post_id       [description]
     */
    public function submit_button( $form_id, $form_settings, $post_id = null ) { ?>

        <li class="wpuf-submit">
            <div class="wpuf-label">
                &nbsp;
            </div>

            <?php wp_nonce_field( 'wpuf_form_add' ); ?>
            <input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
            <input type="hidden" name="page_id" value="<?php echo get_post( $post_id ) ? esc_attr( get_the_ID() ) : '0'; ?>">
            <input type="hidden" id="del_attach" name="delete_attachments[]">
            <input type="hidden" name="action" value="wpuf_submit_post">

            <?php do_action( 'wpuf_submit_btn', $form_id, $form_settings ); ?>

            <?php
            if ( $post_id ) {
                $cur_post = get_post( $post_id );
                ?>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                <input type="hidden" name="post_date" value="<?php echo esc_attr( $cur_post->post_date ); ?>">
                <input type="hidden" name="comment_status" value="<?php echo esc_attr( $cur_post->comment_status ); ?>">
                <input type="hidden" name="post_author" value="<?php echo esc_attr( $cur_post->post_author ); ?>">
                <input type="submit" class="wpuf-submit-button wpuf_submit_<?php echo esc_attr( $form_id ); ?>" name="submit" value="<?php echo esc_attr( $form_settings['update_text'] ); ?>" />
                <?php
            } else {
                ?>
                <input type="submit" class="wpuf-submit-button wpuf_submit_<?php echo esc_attr( $form_id ); ?>" name="submit" value="<?php echo esc_attr( $form_settings['submit_text'] ); ?>" />
            <?php } ?>

            <?php if ( isset( $form_settings['draft_post'] ) && $form_settings['draft_post'] == 'true' ) { ?>
                <a href="#" class="btn" id="wpuf-post-draft"><?php esc_html_e( 'Save Draft', 'wp-user-frontend' ); ?></a>
            <?php } ?>
        </li>

        <?php
    }

    /**
     * guest post field
     *
     * @param [type] $form_settings [description]
     */
    public function guest_fields( $form_settings ) {
        ?>
        <li class="el-name">
            <div class="wpuf-label">
                <label><?php echo esc_html( $form_settings['name_label'] ); ?> <span class="required">*</span></label>
            </div>

            <div class="wpuf-fields">
                <input type="text" required="required" data-required="yes" data-type="text" name="guest_name" value="" size="40">
            </div>
        </li>

        <li class="el-email">
            <div class="wpuf-label">
                <label><?php echo esc_html( $form_settings['email_label'] ); ?> <span class="required">*</span></label>
            </div>

            <div class="wpuf-fields">
                <input type="email" required="required" data-required="yes" data-type="email" name="guest_email" value="" size="40">
            </div>
        </li>
        <?php
    }

    /**
     * Form preview handler
     *
     * @return void
     */
    public function preview_form() {
        $form_id = isset( $_GET['form_id'] ) ? intval( wp_unslash( $_GET['form_id'] ) ) : 0;

        if ( $form_id ) {
            ?>

            <!doctype html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>__( 'Form Preview', 'wp-user-frontend' )</title>
                    <link rel="stylesheet" href="<?php echo esc_url( plugins_url( 'assets/css/frontend-forms.css', __DIR__ ) ); ?>">

                    <style type="text/css">
                        body {
                            margin: 0;
                            padding: 0;
                            background: #eee;
                        }

                        .container {
                            width: 700px;
                            margin: 0 auto;
                            margin-top: 20px;
                            padding: 20px;
                            background: #fff;
                            border: 1px solid #DFDFDF;
                            -webkit-box-shadow: 1px 1px 2px rgba(0,0,0,0.1);
                            box-shadow: 1px 1px 2px rgba(0,0,0,0.1);
                        }
                    </style>

                    <script type="text/javascript" src="<?php echo esc_url( includes_url( 'js/jquery/jquery.js' ) ); ?>"></script>
                </head>
                <body>
                    <div class="container">
                        <?php $this->render_form( $form_id, null, null, null ); ?>
                    </div>
                </body>
            </html>

            <?php
        } else {
            wp_die( 'Error generating the form preview' );
        }

        exit;
    }

    /**
     * render form
     *
     * @param [type] $form_id [description]
     * @param [type] $post_id [description]
     * @param array  $atts    [description]
     * @param [type] $form    [description]
     */
    public function render_form( $form_id, $post_id = null, $atts = [], $form = null ) {
        $form_status = get_post_status( $form_id );

        if ( ! $form_status ) {
            echo wp_kses_post( '<div class="wpuf-message">' . __( 'Your selected form is no longer available.', 'wp-user-frontend' ) . '</div>' );

            return;
        }

        if ( $form_status != 'publish' ) {
            echo wp_kses_post( '<div class="wpuf-message">' . __( "Please make sure you've published your form.", 'wp-user-frontend' ) . '</div>' );

            return;
        }

        $label_position = isset( $this->form_settings['label_position'] ) ? $this->form_settings['label_position'] : 'left';

        $layout = isset( $this->form_settings['form_layout'] ) ? $this->form_settings['form_layout'] : 'layout1';

        $theme_css = isset( $this->form_settings['use_theme_css'] ) ? $this->form_settings['use_theme_css'] : 'wpuf-style';

        do_action( 'wpuf_before_form_render', $form_id );

        if ( ! empty( $layout ) ) {
            wp_enqueue_style( 'wpuf-' . $layout );
        }

        if ( ! is_user_logged_in() && $this->form_settings['guest_post'] !== 'true' ) {
            $login         = wpuf()->login->get_login_url();
            $register      = wpuf()->login->get_registration_url();
            $replace       = [ "<a href='" . $login . "'>Login</a>", "<a href='" . $register . "'>Register</a>" ];
            $placeholders  = [ '%login%', '%register%' ];

            $restrict_message = str_replace( $placeholders, $replace, $this->form_settings['message_restrict'] );

            echo wp_kses_post( '<div class="wpuf-message">' . $restrict_message . '</div>' );

            return;
        }

        if (
                isset( $this->form_settings['role_base'] )
                && wpuf_validate_boolean( $this->form_settings['role_base'] )
                && ! wpuf_user_has_roles( $this->form_settings['roles'] )
            ) {
            ?>
            <div class="wpuf-message"><?php esc_html_e( 'You do not have sufficient permissions to access this form.', 'wp-user-frontend' ); ?></div>
            <?php

            return;
        }

        if ( $this->form_fields ) {
            ?>

                <form class="wpuf-form-add wpuf-form-<?php echo esc_attr( $layout ); ?> <?php echo ( $layout == 'layout1' ) ? esc_html( $theme_css ) : 'wpuf-style'; ?>" action="" method="post">


                   <script type="text/javascript">
                        if ( typeof wpuf_conditional_items === 'undefined' ) {
                            wpuf_conditional_items = [];
                        }

                        if ( typeof wpuf_plupload_items === 'undefined' ) {
                            wpuf_plupload_items = [];
                        }

                        if ( typeof wpuf_map_items === 'undefined' ) {
                            wpuf_map_items = [];
                        }
                    </script>

                    <ul class="wpuf-form form-label-<?php echo esc_attr( $label_position ); ?>">

                    <?php

                        do_action( 'wpuf_form_fields_top', $form, $this->form_fields );

                    if ( ! $post_id ) {
                        do_action( 'wpuf_add_post_form_top', $form_id, $this->form_settings );
                    } else {
                        do_action( 'wpuf_edit_post_form_top', $form_id, $post_id, $this->form_settings );
                    }

                    if ( ! is_user_logged_in() && $this->form_settings['guest_post'] == 'true' && $this->form_settings['guest_details'] == 'true' ) {
                        $this->guest_fields( $this->form_settings );
                    }

                        $this->render_featured_field( $post_id );

                        wpuf()->fields->render_fields( $this->form_fields, $form_id, $atts, $type = 'post', $post_id );

                        $this->submit_button( $form_id, $this->form_settings, $post_id );

                    if ( ! $post_id ) {
                        do_action( 'wpuf_add_post_form_bottom', $form_id, $this->form_settings );
                    } else {
                        do_action( 'wpuf_edit_post_form_bottom', $form_id, $post_id, $this->form_settings );
                    }

                    ?>

                    </ul>

                </form>

                <?php
        } //endif

        do_action( 'wpuf_after_form_render', $form_id );
    }

    /**
     * add post field setting on form builder
     *
     * @param array $field_settings
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
                if ( ! empty( $taxonomies ) ) {
                    foreach ( $taxonomies as $tax_name => $taxonomy ) {
                        if ( 'post_tag' === $tax_name ) {
                            // $taxonomy_templates['post_tag'] = self::post_tags();
                            $taxonomy_templates['post_tags'] = new WPUF_Form_Field_Post_Tags();
                        } else {
                            // $taxonomy_templates[ $tax_name ] = self::taxonomy_template( $tax_name, $taxonomy );
                            $taxonomy_templates['taxonomy'] = new WPUF_Form_Field_Post_Taxonomy( $tax_name, $taxonomy );
                        }
                    }
                }
            }

            $field_settings = array_merge( $field_settings, $taxonomy_templates );
        }

        return $field_settings;
    }

    /**
     * Guess a suitable username for registration based on email address
     *
     * @param string $email email address
     *
     * @return string username
     */
    public function guess_username( $email ) {
        // username from email address
        $username = sanitize_user( substr( $email, 0, strpos( $email, '@' ) ) );

        if ( ! username_exists( $username ) ) {
            return $username;
        }

        // try to add some random number in username
        // and may be we got our username
        $username .= rand( 1, 199 );

        if ( ! username_exists( $username ) ) {
            return $username;
        }
    }

    /**
     * Populate available wp post types
     *
     * @since 2.5
     *
     * @return void
     */
    public function set_wp_post_types() {
        $args = [ '_builtin' => true ];

        $wpuf_post_types = wpuf_get_post_types( $args );

        $ignore_taxonomies = apply_filters(
            'wpuf-ignore-taxonomies', [
                'post_format',
            ]
        );

        foreach ( $wpuf_post_types as $post_type ) {
            $this->wp_post_types[ $post_type ] = [];

            $taxonomies = get_object_taxonomies( $post_type, 'object' );

            foreach ( $taxonomies as $tax_name => $taxonomy ) {
                if ( ! in_array( $tax_name, $ignore_taxonomies ) ) {
                    $this->wp_post_types[ $post_type ][ $tax_name ] = [
                        'title'         => $taxonomy->label,
                        'hierarchical'  => $taxonomy->hierarchical,
                    ];

                    $this->wp_post_types[ $post_type ][ $tax_name ]['terms'] = get_terms(
                        [
                            'taxonomy'   => $tax_name,
                            'hide_empty' => false,
                        ]
                    );
                }
            }
        }
    }

    /**
     * get Input fields
     *
     * @param array $form_vars
     *
     * @return array
     */
    public function get_input_fields( $form_vars ) {
        $ignore_lists = [ 'section_break', 'html' ];
        $post_vars    = $meta_vars = $taxonomy_vars = [];

        foreach ( $form_vars as $key => $value ) {
            // get column field input fields
            if ( $value['input_type'] == 'column_field' ) {
                $inner_fields = $value['inner_fields'];

                foreach ( $inner_fields as $column_key => $column_fields ) {
                    if ( ! empty( $column_fields ) ) {
                        // ignore section break and HTML input type
                        foreach ( $column_fields as $column_field_key => $column_field ) {
                            if ( in_array( $column_field['input_type'], $ignore_lists ) ) {
                                continue;
                            }

                            //separate the post and custom fields
                            if ( isset( $column_field['is_meta'] ) && $column_field['is_meta'] == 'yes' ) {
                                $meta_vars[] = $column_field;
                                continue;
                            }

                            if ( $column_field['input_type'] == 'taxonomy' ) {

                                // don't add "category"
                                // if ( $column_field['name'] == 'category' ) {
                                //     continue;
                                // }

                                $taxonomy_vars[] = $column_field;
                            } else {
                                $post_vars[] = $column_field;
                            }
                        }
                    }
                }
                continue;
            }

            // ignore section break and HTML input type
            if ( in_array( $value['input_type'], $ignore_lists ) ) {
                continue;
            }

            //separate the post and custom fields
            if ( isset( $value['is_meta'] ) && $value['is_meta'] == 'yes' ) {
                $meta_vars[] = $value;
                continue;
            }

            if ( $value['input_type'] == 'taxonomy' ) {

                // don't add "category"
                // if ( $value['name'] == 'category' ) {
                //     continue;
                // }

                $taxonomy_vars[] = $value;
            } else {
                $post_vars[] = $value;
            }
        }

        return [ $post_vars, $taxonomy_vars, $meta_vars ];
    }

    /**
     * set custom taxonomy
     *
     * @param int   $post_id
     * @param array $taxonomy_vars
     */
    public function set_custom_taxonomy( $post_id, $taxonomy_vars ) {
        check_ajax_referer( 'wpuf_form_add' );
        // save any custom taxonomies
        $woo_attr = [];

        foreach ( $taxonomy_vars as $taxonomy ) {
            if ( isset( $_POST[ $taxonomy['name'] ] ) && is_array( $_POST[ $taxonomy['name'] ] ) ) {
                $taxonomy_name = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $taxonomy['name'] ] ) );
            }

            if ( isset( $_POST[ $taxonomy['name'] ] ) && ! is_array( $_POST[ $taxonomy['name'] ] ) ) {
                $taxonomy_name = sanitize_text_field( wp_unslash( $_POST[ $taxonomy['name'] ] ) );

                if ( 'text' === $taxonomy['type'] ) {
                    $terms = explode( ',', $taxonomy_name );
                    $terms = array_map(
                        function ( $term_name ) use ( $taxonomy ) {
                            $term = get_term_by( 'name', $term_name, $taxonomy['name'] );

                            if ( empty( $term_name ) ) {
                                  return null;
                            }

                            if ( $term instanceof WP_Term ) {
                                return $term->term_id;
                            }

                            $new_term = wp_insert_term( $term_name, $taxonomy['name'] );

                            return $new_term['term_id'];
                        }, $terms
                    );

                    $taxonomy_name = array_filter( $terms );
                }
            }

            // At this point $taxonomy_name should be a single id or array of ids
            if ( isset( $taxonomy_name ) && $taxonomy_name != 0 && $taxonomy_name != -1 ) {
                if ( is_object_in_taxonomy( $this->form_settings['post_type'], $taxonomy['name'] ) ) {
                    $tax = $taxonomy_name;
                    // if it's not an array, make it one
                    if ( ! is_array( $tax ) ) {
                        $tax = [ $tax ];
                    }

                    if ( $taxonomy['type'] == 'text' ) {
                        wp_set_object_terms( $post_id, $taxonomy_name, $taxonomy['name'] );

                        // woocommerce check
                        if ( isset( $taxonomy['woo_attr'] ) && $taxonomy['woo_attr'] == 'yes' && ! empty( $taxonomy_name ) ) {
                            $woo_attr[ $taxonomy['name'] ] = $this->woo_attribute( $taxonomy );
                        }
                    } else {
                        if ( is_taxonomy_hierarchical( $taxonomy['name'] ) ) {
                            wp_set_post_terms( $post_id, $taxonomy_name, $taxonomy['name'] );

                            // woocommerce check
                            if ( isset( $taxonomy['woo_attr'] ) && $taxonomy['woo_attr'] == 'yes' && ! empty( $taxonomy_name ) ) {
                                $woo_attr[ $taxonomy['name'] ] = $this->woo_attribute( $taxonomy );
                            }
                        } else {
                            if ( $tax ) {
                                $non_hierarchical = [];

                                foreach ( $tax as $value ) {
                                    $term = get_term_by( 'id', $value, $taxonomy['name'] );

                                    if ( $term && ! is_wp_error( $term ) ) {
                                        $non_hierarchical[] = $term->name;
                                    }
                                }

                                wp_set_post_terms( $post_id, $non_hierarchical, $taxonomy['name'] );

                                // woocommerce check
                                if ( isset( $taxonomy['woo_attr'] ) && $taxonomy['woo_attr'] == 'yes' && ! empty( $_POST[ $taxonomy['name'] ] ) ) {
                                    $woo_attr[ $taxonomy['name'] ] = $this->woo_attribute( $taxonomy );
                                }
                            }
                        } // hierarchical
                    } // is text
                } // is object tax
            } // isset tax

            else {
                if ( ! isset( $taxonomy['woo_attr'] ) ) {
                    $this->set_default_taxonomy( $post_id );
                }
            }
        }

        // if a woocommerce attribute
        if ( $woo_attr ) {
            update_post_meta( $post_id, '_product_attributes', $woo_attr );
        }

        return $woo_attr;
    }


    public function set_default_taxonomy( $post_id ) {
        $post_taxonomies = get_object_taxonomies( $this->form_settings['post_type'], 'objects' );
        foreach ( $post_taxonomies as $tax ) {
            if ( $tax->hierarchical ) {
                $name = 'default_' . $tax->name;
                if ( isset( $this->form_settings[ $name ] ) && ! empty( $this->form_settings[ $name ] ) ) {
                    $value = $this->form_settings[ $name ];
                    wp_set_post_terms( $post_id, $value, $tax->name );
                }
            }
        }
    }

    /**
     * prepare meta fields
     *
     * @param array $meta_vars
     *
     * @return array
     */
    public static function prepare_meta_fields( $meta_vars ) {
        // loop through custom fields
        // skip files, put in a key => value paired array for later executation
        // process repeatable fields separately
        // if the input is array type, implode with separator in a field
        // /check_ajax_referer( 'wpuf_form_add' );
        $post_data = wp_unslash( $_POST ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        $files          = [];
        $meta_key_value = [];
        $multi_repeated = []; //multi repeated fields will in sotre duplicated meta key

        foreach ( $meta_vars as $key => $value ) {
            $wpuf_field = wpuf()->fields->get_field( $value['template'] );
            $posted_field_data = isset( $post_data[ $value['name'] ] ) ? $post_data[ $value['name'] ] : null;

            if ( isset( $posted_field_data ) && method_exists( $wpuf_field, 'sanitize_field_data' ) ) {
                $meta_key_value[ $value['name'] ] = $wpuf_field->sanitize_field_data( $posted_field_data, $value );
                continue;
            } elseif ( isset( $post_data[ $value['name'] ] ) && is_array( $post_data[ $value['name'] ] ) ) {
                $value_name = isset( $post_data[ $value['name'] ] ) ? array_map( 'sanitize_text_field', wp_unslash( $post_data[ $value['name'] ] ) ) : '';
            } else {
                $value_name = isset( $post_data[ $value['name'] ] ) ? sanitize_text_field( wp_unslash( $post_data[ $value['name'] ] ) ) : '';
            }

            if ( isset( $post_data['wpuf_files'][ $value['name'] ] ) ) {
                $wpuf_files = isset( $post_data['wpuf_files'] ) ? array_map( 'sanitize_text_field', wp_unslash( $post_data['wpuf_files'][ $value['name'] ] ) ) : [];
            } else {
                $wpuf_files = [];
            }

            switch ( $value['input_type'] ) {

                // put files in a separate array, we'll process it later
                case 'file_upload':
                case 'image_upload':
                    $files[] = [
                        'name'  => $value['name'],
                        // 'value' => $wpuf_files[$value['name']],
                        'value' => isset( $wpuf_files ) ? $wpuf_files : [],
                        'count' => $value['count'],
                    ];
                    break;

                case 'repeat':
                    $repeater_value = wp_unslash( $_POST[ $value['name'] ] ); // WPCS: sanitization ok.

                    // if it is a multi column repeat field
                    if ( isset( $value['multiple'] ) && $value['multiple'] == 'true' ) {

                        // if there's any items in the array, process it
                        if ( $repeater_value ) {
                            $ref_arr = array();
                            $cols    = count( $value['columns'] );
                            $first   = array_shift( array_values( $repeater_value ) ); //first element
                            $rows    = count( $first );

                            // loop through columns
                            for ( $i = 0; $i < $rows; $i++ ) {

                                // loop through the rows and store in a temp array
                                $temp = array();
                                for ( $j = 0; $j < $cols; $j++ ) {
                                    $temp[] = $repeater_value[ $j ][ $i ];
                                }

                                // store all fields in a row with self::$separator separated
                                $ref_arr[] = implode( self::$separator, $temp );
                            }

                            // now, if we found anything in $ref_arr, store to $multi_repeated
                            if ( $ref_arr ) {
                                $multi_repeated[ $value['name'] ] = array_slice( $ref_arr, 0, $rows );
                            }
                        }
                    } else {
                        $meta_key_value[ $value['name'] ] = implode( self::$separator, $repeater_value );
                    }

                    break;

                case 'address':
                    if ( is_array( $value_name ) ) {
                        foreach ( $value_name as $address_field => $field_value ) {
                            $meta_key_value[ $value['name'] ][ $address_field ] = sanitize_text_field( $field_value );
                        }
                    }

                    break;

                case 'text':
                case 'email':
                case 'number':
                case 'date':
                    $meta_key_value[ $value['name'] ] = $value_name;

                    break;

                case 'textarea':
                    $allowed_tags = wp_kses_allowed_html( 'post' );
                    $meta_key_value[ $value['name'] ] = wp_kses( $value_name, $allowed_tags );

                    break;

                case 'map':
                    $data           = [];
                    $map_field_data = $value_name;

                    if ( ! empty( $map_field_data ) ) {
                        if ( stripos( $map_field_data, '||' ) !== false ) {
                            list( $data['address'], $data['lat'], $data['lng'] ) = explode( ' || ', $map_field_data );
                            $meta_key_value[ $value['name'] ]  = $data;
                        } else {
                            $meta_key_value[ $value['name'] ] = json_decode( $map_field_data, true );
                        }
                    }
                    break;

                case 'checkbox':
                    if ( count( $value_name ) > 1 ) {
                        $meta_key_value[ $value['name'] ] = implode( self::$separator, $value_name );
                    } else {
                        $meta_key_value[ $value['name'] ] = $value_name[0];
                    }
                    break;

                default:
                    // if it's an array, implode with this->separator
                    if ( ! empty( $value_name ) && is_array( $value_name ) ) {
                        $acf_compatibility = wpuf_get_option( 'wpuf_compatibility_acf', 'wpuf_general', 'no' );

                        if ( $value['input_type'] == 'address' ) {
                            $meta_key_value[ $value['name'] ] = $value_name;
                        } elseif ( ! empty( $acf_compatibility ) && $acf_compatibility == 'yes' ) {
                            $meta_key_value[ $value['name'] ] = $value_name;
                        } else {
                            $meta_key_value[ $value['name'] ] = implode( self::$separator, $value_name );
                        }
                    } elseif ( ! empty( $value_name ) ) {
                        $meta_key_value[ $value['name'] ] = trim( $value_name );
                    } else {
                        $meta_key_value[ $value['name'] ] = trim( $value_name );
                    }

                    break;
            }
        } //end foreach
        return [ $meta_key_value, $multi_repeated, $files ];
    }

    /**
     * checking recaptcha
     *
     * @param [type] $post_vars [description]
     *
     * @return void
     */
    public function on_edit_no_check_recaptcha( $post_vars ) {
        check_ajax_referer( 'wpuf_form_add' );
        // search if rs captcha is there
        if ( $this->search( $post_vars, 'input_type', 'really_simple_captcha' ) ) {
            $this->validate_rs_captcha();
        }
        $no_captcha      = $invisible_captcha      = $recaptcha_type      = '';
        $check_recaptcha = $this->search( $post_vars, 'input_type', 'recaptcha' );

        if ( ! empty( $check_recaptcha ) ) {
            $recaptcha_type = $check_recaptcha[0]['recaptcha_type'];
        }
        // check recaptcha
        if ( $check_recaptcha ) {
            if ( isset( $_POST['g-recaptcha-response'] ) ) {
                if ( empty( $_POST['g-recaptcha-response'] ) && $check_recaptcha[0]['recaptcha_type'] !== 'invisible_recaptcha' ) {
                    $this->send_error( __( 'Empty reCaptcha Field', 'wp-user-frontend' ) );
                }

                if ( $recaptcha_type == 'enable_no_captcha' ) {
                    $no_captcha        = 1;
                    $invisible_captcha = 0;
                } elseif ( $recaptcha_type == 'invisible_recaptcha' ) {
                    $invisible_captcha = 1;
                    $no_captcha        = 0;
                } else {
                    $invisible_captcha = 0;
                    $no_captcha        = 0;
                }
            }
            $this->validate_re_captcha( $no_captcha, $invisible_captcha );
        }
    }

    /**
     * Render a checkbox for enabling feature item
     */
    public function render_featured_field( $post_id = null ) {
        $user_sub = WPUF_Subscription::get_user_pack( get_current_user_id() );
        $is_featured = false;
        if ( $post_id ) {
            $stickies = get_option( 'sticky_posts' );
            $is_featured   = in_array( intval( $post_id ), $stickies, true );
        }

        if ( ! empty( $user_sub['total_feature_item'] ) || $is_featured ) {
            ?>
            <li class="wpuf-el field-size-large" data-label="Is featured">
                <div class="wpuf-label">
                    <label for="wpuf_is_featured"><?php esc_html_e( 'Featured', 'wp-user-frontend' ); ?></label>
                </div>
                <div >
                    <label >
                         <input type="checkbox" class="wpuf_is_featured" name="is_featured_item" value="1" <?php echo $is_featured ? 'checked' : ''; ?> >
                         <span class="wpuf-items-table-containermessage-box" id="remaining-feature-item"> <?php echo sprintf( __( 'Mark the %s as featured (remaining %d)', 'wp-user-frontend' ), $this->form_settings['post_type'], $user_sub['total_feature_item'] ); ?></span>
                    </label>
                </div>
            </li>
            <script>
                (function ($) {
                    $('.wpuf_is_featured').on('change', function (e) {
                        var counter = $('.wpuf-message-box');
                        var count = parseInt( counter.text().match(/\d+/) );
                        if ($(this).is(':checked')) {
                            counter.text( counter.text().replace( /\d+/, count - 1 ) );
                        } else {
                            counter.text( counter.text().replace( /\d+/, count + 1 ) );
                        }
                    })
                })(jQuery)
            </script>
            <?php
        }
    }
}
