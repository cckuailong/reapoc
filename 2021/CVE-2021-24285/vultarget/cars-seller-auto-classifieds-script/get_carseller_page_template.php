<?php

function get_carseller_post_type_template($single_template) {
 global $post;

 if ($post->post_type == 'carsellers') {
      $single_template = dirname( __FILE__ ) . '/single-carsellers.php';
 }
 return $single_template;
}

add_filter( "single_template", "get_carseller_post_type_template" ) ;

function get_carseller_archive_template($archive_template) {
 global $post;

 if ($post->post_type == 'carsellers') {
      $archive_template = dirname( __FILE__ ) . '/archive-carsellers.php';
 }
 return $archive_template;
}

add_filter( "archive_template", "get_carseller_archive_template" ) ;