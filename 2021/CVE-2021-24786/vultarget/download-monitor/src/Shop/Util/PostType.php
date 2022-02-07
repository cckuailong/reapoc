<?php

namespace Never5\DownloadMonitor\Shop\Util;

class PostType {

	const KEY = 'dlm_product';

	public function setup() {

		// register the post type
		add_action( 'dlm_after_post_type_register', function () {

			register_post_type( PostType::KEY,
				apply_filters( 'dlm_cpt_dlm_product_args', array(
					'labels'              => array(
						'all_items'          => __( 'All Products', 'download-monitor' ),
						'name'               => __( 'Shop', 'download-monitor' ),
						'singular_name'      => __( 'Product', 'download-monitor' ),
						'add_new'            => __( 'Add New Product', 'download-monitor' ),
						'add_new_item'       => __( 'Add Product', 'download-monitor' ),
						'edit'               => __( 'Edit', 'download-monitor' ),
						'edit_item'          => __( 'Edit Product', 'download-monitor' ),
						'new_item'           => __( 'New Product', 'download-monitor' ),
						'view'               => __( 'View Product', 'download-monitor' ),
						'view_item'          => __( 'View Product', 'download-monitor' ),
						'search_items'       => __( 'Search Products', 'download-monitor' ),
						'not_found'          => __( 'No Products found', 'download-monitor' ),
						'not_found_in_trash' => __( 'No Products found in trash', 'download-monitor' ),
						'parent'             => __( 'Parent Product', 'download-monitor' )
					),
					'taxonomies'          => array(),
					'description'         => __( 'This is where you can create and manage download products for your site.', 'download-monitor' ),
					'public'              => true,
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
					'hierarchical'        => false,
					'publicly_queryable'  => true,
					'exclude_from_search' => true,
					'rewrite'             => array(
						'slug'       => 'product',
						'with_front' => true,
						'pages'      => true,
						'feeds'      => true,
					),
					'supports'            => apply_filters( 'dlm_cpt_dlm_product_supports', array(
						'title',
						'editor',
						'excerpt',
						'thumbnail',
						'custom-fields'
					) ),
					'has_archive'         => 'products',
					'show_in_nav_menus'   => false,
					'menu_icon'           => 'dashicons-cart',
					'can_export'          => true,
					'menu_position'       => 36
				) )
			);

		} );

		// setup a custom separator
		add_action( 'admin_menu', function () {

			global $menu;

			$menu["34.9999"] = array(
				'',
				'read',
				'separator-download-monitor',
				'',
				'wp-menu-separator'
			);


		}, 9 );
	}


}