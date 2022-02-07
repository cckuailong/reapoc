<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


add_filter('the_content','post_grid_preview_content');

function post_grid_preview_content($content){

    if(is_singular('post_grid')){

        $post_id = get_the_id();

        $content .= do_shortcode('[post_grid id="'.$post_id.'"]');

    }

    return $content;

}





function post_grid_image_sizes(){

    $get_intermediate_image_sizes =  get_intermediate_image_sizes();
    $image_sizes = array();

    foreach($get_intermediate_image_sizes as $size_key){
        $size_key_name = str_replace('-', ' ',$size_key);
        $size_key_name = str_replace('_', ' ',$size_key);
        $size_key_name = ucfirst($size_key);
        $image_sizes[$size_key] = $size_key_name;

    }

    return $image_sizes;
}






function post_grid_posttypes_array(){

    $post_types_array = array();
    global $wp_post_types;

    $post_types_all = get_post_types( '', 'names' );
    foreach ( $post_types_all as $post_type ) {


        $obj = $wp_post_types[$post_type];
        $post_types_array[$post_type] = $obj->labels->singular_name;
    }


    return $post_types_array;
}

function post_grid_get_taxonomies($post_types){
    //$taxonomies = get_taxonomies();
    $taxonomies = get_object_taxonomies( $post_types );
    return $taxonomies;
    //var_dump($taxonomies);
}


