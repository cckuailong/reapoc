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

		// vars
		var $settings, // will hold info such as dir / path
			$defaults; // will hold default field options


		/*
		*  __construct
		*
		*  Set name / label needed for actions / filters
		*
		*  @since	3.6
		*  @date	23/01/13
		*/

		function __construct( $settings )
		{
			include( dirname(dirname(__FILE__)) . '/includes/__construct.php' );
		}


		/*
		*  create_options()
		*
		*  Create extra options for your field. This is rendered when editing a field.
		*  The value of $field['name'] can be used (like below) to save extra data to the $field
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field	- an array holding all the field's data
		*/

		function create_options($field)
		{
			include( dirname(dirname(__FILE__)) . '/includes/v4/create_options.php' );
		}
		
		/*
		*  create_field()
		*
		*  Create the HTML interface for your field
		*
		*  @param	$field - an array holding all the field's data
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*/

		function create_field( $field )
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