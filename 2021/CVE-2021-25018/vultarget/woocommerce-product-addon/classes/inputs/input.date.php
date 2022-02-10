<?php
/*
 * Followig class handling date input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Date_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Date Input', 'ppom' );
		$this -> desc		= __ ( 'regular date input', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-calendar" aria-hidden="true"></i>', 'ppom' );
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
			'placeholder' => array (
					'type' => 'text',
					'title' => __ ( 'Placeholder', 'ppom' ),
					'desc' => __ ( 'Optional.', 'ppom' ) 
			),
			'error_message' => array (
					'type' => 'text',
					'title' => __ ( 'Error message', 'ppom' ),
					'desc' => __ ( 'Insert the error message for validation.', 'ppom' ) 
			),
			'class' => array (
					'type' => 'text',
					'title' => __ ( 'Class', 'ppom' ),
					'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'default_value' => array (
					'type' => 'text',
					'title' => __ ( 'Default Date', 'ppom' ),
					'desc' => __ ( 'User format YYYY-MM-DD e.g: 2017-05-25.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'date_formats' => array (
					'type' => 'select',
					'title' => __ ( 'Date formats', 'ppom' ),
					'desc' => __ ( 'Select date format. (if jQuery enabled below)', 'ppom' ),
					'options' => ppom_get_date_formats(),
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
			'past_dates' => array (
			    'type' => 'text',
			    'title' => __ ( 'Disable Past Dates', 'ppom' ),
			    'desc' => __ ( 'Disable dates before a certain day.  Enter a date or use shortcode (example: +10d)', 'ppom' ),
			    'col_classes' => array('col-md-3','col-sm-12')
			),
			'year_range' => array (
					'type' => 'text',
					'title' => __ ( 'Year Range', 'ppom' ),
					'desc' => __ ( 'e.g: 1950:2016. (if jQuery enabled below) Set start/end year like used example.', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="http://api.jqueryui.com/datepicker/#option-yearRange">Example</a>', 'ppom' ),
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
			'jquery_dp' => array (
					'type' => 'checkbox',
					'title' => __ ( 'jQuery Datepicker', 'ppom' ),
					'desc' => __ ( 'It will load jQuery fancy datepicker.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'no_weekends' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Disable Weekends', 'ppom' ),
					'desc' => __ ( 'It will disable Weekends.', 'ppom' ),
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
		
		$type = 'date';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}