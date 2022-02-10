<?php
/*
 * Followig class handling file input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_File_wooproduct extends PPOM_Inputs{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	
	/*
	 * this var is pouplated with current plugin meta 
	 */
	var $plugin_meta;
	
	function __construct(){
		
		$this -> plugin_meta = ppom_get_plugin_meta();
		
		$this -> title 		= __ ( 'File Input', "ppom" );
		$this -> desc		= __ ( 'regular file input', "ppom" );
		$this -> icon		= __ ( '<i class="fa fa-file" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		$input_meta = array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', "ppom" ),
					'desc' => __ ( 'It will be shown as field label', "ppom" ) 
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', "ppom" ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', "ppom" ) 
			),
			'description' => array (
					'type' => 'textarea',
					'title' => __ ( 'Description', "ppom" ),
					'desc' => __ ( 'Small description, it will be display near name title.', "ppom" ) 
			),
			'error_message' => array (
					'type' => 'text',
					'title' => __ ( 'Error message', "ppom" ),
					'desc' => __ ( 'Insert the error message for validation.', "ppom" ) 
			),
			'file_cost' => array (
					'type' => 'text',
					'title' => __ ( 'File cost/price', "ppom" ),
					'desc' => __ ( 'This will be added into cart', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'class' => array (
					'type' => 'text',
					'title' => __ ( 'Class', "ppom" ),
					'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'width' => array (
					'type' => 'select',
					'title' => __ ( 'Width', 'ppom' ),
					'desc' => __ ( 'Select width column.', "ppom"),
					'options'	=> ppom_get_input_cols(),
					'default'	=> 12,
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'button_label_select' => array (
					'type' => 'text',
					'title' => __ ( 'Button label (select files)', "ppom" ),
					'desc' => __ ( 'Type button label e.g: Select Photos', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'button_class' => array (
					'type' => 'text',
					'title' => __ ( 'Button class', "ppom" ),
					'desc' => __ ( 'Type class for both (select, upload) buttons', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),			
			'files_allowed' => array (
					'type' => 'text',
					'title' => __ ( 'Files allowed', "ppom" ),
					'desc' => __ ( 'Type number of files allowed per upload by user, e.g: 3', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'file_types' => array (
					'type' => 'text',
					'title' => __ ( 'File types', "ppom" ),
					'desc' => __ ( 'File types allowed seperated by comma, e.g: jpg,pdf,zip', "ppom" ),
					'default' => 'jpg,pdf,zip',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'file_size' => array (
					'type' => 'text',
					'title' => __ ( 'File size', "ppom" ),
					'desc' => __ ( 'Type size with units in kb|mb per file uploaded by user, e.g: 3mb', "ppom" ),
					'default' => '1mb',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'min_img_h' => array (
					'type' => 'text',
					'title' => __ ( 'Min Height', "ppom" ),
					'desc' => __ ( 'Provide minimum image height.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'max_img_h' => array (
					'type' => 'text',
					'title' => __ ( 'Max Height', "ppom" ),
					'desc' => __ ( 'Provide maximum image height.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'min_img_w' => array (
					'type' => 'text',
					'title' => __ ( 'Min Width', "ppom" ),
					'desc' => __ ( 'Provide minimum image width.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'max_img_w' => array (
					'type' => 'text',
					'title' => __ ( 'Max Width', "ppom" ),
					'desc' => __ ( 'Provide maximum image width.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'img_dimension_error' => array (
					'type' => 'text',
					'title' => __ ( 'Error Message', "ppom" ),
					'desc' => __ ( 'Provide image dimension error message. It will display on frontend while uploading the image.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'visibility' => array (
					'type' => 'select',
					'title' => __ ( 'Visibility', 'ppom' ),
					'desc' => __ ( 'Set field visibility based on user.', "ppom"),
					'options'	=> ppom_field_visibility_options(),
					'default'	=> 'everyone',
			),
			'visibility_role' => array (
					'type' => 'text',
					'title' => __ ( 'User Roles', 'ppom' ),
					'desc' => __ ( 'Role separated by comma.', "ppom"),
					'hidden' => true,
			),
			'desc_tooltip' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show tooltip (PRO)', 'ppom' ),
					'desc' => __ ( 'Show Description in Tooltip with Help Icon', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'required' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Required', "ppom" ),
					'desc' => __ ( 'Select this if it must be required.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'onetime' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Fixed Fee', "ppom" ),
					'desc' => __ ( 'Add one time fee to cart total.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'logic' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Conditions', "ppom" ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', "ppom" )
			),
			'conditions' => array (
					'type' => 'html-conditions',
					'title' => __ ( 'Conditions', "ppom" ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', "ppom" )
			),
		);
		
		$type = 'file';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}