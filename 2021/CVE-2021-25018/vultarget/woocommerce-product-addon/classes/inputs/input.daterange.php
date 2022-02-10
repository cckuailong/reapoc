<?php
/*
 * Followig class handling date input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Daterange_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'DateRange Input', 'ppom' );
		$this -> desc		= __ ( '<a href="http://www.daterangepicker.com/" target="_blank">More detail</a>', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-table" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		$input_meta = array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', 'ppom' ),
					'desc' => __ ( 'All about Daterangepicker, see daterangepicker', 'ppom' ), 
					'link' => __ ( '<a href="http://www.daterangepicker.com/" target="_blank">Daterangepicker</a>', 'ppom' ) 
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
			'open_style' => array (
					'type' => 'select',
					'title' => __ ( 'Open Style', 'ppom' ),
					'desc' => __ ( 'Default is down.', 'ppom' ),
					'options' => array('down'=>'Down', 'up'=>'Up'),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'date_formats' => array (
					'type' => 'text',
					'title' => __ ( 'Format', 'ppom' ),
					'desc' => __ ( 'e.g MM-DD-YYYY, DD-MMM-YYYY', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'tp_increment' => array (
					'type' => 'text',
					'title' => __ ( 'Timepicker increment', 'ppom' ),
					'desc' => __ ( 'e.g: 30', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'start_date' => array (
					'type' => 'text',
					'title' => __ ( 'Start Date', 'ppom' ),
					'desc' => __ ( 'Must be same format as defined in above (Format) field.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'end_date' => array (
					'type' => 'text',
					'title' => __ ( 'End Date', 'ppom' ),
					'desc' => __ ( 'Must be same format as defined in above (Format) field.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'min_date' => array (
					'type' => 'text',
					'title' => __ ( 'Min Date', 'ppom' ),
					'desc' => __ ( 'e.g: 2017-02-25', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'max_date' => array (
					'type' => 'text',
					'title' => __ ( 'Max Date', 'ppom' ),
					'desc' => __ ( 'e.g: 2017-09-15', 'ppom' ),
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
			'time_picker' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Timepicker', 'ppom' ),
					'desc' => __ ( 'Show Timepicker.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'tp_24hours' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Timepicker 24 Hours', 'ppom' ),
					'desc' => __ ( 'Left blank for default', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'tp_seconds' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Timepicker Seconds', 'ppom' ),
					'desc' => __ ( 'Left blank for default', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'drop_down' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Dropdown', 'ppom' ),
					'desc' => __ ( 'Left blank for default', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'show_weeks' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Week Numbers', 'ppom' ),
					'desc' => __ ( 'Left blank for default.', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'auto_apply' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Auto Apply Changes', 'ppom' ),
					'desc' => __ ( 'Hide the Apply/Cancel button.', 'ppom' ),
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
		
		$type = 'daterange';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}