<?php

class WPUF_Frontend_Form extends WPUF_Frontend_Render_Form {
    private static $_instance;

    private $post_expiration_date = 'wpuf-post_expiration_date';

    private $expired_post_status = 'wpuf-expired_post_status';

    private $post_expiration_message = 'wpuf-post_expiration_message';

    public function __construct() {
        add_shortcode( 'wpuf_form', [ $this, 'add_post_shortcode' ] );
        add_shortcode( 'wpuf_edit', [ $this, 'edit_post_shortcode' ] );
        // ajax requests
        add_action( 'wp_ajax_wpuf_form_preview', [ $this, 'preview_form' ] );
        add_action( 'wp_ajax_wpuf_submit_post', [ $this, 'submit_post' ] );
        add_action( 'wp_ajax_nopriv_wpuf_submit_post', [ $this, 'submit_post' ] );
        add_action( 'wp_ajax_make_media_embed_code', [ $this, 'make_media_embed_code' ] );
        add_action( 'wp_ajax_nopriv_make_media_embed_code', [ $this, 'make_media_embed_code' ] );
        // // guest post hook
        add_action( 'init', [ $this, 'publish_guest_post' ] );
        // draft
        add_action( 'wp_ajax_wpuf_draft_post', [ $this, 'draft_post' ] );
        add_action( 'wp_ajax_nopriv_wpuf_draft_post', [ $this, 'draft_post' ] );
        // form preview
        add_action( 'wp_ajax_wpuf_form_preview', [ $this, 'preview_form' ] );
        $this->set_wp_post_types();

        // Enable post edit link for post authors in frontend
        if ( ! is_admin() ) {
            add_filter( 'user_has_cap', [ $this, 'map_capabilities_for_post_authors' ], 10, 4 );
            add_filter( 'get_edit_post_link', [ $this, 'get_edit_post_link' ], 10, 3 );
        }
    }

