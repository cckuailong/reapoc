<?php
/**
 * Customizer Builder
 * Color Picker Field Control
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Colorpicker_Control extends CFF_Controls_Base{

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
		return 'colorpicker';
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
		<div class="sb-control-input-ctn cff-fb-fs sb-control-colorpicker-ctn" :data-picker-style="control.pickerType ? control.pickerType : 'default'" @click.stop="showColorPickerPospup(control.id)" v-on-clickaway="hideColorPickerPospup">
			<!--<cff-colorpicker :color="<?php echo $controlEditingTypeModel ?>[control.id]" v-on:change="changeSettingValue(control.id,...arguments)" :control-id="control.id"></cff-colorpicker>-->
			<input class="sb-control-input" placeholder="Select" type="text"  v-model="<?php echo $controlEditingTypeModel ?>[control.id]">
			<div class="sb-control-colorpicker-swatch" :style="'background:'+<?php echo $controlEditingTypeModel ?>[control.id]+';'"></div>
			<div class="sb-control-colorpicker-popup" v-show="customizerScreens.activeColorPicker == control.id">
				<sketch-picker
				  @input="updateColorValue(control.id)"
				  v-model="<?php echo $controlEditingTypeModel ?>[control.id]"
				  :value="<?php echo $controlEditingTypeModel ?>[control.id]"
				  :preset-colors="['#fff','#000','#e92b2b','#ffc104','#31e92b','#2b4ee9','#a72be9','#e92b82']"
				></sketch-picker>
				<button class="sb-control-action-button sb-colorpicker-reset-btn sb-btn cff-fb-fs sb-btn-grey" @click.prevent.default="resetColor(control.id)">
					<div v-html="svgIcons['update']"></div>
					<span>{{genericText.reset}}</span>
				</button>
			</div>

			<div class="sb-control-colorpicker-btn" v-if="control.pickerType == 'reset'">{{genericText.reset}}</div>
		</div>
		<?php
	}

}