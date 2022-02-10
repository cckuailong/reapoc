<?php

namespace NotificationX\Core;
use NotificationX\GetInstance;

class Locations {
    /**
     * Instance of Locations
     *
     * @var Locations
     */
    use GetInstance;

    public function __construct(){


    }

    public function get_locations( $type = 'global' ) {
        $locations = array(
            'is_front_page'  => __( 'Front page', 'notificationx' ),
            'is_home'        => __( 'Blog page', 'notificationx' ),
            'is_singular'    => __( 'All posts, pages and custom post types', 'notificationx' ),
            'is_single'      => __( 'All posts', 'notificationx' ),
            'is_page'        => __( 'All pages', 'notificationx' ),
            'is_attachment'  => __( 'All attachments', 'notificationx' ),
            'is_search'      => __( 'Search results', 'notificationx' ),
            'is_404'         => __( '404 error page', 'notificationx' ),
            'is_archive'     => __( 'All archives', 'notificationx' ),
            'is_category'    => __( 'All category archives', 'notificationx' ),
            'is_tag'         => __( 'All tag archives', 'notificationx' ),
        );

        if ( 'global' == $type ) {
            return $locations;
        }

        $post_types = Helper::post_types();
        $taxonomies = Helper::taxonomies();

        if ( ! empty( $post_types ) ) {

            unset( $post_types['post'] );
            unset( $post_types['page'] );
            unset( $post_types['elementor_library'] );

            foreach ( $post_types as $slug => $type ) {

                // translators: %s: Post Type label
                $locations[ 'is_singular-' . $slug ] = sprintf( __('All %s posts', 'notificationx'), $type->label );

                if ( $type->has_archive ) {
                    // translators: %s: Post Type label
                    $locations[ 'is_archive-' . $slug ] = sprintf( __('All %s archives', 'notificationx'), $type->label );
                }
            }

            foreach ( $taxonomies as $slug => $tax ) {
                // translators: %s: Taxonomy label
                $locations[ 'is_tax-' . $slug ] = sprintf( __('All %s taxonomy archives', 'notificationx'), $tax->label );
            }
        }

        return $locations;
    }

    public function check_location( $locations = array(), $custom_ids = '' ) {
        if ( empty( $locations ) ) {
            return true;
        }
        if ( !is_array( $locations ) ) {
            $locations = [$locations];
        }

        $status = array(
			'is_front_page' => is_front_page(),
			'is_home'       => is_home(),
			'is_singular'   => is_singular(),
			'is_single'     => is_singular( 'post' ),
			'is_page'       => ( is_page() && ! is_front_page() ),
			'is_attachment' => is_attachment(),
			'is_search'     => is_search(),
			'is_404'        => is_404(),
			'is_archive'    => is_archive(),
			'is_category'   => is_category(),
			'is_tag'        => is_tag(),
        );

        $status = apply_filters('nx_location_status', $status, $custom_ids);

        $post_types = Helper::post_types();
        $taxonomies = Helper::taxonomies();

        if ( ! empty( $post_types ) ) {

            unset( $post_types['post'] );
            unset( $post_types['page'] );
            unset( $post_types['elementor_library'] );

            foreach ( $post_types as $slug => $type ) {

                $status[ 'is_singular-' . $slug ] = is_singular( $slug );

                if ( $type->has_archive ) {
                    $status[ 'is_archive-' . $slug ] = is_post_type_archive( $slug );
                }
            }

            foreach ( $taxonomies as $slug => $tax ) {
                $status[ 'is_tax-' . $slug ] = is_tax( $slug );
            }
        }

        $status_flag = false;
        foreach ( $locations as $location ) {
            if ( ! isset( $status[$location] ) || ! $status[$location] ) {
                continue;
            } else {
                $status_flag = true;
            }
        }
        return $status_flag;
    }
}