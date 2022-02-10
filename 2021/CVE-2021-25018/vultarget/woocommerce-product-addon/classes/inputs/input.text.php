<?php
/*
 * Followig class handling text input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Text_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Text Input', 'ppom' );
		$this -> desc		= __ ( 'regular text input', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		$regex_help_url = 'https://github.com/RobinHerbots/Inputmask#any-option-can-also-be-passed-through-the-use-of-a-data-attribute-use-data-inputmask-the-name-of-the-optionvalue';
		
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
				'placeholder' => array (
						'type' => 'text',
						'title' => __ ( 'Placeholder', 'ppom' ),
						'desc' => __ ( 'Optionally placeholder.', 'ppom' ) 
				),
				'error_message' => array (
						'type' => 'text',
						'title' => __ ( 'Error message', 'ppom' ),
						'desc' => __ ( 'Insert the error message for validation.', 'ppom' ) 
				),
				'maxlength' => array (
						'type' => 'text',
						'title' => __ ( 'Max. Length', 'ppom' ),
						'desc' => __ ( 'Max. characters allowed, leave blank for default', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				
				'minlength' => array (
						'type' => 'text',
						'title' => __ ( 'Min. Length', 'ppom' ),
						'desc' => __ ( 'Min. characters allowed, leave blank for default', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'default_value' => array (
						'type' => 'text',
						'title' => __ ( 'Set default value', 'ppom' ),
						'desc' => __ ( 'Pre-defined value for text input', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				
				'price' => array (
						'type' => 'text',
						'title' => __ ( 'Addon-on Price', 'ppom' ),
						'desc' => __ ( 'Price will be added as Add-on if text provided', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'class' => array (
						'type' => 'text',
						'title' => __ ( 'Class', 'ppom' ),
						'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'input_mask' => array (
						'type' => 'text',
						'title' => __ ( 'Input Masking', 'ppom' ),
						'desc' => __ ( 'Click options to see all Masking Options', 'ppom' ),
						'link' => __ ( '<a href="https://github.com/RobinHerbots/Inputmask" target="_blank">Options</a>', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'width' => array (
						'type' => 'select',
						'title' => __ ( 'Width', 'ppom' ),
						'desc' => __ ( 'Type field width in % e.g: 50%', "ppom"),
						'options'	=> ppom_get_input_cols(),
						'default'	=> 12,
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'visibility' => array (
						'type' => 'select',
						'title' => __ ( 'Visibility', 'ppom' ),
						'desc' => __ ( 'Set field visibility based on user.', "ppom"),
						'options'	=> ppom_field_visibility_options(),
						'default'	=> 'everyone',
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'visibility_role' => array (
						'type' => 'text',
						'title' => __ ( 'User Roles', 'ppom' ),
						'desc' => __ ( 'Role separated by comma.', "ppom"),
						'hidden' => true,
				),
				'use_regex' => array (
						'type' => 'checkbox',
						'title' => __ ( 'Use Regex Expresession', 'ppom' ),
						'link' => __ ( '<a target="_blank" href="'.esc_url($regex_help_url).'">See More</a>', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'onetime' => array (
						'type' => 'checkbox',
						'title' => __ ( 'One Time Fee/Charge', 'ppom' ),
						'desc' => __ ( 'Will not multiply with quantity', 'ppom' ),
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
						'desc' => __ ( 'Tick it to turn conditional logic to work below', 'ppom' ),
				),
				'conditions' => array (
						'type' => 'html-conditions',
						'title' => __ ( 'Conditions', 'ppom' ),
						'desc' => __ ( 'Tick it to turn conditional logic to work below', 'ppom' )
				),
				
		);
		
		$type = 'text';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}