<?php

if ( ! class_exists( 'WPUF_Dokan_Integration' ) ) {

    /**
     * WPUF Dokan Integration Class
     *
     * @since  2.7
     */
    class WPUF_Dokan_Integration {

        public function __construct() {
            add_filter( 'dokan_get_dashboard_nav', [ $this, 'add_wpuf_posts_page' ] );
            add_action( 'dokan_load_custom_template', [ $this, 'load_wpuf_posts_template' ] );
            add_filter( 'dokan_query_var_filter', [ $this, 'register_wpuf_posts_queryvar' ] );
            add_filter( 'dokan_settings_fields', [ $this, 'dokan_wpuf_settings' ] );
            add_filter( 'wpuf_edit_post_link', [ $this, 'generate_edit_post_link' ] );
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
        public function add_wpuf_posts_page( $urls ) {
            $access = dokan_get_option( 'allow_wpuf_post', 'dokan_general' );

            if ( $access === 'on' ) {
                $urls['posts'] = [
                    'title' => __( 'Posts', 'wp-user-frontend' ),
                    'icon'  => '<i class="fa fa-wordpress"></i>',
                    'url'   => dokan_get_navigation_url( 'posts' ),
                    'pos'   => 56,
                ];
            }

            return $urls;
        }

        /**
         * Load posts template
         *
         * @param array $query_vars
         *
         * @since  2.7
         *
         * @return void
         */
        public function load_wpuf_posts_template( $query_vars ) {
            if ( isset( $query_vars['posts'] ) ) {
                wpuf_load_template( 'dokan/posts.php' );
            }
        }

        /**
         * Register WPUF query var
         *
         * @param array $query_vars
         *
         * @since  2.7
         *
         * @return void
         */
        public function register_wpuf_posts_queryvar( $query_vars ) {
            $query_vars[] = 'posts';

            return $query_vars;
        }

        /**
         * Dokan settings for WPUF integration
         *
         * @param array $settings_fields
         *
         * @since  2.7
         *
         * @return array $settings_fields
         */
        public function dokan_wpuf_settings( $settings_fields ) {
            $settings_fields['dokan_general']['allow_wpuf_post'] = [
                'name'    => 'allow_wpuf_post',
                'label'   => __( 'Allow Post', 'wp-user-frontend' ),
                'desc'    => __( 'Allow Vendors to submit post from dashboard area', 'wp-user-frontend' ),
                'type'    => 'checkbox',
                'default' => 'off',
            ];

            $settings_fields['dokan_general']['wpuf_post_forms'] = [
                'name'    => 'wpuf_post_forms',
                'label'   => __( 'Select Post Form', 'wp-user-frontend' ),
                'desc'    => __( 'Select a post form that will show on the vendor dashboard.', 'wp-user-frontend' ),
                'type'    => 'select',
                'options' => $this->get_post_forms(),
                'default' => 'seller',
            ];

            return $settings_fields;
        }

        /**
         * Get all the post forms
         *
         * @param string $post_type
         *
         * @since  2.7
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
         * @since  2.7
         *
         * @return string $url
         */
        public function generate_edit_post_link( $url ) {
            global $post;

            $posts_page_url = dokan_get_navigation_url( 'posts' );
            $dashboard      = (int) dokan_get_option( 'dashboard', 'dokan_pages' );

            if ( is_page( $dashboard ) ) {
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
    }
}
