<?php
/*
 * Followig class handling radio input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Palettes_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Color Palettes', 'ppom' );
		$this -> desc		= __ ( 'color boxes', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-user-plus" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
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
					'title' => __ ( 'Error message', 'ppom' ),
					'desc' => __ ( 'Insert the error message for validation.', 'ppom' ) 
			),
			'selected_palette_bcolor' => array (
					'type' => 'color',
					'title' => __ ( 'Selected Border Color', 'ppom' ),
					'desc' => __ ( 'Change selected palette border color, e.g: #fff', 'ppom' ),
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
			'max_selected' => array (
					'type' => 'number',
					'title' => __ ( 'Max selected', 'ppom' ),
					'desc' => __ ( 'Max. selected, leave blank for default.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'options' => array (
						'type' => 'paired-palettes',
						'title' => __ ( 'Add colors', 'ppom' ),
						'desc' => __ ( 'Type color code with price (optionally). To write label, use #colorcode - White', 'ppom' )
			),
			'selected' => array (
					'type' => 'text',
					'title' => __ ( 'Selected color', 'ppom' ),
					'desc' => __ ( 'Type color code given in (Add Options) tab if you want already selected.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'color_width' => array (
					'type' => 'text',
					'title' => __ ( 'Color width', 'ppom' ),
					'desc' => __ ( 'default is 50, e.g: 75', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'color_height' => array (
					'type' => 'text',
					'title' => __ ( 'Color height', 'ppom' ),
					'desc' => __ ( 'default is 50, e.g: 100', 'ppom' ),
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
			'multiple_allowed' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Multiple selections?', 'ppom' ),
					'desc' => __ ( 'Allow users to select more then one palette?.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'onetime' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Fixed Fee', 'ppom' ),
					'desc' => __ ( 'Add one time fee to cart total.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'circle' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show as Circle', 'ppom' ),
					'desc' => __ ( 'It will display color palettes as circle', 'ppom' ),
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
		
		$type = 'palettes';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}