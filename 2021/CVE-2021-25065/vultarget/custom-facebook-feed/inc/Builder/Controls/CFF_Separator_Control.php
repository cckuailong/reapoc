<?php
/**
 * Customizer Builder
 * Separator Control
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Separator_Control extends CFF_Controls_Base{

	/**
	 * Get control type.
	 *
	 * Getting the Control Type
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @return string
	*/
	public function get_type(){
		return 'separator';
	}

	/**
	 * Output Control
	 *
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @return HTML
	*/
	public function get_control_output($controlEditingTypeModel){
		?>
			<div class="sb-control-elem-separator cff-fb-fs" :style="'margin-top:'+ (control.top ? control.top : 0) +'px;margin-bottom:'+ (control.bottom ? control.bottom : 0) +'px;'"></div>
		<?php
	}

}