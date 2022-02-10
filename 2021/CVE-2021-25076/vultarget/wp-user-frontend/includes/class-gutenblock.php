<?php if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Adds WPUF block
 */
class WPUF_Form_Block {
    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        //Enqueue the Dashicons script as they are not loading
        //when wpuf_form shortcode exists in admin pages.
        add_action( 'admin_enqueue_scripts', [ $this, 'load_dashicons' ] );
        // wait for Gutenberg to enqueue it's block assets
        add_action( 'enqueue_block_editor_assets', [ $this, 'wpuf_form_block' ], 10 );
        // load the preview information and form
        add_action( 'wp_head', [ $this, 'load_preview_data' ] );
    }

    public function load_dashicons() {
        // load dashicons & editor style as they are not loading when wpuf_form shortcode exists in admin pages.
        wp_register_style( 'wpuf_dashicons', includes_url() . 'css/dashicons.css', false, '1.0.0' );
        wp_enqueue_style( 'wpuf_dashicons' );
    }

    public function wpuf_form_block() {
        $js_dir  = WPUF_ASSET_URI . '/js/admin/';
        $css_dir = WPUF_ASSET_URI . '/css/admin/';

        // Once we have Gutenberg block javascript, we can enqueue our assets
        wp_register_script( 
            'wpuf-forms-block',
            $js_dir . 'gutenblock.js',
            [ 'wp-blocks', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-element', 'underscore' ]
         );

        wp_register_style( 
            'wpuf-forms-block-style',
            $css_dir . 'gutenblock.css',
            [ 'wp-edit-blocks' ]
         );
        wp_register_style( 
            'wpuf-forms-block-editor',
            $css_dir . 'gutenblock-editor.css',
            [ 'wp-edit-blocks', 'wpuf-forms-block-style' ]
         );

        /*
         * we need to get our forms so that the block can build a dropdown
         * with the forms
         * */
        wp_enqueue_script( 'wpuf-forms-block' );

        $forms      = [];
        $all_forms  = wpuf()->forms->get_forms( [ 'post_status' => 'publish' ] );

        foreach ( $all_forms['forms'] as $form ) {
            $forms[] = [
                'value' => $form->id,
                'label' => $form->get_title(),
            ];
        }

        $block_logo     = WPUF_ASSET_URI . '/images/icon-128x128.png';
        $thumbnail_logo = WPUF_ASSET_URI . '/images/icon-128x128.png';

        wp_localize_script( 'wpuf-forms-block', 'wpufBlock', [
            'forms'          => $forms,
            'siteUrl'        => get_home_url(),
            'block_logo'     => $block_logo,
            'thumbnail_logo' => $thumbnail_logo,
        ] );
        wp_enqueue_style( 'wpuf-forms-block-style' );
        wp_enqueue_style( 'wpuf-forms-block-editor' );
    }

    public function load_preview_data() {
        $js_dir  = WPUF_ASSET_URI . '/js/admin/';

        // check for preview and iframe get parameters
        if ( isset( $_GET[ 'wpuf_preview' ] ) && isset( $_GET[ 'wpuf_iframe' ] ) ) {
            $form_id = intval( $_GET[ 'wpuf_preview' ] );
            // Style below: update width and height for particular form ?>
            <style media="screen">
                #wpadminbar {
                    display: none;
                }
                header,
                footer{
                    display: none;
                }

                .wpuf-form-add {
                    z-index: 9001;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background-color: white;
                    display: block !important;
                }

            </style>
            <?php

            // register our script to target the form iFrame in page builder
            wp_register_script( 
                'wpuf-block-setup',
                $js_dir . 'blockFrameSetup.js',
                [ 'underscore', 'jquery' ]
             );

            wp_localize_script( 'wpuf-block-setup', 'wpufBlockSetup', [
                'form_id' => $form_id,
            ] );

            wp_enqueue_script( 'wpuf-block-setup' );
        }
    }
}
