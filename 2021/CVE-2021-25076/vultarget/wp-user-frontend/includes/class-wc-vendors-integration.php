<?php

if ( ! class_exists( 'WPUF_WC_Vendors_Integration' ) ) {

    /**
     * WC Vendors Integration Class
     *
     * @since  3.0
     */
    class WPUF_WC_Vendors_Integration {

        public function __construct() {
            add_action( 'wcvendors_after_links', [ $this, 'add_wpuf_posts_page' ] );
            add_action( 'wcvendors_after_dashboard', [ $this, 'after_dashboard' ] );

            add_filter( 'wcvendors_get_settings_general', [ $this, 'add_wpuf_options' ], 10, 2 );
            add_filter( 'wpuf_edit_post_link', [ $this, 'generate_edit_post_link' ] );
            add_filter( 'wpuf_edit_post_redirect', [ $this, 'update_edit_post_redirect_url' ], 10, 4 );
            add_filter( 'wpuf_delete_post_redirect', [ $this, 'update_delete_post_redirect_url' ] );
        }

        /**
         * WC Vendors settings for WPUF integration
         *
         * @param array $settings_fields
         *
         * @since  3.0
         *
         * @return array $settings_fields
         */
        public function add_wpuf_options( $settings, $current_section ) {
            $last_option = end( $settings );
            array_pop( $settings );

            $settings[] = [
                'title'     => __( 'Allow Post', 'wp-user-frontend' ),
                'desc'      => __( 'If checked, vendor can submit post from dashboard area.', 'wp-user-frontend' ),
                'id'        => 'allow_wcvendors_wpuf_post',
                'default'   => 'no',
                'type'      => 'checkbox',
            ];

            $settings[] = [
                'title'     => __( 'Select Post Form', 'wp-user-frontend' ),
                'desc_tip'  => __( 'Select a post form that will show on the vendor dashboard.', 'wp-user-frontend' ),
                'id'        => 'wcvendors_wpuf_allowed_post_form',
                'type'      => 'select',
                'class'     => 'wc-enhanced-select',
                'options'   => $this->get_post_forms(),
            ];

            array_push( $settings, $last_option );

            return $settings;
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
         * Insert new URL's to the frontend dashboard navigation bar
         *
         * @param array $urls
         *
         * @since  3.0
         *
         * @return array
         */
        public function add_wpuf_posts_page() {
            $allow_wpuf_post = get_option( 'allow_wcvendors_wpuf_post', 'no' );

            if ( $allow_wpuf_post === 'yes' ) {
                $dashboard_url = get_permalink( get_option( 'wcvendors_vendor_dashboard_page_id' ) );
                $post_page_url = add_query_arg(
                    [
                        'action' => 'post-listing',
                    ], $dashboard_url
                );

                $output  = '<a href="' . $post_page_url . '" class="button">';
                $output .= __( 'Posts', 'wp-user-frontend' );
                $output .= '</a>';

                echo wp_kses_post( $output );
            }
        }

        /**
         * Include requrired template & load additional style, script after dashboard
         */
        public function after_dashboard() {
            $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

            if ( $action === 'post-listing' || $action === 'new-post' || $action === 'edit-post' || $action === 'del' ) {
                wpuf_load_template( 'wc-vendors/posts.php' ); ?>
            <script type="text/javascript">
                var WPUFContent = document.querySelector('.wpuf-wc-vendors-submit-post-page');
                var WCVendorArea = WPUFContent.parentElement;
                WCVendorArea.className += " hide-if-wpuf-page";
            </script>
            <style type="text/css">
                .table-vendor-sales-report,
                .wpuf-wc-vendors-submit-post-page .page-head,
                .hide-if-wpuf-page h2,
                .hide-if-wpuf-page>form{
                    display: none;
                }

                .wpuf-wc-vendors-submit-post-page .pull-right{
                    float: right;
                }

                .wpuf-wc-vendors-submit-post-page .post_count{
                    margin-bottom: 30px;
                    margin-top: 20px;
                }

                .wpuf-wc-vendors-submit-post-page .wpuf-add-post-button{
                    background: #1a1a1a;
                    border: 0;
                    border-radius: 2px;
                    color: #fff;
                    font-family: Montserrat, "Helvetica Neue", sans-serif;
                    font-weight: 700;
                    letter-spacing: 0.046875em;
                    line-height: 1;
                    padding: 0.84375em 0.875em 0.78125em;
                    text-transform: uppercase;
                }
                .wpuf-wc-vendors-submit-post-page .wpuf-add-post-button:hover,
                .wpuf-wc-vendors-submit-post-page .wpuf-add-post-button:focus,
                .wpuf-wc-vendors-submit-post-page .wpuf-add-post-button:active{
                    background: #007acc;
                }
            </style>
                <?php
            }
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

            $posts_page_url = get_permalink( get_option( 'wcvendors_vendor_dashboard_page_id' ) );

            if ( is_page( get_option( 'wcvendors_vendor_dashboard_page_id' ) ) ) {
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
            $selected_form = get_option( 'wcvendors_wpuf_allowed_post_form', '' );

            if ( $role[0] === 'vendor' && $form_id === $selected_form && $form_settings['edit_redirect_to'] === 'same' ) {
                $post_page_url = get_permalink( get_option( 'wcvendors_vendor_dashboard_page_id' ) );

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
            if ( is_page( get_option( 'wcvendors_vendor_dashboard_page_id' ) ) ) {
                $post_page_url = get_permalink( get_option( 'wcvendors_vendor_dashboard_page_id' ) );

                $redirect_url = add_query_arg(
                    [
                        'action' => 'post-listing',
                        'msg'    => 'deleted',
                    ],
                    $post_page_url
                );
            }

            return $redirect_url;
        }
    }
}
