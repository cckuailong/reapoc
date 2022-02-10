<?php
/**
 * Admin settings class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 * @subpackage        Settings
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Admin settings class
 */
class TInvWL_Admin_Settings_Integrations extends TInvWL_Admin_BaseSection
{

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 110;

	/**
	 * This class
	 *
	 * @var \TInvWL_Admin_Settings_Integrations
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Admin_Settings_Integrations
	 */
	public static function instance($plugin_name = TINVWL_PREFIX, $plugin_version = TINVWL_FVERSION)
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self($plugin_name, $plugin_version);
		}

		return self::$_instance;
	}

	/**
	 * Menu array
	 *
	 * @return array
	 */
	function menu()
	{
		return array(
			'title' => __('Integrations', 'ti-woocommerce-wishlist'),
			'page_title' => __('Wishlist Integrations with 3rd party plugins and themes', 'ti-woocommerce-wishlist'),
			'method' => array($this, '_print_'),
			'slug' => 'integrations-settings',
			'capability' => 'tinvwl_integrations_settings',
		);
	}

	/**
	 * Create sections for this settings
	 *
	 * @return array
	 */
	function constructor_data()
	{

		global $tinvwl_integrations;
		$fields = array();

		if (is_array($tinvwl_integrations)) {
			foreach ($tinvwl_integrations as $slug => $settings) {

				$disabled = ($settings['available']) ? array() : array('disabled' => 'disabled');

				$fields[] = array(
					'type' => 'checkboxonoff',
					'name' => $slug,
					'text' => $settings['name'],
					'std' => true,
					'extra' => $disabled,
				);
			}
		}

		$settings = array(

			array(
				'id' => 'integrations',
				'title' => __('Available Integrations', 'ti-woocommerce-wishlist'),
				'show_names' => true,
				'fields' => $fields,
				'desc' => __('You can disable built-in integrations with 3rd party plugins and themes.', 'ti-woocommerce-wishlist'),
			),

		);


		// Buttons.
		$settings[] = array(
			'id' => 'save_buttons',
			'class' => 'only-button',
			'noform' => true,
			'fields' => array(
				array(
					'type' => 'button_submit',
					'name' => 'setting_save',
					'std' => '<span><i class="ftinvwl ftinvwl-check"></i></span>' . __('Save Settings', 'ti-woocommerce-wishlist'),
					'extra' => array('class' => 'tinvwl-btn split status-btn-ok'),
				),
				array(
					'type' => 'button_submit_quick',
					'name' => 'setting_save_quick',
					'std' => '<span><i class="ftinvwl ftinvwl-floppy-o"></i></span>' . __('Save', 'ti-woocommerce-wishlist'),
				),
			),
		);

		return $settings;
	}
}
