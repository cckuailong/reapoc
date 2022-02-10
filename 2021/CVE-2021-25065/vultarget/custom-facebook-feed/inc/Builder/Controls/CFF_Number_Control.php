<?php
/**
 * Customizer Builder
 * Number Field Control
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Number_Control extends CFF_Controls_Base{

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
		return 'number';
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
		<div class="sb-control-input-ctn cff-fb-fs" :data-contains-suffix="control.fieldSuffix !== undefined ? 'true' : 'false'">
			<div class="sb-control-input-info" v-if="control.fieldPrefix">{{control.fieldPrefix.replace(/ /g,"&nbsp;")}}</div>
			<input type="number" class="sb-control-input cff-fb-fs" :placeholder="control.placeholder ? control.placeholder : ''" :step="control.step ? control.step : 1" :max="control.max ? control.max : 1000" :min="control.min ? control.min : 0" v-model="<?php echo $controlEditingTypeModel ?>[control.id]"  @change.prevent.default="changeSettingValue(control.id,false,false, control.ajaxAction ? control.ajaxAction : false)">
			<div class="sb-control-input-info" v-if="control.fieldSuffix">{{control.fieldSuffix.replace(/ /g,"&nbsp;")}}</div>
		</div>
		<?php
	}

}