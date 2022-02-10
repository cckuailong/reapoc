<?php

if ( ! class_exists( 'WPUF_WCMp_Integration' ) ) {

    /**
     * WC Marketplace Integration Class
     *
     * @since  3.0
     */
    class WPUF_WCMp_Integration {

        public function __construct() {
            add_filter( 'wcmp_general_tab_filds', [ $this, 'add_wpuf_options' ] );
            add_filter( 'settings_general_tab_new_input', [ $this, 'option_fields_sanitize' ], 10, 2 );
            add_filter( 'settings_vendor_general_tab_options', [ $this, 'add_dashboard_endpoints' ] );
            add_filter( 'wcmp_vendor_dashboard_nav', [ $this, 'add_wpuf_posts_page' ] );
            add_filter( 'wcmp_endpoints_query_vars', [ $this, 'add_query_var' ] );
            add_filter( 'settings_vendor_general_tab_new_input', [ $this, 'endpoint_option_fields_sanitize' ], 10, 2 );
            add_action( 'wcmp_vendor_dashboard_submit-post_endpoint', [ $this, 'wcmp_vendor_dashboard_submit_post_endpoint' ] );
            add_filter( 'wpuf_edit_post_link', [ $this, 'generate_edit_post_link' ] );
            add_filter( 'wpuf_edit_post_redirect', [ $this, 'update_edit_post_redirect_url' ], 10, 4 );
            add_filter( 'wpuf_delete_post_redirect', [ $this, 'update_delete_post_redirect_url' ] );
        }

        /**
         * WC Marketplace settings for WPUF integration
         *
         * @param array $settings_fields
         *
         * @since  3.0
         *
         * @return array $settings_fields
         */
        public function add_wpuf_options( $settings_fields ) {
            $settings_fields['allow_wpuf_post'] = [
                'title'     => __( 'Allow Post', 'wp-user-frontend' ),
                'type'      => 'checkbox',
                'id'        => 'allow_wpuf_post',
                'label_for' => 'allow_wpuf_post',
                'text'      => __( 'If checked, vendor can submit post from dashboard area.', 'wp-user-frontend' ),
                'name'      => 'allow_wpuf_post',
                'value'     => 'yes',
            ];

            $settings_fields['wpuf_post_forms'] = [
                'title'     => __( 'Select Post Form', 'wp-user-frontend' ),
                'type'      => 'select',
                'id'        => 'wpuf_post_forms',
                'name'      => 'wpuf_post_forms',
                'label_for' => 'wpuf_post_forms',
                'desc'      => stripslashes( __( 'Select a post form that will show on the vendor dashboard.', 'wp-user-frontend' ) ),
                'options'   => $this->get_post_forms(),
            ];

            return $settings_fields;
        }

        /**
         * Sanitize option fields
         *
         * @param array $new_input, $input
         *
         * @since  3.0
         *
         * @return array $new_input
         */
        public function option_fields_sanitize( $new_input, $input ) {
            if ( isset( $input['allow_wpuf_post'] ) ) {
                $new_input['allow_wpuf_post'] = sanitize_text_field( $input['allow_wpuf_post'] );
            }

            if ( isset( $input['wpuf_post_forms'] ) ) {
                $new_input['wpuf_post_forms'] = $input['wpuf_post_forms'];
            }

            return $new_input;
        }

        /**
         * Add vendor dashboard endpoint for submit post menu
         */
        public function add_dashboard_endpoints( $settings_tab_options ) {
            $settings_tab_options['sections']['wcmp_vendor_general_settings_endpoint_ssection']['fields']['wcmp_vendor_submit_post_endpoint'] = [
                'title'       => __( 'Vendor Submit Post', 'wp-user-frontend' ),
                'type'        => 'text',
                'id'          => 'wcmp_vendor_submit_post_endpoint',
                'label_for'   => 'wcmp_vendor_submit_post_endpoint',
                'name'        => 'wcmp_vendor_submit_post_endpoint',
                'hints'       => __( 'Set endpoint for vendor submit post page', 'wp-user-frontend' ),
                'placeholder' => 'submit-post',
            ];

            return $settings_tab_options;
        }

        /**
         * Update option field data
         */
        public function endpoint_option_fields_sanitize( $new_input, $input ) {
            if ( isset( $input['wcmp_vendor_submit_post_endpoint'] ) && ! empty( $input['wcmp_vendor_submit_post_endpoint'] ) ) {
                $new_input['wcmp_vendor_submit_post_endpoint'] = sanitize_text_field( $input['wcmp_vendor_submit_post_endpoint'] );
            }

            return $new_input;
        }

        /**
         * Template for vendor submit post page
         */
        public function wcmp_vendor_dashboard_submit_post_endpoint() {
            //phpcs:ignore
            global $WCMp, $wp;
            wpuf_load_template( 'wc-marketplace/posts.php' );
        }

        /**
         * Insert new URL's to the dashboard navigation bar
         *
         * @param array $urls
         *
         * @since  2.7
         *
         * @return array
         */
        public function add_wpuf_posts_page( $vendor_nav ) {
            $vendor_nav['submit-post'] = [
                'label'       => __( 'Submit Post', 'wp-user-frontend' ),
                'url'         => wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_vendor_submit_post_endpoint', 'vendor', 'general', 'submit-post' ) ),
                'capability'  => apply_filters( 'wcmp_vendor_dashboard_menu_vendor_submit_post_capability', true ),
                'position'    => 90,
                'submenu'     => [],
                'link_target' => '_self',
                'nav_icon'    => 'wcmp-font ico-plus-icon',
            ];

            return $vendor_nav;
        }

        /**
         * Add query var for vendor submit post page
         */
        public function add_query_var( $query_vars ) {
            $query_vars['submit-post'] = [
                'label'    => __( 'Submit Post', 'wp-user-frontend' ),
                'endpoint' => get_wcmp_vendor_settings( 'wcmp_vendor_submit_post_endpoint', 'vendor', 'general', 'submit-post' ),
            ];

            return $query_vars;
        }

        /**
         * Get all the post forms
         *
         * @param string $post_type
         *
         * @since  3.0
         *
         * @return array $post_forms
         */
        public function get_post_forms( $post_type = 'post' ) {
            $post_forms = [];

            $args = [
                'post_type'   => 'wpuf_forms',
                'post_status' => 'publish',
                'numberposts' => -1,
            ];

            $form_posts = get_posts( $args );

            foreach ( $form_posts as $form ) {
                $form_settings  = wpuf_get_form_settings( $form->ID );
                $form_post_type = isset( $form_settings['post_type'] ) ? $form_settings['post_type'] : '';

                if ( $form_post_type === $post_type ) {
                    $post_forms[ $form->ID ] = $form->post_title;
                }
            }

            return $post_forms;
        }

        /**
         * Generate edit post link
         *
         * @param string $url
         *
         * @since  3.0
         *
         * @return string $url
         */
        public function generate_edit_post_link( $url ) {
            global $post;

            $posts_page_url = wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_vendor_submit_post_endpoint', 'vendor', 'general', 'submit-post' ) );

            if ( is_vendor_dashboard() ) {
                $url = add_query_arg(
                    [
                        'action' => 'edit-post',
                        'pid'    => $post->ID,
                    ],
                    $posts_page_url
                );
            }

            return $url;
        }

        /**
         * Redirect user after editing post from vendor dashboard
         */
        public function update_edit_post_redirect_url( $response, $post_id, $form_id, $form_settings ) {
            $user          = wp_get_current_user();
            $role          = (array) $user->roles;
            $selected_form = ( get_wcmp_vendor_settings( 'wpuf_post_forms', 'general' ) ) ? get_wcmp_vendor_settings( 'wpuf_post_forms', 'general' ) : '';

            if ( $role[0] === 'dc_vendor' && $form_id === $selected_form && $form_settings['edit_redirect_to'] === 'same' ) {
                $post_page_url = wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_vendor_submit_post_endpoint', 'vendor', 'general', 'submit-post' ) );

                $redirect_url = add_query_arg(
                    [
                        'action'    => 'edit-post',
                        'pid'       => $post_id,
                        '_wpnonce'  => wp_create_nonce( 'wpuf_edit' ),
                        'msg'       => 'post_updated',
                    ],
                    $post_page_url
                );

                $response['redirect_to'] = $redirect_url;
            }

            return $response;
        }

        /**
         * Redirect user after deleting post from vendor dashboard
         */
        public function update_delete_post_redirect_url( $redirect_url ) {
            if ( is_vendor_dashboard() ) {
                $post_page_url = wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_vendor_submit_post_endpoint', 'vendor', 'general', 'submit-post' ) );

                $redirect_url = add_query_arg(
                    [
                        'msg' => 'deleted',
                    ],
                    $post_page_url
                );
            }

            return $redirect_url;
        }
    }
}
