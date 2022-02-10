<?php
/**
 * Run plugin class
 *
 * @since             1.0.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Run plugin class
 */
class TInvWL
{

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $_version;
	/**
	 * Admin class
	 *
	 * @var TInvWL_Admin_TInvWL
	 */
	public $object_admin;
	/**
	 * Public class
	 *
	 * @var TInvWL_Public_TInvWL
	 */
	public $object_public;
	/**
	 * Array of deprecated hook handlers.
	 *
	 * @var array of WC_Deprecated_Hooks
	 */
	public $deprecated_hook_handlers = array();

	/**
	 * Constructor
	 * Created admin and public class
	 */
	function __construct()
	{
		$this->_name = TINVWL_PREFIX;
		$this->_version = TINVWL_FVERSION;

		$this->set_locale();
		$this->maybe_update();
		$this->load_function();
		$this->define_hooks();
		$this->object_admin = new TInvWL_Admin_TInvWL($this->_name, $this->_version);

		// Allow to disable wishlist for frontend conditionally. Must be hooked on 'plugins_loaded' action.
		if (apply_filters('tinvwl_load_frontend', true)) {
			$this->object_public = TInvWL_Public_TInvWL::instance($this->_name, $this->_version);
		}
	}

	/**
	 * Run plugin
	 */
	function run()
	{
		if (is_null(get_option($this->_name . '_db_ver', null))) {
			TInvWL_Activator::activate();
		}

		TInvWL_View::_init($this->_name, $this->_version);
		TInvWL_Form::_init($this->_name);

		if (is_admin()) {
			new TInvWL_WizardSetup($this->_name, $this->_version);
			new TInvWL_Export($this->_name, $this->_version);
			$this->object_admin->load_function();
		} else {
			// Allow to disable wishlist for frontend conditionally. Must be hooked on 'plugins_loaded' action.
			if (apply_filters('tinvwl_load_frontend', true)) {
				$this->object_public->load_function();
			}
		}

		$this->deprecated_hook_handlers['actions'] = new TInvWL_Deprecated_Actions();
		$this->deprecated_hook_handlers['filters'] = new TInvWL_Deprecated_Filters();
		$this->rest_api = TInvWL_API::init();
	}

	/**
	 * Set localization
	 */
	private function set_locale()
	{
		if (function_exists('determine_locale')) {
			$locale = determine_locale();
		} else {
			$locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
		}

		$locale = apply_filters('plugin_locale', $locale, TINVWL_DOMAIN);

		$mofile = sprintf('%1$s-%2$s.mo', TINVWL_DOMAIN, $locale);
		$mofiles = array();

		$mofiles[] = WP_LANG_DIR . DIRECTORY_SEPARATOR . basename(TINVWL_PATH) . DIRECTORY_SEPARATOR . $mofile;
		$mofiles[] = WP_LANG_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $mofile;
		$mofiles[] = TINVWL_PATH . 'languages' . DIRECTORY_SEPARATOR . $mofile;
		foreach ($mofiles as $mofile) {
			if (file_exists($mofile) && load_textdomain(TINVWL_DOMAIN, $mofile)) {
				return;
			}
		}

		load_plugin_textdomain(TINVWL_DOMAIN, false, basename(TINVWL_PATH) . DIRECTORY_SEPARATOR . 'languages');
	}

	/**
	 * Define hooks
	 */
	function define_hooks()
	{
		add_filter('plugin_action_links_' . plugin_basename(TINVWL_PATH . 'ti-woocommerce-wishlist.php'), array(
			$this,
			'action_links',
		));
		add_action('after_setup_theme', 'tinvwl_set_utm', 100);
	}

	/**
	 * Load function
	 */
	function load_function()
	{
	}

	/**
	 * Testing for the ability to update the functional
	 */
	function maybe_update()
	{
		$prev = get_option($this->_name . '_ver');
		if (false === $prev) {
			add_option($this->_name . '_ver', $this->_version);
			$prev = $this->_version;
		}
		if (version_compare($this->_version, $prev, 'gt')) {
			TInvWL_Activator::update();
			new TInvWL_Update($this->_version, $prev);
			update_option($this->_name . '_ver', $this->_version);
			do_action('tinvwl_updated', $this->_version, $prev);
		}
	}

	/**
	 * Action_links function.
	 *
	 * @access public
	 *
	 * @param mixed $links Links.
	 *
	 * @return array
	 */
	public function action_links($links)
	{
		$plugin_links[] = '<a href="' . admin_url('admin.php?page=tinvwl') . '">' . __('Settings', 'ti-woocommerce-wishlist') . '</a>';
		$plugin_links[] = '<a target="_blank" href="https://templateinvaders.com/product/ti-woocommerce-wishlist-wordpress-plugin/?utm_source=' . TINVWL_UTM_SOURCE . '&utm_campaign=' . TINVWL_UTM_CAMPAIGN . '&utm_medium=' . TINVWL_UTM_MEDIUM . '&utm_content=action_link&partner=' . TINVWL_UTM_SOURCE . '" style="color:#46b450;font-weight:700;">' . __('Premium Version', 'ti-woocommerce-wishlist') . '</a>';
		$plugin_links[] = '<a target="_blank" href="https://woocommercewishlist.com/preview/?utm_source=' . TINVWL_UTM_SOURCE . '&utm_campaign=' . TINVWL_UTM_CAMPAIGN . '&utm_medium=' . TINVWL_UTM_MEDIUM . '&utm_content=action_link&partner=' . TINVWL_UTM_SOURCE . '"  style="color:#515151">' . __('Live Demo', 'ti-woocommerce-wishlist') . '</a>';

		return array_merge($links, $plugin_links);
	}
}
