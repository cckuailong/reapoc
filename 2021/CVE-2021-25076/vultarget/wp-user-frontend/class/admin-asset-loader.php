<?php

/**
 * The Admin Asset Loader Class
 */
class WPUF_Admin_Assets {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );
        add_action( 'wp_enqueue_style', [ $this, 'register_admin_styles' ] );
    }

    /**
     * Register the scripts
     *
     * @return void
     */
    public function register_admin_scripts() {
        global $post;
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        wp_register_script( 'wpuf-vue', WPUF_ASSET_URI . '/vendor/vue/vue' . $prefix . '.js', [], WPUF_VERSION, true );
        wp_register_script( 'wpuf-vuex', WPUF_ASSET_URI . '/vendor/vuex/vuex' . $prefix . '.js', [ 'wpuf-vue' ], WPUF_VERSION, true );
        wp_register_script( 'wpuf-sweetalert2', WPUF_ASSET_URI . '/vendor/sweetalert2/dist/sweetalert2.js', [], WPUF_VERSION, true );
        wp_register_script( 'wpuf-jquery-scrollTo', WPUF_ASSET_URI . '/vendor/jquery.scrollTo/jquery.scrollTo' . $prefix . '.js', [ 'jquery' ], WPUF_VERSION, true );
        wp_register_script( 'wpuf-selectize', WPUF_ASSET_URI . '/vendor/selectize/js/standalone/selectize' . $prefix . '.js', [ 'jquery' ], WPUF_VERSION, true );
        wp_register_script( 'wpuf-toastr', WPUF_ASSET_URI . '/vendor/toastr/toastr' . $prefix . '.js', [], WPUF_VERSION, true );
        wp_register_script( 'wpuf-clipboard', WPUF_ASSET_URI . '/vendor/clipboard/clipboard' . $prefix . '.js', [], WPUF_VERSION, true );
        wp_register_script( 'wpuf-tooltip', WPUF_ASSET_URI . '/vendor/tooltip/tooltip' . $prefix . '.js', [], WPUF_VERSION, true );

        $form_builder_js_deps = apply_filters( 'wpuf-form-builder-js-deps', [
            'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'underscore',
            'wpuf-vue', 'wpuf-vuex', 'wpuf-sweetalert2', 'wpuf-jquery-scrollTo',
            'wpuf-selectize', 'wpuf-toastr', 'wpuf-clipboard', 'wpuf-tooltip',
        ] );

        wp_register_script( 'wpuf-form-builder-mixins', WPUF_ASSET_URI . '/js/wpuf-form-builder-mixins.js', $form_builder_js_deps, WPUF_VERSION, true );
        wp_register_script( 'wpuf-form-builder-components', WPUF_ASSET_URI . '/js/wpuf-form-builder-components.js', [ 'wpuf-form-builder-mixins' ], WPUF_VERSION, true );
        wp_register_script( 'wpuf-form-builder', WPUF_ASSET_URI . '/js/wpuf-form-builder.js', [ 'wpuf-form-builder-components' ], WPUF_VERSION, true );

        /*
         * Data required for building the form
         */
        require_once WPUF_ROOT . '/admin/form-builder/class-wpuf-form-builder-field-settings.php';
        require_once WPUF_ROOT . '/includes/free/prompt.php';

        $wpuf_form_builder = apply_filters( 'wpuf-form-builder-localize-script', [
            'post'              => $post,
            'form_fields'       => wpuf_get_form_fields( $post->ID ),
            'field_settings'    => wpuf()->fields->get_js_settings(),
            'notifications'     => wpuf_get_form_notifications( $post->ID ),
            'pro_link'          => WPUF_Pro_Prompt::get_pro_url(),
            'site_url'          => site_url( '/' ),
            'recaptcha_site'    => wpuf_get_option( 'recaptcha_public', 'wpuf_general' ),
            'recaptcha_secret'  => wpuf_get_option( 'recaptcha_private', 'wpuf_general' ),
        ] );

        wp_localize_script( 'wpuf-form-builder-mixins', 'wpuf_form_builder', $wpuf_form_builder );

        // mixins
        $wpuf_mixins = [
            'root'           => apply_filters( 'wpuf-form-builder-js-root-mixins', [] ),
            'builder_stage'  => apply_filters( 'wpuf-form-builder-js-builder-stage-mixins', [] ),
            'form_fields'    => apply_filters( 'wpuf-form-builder-js-form-fields-mixins', [] ),
            'field_options'  => apply_filters( 'wpuf-form-builder-js-field-options-mixins', [] ),
        ];

        wp_localize_script( 'wpuf-form-builder-mixins', 'wpuf_mixins', $wpuf_mixins );
    }

    /**
     * Register the styles
     *
     * @return void
     */
    public function register_admin_styles() {
        wp_register_style( 'wpuf-css', WPUF_ASSET_URI . '/css/frontend-forms.css' );
        wp_register_style( 'wpuf-font-awesome', WPUF_ASSET_URI . '/vendor/font-awesome/css/font-awesome.min.css', [], WPUF_VERSION );
        wp_register_style( 'wpuf-sweetalert2', WPUF_ASSET_URI . '/vendor/sweetalert2/dist/sweetalert2.css', [], WPUF_VERSION );
        wp_register_style( 'wpuf-selectize', WPUF_ASSET_URI . '/vendor/selectize/css/selectize.default.css', [], WPUF_VERSION );
        wp_register_style( 'wpuf-toastr', WPUF_ASSET_URI . '/vendor/toastr/toastr.min.css', [], WPUF_VERSION );
        wp_register_style( 'wpuf-tooltip', WPUF_ASSET_URI . '/vendor/tooltip/tooltip.css', [], WPUF_VERSION );

        $form_builder_css_deps = apply_filters( 'wpuf-form-builder-css-deps', [
            'wpuf-css', 'wpuf-font-awesome', 'wpuf-sweetalert2', 'wpuf-selectize', 'wpuf-toastr', 'wpuf-tooltip',
        ] );

        wp_register_style( 'wpuf-form-builder', WPUF_ASSET_URI . '/css/wpuf-form-builder.css', $form_builder_css_deps, WPUF_VERSION );
    }
}
