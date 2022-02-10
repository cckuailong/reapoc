<?php
/**
 * Customizer Builder
 * Toggle Buttons
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Togglebutton_Control extends CFF_Controls_Base{

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
		return 'togglebutton';
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
		<div class="sb-control-togglebutton-ctn cff-fb-fs">
			<div class="sb-control-togglebutton-elm cff-fb-fs sb-tr-1" v-for="toggle in control.options" :data-active="<?php echo $controlEditingTypeModel ?>[control.id] == toggle.value" v-show="toggle.condition != undefined ? checkControlCondition(toggle.condition) : true"  @click.prevent.default="changeSettingValue(control.id,toggle.value, true)" >
				{{toggle.label}}
			</div>
		</div>

		<?php
	}

}