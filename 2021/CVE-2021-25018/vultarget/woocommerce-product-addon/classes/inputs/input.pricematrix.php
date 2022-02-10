<?php
/*
 * Followig class handling price matrix based on quantity provied in range
 * like 1-25
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_PriceMatrix_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Price Matrix', "ppom" );
		$this -> desc		= __ ( 'Price/Quantity', "ppom" );
		$this -> icon		= __ ( '<i class="fa fa-usd" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		$matrix_detail = 'https://najeebmedia.com/2014/04/23/woocommerce-personalized-product-with-best-price-handling-ever-with-fixed-and-price-matrix/';
		
		$input_meta = array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', "ppom" ),
					'desc' => __ ( 'It will as section heading wrapped in h2', "ppom" )
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', "ppom" ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', "ppom" )
			),
			'description' => array (
					'type' => 'textarea',
					'title' => __ ( 'Description', "ppom" ),
					'desc' => __ ( 'Type description, it will be display under section heading.', "ppom" )
			),
			'discount_type' => array (
					'type' => 'select',
					'title' => __ ( 'Discount On?', "ppom" ),
					'desc' => __ ( 'Select discount option.', "ppom" ),
					'options'	=> array( 'both'	=> "Base & Option", 'base'	=> "Only Base"),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'options' => array (
					'type' => 'paired-pricematrix',
					'title' => __ ( 'Price matrix', "ppom" ),
					'desc' => __ ( 'Type quantity range with price.', "ppom" ),
					'link' => __ ( '<a target="_blank" href="'.$matrix_detail.'">More Detail</a>', 'ppom' ) 
			),
			'qty_step' => array (
					'type' => 'text',
					'title' => __ ( 'Quantity Step', "ppom" ),
					'desc' => __ ( 'Quantity step e.g: 3', "ppom" ),
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
			'discount' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Apply as discount', "ppom" ),
					'desc' => __ ( 'Check for Apply as discount', 'ppom' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'show_slider' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Quantity Slider', "ppom" ),
					'desc' => __ ( 'It will display Range slider for quantity under matrix', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'show_price_per_unit' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show price per unit?', "ppom" ),
					'desc' => __ ( 'It will calculate price against per unit and show along total', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'hide_matrix_table' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Hide Price Matrix?', "ppom" ),
					'desc' => __ ( 'Price Matrix table will be hidden', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'desc_tooltip' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show tooltip (PRO)', 'ppom' ),
					'desc' => __ ( 'Show Description in Tooltip with Help Icon', 'ppom' ),
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
		
		$type = 'pricematrix';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}