<?php
/*
 * Followig class handling image cropping
*/

class NM_Cropper_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Image Cropper', 'ppom' );
		$this -> desc		= __ ( 'Crop images', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-crop" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	// 'link' => __ ( '<a href="https://github.com/RobinHerbots/Inputmask" target="_blank">Options</a>', 'ppom' ) 
	
	private function get_settings(){
		
		$croppie_options_link = 'https://najeebmedia.com/2017/12/24/crops-photos-woocommerce-ppom-using-croppie/';
		$input_meta = array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', 'ppom' ),
					'desc' => __ ( 'It will be shown as field label', 'ppom' ) 
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', 'ppom' ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'ppom' ) 
			),
			'description' => array (
					'type' => 'textarea',
					'title' => __ ( 'Description', 'ppom' ),
					'desc' => __ ( 'Small description, it will be display near name title.', 'ppom' ) 
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
					'title' => __ ( 'Class', 'ppom' ),
					'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'ppom' ),
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
					'title' => __ ( 'Button label (select files)', 'ppom' ),
					'desc' => __ ( 'Type button label e.g: Select Photos', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'button_class' => array (
					'type' => 'text',
					'title' => __ ( 'Button class', 'ppom' ),
					'desc' => __ ( 'Type class for both (select, upload) buttons', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'files_allowed' => array (
					'type' => 'text',
					'title' => __ ( 'Files allowed', 'ppom' ),
					'desc' => __ ( 'Type number of files allowed per upload by user, e.g: 3', 'ppom' ),
					'default'	=> 1,
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'file_types' => array (
					'type' => 'text',
					'title' => __ ( 'Image types', 'ppom' ),
					'desc' => __ ( 'Image types allowed seperated by comma, e.g: jpg,png', 'ppom' ),
					'default' => 'jpg,png',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'file_size' => array (
					'type' => 'text',
					'title' => __ ( 'Image size', 'ppom' ),
					'desc' => __ ( 'Type size with units in kb|mb per file uploaded by user, e.g: 3mb', 'ppom' ),
					'default' => '1mb',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'selected' => array (
					'type' => 'text',
					'title' => __ ( 'Selected option', 'ppom' ),
					'desc' => __ ( 'Type option name given in (Add Options) tab if you want already selected.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'first_option' => array (
					'type' => 'text',
					'title' => __ ( 'First option', 'ppom' ),
					'desc' => __ ( 'Just for info e.g: Select your option.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'options' => array (
					'type' => 'paired-cropper',
					'title' => __ ( 'Viewport Size', "ppom" ),
					'desc' => __ ( 'Add Options', "ppom" )
			),
			'viewport_type' => array (
					'type' => 'select',
					'title' => __ ( 'Viewport type', 'ppom' ),
					'desc' => __ ( 'Select Squar or circle, see help', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="'.esc_url($croppie_options_link).'">Help</a>', 'ppom' ),
					'options'	=> array('square' => 'Squar', 'circle'	=> 'Circle'),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'boundary' => array (
					'type' => 'text',
					'title' => __ ( 'Boundary height,width', 'ppom' ),
					'desc' => __ ( 'Separated by command h,w e.g: 200,200, see help', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="'.esc_url($croppie_options_link).'">Help</a>', 'ppom' ),
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
			'onetime_taxable' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Fee Taxable?', "ppom" ),
					'desc' => __ ( 'Calculate Tax for Fixed Fee', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'enforce_boundary' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enforce Boundary', 'ppom' ),
					'desc' => __ ( 'Restricts zoom so image cannot be smaller than viewport.', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="'.esc_url($croppie_options_link).'">Help</a>', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'resize' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Allow Resize', 'ppom' ),
					'desc' => __ ( 'Show cropping handler resize.', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="'.esc_url($croppie_options_link).'">Help</a>', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'enable_zoom' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Zoom', 'ppom' ),
					'desc' => __ ( 'Enable zooming functionality. If set to false - scrolling and pinching would not zoom.', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="'.esc_url($croppie_options_link).'">Help</a>', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'show_zoomer' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Zoomer', 'ppom' ),
					'desc' => __ ( 'Hide or Show the zoom slider.', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="'.esc_url($croppie_options_link).'">Help</a>', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'enable_exif' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Exif', 'ppom' ),
					'desc' => __ ( 'Enable zooming functionality. If set to false - scrolling and pinching would not zoom.', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="'.esc_url($croppie_options_link).'">Help</a>', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'desc_tooltip' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show tooltip (PRO)', 'ppom' ),
					'desc' => __ ( 'Show Description in Tooltip with Help Icon', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'required' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Required', 'ppom' ),
					'desc' => __ ( 'Select this if it must be required.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'logic' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Conditions', 'ppom' ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', 'ppom' )
			),
			'conditions' => array (
					'type' => 'html-conditions',
					'title' => __ ( 'Conditions', 'ppom' ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', 'ppom' )
			),
		);
		
		$type = 'cropper';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}