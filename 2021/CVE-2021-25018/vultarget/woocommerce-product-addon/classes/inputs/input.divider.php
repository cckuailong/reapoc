<?php
/*
 * Followig class handling text input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Divider_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Divider', 'ppom' );
		$this -> desc		= __ ( 'regular didider input', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	function ppom_divider_style(){

        return array(
        	'style1' => __( 'Style 1', 'ppom' ), 
        	'style2' => __( 'Style 2', 'ppom' ),			
        	'style3' => __( 'Style 3', 'ppom' ), 
            'style4' => __( 'Style 4', 'ppom' ),
            'style5' => __( 'Style 5', 'ppom' ),
       );
    }
 
     function border_style(){ 
     	
    	return array(
			'solid'  => __( 'Solid', 'ppom' ),
			'dotted' => __( 'Dotted', 'ppom' ),
			'dashed' => __( 'Dashed', 'ppom' ),
			'double' => __( 'Double', 'ppom' ),
			'groove' => __( 'Groove', 'ppom' ),
			'ridge'  => __( 'Ridge', 'ppom' ),
			'inset'  => __( 'Inset', 'ppom' ),
			'outset' => __( 'Outset', 'ppom' ),
		);
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
				'divider_styles' => array (
						'type' => 'select',
						'title' => __ ( 'Select style', 'ppom' ),
						'desc' => __ ( 'Select style you want to render', "ppom"),
						'options'	=> $this->ppom_divider_style(),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'style1_border' => array (
						'type' => 'select',
						'title' => __ ( 'Style border', 'ppom' ),
						'desc' => __ ( 'It will only apply on style 1.', "ppom"),
						'options'	=> $this-> border_style(),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'divider_height' => array (
						'type'  => 'text',
						'title' => __ ( 'Divider height', 'ppom' ),
						'desc'  => __ ( 'Provide the divider height e.g: 3px.', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'divider_txtsize' => array (
						'type' => 'text',
						'title' => __ ( 'Font size', 'ppom' ),
						'desc' => __ ( 'Provide divider text font size e.g: 18px', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'divider_color' => array (
						'type' => 'color',
						'title' => __ ( 'Divider color', 'ppom' ),
						'desc' => __ ( 'Choose the divider color.', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'divider_txtclr' => array (
						'type' => 'color',
						'title' => __ ( 'Divider text color', 'ppom' ),
						'desc' => __ ( 'Choose the divider text color.', 'ppom' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				
		);
		
		$type = 'divider';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}