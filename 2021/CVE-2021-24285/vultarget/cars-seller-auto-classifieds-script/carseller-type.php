<?php


/*---------carsellers Custom Post Types---------------------------------*/

function my_custom_post_carsellers() {
    $labels = array(
        'name'               => _x( 'carsellers', 'post type general name' ),
        'singular_name'      => _x( 'Car', 'post type singular name' ),
        'add_new'            => _x( 'Add New Car', 'carseller' ),
        'add_new_item'       => __( 'Add New Car' ),
        'edit_item'          => __( 'Edit Car' ),
        'new_item'           => __( 'New Car' ),
        'all_items'          => __( 'All Cars' ),
        'view_item'          => __( 'View car' ),
        'search_items'       => __( 'Search car' ),
        'not_found'          => __( 'No car found' ),
        'not_found_in_trash' => __( 'No car found in the Trash' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'carsellers',

        
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds our carsellers and carsellers specific data',
		'public'        => true,
        'menu_position' => 10,
        'supports'      => array( 'title', 'thumbnail',  ),
        'has_archive'   => true,
        'rewrite'       =>true,
        '_builtin' => false, // It's a custom post type, not built in!
		'_edit_link' => 'post.php?post=%d',
		'capability_type' => 'post',
		'hierarchical' => false,
        'menu_icon' => plugins_url('cars-seller-auto-classifieds-script') . '/images/carseller-icon.png',

    );
    register_post_type( 'carsellers', $args );
	
	
	 
	 
}
add_action( 'init', 'my_custom_post_carsellers' );

function my_taxonomies_carsellers() {
    $labels = array(
        'name'              => _x( 'Car Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Car Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Car Categories' ),
        'all_items'         => __( 'All Car Categories' ),
        'parent_item'       => __( 'Parent CarCategory' ),
        'parent_item_colon' => __( 'Parent Car Category:' ),
        'edit_item'         => __( 'Edit Car Category' ),
        'update_item'       => __( 'Update Car Category' ),
        'add_new_item'      => __( 'Add New Car Category' ),
        'new_item_name'     => __( 'New Car Category' ),
        'menu_name'         => __( 'Car Categories' ),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
		
    );
    register_taxonomy( 'carsellers_category', 'carsellers', $args );
	
	
	
	 
	 
	 
}
add_action( 'init', 'my_taxonomies_carsellers', 0 );


include('fields_carseller.php');




























