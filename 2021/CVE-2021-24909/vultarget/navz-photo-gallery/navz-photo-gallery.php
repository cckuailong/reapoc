<?php

/*
Plugin Name: ACF Photo Gallery Field
Plugin URI: http://www.navz.me/
Description: An extension for Advance Custom Fields which lets you add photo gallery functionality on your websites.
Version: 1.7.7
Author: Navneil Naicker
Author URI: http://www.navz.me/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_plugin_photo_gallery') ) :

	class acf_plugin_photo_gallery{
			
		// vars
		var $settings;

		/*
		*  __construct
		*
		*  This function will setup the class functionality
		*
		*  @type	function
		*  @date	17/02/2016
		*  @since	1.0.0
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		function __construct() {
			$this->settings = array(
				'version'	=> '1.7.6',
				'url'		=> plugin_dir_url( __FILE__ ),
				'path'		=> plugin_dir_path( __FILE__ )
			);
			load_plugin_textdomain( 'acf-photo_gallery', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' ); 
			add_action( 'admin_enqueue_scripts', array($this, 'acf_photo_gallery_sortable') );			
			add_action('acf/include_field_types', array($this, 'include_field_types')); // v5
			add_action('acf/register_fields', array($this, 'include_field_types')); // v4
			add_filter( 'acf_photo_gallery_caption_from_attachment', '__return_false' );
			add_filter("rest_prepare_page", array($this, 'rest_prepare_post'), 10, 3);
	        add_action('elementor/dynamic_tags/register_tags', array($this, 'register_tags'));
		}

		function register_tags( $dynamic_tags ){
			if (class_exists('ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag')) {
				\Elementor\Plugin::$instance->dynamic_tags->register_group( 'acf-photo-gallery', [
					'title' => 'ACF' 
				]);
				include(__DIR__ . '/includes/elementor_register_tag.php');
				$dynamic_tags->register_tag( 'register_tag' );
			}
		}
		
		//Add in jquery-ui-sortable script
		function acf_photo_gallery_sortable($hook) {
			if ( 'post.php' == $hook ) { wp_enqueue_script( 'jquery-ui-sortable', 'jquery-ui-sortable', 'jquery', '9999', true); }
		}

		/*
		*  include_field_types
		*
		*  This function will include the field type class
		*
		*  @type	function
		*  @date	17/02/2016
		*  @since	1.0.0
		*
		*  @param	$version (int) major ACF version. Defaults to false
		*  @return	n/a
		*/
		
		function include_field_types( $version = false ) {
			
			// support empty $version
			if( !$version ) $version = 4;
			
			// include
			include_once('fields/acf-photo_gallery-v' . $version . '.php');
			
		}

		function rest_prepare_post( $data, $post, $request ){
			$images = array();
			$field_groups = acf_get_field_groups(array('post_id' => $post->ID));
			foreach ( $field_groups as $group ){
				$fields = get_posts(array(
					'posts_per_page' => -1,
					'post_type' => 'acf-field',
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'suppress_filters' => true,
					'post_parent' => $group['ID'],
					'post_status' => 'publish',
					'update_post_meta_cache' => false
				));
				foreach ( $fields as $field ) {
					$object = get_field_object($field->post_name);
					if( $object['type'] == 'photo_gallery' ){
						$images[] = acf_photo_gallery($object['name'], $post->ID);
						$data->data['acf']['photo_gallery'][$object['name']] = $images;
					}
				}
			}
			return $data;
		}

	}

	// initialize
	new acf_plugin_photo_gallery();

// class_exists check
endif;

//Helper function for pulling the images
require_once( dirname(__FILE__) . '/includes/acf_photo_gallery.php' );

//Resizes the image
require_once( dirname(__FILE__) . '/includes/acf_photo_gallery_resize_image.php' );

//Set the default fields for the edit gallery
require_once( dirname(__FILE__) . '/includes/acf_photo_gallery_image_fields.php' );

//Metabox for the photo edit
require_once( dirname(__FILE__) . '/includes/acf_photo_gallery_edit.php' );