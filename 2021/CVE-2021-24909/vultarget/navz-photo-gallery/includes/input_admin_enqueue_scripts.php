<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

// vars
$url = $this->settings['url'];
$version = $this->settings['version'];

// register & include JS
wp_enqueue_script( 'jquery-ui-sortable' );

wp_register_script( 'acf-input-photo_gallery', "{$url}assets/js/acf-photo-gallery-field.js", array('acf-input'), $version );
wp_enqueue_script( 'acf-input-photo_gallery');

wp_register_script( 'acf-input-photo_gallery_swal', "{$url}assets/js/sweetalert2.min.js", array('acf-input'), $version );
wp_enqueue_script( 'acf-input-photo_gallery_swal');


// register & include CSS
wp_register_style( 'acf-input-photo_gallery', "{$url}assets/css/acf-photo-gallery-field.css", array('acf-input'), $version );
wp_enqueue_style('acf-input-photo_gallery');

wp_register_style( 'acf-input-photo_gallery_swal', "{$url}assets/css/sweetalert2.min.css", array('acf-input'), $version );
wp_enqueue_style('acf-input-photo_gallery_swal');