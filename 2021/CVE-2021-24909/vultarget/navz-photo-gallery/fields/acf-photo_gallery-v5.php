<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( !defined('ACF_VERSION') ){ define('ACF_VERSION', $version); }

//Saving the gallery image ids into the database postmeta table in the same order.
include( dirname(dirname(__FILE__)) . '/includes/acf_photo_gallery_save.php' );

//Remove photo from the database array
include( dirname(dirname(__FILE__)) . '/includes/acf_photo_gallery_remove_photo.php' );

//Editing and saving photo details
include( dirname(dirname(__FILE__)) . '/includes/acf_photo_gallery_edit_save.php' );

// check if class already exists
if( !class_exists('acf_field_photo_gallery') ) :

	class acf_field_photo_gallery extends acf_field {
		
		
		/*
		*  __construct
		*
		*  This function will setup the field type data
		*
		*  @type	function
		*  @date	5/03/2014
		*  @since	5.0.0
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		function __construct( $settings )
		{
			//add_filter('acf/load_value', array($this, 'load_value'), 10, 3);
			include( dirname(dirname(__FILE__)) . '/includes/__construct.php' );			
		}

		function load_value( $value, $post_id, $field ) {
			if( !is_admin() ){
				return acf_photo_gallery_make_images($value, $field, $post_id);	
			}
			return $value;
		}

		/*
		*  render_field_settings()
		*
		*  Create extra settings for your field. These are visible when editing a field
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field (array) the $field being edited
		*  @return	n/a
		*/

		function render_field_settings( $field )
		{
			include( dirname(dirname(__FILE__)) . '/includes/v5/render_field_settings.php' );
		}

		
		/*
		*  render_field()
		*
		*  Create the HTML interface for your field
		*
		*  @param	$field (array) the $field being rendered
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field (array) the $field being edited
		*  @return	n/a
		*/
		
		function render_field( $field )
		{
			include( dirname(dirname(__FILE__)) . '/includes/render_field.php' );
		}
		
			
		/*
		*  input_admin_enqueue_scripts()
		*
		*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
		*  Use this action to add CSS + JavaScript to assist your create_field() action.
		*
		*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*/

		function input_admin_enqueue_scripts()
		{
			include( dirname(dirname(__FILE__)) . '/includes/input_admin_enqueue_scripts.php' );
		}
				
	}

	// initialize
	new acf_field_photo_gallery( $this->settings );

// class_exists check
endif;