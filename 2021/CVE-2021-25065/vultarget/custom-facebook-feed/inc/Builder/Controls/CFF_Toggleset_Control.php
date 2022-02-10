<?php
/**
 * Customizer Builder
 * Toggle Set Control
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Toggleset_Control extends CFF_Controls_Base{

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
		return 'toggleset';
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
		<div class="sb-control-toggle-set-ctn cff-fb-fs">
			<div class="sb-control-toggle-elm cff-fb-fs sb-tr-2" v-for="toggle in control.options" :data-active="<?php echo $controlEditingTypeModel ?>[control.id] == toggle.value" @click.prevent.default="changeSettingValue(control.id,toggle.value, toggle.checkExtension != undefined ? checkExtensionActive(toggle.checkExtension) : true)"  v-show="toggle.condition != undefined ? checkControlCondition(toggle.condition) : true" :data-disabled="toggle.checkExtension != undefined ? !checkExtensionActive(toggle.checkExtension) : false">
				<div class="sb-control-toggle-extension-cover" v-show="toggle.checkExtension != undefined && !checkExtensionActive(toggle.checkExtension)"></div>
				<div class="sb-control-toggle-deco sb-tr-1"></div>
				<div class="sb-control-toggle-icon" v-if="toggle.icon" v-html="svgIcons[toggle.icon]"></div>
				<div class="sb-control-label">{{toggle.label}}</div>
			</div>
		</div>
		<?php
	}

}