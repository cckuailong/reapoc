<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_Taxonomy_Manager {

	/**
	 * Setup hooks
	 */
	public function setup() {
		add_action( 'init', array( $this, 'register' ), 9 );
	}

	/**
	 * Register Taxonomies
	 */
	public function register() {

		// Register Download Category
		register_taxonomy( 'dlm_download_category',
			array( 'dlm_download' ),
			apply_filters( 'dlm_download_category_args', array(
				'hierarchical'          => true,
				'update_count_callback' => '_update_post_term_count',
				'label'                 => __( 'Categories', 'download-monitor' ),
				'labels'                => array(
					'name'              => __( 'Download Categories', 'download-monitor' ),
					'menu_name'         => __( 'Categories', 'download-monitor' ),
					'singular_name'     => __( 'Download Category', 'download-monitor' ),
					'search_items'      => __( 'Search Download Categories', 'download-monitor' ),
					'all_items'         => __( 'All Download Categories', 'download-monitor' ),
					'parent_item'       => __( 'Parent Download Category', 'download-monitor' ),
					'parent_item_colon' => __( 'Parent Download Category', 'download-monitor' ),
					'edit_item'         => __( 'Edit Download Category', 'download-monitor' ),
					'update_item'       => __( 'Update Download Category', 'download-monitor' ),
					'add_new_item'      => __( 'Add New Download Category', 'download-monitor' ),
					'new_item_name'     => __( 'New Download Category Name', 'download-monitor' )
				),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_downloads',
					'edit_terms'   => 'manage_downloads',
					'delete_terms' => 'manage_downloads',
					'assign_terms' => 'manage_downloads',
				),
				'rewrite'               => false,
				'show_in_nav_menus'     => false
			) )
		);

		// Register Download Tag
		register_taxonomy( 'dlm_download_tag',
			array( 'dlm_download' ),
			apply_filters( 'dlm_download_tag_args', array(
				'hierarchical'      => false,
				'label'             => __( 'Tags', 'download-monitor' ),
				'labels'            => array(
					'name'              => __( 'Download Tags', 'download-monitor' ),
					'menu_name'         => __( 'Tags', 'download-monitor' ),
					'singular_name'     => __( 'Download Tag', 'download-monitor' ),
					'search_items'      => __( 'Search Download Tags', 'download-monitor' ),
					'all_items'         => __( 'All Download Tags', 'download-monitor' ),
					'parent_item'       => __( 'Parent Download Tag', 'download-monitor' ),
					'parent_item_colon' => __( 'Parent Download Tag', 'download-monitor' ),
					'edit_item'         => __( 'Edit Download Tag', 'download-monitor' ),
					'update_item'       => __( 'Update Download Tag', 'download-monitor' ),
					'add_new_item'      => __( 'Add New Download Tag', 'download-monitor' ),
					'new_item_name'     => __( 'New Download Tag Name', 'download-monitor' )
				),
				'show_ui'           => true,
				'query_var'         => true,
				'capabilities'      => array(
					'manage_terms' => 'manage_downloads',
					'edit_terms'   => 'manage_downloads',
					'delete_terms' => 'manage_downloads',
					'assign_terms' => 'manage_downloads',
				),
				'rewrite'           => false,
				'show_in_nav_menus' => false
			) )
		);
	}

}