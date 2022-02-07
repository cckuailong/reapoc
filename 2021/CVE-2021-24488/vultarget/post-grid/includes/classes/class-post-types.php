<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_post_grid_post_types{
	
	
	public function __construct(){
		add_action( 'init', array( $this, '_posttype_post_grid' ), 0 );
        add_action( 'init', array( $this, '_posttype_post_grid_layout' ), 0 );

    }
	
	
	public function _posttype_post_grid(){
			
		if ( post_type_exists( "post_grid" ) )
		return;
	 
		$singular  = __( 'Post Grid', 'post-grid' );
		$plural    = __( 'Post Grid', 'post-grid' );
        $post_grid_settings = get_option('post_grid_settings');
        $post_grid_preview = isset($post_grid_settings['post_grid_preview']) ? $post_grid_settings['post_grid_preview'] : 'yes';


        register_post_type( "post_grid",
			apply_filters( "post_grid_posttype_post_grid", array(
				'labels' => array(
					'name' 					=> $plural,
					'singular_name' 		=> $singular,
					'menu_name'             => $singular,
					'all_items'             => sprintf( __( 'All %s', 'post-grid' ), $plural ),
					'add_new' 				=> __( 'Add New', 'post-grid' ),
					'add_new_item' 			=> sprintf( __( 'Add %s', 'post-grid' ), $singular ),
					'edit' 					=> __( 'Edit', 'post-grid' ),
					'edit_item' 			=> sprintf( __( 'Edit %s', 'post-grid' ), $singular ),
					'new_item' 				=> sprintf( __( 'New %s', 'post-grid' ), $singular ),
					'view' 					=> sprintf( __( 'View %s', 'post-grid' ), $singular ),
					'view_item' 			=> sprintf( __( 'View %s', 'post-grid' ), $singular ),
					'search_items' 			=> sprintf( __( 'Search %s', 'post-grid' ), $plural ),
					'not_found' 			=> sprintf( __( 'No %s found', 'post-grid' ), $plural ),
					'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'post-grid' ), $plural ),
					'parent' 				=> sprintf( __( 'Parent %s', 'post-grid' ), $singular )
				),
				'description' => sprintf( __( 'This is where you can create and manage %s.', 'post-grid' ), $plural ),
				'public' 				=> false,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> ($post_grid_preview =='yes') ?true : false,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'query_var' 			=> true,
				'supports' 				=> array( 'title' ),
				'show_in_nav_menus' 	=> false,
				'menu_icon' => 'dashicons-grid-view',

			) )
		); 

	}



    public function _posttype_post_grid_layout(){

        if ( post_type_exists( "post_grid_layout" ) )
            return;

        $singular  = __( 'Layout', 'post-grid' );
        $plural    = __( 'Layouts', 'post-grid' );


        register_post_type( "post_grid_layout",
            apply_filters( "post_grid_posttype_post_grid_layout", array(
                'labels' => array(
                    'name' 					=> $plural,
                    'singular_name' 		=> $singular,
                    'menu_name'             => $singular,
                    'all_items'             => sprintf( __( 'All %s', 'post-grid' ), $plural ),
                    'add_new' 				=> __( 'Add New', 'post-grid' ),
                    'add_new_item' 			=> sprintf( __( 'Add %s', 'post-grid' ), $singular ),
                    'edit' 					=> __( 'Edit', 'post-grid' ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', 'post-grid' ), $singular ),
                    'new_item' 				=> sprintf( __( 'New %s', 'post-grid' ), $singular ),
                    'view' 					=> sprintf( __( 'View %s', 'post-grid' ), $singular ),
                    'view_item' 			=> sprintf( __( 'View %s', 'post-grid' ), $singular ),
                    'search_items' 			=> sprintf( __( 'Search %s', 'post-grid' ), $plural ),
                    'not_found' 			=> sprintf( __( 'No %s found', 'post-grid' ), $plural ),
                    'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'post-grid' ), $plural ),
                    'parent' 				=> sprintf( __( 'Parent %s', 'post-grid' ), $singular )
                ),
                'description' => sprintf( __( 'This is where you can create and manage %s.', 'post-grid' ), $plural ),
                'public' 				=> false,
                'show_ui' 				=> true,
                'capability_type' 		=> 'post',
                'map_meta_cap'          => true,
                'publicly_queryable' 	=> false,
                'exclude_from_search' 	=> false,
                'hierarchical' 			=> false,
                'query_var' 			=> true,
                'supports' 				=> array( 'title' ), // 'editor'
                'show_in_nav_menus' 	=> false,
                'show_in_menu' 	=> 'edit.php?post_type=post_grid',
                'menu_icon' => 'dashicons-businessman',
                //'show_in_rest' => true,

            ) )
        );

    }








}
	

new class_post_grid_post_types();