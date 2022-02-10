<?php
/**
 * Customizer Builder
 * TextArea Field Control
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Textarea_Control extends CFF_Controls_Base{

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
		return 'textarea';
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
		<div class="sb-control-textarea-ctn cff-fb-fs">
			<textarea class="sb-control-input-textrea cff-fb-fs" v-model="<?php echo $controlEditingTypeModel ?>[control.id]" :placeholder="control.placeholder ? control.placeholder : ''" @focusout.prevent.default="changeSettingValue(false,false,false, control.ajaxAction ? control.ajaxAction : false)"></textarea>
		</div>
		<?php
	}

}