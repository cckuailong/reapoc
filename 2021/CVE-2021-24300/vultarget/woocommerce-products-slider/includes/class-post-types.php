<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_wcps_post_types{
	
	
	public function __construct(){
		add_action( 'init', array( $this, '_posttype_wcps' ), 0 );
        add_action( 'init', array( $this, '_posttype_wcps_layout' ), 0 );

    }
	
	
	public function _posttype_wcps(){
			
		if ( post_type_exists( "wcps" ) )
		return;
	 
		$singular  = __( 'WCPS', 'woocommerce-products-slider' );
		$plural    = __( 'WCPS', 'woocommerce-products-slider' );
        $wcps_settings = get_option('wcps_settings');
        $wcps_preview = isset($wcps_settings['wcps_preview']) ? $wcps_settings['wcps_preview'] : 'yes';


        register_post_type( "wcps",
			apply_filters( "wcps_posttype_wcps", array(
				'labels' => array(
					'name' 					=> $plural,
					'singular_name' 		=> $singular,
					'menu_name'             => $singular,
					'all_items'             => sprintf( __( 'All %s', 'woocommerce-products-slider' ), $plural ),
					'add_new' 				=> __( 'Add New', 'woocommerce-products-slider' ),
					'add_new_item' 			=> sprintf( __( 'Add %s', 'woocommerce-products-slider' ), $singular ),
					'edit' 					=> __( 'Edit', 'woocommerce-products-slider' ),
					'edit_item' 			=> sprintf( __( 'Edit %s', 'woocommerce-products-slider' ), $singular ),
					'new_item' 				=> sprintf( __( 'New %s', 'woocommerce-products-slider' ), $singular ),
					'view' 					=> sprintf( __( 'View %s', 'woocommerce-products-slider' ), $singular ),
					'view_item' 			=> sprintf( __( 'View %s', 'woocommerce-products-slider' ), $singular ),
					'search_items' 			=> sprintf( __( 'Search %s', 'woocommerce-products-slider' ), $plural ),
					'not_found' 			=> sprintf( __( 'No %s found', 'woocommerce-products-slider' ), $plural ),
					'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'woocommerce-products-slider' ), $plural ),
					'parent' 				=> sprintf( __( 'Parent %s', 'woocommerce-products-slider' ), $singular )
				),
				'description' => sprintf( __( 'This is where you can create and manage %s.', 'woocommerce-products-slider' ), $plural ),
				'public' 				=> false,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> ($wcps_preview =='yes') ?true : false,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'query_var' 			=> true,
				'supports' 				=> array( 'title' ),
				'show_in_nav_menus' 	=> false,
				'menu_icon' => 'dashicons-slides',

			) )
		); 

	}




    public function _posttype_wcps_layout(){

        if ( post_type_exists( "wcps_layout" ) )
            return;

        $singular  = __( 'WCPS layout', 'woocommerce-products-slider' );
        $plural    = __( 'WCPS layouts', 'woocommerce-products-slider' );


        register_post_type( "wcps_layout",
            apply_filters( "wcps_posttype_wcps_layout", array(
                'labels' => array(
                    'name' 					=> $plural,
                    'singular_name' 		=> $singular,
                    'menu_name'             => $singular,
                    'all_items'             => sprintf( __( 'All %s', 'woocommerce-products-slider' ), $plural ),
                    'add_new' 				=> __( 'Add New', 'woocommerce-products-slider' ),
                    'add_new_item' 			=> sprintf( __( 'Add %s', 'woocommerce-products-slider' ), $singular ),
                    'edit' 					=> __( 'Edit', 'woocommerce-products-slider' ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', 'woocommerce-products-slider' ), $singular ),
                    'new_item' 				=> sprintf( __( 'New %s', 'woocommerce-products-slider' ), $singular ),
                    'view' 					=> sprintf( __( 'View %s', 'woocommerce-products-slider' ), $singular ),
                    'view_item' 			=> sprintf( __( 'View %s', 'woocommerce-products-slider' ), $singular ),
                    'search_items' 			=> sprintf( __( 'Search %s', 'woocommerce-products-slider' ), $plural ),
                    'not_found' 			=> sprintf( __( 'No %s found', 'woocommerce-products-slider' ), $plural ),
                    'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'woocommerce-products-slider' ), $plural ),
                    'parent' 				=> sprintf( __( 'Parent %s', 'woocommerce-products-slider' ), $singular )
                ),
                'description' => sprintf( __( 'This is where you can create and manage %s.', 'woocommerce-products-slider' ), $plural ),
                'public' 				=> false,
                'show_ui' 				=> true,
                'capability_type' 		=> 'post',
                'map_meta_cap'          => true,
                'publicly_queryable' 	=> false,
                'exclude_from_search' 	=> false,
                'hierarchical' 			=> false,
                'query_var' 			=> true,
                'supports' 				=> array( 'title' ),
                'show_in_nav_menus' 	=> false,
                'show_in_menu' 	=> 'edit.php?post_type=wcps',
                'menu_icon' => 'dashicons-businessman',

            ) )
        );

    }







}
	

new class_wcps_post_types();