    /**
     * Edit post shortcode handler
     *
     * @param array $atts
     *
     * @return
     **/
    public function edit_post_shortcode( $atts ) {
        add_filter( 'wpuf-form-fields', [ $this, 'add_field_settings' ] );
        // @codingStandardsIgnoreStart
        extract( shortcode_atts( [ 'id' => 0 ], $atts ) );

        // @codingStandardsIgnoreEnd
        ob_start();

        global $userdata;

        ob_start();

        if ( ! is_user_logged_in() ) {
            echo wp_kses_post( '<div class="wpuf-message">' . __( 'You are not logged in', 'wp-user-frontend' ) . '</div>' ),

            wp_login_form();

            return;
        }

        $post_id = isset( $_GET['pid'] ) ? intval( wp_unslash( $_GET['pid'] ) ) : 0;

        if ( ! $post_id ) {
            return '<div class="wpuf-info">' . __( 'Invalid post', 'wp-user-frontend' ) . '</div>';
        }

        $edit_post_lock      = get_post_meta( $post_id, '_wpuf_lock_editing_post', true );
        $edit_post_lock_time = get_post_meta( $post_id, '_wpuf_lock_user_editing_post_time', true );

        if ( $edit_post_lock === 'yes' ) {
            return '<div class="wpuf-info">' . apply_filters( 'wpuf_edit_post_lock_user_notice', __( 'Your edit access for this post has been locked by an administrator.', 'wp-user-frontend' ) ) . '</div>';
        }

        if ( ! empty( $edit_post_lock_time ) && $edit_post_lock_time < time() ) {
            return '<div class="wpuf-info">' . apply_filters( 'wpuf_edit_post_lock_expire_notice', __( 'Your allocated time for editing this post has been expired.', 'wp-user-frontend' ) ) . '</div>';
        }

        if ( wpuf_get_user()->edit_post_locked() ) {
            if ( wpuf_get_user()->edit_post_lock_reason() ) {
                return '<div class="wpuf-info">' . wpuf_get_user()->edit_post_lock_reason() . '</div>';
            }

            return '<div class="wpuf-info">' . apply_filters( 'wpuf_user_edit_post_lock_notice', __( 'Your post edit access has been locked by an administrator.', 'wp-user-frontend' ) ) . '</div>';
        }

        //is editing enabled?
        if ( wpuf_get_option( 'enable_post_edit', 'wpuf_dashboard', 'yes' ) !== 'yes' ) {
            return '<div class="wpuf-info">' . __( 'Post Editing is disabled', 'wp-user-frontend' ) . '</div>';
        }

        $curpost = get_post( $post_id );

        if ( ! $curpost ) {
            return '<div class="wpuf-info">' . __( 'Invalid post', 'wp-user-frontend' );
        }

        // has permission?
        if ( ! current_user_can( 'delete_others_posts' ) && ( $userdata->ID !== (int) $curpost->post_author ) ) {
            return '<div class="wpuf-info">' . __( 'You are not allowed to edit', 'wp-user-frontend' ) . '</div>';
        }

        $form_id = get_post_meta( $post_id, self::$config_id, true );

        // fallback to default form
        if ( ! $form_id ) {
            $form_id = wpuf_get_option( 'default_post_form', 'wpuf_frontend_posting' );
        }

        if ( ! $form_id ) {
            return '<div class="wpuf-info">' . __( "I don't know how to edit this post, I don't have the form ID", 'wp-user-frontend' ) . '</div>';
        }

        $form = new WPUF_Form( $form_id );

        $this->form_fields = $form->get_fields();
        // $form_settings = wpuf_get_form_settings( $form_id );
        $this->form_settings = $form->get_settings();

        $disable_pending_edit = wpuf_get_option( 'disable_pending_edit', 'wpuf_dashboard', 'on' );

        if ( $curpost->post_status === 'pending' && $disable_pending_edit === 'on' ) {
            return '<div class="wpuf-info">' . __( 'You can\'t edit a post while in pending mode.', 'wp-user-frontend' );
        }

        $msg = isset( $_GET['msg'] ) ? sanitize_text_field( wp_unslash( $_GET['msg'] ) ) : '';

        if ( $msg === 'post_updated' ) {
            echo wp_kses_post( '<div class="wpuf-success">' );
            echo wp_kses_post( str_replace( '%link%', get_permalink( $post_id ), $this->form_settings['update_message'] ) );
            echo wp_kses_post( '</div>' );
        }

        $this->render_form( $form_id, $post_id, $atts, $form );

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    public static function init() {
        if ( ! self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * This will embed media to the editor
     */
    public function make_media_embed_code() {
        $nonce = isset( $_GET['nonce'] ) ? sanitize_key( wp_unslash( $_GET['nonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf-upload-nonce' ) ) {
            return;
        }

        $content = isset( $_POST['content'] ) ? sanitize_text_field( wp_unslash( $_POST['content'] ) ) : '';
        $embed_code = wp_oembed_get( $content );

        if ( $embed_code ) {
            echo esc_html( $embed_code );
        } else {
            echo '';
        }
        exit;
    }

    /**
     * Draft Post
     */
    public function draft_post() {
        check_ajax_referer( 'wpuf_form_add' );
        add_filter( 'wpuf-form-fields', [ $this, 'add_field_settings' ] );
        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

        $form_id             = isset( $_POST['form_id'] ) ? intval( wp_unslash( $_POST['form_id'] ) ) : 0;
        $form                = new WPUF_Form( $form_id );
        $this->form_settings = $form->get_settings();
        $this->form_fields   = $form->get_fields();
        $pay_per_post        = $form->is_enabled_pay_per_post();

        list( $post_vars, $taxonomy_vars, $meta_vars ) = $this->get_input_fields( $this->form_fields );

        $entry_fields = $form->prepare_entries();
        $allowed_tags = wp_kses_allowed_html( 'post' );
        $post_content = isset( $_POST['post_content'] ) ? wp_kses( wp_unslash( $_POST['post_content'] ), $allowed_tags ) : '';
        $postarr = [
            'post_type'    => $this->form_settings['post_type'],
            'post_status'  => wpuf_get_draft_post_status( $this->form_settings ),
            'post_author'  => get_current_user_id(),
            'post_title'   => isset( $_POST['post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['post_title'] ) ) : '',
            'post_content' => $post_content,
            'post_excerpt' => isset( $_POST['post_excerpt'] ) ? wp_kses( wp_unslash( $_POST['post_excerpt'] ), $allowed_tags ) : '',
        ];

        if ( ! empty( $this->form_fields ) ) {
            foreach ( $this->form_fields as $field ) {
                if ( $field['template'] === 'taxonomy' ) {
                    $category_name = $field['name'];

                    if ( isset( $_POST[ $category_name ] ) && is_array( $_POST[ $category_name ] ) ) { // WPCS: sanitization ok.
                        $category = isset( $_POST[ $category_name ] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $category_name ] ) ) : [];
                    } else {
                        $category = isset( $_POST[ $category_name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $category_name ] ) ) : '';
                    }

                    if ( $category !== '' && $category !== '0' && $category[0] !== '-1' ) {
                        if ( ! is_array( $category ) && is_string( $category ) ) {
                            $category_strings = explode( ',', $category );
                            $cat_ids          = [];

                            foreach ( $category_strings as $key => $each_cat_string ) {
                                $cat_ids[]                = get_cat_ID( trim( $each_cat_string ) );
                                $postarr['post_category'] = $cat_ids;
                            }
                        } else {
                            $postarr['post_category'] = $category;
                        }
                    }
                }
            }
        }

        // set default post category if it's not been set yet and if post type supports
        if ( ! isset( $postarr['post_category'] ) && isset( $this->form_settings['default_cat'] ) && is_object_in_taxonomy( $this->form_settings['post_type'], 'category' ) ) {
            if ( is_array( $this->form_settings['default_cat'] ) ) {
                $postarr['post_category'] = $this->form_settings['default_cat'];
            } else {
                $postarr['post_category'] = [ $this->form_settings['default_cat'] ];
            }
        }

        if ( isset( $_POST['tags'] ) ) {
            $postarr['tags_input'] = explode( ',', sanitize_text_field( wp_unslash( $_POST['tags'] ) ) );
        }

        // if post_id is passed, we update the post
        if ( isset( $_POST['post_id'] ) ) {
            $is_update                 = true;
            $postarr['ID']             = intval( wp_unslash( $_POST['post_id'] ) );
            $postarr['comment_status'] = 'open';
        }

        $post_id = wp_insert_post( $postarr );

        // add post revision when post edit from the frontend
        wpuf_frontend_post_revision( $post_id, $this->form_settings );

        if ( $post_id ) {
            self::update_post_meta( $meta_vars, $post_id );

            // set the post form_id for later usage
            update_post_meta( $post_id, self::$config_id, $form_id );

            // save post formats if have any
            if ( isset( $this->form_settings['post_format'] ) && $this->form_settings['post_format'] !== '0' ) {
                if ( post_type_supports( $this->form_settings['post_type'], 'post-formats' ) ) {
                    set_post_format( $post_id, $this->form_settings['post_format'] );
                }
            }

            if ( ! empty( $taxonomy_vars ) ) {
                $this->set_custom_taxonomy( $post_id, $taxonomy_vars );
            } else {
                $this->set_default_taxonomy( $post_id );
            }
        }

        //used to add code to run when the post is going to draft
        do_action( 'wpuf_draft_post_after_insert', $post_id, $form_id, $this->form_settings, $this->form_fields );

        wpuf_clear_buffer();

        echo json_encode(
            [
                'post_id'        => $post_id,
                'action'         => isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '',
                'date'           => current_time( 'mysql' ),
                'post_author'    => get_current_user_id(),
                'comment_status' => get_option( 'default_comment_status' ),
                'url'            => add_query_arg( 'preview', 'true', get_permalink( $post_id ) ),
            ]
        );

        exit;
    }

    /**
     * New/Edit post submit handler
     *
     * @return void
     */
    public function submit_post() {
        check_ajax_referer( 'wpuf_form_add' );
        add_filter( 'wpuf-form-fields', [ $this, 'add_field_settings' ] );
        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

        $form_id               = isset( $_POST['form_id'] ) ? intval( wp_unslash( $_POST['form_id'] ) ) : 0;
        $form                  = new WPUF_Form( $form_id );
        $this->form_settings   = $form->get_settings();
        $this->form_fields     = $form->get_fields();
        $guest_mode            = isset( $this->form_settings['guest_post'] ) ? $this->form_settings['guest_post'] : '';
        $guest_verify          = isset( $this->form_settings['guest_email_verify'] ) ? $this->form_settings['guest_email_verify'] : 'false';
        $attachments_to_delete = isset( $_POST['delete_attachments'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['delete_attachments'] ) ) : [];

        foreach ( $attachments_to_delete as $attach_id ) {
            wp_delete_attachment( $attach_id, true );
        }

        list( $post_vars, $taxonomy_vars, $meta_vars ) = $this->get_input_fields( $this->form_fields );

        if ( ! isset( $_POST['post_id'] ) ) {
            $has_limit = ( isset( $this->form_settings['limit_entries'] ) && $this->form_settings['limit_entries'] === 'true' ) ? true : false;

            if ( $has_limit ) {
                $limit        = (int) ! empty( $this->form_settings['limit_number'] ) ? $this->form_settings['limit_number'] : 0;
                $form_entries = wpuf_form_posts_count( $form_id );

                if ( $limit && $limit <= $form_entries ) {
                    $this->send_error( $this->form_settings['limit_message'] );
                }
            }
            $this->on_edit_no_check_recaptcha( $post_vars );
        }

        $is_update           = false;
        $post_author         = null;
        $default_post_author = wpuf_get_option( 'default_post_owner', 'wpuf_frontend_posting', 1 );
        $post_author         = $this->wpuf_get_post_user();

        $allowed_tags = wp_kses_allowed_html( 'post' );
        $postarr = [
            'post_type'    => $this->form_settings['post_type'],
            'post_status'  => isset( $this->form_settings['post_status'] ) ? $this->form_settings['post_status'] : 'publish',
            'post_author'  => $post_author,
            'post_title'   => isset( $_POST['post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['post_title'] ) ) : '',
            'post_content' => isset( $_POST['post_content'] ) ? wp_kses( wp_unslash( $_POST['post_content'] ), $allowed_tags ) : '',
            'post_excerpt' => isset( $_POST['post_excerpt'] ) ? wp_kses( wp_unslash( $_POST['post_excerpt'] ), $allowed_tags ) : '',
        ];

        // $charging_enabled = wpuf_get_option( 'charge_posting', 'wpuf_payment' );
        $charging_enabled = '';
        $form             = new WPUF_Form( $form_id );
        $payment_options  = $form->is_charging_enabled();
        $ppp_cost_enabled = $form->is_enabled_pay_per_post();
        $current_user     = wpuf_get_user();

        if ( ! $payment_options ) {
            $charging_enabled = 'no';
        } else {
            $charging_enabled = 'yes';
        }

        if ( $guest_mode === 'true' && $guest_verify === 'true' && ! is_user_logged_in() && $charging_enabled === 'yes' ) {
            $postarr['post_status'] = wpuf_get_draft_post_status( $this->form_settings );
        } elseif ( $guest_mode === 'true' && $guest_verify === 'true' && ! is_user_logged_in() ) {
            $postarr['post_status'] = 'draft';
        }
        //if date is set and assigned as publish date
        if ( isset( $_POST['wpuf_is_publish_time'] ) ) {
            if ( isset( $_POST[ $_POST['wpuf_is_publish_time'] ] ) && ! empty( $_POST[ $_POST['wpuf_is_publish_time'] ] ) ) {
                // $postarr['post_date'] = date( 'Y-m-d H:i:s', strtotime( str_replace( array( ':', '/' ), '-', $_POST[$_POST['wpuf_is_publish_time']] ) ) );
                $date_time = explode( ' ', sanitize_text_field( wp_unslash( ( $_POST[ $_POST['wpuf_is_publish_time'] ] ) ) ) );

                if ( ! empty( $date_time[0] ) ) {
                    $timestamp = strtotime( str_replace( [ '/' ], '-', $date_time[0] ) );
                }

                if ( ! empty( $date_time[1] ) ) {
                    $time       = explode( ':', $date_time[1] );
                    $seconds    = ( $time[0] * 60 * 60 ) + ( $time[1] * 60 );
                    $timestamp  = $timestamp + $seconds;
                }
                $postarr['post_date'] = gmdate( 'Y-m-d H:i:s', $timestamp );
            }
        }

        if ( isset( $_POST['category'] ) && is_array( $_POST['category'] ) ) { // WPCS: sanitization ok.
            $category = isset( $_POST['category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['category'] ) ) : [];
        } else {
            $category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
        }

        if ( $category !== '' && $category !== '0' && $category[0] !== '-1' ) {
            if ( ! is_array( $category ) && is_string( $category ) ) {
                $category_strings = explode( ',', $category );
                $cat_ids          = [];

                foreach ( $category_strings as $key => $each_cat_string ) {
                    $cat_ids[]                = get_cat_ID( trim( $each_cat_string ) );
                    $postarr['post_category'] = $cat_ids;
                }
            } else {
                $postarr['post_category'] = $category;
            }
        }

        if ( isset( $_POST['tags'] ) ) {
            $postarr['tags_input'] = explode( ',', sanitize_text_field( wp_unslash( $_POST['tags'] ) ) );
        }

        // if post_id is passed, we update the post
        if ( isset( $_POST['post_id'] ) ) {
            $post_id                   = intval( wp_unslash( $_POST['post_id'] ) );
            $is_update                 = true;
            $postarr['ID']             = $post_id;
            $postarr['post_date']      = isset( $_POST['post_date'] ) ? sanitize_text_field( wp_unslash( $_POST['post_date'] ) ) : '';
            $postarr['comment_status'] = isset( $_POST['comment_status'] ) ? sanitize_text_field( wp_unslash( $_POST['comment_status'] ) ) : '';
            $postarr['post_author']    = isset( $_POST['post_author'] ) ? sanitize_text_field( wp_unslash( $_POST['post_author'] ) ) : '';
            $postarr['post_parent']    = get_post_field( 'post_parent', $post_id );

            $menu_order = get_post_field( 'menu_order', $post_id );

            if ( ! empty( $menu_order ) ) {
                $postarr['menu_order'] = $menu_order;
            }

            if ( $this->form_settings['edit_post_status'] === '_nochange' ) {
                $postarr['post_status'] = get_post_field( 'post_status', $post_id );
            } else {
                $postarr['post_status'] = $this->form_settings['edit_post_status'];
            }
            // handle for falback ppp
            if ( 'pending' === get_post_meta( $post_id, '_wpuf_payment_status', true ) ) {
                $postarr['post_status'] = 'pending';
            }
        } else {
            if ( isset( $this->form_settings['comment_status'] ) ) {
                $postarr['comment_status'] = $this->form_settings['comment_status'];
            }
        }

        // check the form status, it might be already a draft
        // in that case, it already has the post_id field
        // so, WPUF's add post action/filters won't work for new posts
        $wpuf_form_status = isset( $_POST['wpuf_form_status'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_form_status'] ) ) : '';
        if ( $wpuf_form_status === 'new' ) {
            $is_update = false;
        }

        // set default post category if it's not been set yet and if post type supports
        if ( ! isset( $postarr['post_category'] ) && isset( $this->form_settings['default_cat'] ) && is_object_in_taxonomy( $this->form_settings['post_type'], 'category' ) ) {
            if ( is_array( $this->form_settings['default_cat'] ) ) {
                $postarr['post_category'] = $this->form_settings['default_cat'];
            } else {
                $postarr['post_category'] = [ $this->form_settings['default_cat'] ];
            }
        }

        // validation filter
        if ( $is_update ) {
            $error = apply_filters( 'wpuf_update_post_validate', '' );
        } else {
            $error = apply_filters( 'wpuf_add_post_validate', '' );
        }

        if ( ! empty( $error ) ) {
            $this->send_error( $error );
        }
        // ############ It's Time to Save the World ###############
        if ( $is_update ) {
            $postarr = apply_filters( 'wpuf_update_post_args', $postarr, $form_id, $this->form_settings, $this->form_fields );
        } else {
            $postarr = apply_filters( 'wpuf_add_post_args', $postarr, $form_id, $this->form_settings, $this->form_fields );
        }

        $post_id = wp_insert_post( $postarr, $wp_error = false );

        // add post revision when post edit from the frontend
        wpuf_frontend_post_revision( $post_id, $this->form_settings );

        // add _wpuf_lock_editing_post_time meta to
        // lock user from editing the published post after a certain time
        if ( ! $is_update ) {
            $lock_edit_post = isset( $this->form_settings['lock_edit_post'] ) ? floatval( $this->form_settings['lock_edit_post'] ) : 0;

            if ( $post_id && $lock_edit_post > 0 ) {
                $lock_edit_post_time = time() + ( $lock_edit_post * 60 * 60 );
                update_post_meta( $post_id, '_wpuf_lock_user_editing_post_time', $lock_edit_post_time );
            }
        }

        if ( $post_id ) {
            self::update_post_meta( $meta_vars, $post_id );
            // set the post form_id for later usage
            update_post_meta( $post_id, self::$config_id, $form_id );
            // if user has a subscription pack
            $this->wpuf_user_subscription_pack( $this->form_settings, $post_id );
            // set the post form_id for later usage
            update_post_meta( $post_id, self::$config_id, $form_id );

            // save post formats if have any
            if ( isset( $this->form_settings['post_format'] ) && $this->form_settings['post_format'] !== '0' ) {
                if ( post_type_supports( $this->form_settings['post_type'], 'post-formats' ) ) {
                    set_post_format( $post_id, $this->form_settings['post_format'] );
                }
            }

            // find our if any images in post content and associate them
            if ( ! empty( $postarr['post_content'] ) ) {
                $dom = new DOMDocument();
                @$dom->loadHTML( $postarr['post_content'] );
                $images = $dom->getElementsByTagName( 'img' );

                if ( $images->length ) {
                    foreach ( $images as $img ) {
                        $url           = $img->getAttribute( 'src' );
                        $url           = str_replace( [ '"', "'", '\\' ], '', $url );
                        $attachment_id = wpuf_get_attachment_id_from_url( $url );

                        if ( $attachment_id ) {
                            wpuf_associate_attachment( $attachment_id, $post_id );
                        }
                    }
                }
            }

            if ( ! empty( $taxonomy_vars ) ) {
                $this->set_custom_taxonomy( $post_id, $taxonomy_vars );
            } else {
                $this->set_default_taxonomy( $post_id );
            }

            $response = $this->send_mail_for_guest( $charging_enabled, $post_id, $form_id, $is_update, $post_author, $meta_vars );
            wpuf_clear_buffer();
            wp_send_json( $response );
        }
        $this->send_error( __( 'Something went wrong', 'wp-user-frontend' ) );
    }

    public function wpuf_get_post_user() {
        $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf_form_add' ) ) {
            return;
        }

        $default_post_author = wpuf_get_option( 'default_post_owner', 'wpuf_frontend_posting', 1 );

        if ( ! is_user_logged_in() ) {
            if ( isset( $this->form_settings['guest_post'] ) && $this->form_settings['guest_post'] === 'true' && $this->form_settings['guest_details'] === 'true' ) {
                $guest_name = isset( $_POST['guest_name'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_name'] ) ) : '';

                $guest_email = isset( $_POST['guest_email'] ) ? sanitize_email( wp_unslash( $_POST['guest_email'] ) ) : '';
                $page_id = isset( $_POST['page_id'] ) ? sanitize_text_field( wp_unslash( $_POST['page_id'] ) ) : '';

                // is valid email?
                if ( ! is_email( $guest_email ) ) {
                    $this->send_error( __( 'Invalid email address.', 'wp-user-frontend' ) );
                }

                // check if the user email already exists
                $user = get_user_by( 'email', $guest_email );

                if ( $user ) {
                    // $post_author = $user->ID;
                    wp_send_json(
                        [
                            'success'     => false,
                            'error'       => __( "You already have an account in our site. Please login to continue.\n\nClicking 'OK' will redirect you to the login page and you will lose the form data.\nClick 'Cancel' to stay at this page.", 'wp-user-frontend' ),
                            'type'        => 'login',
                            'redirect_to' => wp_login_url( get_permalink( $page_id ) ),
                        ]
                    );
                } else {

                    // user not found, lets register him
                    // username from email address
                    $username = $this->guess_username( $guest_email );

                    $user_pass = wp_generate_password( 12, false );

                    $errors = new WP_Error();

                    do_action( 'register_post', $username, $guest_email, $errors );

                    $user_id = wp_create_user( $username, $user_pass, $guest_email );

                    // if its a success and no errors found

                    if ( $user_id && ! is_wp_error( $user_id ) ) {
                        update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

                        if ( class_exists( 'Theme_My_Login_Custom_Email' ) ) {
                            do_action( 'tml_new_user_registered', $user_id, $user_pass );
                        } else {
                            wp_send_new_user_notifications( $user_id );
                        }

                        // update display name to full name
                        wp_update_user(
                            [
                                'ID' => $user_id,
                                'display_name' => $guest_name,
                            ]
                        );

                        $post_author = $user_id;
                    } else {
                        //something went wrong creating the user, set post author to the default author
                        $post_author = $default_post_author;
                    }
                }

                // guest post is enabled and details are off
            } elseif ( isset( $this->form_settings['guest_post'] ) && $this->form_settings['guest_post'] === 'true' && $this->form_settings['guest_details'] === 'false' ) {
                $post_author = $default_post_author;
            } elseif ( isset( $this->form_settings['guest_post'] ) && $this->form_settings['guest_post'] !== 'true' ) {
                $this->send_error( $this->form_settings['message_restrict'] );
            }

            // the user must be logged in already
        } elseif ( isset( $this->form_settings['role_base'] ) && $this->form_settings['role_base'] === 'true' && ! wpuf_user_has_roles( $this->form_settings['roles'] ) ) {
                $this->send_error( __( 'You do not have sufficient permissions to access this form.', 'wp-user-frontend' ) );
        } else {
            $post_author = get_current_user_id();
        }

        return $post_author;
    }

    /**
     * Add post shortcode handler
     *
     * @param array $atts
     * @return string
    */

    public function add_post_shortcode( $atts ) {
        add_filter( 'wpuf-form-fields', [ $this, 'add_field_settings' ] );

        // @codingStandardsIgnoreStart
        extract( shortcode_atts( [ 'id' => 0 ], $atts ) );

        // @codingStandardsIgnoreEnd
        ob_start();
        $form                         = new WPUF_Form( $id );
        $this->form_fields            = $form->get_fields();
        $this->form_settings          = $form->get_settings();
        list( $user_can_post, $info ) = $form->is_submission_open( $form, $this->form_settings );
        $info                         = apply_filters( 'wpuf_addpost_notice', $info, $id, $this->form_settings );
        $user_can_post                = apply_filters( 'wpuf_can_post', $user_can_post, $id, $this->form_settings );

        if ( $user_can_post === 'yes' ) {
            $this->render_form( $id, null, $atts, $form );
        } else {
            echo wp_kses_post( '<div class="wpuf-info">' . $info . '</div>' );
        }
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public static function update_post_meta( $meta_vars, $post_id ) {
        // check_ajax_referer( 'wpuf_form_add' );
        // prepare the meta vars
        list( $meta_key_value, $multi_repeated, $files ) = self::prepare_meta_fields( $meta_vars );
        // set featured image if there's any

        // @codingStandardsIgnoreStart
        $wpuf_files = isset( $_POST['wpuf_files'] ) ? $_POST['wpuf_files'] : [];

        if ( isset( $wpuf_files['featured_image'] ) ) {
            $attachment_id = $wpuf_files['featured_image'][0];

            wpuf_associate_attachment( $attachment_id, $post_id );
            set_post_thumbnail( $post_id, $attachment_id );

            $file_data = isset( $_POST['wpuf_files_data'][ $attachment_id ] ) ? $_POST['wpuf_files_data'][ $attachment_id ] : false;

        // @codingStandardsIgnoreEnd
            if ( $file_data ) {
                $args = [
                    'ID'           => $attachment_id,
                    'post_title'   => $file_data['title'],
                    'post_content' => $file_data['desc'],
                    'post_excerpt' => $file_data['caption'],
                ];
                wpuf_update_post( $args );

                update_post_meta( $attachment_id, '_wp_attachment_image_alt', $file_data['title'] );
            }
        }

        // save all custom fields
        foreach ( $meta_key_value as $meta_key => $meta_value ) {
            update_post_meta( $post_id, $meta_key, $meta_value );
        }

        // save any multicolumn repeatable fields
        foreach ( $multi_repeated as $repeat_key => $repeat_value ) {
            // first, delete any previous repeatable fields
            delete_post_meta( $post_id, $repeat_key );

            // now add them
            foreach ( $repeat_value as $repeat_field ) {
                add_post_meta( $post_id, $repeat_key, $repeat_field );
            }
        }

        // save any files attached
        foreach ( $files as $file_input ) {
            // delete any previous value
            delete_post_meta( $post_id, $file_input['name'] );

            $image_ids = '';

            if ( count( $file_input['value'] ) > 1 ) {
                $image_ids = $file_input['value'];
            }

            if ( count( $file_input['value'] ) === 1 ) {
                $image_ids = $file_input['value'][0];
            }

            if ( ! empty( $image_ids ) ) {
                add_post_meta( $post_id, $file_input['name'], $image_ids );
            }

            //to track how many files are being uploaded
            $file_numbers = 0;

            foreach ( $file_input['value'] as $attachment_id ) {

                //if file numbers are greated than allowed number, prevent it from being uploaded
                if ( $file_numbers >= $file_input['count'] ) {
                    wp_delete_attachment( $attachment_id );
                    continue;
                }

                wpuf_associate_attachment( $attachment_id, $post_id );
                //add_post_meta( $post_id, $file_input['name'], $attachment_id );

                // file title, caption, desc update

                // @codingStandardsIgnoreStart
                $file_data = isset( $_POST['wpuf_files_data'][ $attachment_id ] ) ? wp_unslash( $_POST['wpuf_files_data'][ $attachment_id ] ) : false;

                // @codingStandardsIgnoreEnd
                if ( $file_data ) {
                    $args = [
                        'ID'           => $attachment_id,
                        'post_title'   => $file_data['title'],
                        'post_content' => $file_data['desc'],
                        'post_excerpt' => $file_data['caption'],
                    ];
                    wpuf_update_post( $args );

                    update_post_meta( $attachment_id, '_wp_attachment_image_alt', $file_data['title'] );
                }
                $file_numbers++;
            }
        }
    }

    public function prepare_mail_body( $content, $user_id, $post_id ) {
        $user = get_user_by( 'id', $user_id );
        $post = get_post( $post_id );

        $post_field_search = [
            '%post_title%',
            '%post_content%',
            '%post_excerpt%',
            '%tags%',
            '%category%',
            '%author%',
            '%author_email%',
            '%author_bio%',
            '%sitename%',
            '%siteurl%',
            '%permalink%',
            '%editlink%',
        ];

        $home_url = sprintf( '<a href="%s">%s</a>', home_url(), home_url() );
        $post_url = sprintf( '<a href="%s">%s</a>', get_permalink( $post_id ), get_permalink( $post_id ) );
        $post_edit_link = sprintf( '<a href="%s">%s</a>', admin_url( 'post.php?action=edit&post=' . $post_id ), admin_url( 'post.php?action=edit&post=' . $post_id ) );

        $post_field_replace = [
            $post->post_title,
            $post->post_content,
            $post->post_excerpt,
            get_the_term_list( $post_id, 'post_tag', '', ', ' ),
            get_the_term_list( $post_id, 'category', '', ', ' ),
            $user->display_name,
            $user->user_email,
            ( $user->description ) ? $user->description : 'not available',
            get_bloginfo( 'name' ),
            $home_url,
            $post_url,
            $post_edit_link,
        ];

        if ( class_exists( 'WooCommerce' ) ) {
            $post_field_search[] = '%product_cat%';
            $post_field_replace[] = get_the_term_list( $post_id, 'product_cat', '', ', ' );
        }

        $content = str_replace( $post_field_search, $post_field_replace, $content );

        // custom fields
        preg_match_all( '/%custom_([\w-]*)\b%/', $content, $matches );
        list( $search, $replace ) = $matches;

        if ( $replace ) {
            foreach ( $replace as $index => $meta_key ) {
                $value = get_post_meta( $post_id, $meta_key, false );

                if ( isset( $value[0] ) && is_array( $value[0] ) ) {
                    $new_value = implode( '; ', $value[0] );
                } else {
                    $new_value = implode( '; ', $value );
                }

                $original_value = '';
                $meta_val       = '';

                if ( count( $value ) > 1 ) {
                    $is_first = true;

                    foreach ( $value as $val ) {
                        if ( $is_first ) {
                            if ( get_post_mime_type( (int) $val ) ) {
                                $meta_val = wp_get_attachment_url( $val );
                            } else {
                                $meta_val = $val;
                            }
                            $is_first = false;
                        } else {
                            if ( get_post_mime_type( (int) $val ) ) {
                                $meta_val = $meta_val . ', ' . wp_get_attachment_url( $val );
                            } else {
                                $meta_val = $meta_val . ', ' . $val;
                            }
                        }

                        if ( get_post_mime_type( (int) $val ) ) {
                            $meta_val = $meta_val . ',' . wp_get_attachment_url( $val );
                        } else {
                            $meta_val = $meta_val . ',' . $val;
                        }
                    }
                    $original_value = $original_value . $meta_val;
                } else {
                    if ( 'address_field' === $meta_key ) {
                        $value     = get_post_meta( $post_id, $meta_key, true );
                        $new_value = implode( ', ', $value );
                    }

                    if ( get_post_mime_type( (int) $new_value ) ) {
                        $original_value = wp_get_attachment_url( $new_value );
                    } else {
                        $original_value = $new_value;
                    }
                }

                $content = str_replace( $search[ $index ], $original_value, $content );
            }
        }

        return $content;
    }

    public function woo_attribute( $taxonomy ) {
        check_ajax_referer( 'wpuf_form_add' );
        $taxonomy_name = isset( $_POST[ $taxonomy['name'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $taxonomy['name'] ] ) ) : '';

        return [
            'name'         => $taxonomy['name'],
            'value'        => $taxonomy_name,
            'is_visible'   => $taxonomy['woo_attr_vis'] === 'yes' ? 1 : 0,
            'is_variation' => 0,
            'is_taxonomy'  => 1,
        ];
    }

    /**
     * Hook to publish verified guest post with payment
     *
     * @since 2.5.8
     */
    public function publish_guest_post() {
        $post_msg = isset( $_GET['post_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['post_msg'] ) ) : '';
        $pid      = isset( $_GET['p_id'] ) ? sanitize_text_field( wp_unslash( $_GET['p_id'] ) ) : '';
        $fid      = isset( $_GET['f_id'] ) ? sanitize_text_field( wp_unslash( $_GET['f_id'] ) ) : '';

        if ( $post_msg === 'verified' ) {
            $response       = [];
            $post_id        = wpuf_decryption( $pid );
            $form_id        = wpuf_decryption( $fid );
            $form_settings  = wpuf_get_form_settings( $form_id );
            $post_author_id = get_post_field( 'post_author', $post_id );
            $payment_status = new WPUF_Subscription();
            $form           = new WPUF_Form( $form_id );
            $pay_per_post   = $form->is_enabled_pay_per_post();
            $force_pack     = $form->is_enabled_force_pack();

            if ( $form->is_charging_enabled() && $pay_per_post ) {
                if ( ( $payment_status->get_payment_status( $post_id ) ) === 'pending' ) {
                    $response['show_message'] = true;
                    $response['redirect_to']  = add_query_arg(
                        [
                            'action'  => 'wpuf_pay',
                            'type'    => 'post',
                            'post_id' => $post_id,
                        ],
                        get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) )
                    );

                    wp_redirect( $response['redirect_to'] );
                    wpuf_clear_buffer();
                    wp_send_json( $response );
                }
            } else {
                $p_status = get_post_status( $post_id );

                if ( $p_status ) {
                    wp_update_post(
                        [
                            'ID'          => $post_id,
                            'post_status' => isset( $form_settings['post_status'] ) ? $form_settings['post_status'] : 'publish',
                        ]
                    );

                    echo wp_kses_post( "<div class='wpuf-success' style='text-align:center'>" . __( 'Email successfully verified. Please Login.', 'wp-user-frontend' ) . '</div>' );
                }
            }
        }
    }

    public function wpuf_user_subscription_pack( $form_settings, $post_id = null ) {

        // if user has a subscription pack
        $user_wpuf_subscription_pack = get_user_meta( get_current_user_id(), '_wpuf_subscription_pack', true );
        $wpuf_user               = wpuf_get_user();
        $user_subscription       = new WPUF_User_Subscription( $wpuf_user );
        if ( ! empty( $user_wpuf_subscription_pack ) && isset( $user_wpuf_subscription_pack['_enable_post_expiration'] )
            && isset( $user_wpuf_subscription_pack['expire'] ) && strtotime( $user_wpuf_subscription_pack['expire'] ) >= time() ) {
            $expire_date = gmdate( 'Y-m-d', strtotime( '+' . $user_wpuf_subscription_pack['_post_expiration_time'] ) );
            update_post_meta( $post_id, $this->post_expiration_date, $expire_date );
            // save post status after expiration
            $expired_post_status = $user_wpuf_subscription_pack['_expired_post_status'];
            update_post_meta( $post_id, $this->expired_post_status, $expired_post_status );
            // if mail active
            if ( isset( $user_wpuf_subscription_pack['_enable_mail_after_expired'] ) && $user_wpuf_subscription_pack['_enable_mail_after_expired'] === 'on' ) {
                $post_expiration_message = $user_subscription->get_subscription_exp_msg( $user_wpuf_subscription_pack['pack_id'] );
                update_post_meta( $post_id, $this->post_expiration_message, $post_expiration_message );
            }
        } elseif ( ! empty( $user_wpuf_subscription_pack ) && isset( $user_wpuf_subscription_pack['expire'] ) && strtotime( $user_wpuf_subscription_pack['expire'] ) <= time() ) {
            if ( isset( $form_settings['expiration_settings']['enable_post_expiration'] ) ) {
                $expire_date = gmdate( 'Y-m-d', strtotime( '+' . $form_settings['expiration_settings']['expiration_time_value'] . ' ' . $form_settings['expiration_settings']['expiration_time_type'] . '' ) );

                update_post_meta( $post_id, $this->post_expiration_date, $expire_date );
                // save post status after expiration
                $expired_post_status = $form_settings['expiration_settings']['expired_post_status'];
                update_post_meta( $post_id, $this->expired_post_status, $expired_post_status );
                // if mail active
                if ( isset( $form_settings['expiration_settings']['enable_mail_after_expired'] ) && $form_settings['expiration_settings']['enable_mail_after_expired'] === 'on' ) {
                    $post_expiration_message = $form_settings['expiration_settings']['post_expiration_message'];
                    update_post_meta( $post_id, $this->post_expiration_message, $post_expiration_message );
                }
            }
        } elseif ( empty( $user_wpuf_subscription_pack ) || $user_wpuf_subscription_pack === 'Cancel' || $user_wpuf_subscription_pack === 'cancel' ) {
            if ( isset( $form_settings['expiration_settings']['enable_post_expiration'] ) ) {
                $expire_date = gmdate( 'Y-m-d', strtotime( '+' . $form_settings['expiration_settings']['expiration_time_value'] . ' ' . $form_settings['expiration_settings']['expiration_time_type'] . '' ) );
                update_post_meta( $post_id, $this->post_expiration_date, $expire_date );
                // save post status after expiration
                $expired_post_status = $form_settings['expiration_settings']['expired_post_status'];
                update_post_meta( $post_id, $this->expired_post_status, $expired_post_status );
                // if mail active
                if ( isset( $form_settings['expiration_settings']['enable_mail_after_expired'] ) && $form_settings['expiration_settings']['enable_mail_after_expired'] === 'on' ) {
                    $post_expiration_message = $form_settings['expiration_settings']['post_expiration_message'];
                    update_post_meta( $post_id, $this->post_expiration_message, $post_expiration_message );
                }
            }
        }

        //Handle featured item when edit
        $sub_meta = $user_subscription->handle_featured_item( $post_id, $user_wpuf_subscription_pack );
        $user_subscription->update_meta( $sub_meta );
    }

    public function send_mail_for_guest( $charging_enabled, $post_id, $form_id, $is_update, $post_author, $meta_vars ) {
        global $wp;
        check_ajax_referer( 'wpuf_form_add' );
        $show_message = false;
        $redirect_to  = false;
        $response     = [];
        $page_id      = isset( $_POST['page_id'] ) ? intval( wp_unslash( $_POST['page_id'] ) ) : '';

        if ( $is_update ) {
            if ( $this->form_settings['edit_redirect_to'] === 'page' ) {
                $redirect_to = get_permalink( $this->form_settings['edit_page_id'] );
            } elseif ( $this->form_settings['edit_redirect_to'] === 'url' ) {
                $redirect_to = $this->form_settings['edit_url'];
            } elseif ( $this->form_settings['edit_redirect_to'] === 'same' ) {
                $redirect_to = add_query_arg(
                    [
                        'pid'      => $post_id,
                        '_wpnonce' => wp_create_nonce( 'wpuf_edit' ),
                        'msg'      => 'post_updated',
                    ],
                    get_permalink( $page_id )
                );
            } else {
                $redirect_to = get_permalink( $post_id );
            }
        } else {
            if ( $this->form_settings['redirect_to'] === 'page' ) {
                $redirect_to = get_permalink( $this->form_settings['page_id'] );
            } elseif ( $this->form_settings['redirect_to'] === 'url' ) {
                $redirect_to = $this->form_settings['url'];
            } elseif ( $this->form_settings['redirect_to'] === 'same' ) {
                $show_message = true;
            } else {
                $redirect_to = get_permalink( $post_id );
            }
        }

        if ( $charging_enabled === 'yes' && isset( $this->form_settings['enable_pay_per_post'] )
            && wpuf_validate_boolean( $this->form_settings['enable_pay_per_post'] )
            && ! $is_update
        ) {
            $redirect_to = add_query_arg(
                [
                    'action'  => 'wpuf_pay',
                    'type'    => 'post',
                    'post_id' => $post_id,
                ],
                get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) )
            );
        }

        $response = [
            'success'      => true,
            'redirect_to'  => $redirect_to,
            'show_message' => $show_message,
            'message'      => $this->form_settings['message'],
        ];

        $guest_mode     = isset( $this->form_settings['guest_post'] ) ? $this->form_settings['guest_post'] : '';
        $guest_verify   = isset( $this->form_settings['guest_email_verify'] ) ? $this->form_settings['guest_email_verify'] : 'false';

        if ( $guest_mode === 'true' && $guest_verify === 'true' && ! is_user_logged_in() && $charging_enabled !== 'yes' ) {
            $post_id_encoded          = wpuf_encryption( $post_id );
            $form_id_encoded          = wpuf_encryption( $form_id );

            wpuf_send_mail_to_guest( $post_id_encoded, $form_id_encoded, 'no', 1 );

            $response['show_message'] = true;
            $response['redirect_to']  = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
            $response['message']      = __( 'Thank you for posting on our site. We have sent you an confirmation email. Please check your inbox!', 'wp-user-frontend' );
        } elseif ( $guest_mode === 'true' && $guest_verify === 'true' && ! is_user_logged_in() && $charging_enabled === 'yes' ) {
            $post_id_encoded          = wpuf_encryption( $post_id );
            $form_id_encoded          = wpuf_encryption( $form_id );
            $response['show_message'] = true;
            $response['redirect_to']  = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
            $response['message']      = __( 'Thank you for posting on our site. We have sent you an confirmation email. Please check your inbox!', 'wp-user-frontend' );

            update_post_meta( $post_id, '_wpuf_payment_status', 'pending' );
            wpuf_send_mail_to_guest( $post_id_encoded, $form_id_encoded, 'yes', 2 );
        }

        if ( $guest_mode === 'true' && $guest_verify === 'true' && ! is_user_logged_in() ) {
            $response = apply_filters( 'wpuf_edit_post_redirect', $response, $post_id, $form_id, $this->form_settings );
        } elseif ( $is_update ) {
            //now perform some post related actions
            do_action( 'wpuf_edit_post_after_update', $post_id, $form_id, $this->form_settings, $this->form_fields ); // plugin API to extend the functionality

            //send mail notification
            if ( isset( $this->form_settings['notification'] ) && $this->form_settings['notification']['edit'] === 'on' ) {
                $mail_body = $this->prepare_mail_body( $this->form_settings['notification']['edit_body'], $post_author, $post_id );
                $to        = $this->prepare_mail_body( $this->form_settings['notification']['edit_to'], $post_author, $post_id );
                $subject   = $this->prepare_mail_body( $this->form_settings['notification']['edit_subject'], $post_author, $post_id );
                $subject   = wp_strip_all_tags( $subject );
                $mail_body = get_formatted_mail_body( $mail_body, $subject );

                wp_mail( $to, $subject, $mail_body );
            }

            //now redirect the user
            $response = apply_filters( 'wpuf_edit_post_redirect', $response, $post_id, $form_id, $this->form_settings );
        } else {
            // send mail notification
            if ( isset( $this->form_settings['notification'] ) && $this->form_settings['notification']['new'] === 'on' ) {
                $mail_body = $this->prepare_mail_body( $this->form_settings['notification']['new_body'], $post_author, $post_id );
                $to        = $this->prepare_mail_body( $this->form_settings['notification']['new_to'], $post_author, $post_id );
                $subject   = $this->prepare_mail_body( $this->form_settings['notification']['new_subject'], $post_author, $post_id );
                $subject   = wp_strip_all_tags( $subject );
                $mail_body = get_formatted_mail_body( $mail_body, $subject );

                wp_mail( $to, $subject, $mail_body );
            }

            //redirect the user
            $response = apply_filters( 'wpuf_add_post_redirect', $response, $post_id, $form_id, $this->form_settings );
            //now perform some post related actions. it should done after other action.either count related problem emerge
            do_action( 'wpuf_add_post_after_insert', $post_id, $form_id, $this->form_settings, $meta_vars ); // plugin API to extend the functionality

        }

        return $response;
    }

    /**
     * Enable edit post link for post authors
     *
     * @since 3.4.0
     *
     * @param array    $allcaps
     * @param array    $caps
     * @param array    $args
     * @param \WP_User $wp_user
     *
     * @return array
    */
    public function map_capabilities_for_post_authors( $allcaps, $caps, $args, $wp_user ) {
        if (
            empty( $args )
            || count( $args ) < 3
            || empty( $caps )
            || 'edit_post' !== $args[0]
            || isset( $allcaps[ $caps[0] ] )
        ) {
            return $allcaps;
        }

        $post_id = $args[2];
        $post    = get_post( $post_id );

        // We'll show edit link only for posts, not page, product or other post types
        if (
            empty( $post->post_type )
            || 'post' !== $post->post_type
            || ! wpuf_validate_boolean( wpuf_get_option( 'enable_post_edit', 'wpuf_dashboard', 'yes' ) )
            || ! $this->get_frontend_post_edit_link( $post_id )
            || absint( $post->post_author ) !== $wp_user->ID
        ) {
            return $allcaps;
        }

        $allcaps['edit_published_posts'] = 1;

        return $allcaps;
    }

    /**
     * Filter hook for edit post link
     *
     * @since 3.4.0
     *
     * @param string $url
     * @param int    $post_id
     *
     * @return string
    */
    public function get_edit_post_link( $url, $post_id ) {
        if (
            current_user_can( 'edit_post', $post_id )
            && ! current_user_can( 'administrator' )
            && ! current_user_can( 'editor' )
            && ! current_user_can( 'author' )
            && ! current_user_can( 'contributor' )
        ) {
            $post    = get_post( $post_id );
            $form_id = get_post_meta( $post_id, '_wpuf_form_id', true );

            if ( absint( $post->post_author ) === get_current_user_id() && $form_id ) {
                return $this->get_frontend_post_edit_link( $post_id );
            }
        }

        return $url;
    }

    /**
     * Get post edit link
     *
     * @since 3.4.0
     *
     * @param int $post_id
     *
     * @return string
     */
    public function get_frontend_post_edit_link( $post_id ) {
        $edit_page = absint( wpuf_get_option( 'edit_page_id', 'wpuf_frontend_posting' ) );

        if ( ! $edit_page ) {
            return '';
        }

        $url           = add_query_arg( [ 'pid' => $post_id ], get_permalink( $edit_page ) );
        $edit_page_url = apply_filters( 'wpuf_edit_post_link', $url );

        return wp_nonce_url( $edit_page_url, 'wpuf_edit' );
    }
}
