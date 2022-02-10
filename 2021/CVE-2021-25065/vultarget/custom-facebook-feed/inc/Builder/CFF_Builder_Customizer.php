<?php
/**
 * Customizer Builder
 *
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder;

if(!defined('ABSPATH'))	exit;

class CFF_Builder_Customizer{


	/**
	 * Controls Classes Array
	 *
	 *
	 * @since 4.0
	 * @access private
	 *
	 * @var array
	 */
	public static $controls_classes = [];


	/**
	 * Get controls list.
	 *
	 * Getting controls list
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @return array
	*/
	public static function get_controls_list(){
		return [
			'actionbutton',
			'checkbox',
			'checkboxsection',
			'datepicker',
			'colorpicker',
			'number',
			'select',
			'switcher',
			'text',
			'textarea',
			'toggle',
			'toggleset',
			'heading',
			'separator',
			'customview',
			'coloroverride',
			'togglebutton',
			'hidden'
		];
	}

	/**
	 * Register Controls
	 *
	 * Including Control
	 *
	 * @since 4.0
	 * @access public
	 *
	*/
	public static function register_controls(){
		$controls_list = self::get_controls_list();
		foreach ($controls_list as $control) {
			$controlClassName = 'CFF_'.ucfirst($control).'_Control';
			$cls_name = __NAMESPACE__.''.'\Controls\\'.$controlClassName;
			$control_class = new $cls_name();
			self::$controls_classes[$control] = $control_class;
		}
	}

	/**
	 * Print Controls Vue JS Tempalte
	 *
	 * Including Control
	 *
	 * @since 4.0
	 * @access public
	 *
	*/
	public static function get_controls_templates($editingType){
		$controls_list = self::get_controls_list();
		foreach ($controls_list as $control) {
			self::$controls_classes[$control]->print_control_wrapper($editingType);
		}
	}
}