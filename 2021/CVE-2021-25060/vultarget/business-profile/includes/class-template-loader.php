<?php
/**
 * Template Loader for Plugins based on the Gamajo Template Loader from Gary
 * Jones. See https://github.com/GaryJones/Gamajo-Template-Loader
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     1.1
 */

include_once( BPFWP_PLUGIN_DIR . '/lib/class-gamajo-template-loader.php' );

/**
 * Template loader.
 *
 * Originally based on functions in Easy Digital Downloads (thanks Pippin!).
 *
 * When using in a plugin, create a new class that extends this one and just overrides the properties.
 *
 * @package Gamajo_Template_Loader
 * @author  Gary Jones
 */
class bpfwpTemplateLoader extends Bpfwp_Gamajo_Template_Loader {
	/**
	 * Prefix for filter names.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $filter_prefix = 'bpfwp';

	/**
	 * Directory name where custom templates for this plugin should be found in the theme.
	 *
	 * For example: 'your-plugin-templates'.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $theme_template_directory = 'business-profile-templates';

	/**
	 * Reference to the root directory path of this plugin.
	 *
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 *
	 * e.g. YOUR_PLUGIN_TEMPLATE or plugin_dir_path( dirname( __FILE__ ) ); etc.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $plugin_directory = BPFWP_PLUGIN_DIR;

	/**
	 * Directory name where templates are found in this plugin.
	 *
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 *
	 * e.g. 'templates' or 'includes/templates', etc.
	 *
	 * @since  1.1.0
	 * @access protected
	 * @var    string
	 */
	protected $plugin_template_directory = 'bpfwp-templates';
}
