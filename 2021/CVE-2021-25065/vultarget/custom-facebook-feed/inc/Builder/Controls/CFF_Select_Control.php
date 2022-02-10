<?php
/**
 * Customizer Builder
 * Select Field Control
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Select_Control extends CFF_Controls_Base{

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
		return 'select';
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
		<div class="sb-control-input-ctn cff-fb-fs">
			<select class="sb-control-input cff-fb-fs" v-model="<?php echo $controlEditingTypeModel ?>[control.id]" @change.prevent.default="changeSettingValue(control.id,false,false, control.ajaxAction ? control.ajaxAction : false)">
				<option v-for="(opName, opValue) in control.options" :value="opValue">{{opName}}</option>
			</select>
		</div>
		<?php
	}

}