<?php
/*
 * Followig class handling hidden input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Hidden_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Hidden Input', "ppom" );
		$this -> desc		= __ ( 'regular hidden input', "ppom" );
		$this -> icon		= __ ( '<i class="fa fa-hashtag" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		$input_meta = array (

			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', 'ppom' ),
					'desc' => __ ( 'Label will show in cart', 'ppom' ) 
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', "ppom" ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', "ppom" )
			),
			'field_value' => array (
					'type' => 'text',
					'title' => __ ( 'Field value', "ppom" ),
					'desc' => __ ( 'you can pre-set the value of this hidden input.', "ppom" )
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
		);
		
		$type = 'hidden';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}