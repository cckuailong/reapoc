<?php

/**
 * Admin side posting handler
 *
 * Builds custom fields UI for post add/edit screen
 * and handles value saving.
 */
class WPUF_Admin_Posting {

    private static $_instance;

    public function __construct() {
        // meta boxes
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes'] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box_form_select'] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box_post_lock'] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_script'] );
        add_action( 'save_post', [ $this, 'save_meta'], 100, 2 ); // save the custom fields
        add_action( 'save_post', [ $this, 'form_selection_metabox_save' ], 1, 2 ); // save edit form id
        add_action( 'save_post', [ $this, 'post_lock_metabox_save' ], 1, 2 ); // save post lock option
        add_action( 'wp_ajax_wpuf_clear_schedule_lock', [$this, 'clear_schedule_lock'] );
    }

    public static function init() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function enqueue_script() {
        global $pagenow;

        if ( !in_array( $pagenow, [ 'profile.php', 'post-new.php', 'post.php', 'user-edit.php' ] ) ) {
            return;
        }

        $scheme  = is_ssl() ? 'https' : 'http';
        $api_key = wpuf_get_option( 'gmap_api_key', 'wpuf_general' );

        wp_enqueue_style( 'jquery-ui', WPUF_ASSET_URI . '/css/jquery-ui-1.9.1.custom.css' );
        wp_enqueue_script( 'jquery-ui-slider' );

        if( ! class_exists('ACF') ) {
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-ui-timepicker', WPUF_ASSET_URI . '/js/jquery-ui-timepicker-addon.js', ['jquery-ui-datepicker'] );
        }

        if ( !empty( $api_key ) ) {
            wp_enqueue_script( 'google-maps', $scheme . '://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, [], null );
        } else {
            add_action( 'admin_head', 'wpuf_hide_google_map_button' );

            function wpuf_hide_google_map_button() {
                echo wp_kses( "<style>
                button.button[data-name='custom_map'] {
                    display: none;
                }
              </style>", [
                    'style' =>  [],
                    'button'    =>  []
                ] );
            }
        }

        wp_enqueue_style( 'wpuf-sweetalert2', WPUF_ASSET_URI . '/vendor/sweetalert2/dist/sweetalert2.css', [], WPUF_VERSION );
        wp_enqueue_script( 'wpuf-sweetalert2', WPUF_ASSET_URI . '/vendor/sweetalert2/dist/sweetalert2.js', [], WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-upload', WPUF_ASSET_URI . '/js/upload.js', ['jquery', 'plupload-handlers'] );
        wp_localize_script( 'wpuf-upload', 'wpuf_frontend_upload', [
            'confirmMsg' => __( 'Are you sure?', 'wp-user-frontend' ),
            'delete_it'  => __( 'Yes, delete it', 'wp-user-frontend' ),
            'cancel_it'  => __( 'No, cancel it', 'wp-user-frontend' ),
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'nonce'      => wp_create_nonce( 'wpuf_nonce' ),
            'plupload'   => [
                'url'              => admin_url( 'admin-ajax.php' ) . '?nonce=' . wp_create_nonce( 'wpuf-upload-nonce' ),
                'flash_swf_url'    => includes_url( 'js/plupload/plupload.flash.swf' ),
                'filters'          => [['title' => __( 'Allowed Files', 'wp-user-frontend' ), 'extensions' => '*']],
                'multipart'        => true,
                'urlstream_upload' => true,
                'warning'          => __( 'Maximum number of files reached!', 'wp-user-frontend' ),
                'size_error'       => __( 'The file you have uploaded exceeds the file size limit. Please try again.', 'wp-user-frontend' ),
                'type_error'       => __( 'You have uploaded an incorrect file type. Please try again.', 'wp-user-frontend' ),
            ],
        ] );
    }

    /**
     * Meta box for all Post form selection
     *
     * Registers a meta box in public post types to select the desired WPUF
     * form select box to assign a form id.
     *
     * @since 2.5.2
     *
     * @return void
     */
    public function add_meta_box_form_select() {
        $post_types = get_post_types( ['public' => true] );

        foreach ( $post_types as $post_type ) {
            add_meta_box( 'wpuf-select-form', __( 'WPUF Form', 'wp-user-frontend' ), [$this, 'form_selection_metabox'], $post_type, 'side', 'high' );
        }
    }

    /**
     * Form selection meta box in post types
     *
     * Registered via $this->add_meta_box_form_select()
     *
     * @since 2.5.2
     *
     * @global object $post
     */
    public function form_selection_metabox() {
        global $post;

        $forms    = get_posts( ['post_type' => 'wpuf_forms', 'numberposts' => '-1'] );
        $selected = get_post_meta( $post->ID, '_wpuf_form_id', true ); ?>

        <!-- <input type="hidden" name="wpuf_form_select_nonce" value="<?php // echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" /> -->
        <?php wp_nonce_field( plugin_basename( __FILE__ ), 'wpuf_form_select_nonce' ); ?>
        <select name="wpuf_form_select">
            <option value="">--</option>
            <?php foreach ( $forms as $form ) { ?>
                <option value="<?php echo esc_attr( $form->ID ); ?>"<?php selected( $selected, $form->ID ); ?>><?php echo esc_html( $form->post_title ); ?></option>
            <?php } ?>
        </select>
        <div>
            <p><a href="https://wedevs.com/docs/wp-user-frontend-pro/tutorials/purpose-of-the-wpuf-form-metabox/" target="_blank"><?php esc_html_e( 'Learn more', 'wp-user-frontend' ); ?></a></p>
        </div>
        <?php
    }

    /**
     * Saves the form ID from form selection meta box
     *
     * @since 2.5.2
     *
     * @param int    $post_id
     * @param object $post
     *
     * @return int|void
     */
    public function form_selection_metabox_save( $post_id, $post ) {
        if ( !isset( $_POST['wpuf_form_select'] ) ) {
            return $post->ID;
        }
        $nonce = isset( $_POST['wpuf_form_select_nonce'] ) ? sanitize_key( wp_unslash( $_POST['wpuf_form_select_nonce'] ) ) : '';
        if ( isset( $nonce ) && !wp_verify_nonce( $nonce , plugin_basename( __FILE__ ) ) ) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ) ) {
            return $post->ID;
        }
        $wpuf_form_select = isset( $_POST['wpuf_form_select'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_form_select'] ) ) : '';

        update_post_meta( $post->ID, '_wpuf_form_id', $wpuf_form_select );
    }

    /**
     * Meta box for post lock
     *
     * Registers a meta box in public post types to select the desired WPUF
     * form select box to assign a form id.
     *
     * @since 3.0.2
     *
     * @return void
     */
    public function add_meta_box_post_lock() {
        $post_types = get_post_types( ['public' => true] );

        foreach ( $post_types as $post_type ) {
            add_meta_box( 'wpuf-post-lock', __( 'WPUF Lock User', 'wp-user-frontend' ), [$this, 'post_lock_metabox'], $post_type, 'side', 'high' );
        }
    }

    /**
     * Post lock meta box in post types
     *
     * Registered via $this->add_meta_box_post_lock()
     *
     * @since 3.0.2
     *
     * @global object $post
     */
    public function post_lock_metabox() {
        global $post;

        $msg                 = '';
        $edit_post_lock      = get_post_meta( $post->ID, '_wpuf_lock_editing_post', true );
        $edit_post_lock_time = get_post_meta( $post->ID, '_wpuf_lock_user_editing_post_time', true );

        if ( empty( $edit_post_lock_time ) ) {
            $is_locked = false;
        }

        if ( ( !empty( $edit_post_lock_time ) && $edit_post_lock_time < time() ) || $edit_post_lock == 'yes' ) {
            $is_locked = true;
            $msg       = sprintf( __( 'Post is locked, to allow user to edit this post <a id="wpuf_clear_schedule_lock" data="%s" href="#">Click here</a>', 'wp-user-frontend' ), $post->ID );
        }

        if ( !empty( $edit_post_lock_time ) && $edit_post_lock_time > time() ) {
            $is_locked    = false;
            $time         = date( 'Y-m-d H:i:s', $edit_post_lock_time );
            $local_time   = get_date_from_gmt( $time, get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
            $msg          = sprintf( __( 'Frontend edit access for this post will be automatically locked after %s, <a id="wpuf_clear_schedule_lock" data="%s" href="#">Clear Lock</a> Or,', 'wp-user-frontend' ), $local_time, $post->ID );
        } ?>

        <!-- <input type="hidden" name="wpuf_lock_editing_post_nonce" value="<?php // echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" /> -->
        <?php wp_nonce_field( plugin_basename( __FILE__ ), 'wpuf_lock_editing_post_nonce' ); ?>
        <p>
            <?php 
                echo wp_kses( $msg, [ 
                    'a' => [
                            'href' => [],
                            'id'   => [],
                            'data' => [],
                        ] 
                    ] ); 
            ?>
        </p>

        <label>
            <?php if ( !$is_locked ) { ?>
                <input type="hidden" name="wpuf_lock_post" value="no">
                <input type="checkbox" name="wpuf_lock_post" value="yes" <?php checked( $edit_post_lock, 'yes' ); ?>>
                <?php esc_html_e( 'Lock Post Permanently', 'wp-user-frontend' ); ?>
            <?php } ?>
        </label>

        <?php if ( !$is_locked ) { ?>
            <p style="margin-top: 10px"><?php esc_html_e( 'Lock user from editing this post from the frontend dashboard', 'wp-user-frontend' ); ?></p>
        <?php } ?>

        <?php
    }

    /**
     * Save the lock post option
     *
     * @since 3.0.2
     *
     * @param int    $post_id
     * @param object $post
     *
     * @return int|void
     */
    public function post_lock_metabox_save( $post_id, $post ) {
        $edit_post_lock_time = isset( $_POST['_wpuf_lock_user_editing_post_time'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpuf_lock_user_editing_post_time'] ) ) : '';

        if ( !isset( $_POST['wpuf_lock_post'] ) ) {
            return $post->ID;
        }

        $nonce = isset( $_POST['wpuf_lock_editing_post_nonce'] ) ? sanitize_key( wp_unslash( $_POST['wpuf_lock_editing_post_nonce'] ) ) : '';

        if ( isset( $nonce ) && !wp_verify_nonce( $nonce, plugin_basename( __FILE__ ) ) ) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ) ) {
            return $post->ID;
        }
        $wpuf_lock_post = isset( $_POST['wpuf_lock_post'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_lock_post'] ) ) : '';

        update_post_meta( $post->ID, '_wpuf_lock_editing_post', $wpuf_lock_post );
    }

    /**
     * Meta box to show WPUF Custom Fields
     *
     * Registers a meta box in public post types to show WPUF Custom Fields
     *
     * @since 2.5
     *
     * @return void
     */
    public function add_meta_boxes() {
        $post_types = get_post_types( ['public' => true] );

        foreach ( $post_types as $post_type ) {
            add_meta_box( 'wpuf-custom-fields', __( 'WPUF Custom Fields', 'wp-user-frontend' ), [$this, 'render_form'], $post_type, 'normal', 'high' );
        }
    }

    /**
     * function to hide form custom field
     *
     * @since 2.5
     *
     * @return void
     */
    public function hide_form() {
        ?>
        <style type="text/css">
            #wpuf-custom-fields { display: none; }
        </style>
        <?php
    }

    /**
     * generate frontend form field
     *
     * @since 2.5
     *
     * @param int $form_id
     * @param int $post_id
     *
     * @return void
     */
    public function render_form( $form_id, $post_id = null ) {
        global $post;

        $form_id       = get_post_meta( $post->ID, '_wpuf_form_id', true );
        $form_settings = wpuf_get_form_settings( $form_id );

        /**
         * There may be incompatibilities with WPUF metabox display when Advanced Custom Fields
         * is active. By default WPUF metaboxes will be hidden when ACF is detected. However,
         * you can override that by using the following filter.
         */
        $hide_with_acf = class_exists( 'acf' ) ? apply_filters( 'wpuf_hide_meta_when_acf_active', true ) : false;

        // hide the metabox itself if no form ID is set
        if ( !$form_id || $hide_with_acf ) {
            $this->hide_form();

            return;
        }

        list( $post_fields, $taxonomy_fields, $custom_fields ) = $this->get_input_fields( $form_id );

        if ( empty( $custom_fields ) ) {
            esc_html_e( 'No custom fields found.', 'wp-user-frontend' );

            return;
        } ?>

        <?php wp_nonce_field( plugin_basename( __FILE__ ), 'wpuf_cf_update' ); ?>
        <!-- <input type="hidden" name="wpuf_cf_update" value="<?php // echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" /> -->
        <input type="hidden" name="wpuf_cf_form_id" value="<?php echo esc_attr( $form_id ); ?>" />

        <table class="form-table wpuf-cf-table">
            <tbody>

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

            <?php
            $atts = [];
            wpuf()->fields->render_fields( $custom_fields, $form_id, $atts, $type = 'post', $post->ID );
            // wp_nonce_field( 'wpuf_form_add' ); ?>
            </tbody>
        </table>
        <?php
        $this->scripts_styles();
    }

    public function scripts_styles() {
        ?>
        <script type="text/javascript">
            jQuery(function($){
                var wpuf = {
                    init: function() {
                        $('.wpuf-cf-table').on('click', 'img.wpuf-clone-field', this.cloneField);
                        $('.wpuf-cf-table').on('click', 'img.wpuf-remove-field', this.removeField);
                        $('.wpuf-cf-table').on('click', 'a.wpuf-delete-avatar', this.deleteAvatar);
                    },
                    cloneField: function(e) {
                        e.preventDefault();

                        var $div = $(this).closest('tr');
                        var $clone = $div.clone();
                        // console.log($clone);

                        //clear the inputs
                        $clone.find('input').val('');
                        $clone.find(':checked').attr('checked', '');
                        $div.after($clone);
                    },

                    removeField: function() {
                        //check if it's the only item
                        var $parent = $(this).closest('tr');
                        var items = $parent.siblings().andSelf().length;

                        if( items > 1 ) {
                            $parent.remove();
                        }
                    },

                    deleteAvatar: function(e) {
                        e.preventDefault();

                        var data = {
                            action: 'wpuf_delete_avatar',
                            user_id : $('#profile-page').find('#user_id').val(),
                            // _wpnonce: '<?php // echo wp_create_nonce( 'wpuf_nonce' ); ?>'
                            _wpnonce: wpuf_admin_script.nonce
                        };

                        if ( confirm( $(this).data('confirm') ) ) {
                            $.post(ajaxurl, data, function() {
                                window.location.reload();
                            });
                        }
                    }
                };

                wpuf.init();
            });

        </script>
        <style type="text/css">
            ul.wpuf-attachment-list li {
                display: inline-block;
                border: 1px solid #dfdfdf;
                padding: 5px;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                margin-right: 5px;
            }
            .wpuf-cf-table table th,
            .wpuf-cf-table table td{
                padding-left: 0 !important;
            }

            .wpuf-cf-table .required { color: red;}
            .wpuf-cf-table textarea { width: 400px; }

            .wpuf-field-google-map {
                height: 300px;
                width: 100%;
            }
            .wpuf-form-google-map {
                height: 300px;
                width: 100%;
            }
            input[type="text"].wpuf-google-map-search {
                margin-top: 10px !important;
                border: 1px solid transparent !important;
                border-radius: 2px 0 0 2px !important;
                box-sizing: border-box !important;
                -moz-box-sizing: border-box !important;
                height: 32px !important;
                outline: none !important;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3) !important;
                background-color: #fff !important;
                text-overflow: ellipsis !important;
                width: 170px !important;
                font-family: Roboto !important;
                font-size: 15px !important;
                font-weight: 300 !important;
                padding: 0 11px 0 13px !important;
                display: none;
            }
            .gm-style input[type="text"].wpuf-google-map-search {
                display: block;
            }
            .wpuf-form-google-map-container input[type="text"].wpuf-google-map-search {
                width: 230px !important;
            }
            .wpuf-form-google-map-container.hide-search-box .gm-style input[type="text"].wpuf-google-map-search {
                display: none;
            }

        </style>
        <?php
    }

    /**
     * save post meta
     *
     * @since 2.5
     *
     * @param object $post
     *
     * @return void
     */
    // Save the Metabox Data
    public function save_meta( $post_id, $post = null ) {
        $wpuf_cf_update = isset( $_POST['wpuf_cf_update'] ) ? sanitize_key( wp_unslash( $_POST['wpuf_cf_update'] ) ) : '';
        $wpuf_cf_form_id  = isset( $_POST['wpuf_cf_form_id'] ) ? intval( $_POST['wpuf_cf_form_id'] ) : 0;

        if ( !isset( $post_id ) ) {
            return;
        }

        if ( empty( $wpuf_cf_update ) ) {
            return $post_id;
        }

        if ( isset( $wpuf_cf_update ) && !wp_verify_nonce( $wpuf_cf_update, plugin_basename( __FILE__ ) ) ) {
            return $post_id;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        list( $post_vars, $tax_vars, $meta_vars ) = self::get_input_fields( $wpuf_cf_form_id );

        // WPUF_Frontend_Form_Post::update_post_meta( $meta_vars, $post_id );
        WPUF_Frontend_Form::update_post_meta( $meta_vars, $post_id );
    }

    /**
     * Clear Schedule lock
     *
     * @since 3.0.2
     */
    public function clear_schedule_lock() {
        check_ajax_referer( 'wpuf_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : '';

        if ( !empty( $post_id ) ) {
            update_post_meta( $post_id, '_wpuf_lock_user_editing_post_time', '' );
            update_post_meta( $post_id, '_wpuf_lock_editing_post', 'no' );
        }
        exit;
    }

    /**
     * Get input meta fields separated as post vars, taxonomy and meta vars
     *
     * @param int $form_id form id
     *
     * @return array
     */
    public static function get_input_fields( $form_id ) {
        $form_vars    = wpuf_get_form_fields( $form_id );

        $ignore_lists = ['section_break', 'html'];
        $post_vars    = $meta_vars = $taxonomy_vars = [];

        foreach ( $form_vars as $key => $value ) {
            // get column field input fields
            if ( $value['input_type'] == 'column_field' ) {
                $inner_fields = $value['inner_fields'];

                foreach ( $inner_fields as $column_key => $column_fields ) {
                    if ( !empty( $column_fields ) ) {
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
                                if ( $column_field['name'] == 'category' ) {
                                    continue;
                                }

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
                if ( $value['name'] == 'category' ) {
                    continue;
                }

                $taxonomy_vars[] = $value;
            } else {
                $post_vars[] = $value;
            }
        }

        return [$post_vars, $taxonomy_vars, $meta_vars];
    }
}
