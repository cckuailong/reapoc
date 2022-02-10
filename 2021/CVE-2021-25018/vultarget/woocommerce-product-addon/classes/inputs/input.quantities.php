<?php
/*
 * Followig class handling select input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Quantities_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Variation Quantity', 'ppom' );
		$this -> desc		= __ ( 'regular select-box input', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-list-ol" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	function variation_layout() {
	
		$layout_options = array(
								'simple_view'	=> __('Vertical Layout'),
								'horizontal'	=> __('Horizontal Layout'),
								'grid'	=> __('Grid Layout'),
								);
									
		return apply_filters('ppom_variation_layout_options', $layout_options);
	}
	
	private function get_settings(){
		
		$how_link = '<a href="https://najeebmedia.com/2016/09/29/add-quantity-fields-variations-woocommerce/" target="_blank">Example</a>';
		$input_meta = array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', 'ppom' ),
					'desc' => __ ( 'It will be shown as field label. See example for usage.', 'ppom' ),
					'link' => __ ( '<a target="_blank" href="'.esc_url($how_link).'">Help</a>', 'ppom' ),
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
					'desc' => __ ( 'Insert the error message for validation.', "ppom" ) 
			),
			'options' => array (
					'type' => 'paired-quantity',
					'title' => __ ( 'Add options', "ppom" ),
					'desc' => __ ( 'Type option with price (optionally)', "ppom" )
			),
			'view_control' => array (
					'type' => 'select',
					'title' => __ ( 'Variation Layout', 'ppom' ),
					'desc' => __ ( 'Select variation layout design', "ppom"),
					'options'	=> $this->variation_layout(),
					'default'	=> 'simple_view',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'default_price' => array (
					'type' => 'text',
					'title' => __ ( 'Default Price', 'ppom' ),
					'desc' => __ ( 'Default option price, if no prices is given in Options', "ppom"),
					'options'	=> $this->variation_layout(),
					'default'	=> '',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'min_qty' => array (
					'type' => 'text',
					'title' => __ ( 'Min Quantity', "ppom" ),
					'desc' => __ ( 'Enter min quantity allowed.', "ppom" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'max_qty' => array (
					'type' => 'text',
					'title' => __ ( 'Max Quantity', "ppom" ),
					'desc' => __ ( 'Enter max quantity allowed.', "ppom" ),
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
					'desc' => __ ( 'Select width column', "ppom"),
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
			'enable_plusminus' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enhance -/+ controls', 'ppom' ),
					'desc' => __ ( 'Add the -/+ buttons', 'ppom' )
			),
			'manage_stock' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Manage Stock', 'ppom' ),
					'desc' => __ ( 'Check/update stock against each variation', 'ppom' )
			),
			'unlink_order_qty' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Unlink Order Quantity', 'ppom' ),
					'desc' => __ ( 'Order quantity will not be controlled by this.', 'ppom' )
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
		
		$type = 'quantities';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}