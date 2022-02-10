<?php

/**
 * The Asset Loader Class
 */
class WPUF_Assets {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
    }

    /**
     * Register the scripts
     *
     * @return void
     */
    public function register_scripts() {
        global $post;
        $prefix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $scheme  = is_ssl() ? 'https' : 'http';
        $api_key = wpuf_get_option( 'gmap_api_key', 'wpuf_general' );

        if ( !empty( $api_key ) ) {
            wp_register_script( 'google-maps', $scheme . '://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, [], null );
        }

        if ( isset( $post->ID ) ) {
            ?>
            <script type="text/javascript" id="wpuf-language-script">
                var error_str_obj = {
                    'required' : '<?php esc_attr_e( 'is required', 'wp-user-frontend' ); ?>',
                    'mismatch' : '<?php esc_attr_e( 'does not match', 'wp-user-frontend' ); ?>',
                    'validation' : '<?php esc_attr_e( 'is not valid', 'wp-user-frontend' ); ?>'
                }
            </script>
            <?php
            wp_register_script( 'wpuf-form', WPUF_ASSET_URI . '/js/frontend-form' . $prefix . '.js', ['jquery'] );
        }
        wp_register_script( 'wpuf-subscriptions', WPUF_ASSET_URI . '/js/subscriptions.js', ['jquery'], false, true );
        wp_register_script( 'jquery-ui-timepicker', WPUF_ASSET_URI . '/js/jquery-ui-timepicker-addon.js', ['jquery-ui-datepicker'] );
        wp_register_script( 'wpuf-upload', WPUF_ASSET_URI . '/js/upload.js', ['jquery', 'plupload-handlers'] );

        wp_localize_script( 'wpuf-form', 'wpuf_frontend', [
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'error_message' => __( 'Please fix the errors to proceed', 'wp-user-frontend' ),
            'nonce'         => wp_create_nonce( 'wpuf_nonce' ),
            'word_limit'    => __( 'Word limit reached', 'wp-user-frontend' ),
            'cancelSubMsg'  => __( 'Are you sure you want to cancel your current subscription ?', 'wp-user-frontend' ),
            'delete_it'     => __( 'Yes', 'wp-user-frontend' ),
            'cancel_it'     => __( 'No', 'wp-user-frontend' ),
        ] );

        wp_localize_script( 'wpuf-upload', 'wpuf_frontend_upload', [
            'confirmMsg' => __( 'Are you sure?', 'wp-user-frontend' ),
            'delete_it'  => __( 'Yes, delete it', 'wp-user-frontend' ),
            'cancel_it'  => __( 'No, cancel it', 'wp-user-frontend' ),
            'nonce'      => wp_create_nonce( 'wpuf_nonce' ),
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
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

        wp_register_script( 'wpuf-vue', WPUF_ASSET_URI . '/vendor/vue/vue' . $prefix . '.js', [], WPUF_VERSION, true );
        wp_register_script( 'wpuf-vuex', WPUF_ASSET_URI . '/vendor/vuex/vuex' . $prefix . '.js', [ 'wpuf-vue' ], WPUF_VERSION, true );
        wp_register_script( 'wpuf-sweetalert2', WPUF_ASSET_URI . '/vendor/sweetalert2/dist/sweetalert2.js', [], WPUF_VERSION, true );
        wp_register_script( 'wpuf-jquery-scrollTo', WPUF_ASSET_URI . '/vendor/jquery.scrollTo/jquery.scrollTo' . $prefix . '.js', [ 'jquery' ], WPUF_VERSION, true );
        wp_register_script( 'wpuf-selectize', WPUF_ASSET_URI . '/vendor/selectize/js/standalone/selectize' . $prefix . '.js', [ 'jquery' ], WPUF_VERSION, true );
        wp_register_script( 'wpuf-toastr', WPUF_ASSET_URI . '/vendor/toastr/toastr' . $prefix . '.js', [], WPUF_VERSION, true );
        wp_register_script( 'wpuf-clipboard', WPUF_ASSET_URI . '/vendor/clipboard/clipboard' . $prefix . '.js', [], WPUF_VERSION, true );
        wp_register_script( 'wpuf-tooltip', WPUF_ASSET_URI . '/vendor/tooltip/tooltip' . $prefix . '.js', [], WPUF_VERSION, true );
    }

    /**
     * Register the styles
     *
     * @return void
     */
    public function register_styles() {
        wp_register_style( 'wpuf-css', WPUF_ASSET_URI . '/css/frontend-forms.css' );
        wp_register_style( 'jquery-ui', WPUF_ASSET_URI . '/css/jquery-ui-1.9.1.custom.css' );
    }
}
