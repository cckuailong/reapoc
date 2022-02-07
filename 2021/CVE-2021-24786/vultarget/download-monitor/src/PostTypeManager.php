<?php

class DLM_Post_Type_Manager {

	/**
	 * Setup hooks
	 */
	public function setup() {
		add_action( 'init', array( $this, 'register' ), 10 );
	}

	/**
	 * Register Post Types
	 */
	public function register() {

		// Register Download Post Type
		register_post_type( "dlm_download",
			apply_filters( 'dlm_cpt_dlm_download_args', array(
				'labels'              => array(
					'all_items'          => __( 'All Downloads', 'download-monitor' ),
					'name'               => __( 'Downloads', 'download-monitor' ),
					'singular_name'      => __( 'Download', 'download-monitor' ),
					'add_new'            => __( 'Add New', 'download-monitor' ),
					'add_new_item'       => __( 'Add Download', 'download-monitor' ),
					'edit'               => __( 'Edit', 'download-monitor' ),
					'edit_item'          => __( 'Edit Download', 'download-monitor' ),
					'new_item'           => __( 'New Download', 'download-monitor' ),
					'view'               => __( 'View Download', 'download-monitor' ),
					'view_item'          => __( 'View Download', 'download-monitor' ),
					'search_items'       => __( 'Search Downloads', 'download-monitor' ),
					'not_found'          => __( 'No Downloads found', 'download-monitor' ),
					'not_found_in_trash' => __( 'No Downloads found in trash', 'download-monitor' ),
					'parent'             => __( 'Parent Download', 'download-monitor' )
				),
				'description'         => __( 'This is where you can create and manage downloads for your site.', 'download-monitor' ),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'capabilities'        => array(
					'publish_posts'       => 'manage_downloads',
					'edit_posts'          => 'manage_downloads',
					'edit_others_posts'   => 'manage_downloads',
					'delete_posts'        => 'manage_downloads',
					'delete_others_posts' => 'manage_downloads',
					'read_private_posts'  => 'manage_downloads',
					'edit_post'           => 'manage_downloads',
					'delete_post'         => 'manage_downloads',
					'read_post'           => 'manage_downloads'
				),
				'publicly_queryable'  => false,
				'exclude_from_search' => ( 1 !== absint( get_option( 'dlm_wp_search_enabled', 0 ) ) ),
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => apply_filters( 'dlm_cpt_dlm_download_supports', array(
					'title',
					'editor',
					'excerpt',
					'thumbnail',
					'custom-fields'
				) ),
				'has_archive'         => false,
				'show_in_nav_menus'   => false,
				'menu_position'       => 35
			) )
		);

		// Register Download Version Post Type
		register_post_type( "dlm_download_version",
			apply_filters( 'dlm_cpt_dlm_download_version_args', array(
				'labels'              => array(
					'all_items'          => __( 'All Download Versions', 'download-monitor' ),
					'name'               => __( 'Download Versions', 'download-monitor' ),
					'singular_name'      => __( 'Download Version', 'download-monitor' ),
					'add_new'            => __( 'Add New', 'download-monitor' ),
					'add_new_item'       => __( 'Add Download Version', 'download-monitor' ),
					'edit'               => __( 'Edit', 'download-monitor' ),
					'edit_item'          => __( 'Edit Download Version', 'download-monitor' ),
					'new_item'           => __( 'New Download Version', 'download-monitor' ),
					'view'               => __( 'View Download Version', 'download-monitor' ),
					'view_item'          => __( 'View Download Version', 'download-monitor' ),
					'search_items'       => __( 'Search Download Versions', 'download-monitor' ),
					'not_found'          => __( 'No Download Versions found', 'download-monitor' ),
					'not_found_in_trash' => __( 'No Download Versions found in trash', 'download-monitor' ),
					'parent'             => __( 'Parent Download Version', 'download-monitor' )
				),
				'public'              => false,
				'show_ui'             => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'show_in_nav_menus'   => false
			) )
		);

		do_action( 'dlm_after_post_type_register' );


	}

}
