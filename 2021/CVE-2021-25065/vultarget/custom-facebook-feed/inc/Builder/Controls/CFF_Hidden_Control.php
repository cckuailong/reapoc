<?php
/**
 * Customizer Builder
 * Hidden Field Control
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Controls;

if(!defined('ABSPATH'))	exit;

class CFF_Hidden_Control extends CFF_Controls_Base{

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
		return 'hidden';
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
			<input type="hidden" v-model="<?php echo $controlEditingTypeModel ?>[control.id]">
		</div>
		<?php
	}

}