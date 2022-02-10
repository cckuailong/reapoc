<?php
/**
 * Customizer Builder
 * Text Field Control
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Text_Control extends CFF_Controls_Base{

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
		return 'text';
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
			<div class="sb-control-input-info" v-if="control.fieldPrefix">{{control.fieldPrefix.replace(/ /g,"&nbsp;")}}</div>
			<input type="text" class="sb-control-input cff-fb-fs" v-model="<?php echo $controlEditingTypeModel ?>[control.id]" @change.prevent.default="changeSettingValue(control.id, false,false, control.ajaxAction ? control.ajaxAction : false)"  :placeholder="control.placeholder ? control.placeholder : ''">
			<div class="sb-control-input-info" v-if="control.fieldSuffix">{{control.fieldSuffix.replace(/ /g,"&nbsp;")}}</div>
		</div>
		<?php
	}

}