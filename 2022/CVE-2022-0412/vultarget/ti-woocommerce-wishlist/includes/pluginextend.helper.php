<?php
/**
 * Dependency plugin class
 *
 * @since             1.0.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Dependency plugin class
 */
class TInvWL_PluginExtend
{

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Plugin transient name
	 *
	 * @var string
	 */
	private $transient;

	/**
	 * Plugin dependency array
	 *
	 * @var array
	 */
	private $dependency;

	/**
	 * Current dependency name
	 *
	 * @var string
	 */
	private $dependency_current;

	/**
	 * Current dependency nice name
	 *
	 * @var string
	 */
	private $dependency_current_nice_name;

	/**
	 * Plugin path dir
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * Cached plugin data
	 *
	 * @var array
	 */
	private $plugin_data;

	/**
	 * Plugin error message
	 *
	 * @var array
	 */
	public $message;

	/**
	 * Constructor
	 *
	 * @param string $plugin Plugin transient name, or can use Plugin root file.
	 * @param string $root_file Plugin root file, or can use Plugin transient name.
	 * @param string $plugin_name Plugin name.
	 */
	public function __construct($plugin, $root_file = null, $plugin_name = TINVWL_PREFIX)
	{
		$this->_name = $plugin_name;
		if (empty($plugin)) {
			$this->transient = plugin_basename($root_file);
			$this->plugin_path = trailingslashit(plugin_dir_path(dirname($root_file)));
		} else {
			$this->transient = $plugin;
			$this->plugin_path = trailingslashit(dirname(TINVWL_PATH));
		}
		$this->dependency = array();
		$this->plugin_data = array();
		$this->message = array();
	}

	/**
	 * Run hooks dependency
	 */
	public function run()
	{
		if ('plugins.php' === basename($_SERVER['PHP_SELF']) && !(defined('WP_CLI') && WP_CLI)) { // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected
			add_action('admin_notices', array($this, 'admin_notices'));

			$plugins = $this->get_dependency_plugins();

			foreach (array_keys($plugins) as $plugin) {
				add_filter('plugin_action_links_' . $plugin, array($this, 'plugin_action_links_maybe_deactivate'));
				add_filter('network_admin_plugin_action_links_' . $plugin, array(
					$this,
					'plugin_action_links_maybe_deactivate',
				));
			}

			add_action('after_plugin_row_' . $this->transient, array($this, 'plugin_row'), 10);
		} else {
			add_action('update_option_active_sitewide_plugins', array($this, 'maybe_deactivate'), 10, 2);
			add_action('update_option_active_plugins', array($this, 'maybe_deactivate'), 10, 2);
		}
	}

	/**
	 * Set dependency plugin transient name
	 *
	 * @param string $plugin Plugin transient name.
	 * @param string $nice_name Plugin nice name.
	 *
	 * @return \TInvWL_PluginExtend
	 */
	public function set_dependency($plugin, $nice_name)
	{
		$this->dependency_current = $plugin;
		$this->dependency_current_nice_name = $nice_name;

		return $this;
	}

	/**
	 * Reset current dependency plugin transient name
	 *
	 * @return \TInvWL_PluginExtend
	 */
	public function reset_dependency()
	{
		$this->dependency_current = null;

		return $this;
	}

	/**
	 * Set dependency version by index rules
	 *
	 * @param integer $index Index rules.
	 *                    0 Min version.
	 *                    1 Max version.
	 *                    2 Need plugin verion.
	 *                    3 Conflict plugin verion.
	 * @param string $version Version dependency.
	 *
	 * @return boolean
	 */
	private function set_dependency_version($index, $version = '1.0.0')
	{
		if (empty($this->dependency_current)) {
			return false;
		}
		if (empty($version)) {
			$this->dependency[$this->dependency_current][$index] = null;
		} else {
			$this->dependency[$this->dependency_current][$index] = $version;
		}
		$this->dependency[$this->dependency_current]['nice_name'] = $this->dependency_current_nice_name;
	}

	/**
	 * Set minimal dependency version
	 *
	 * @param string $version Version dependency.
	 *
	 * @return \TInvWL_PluginExtend
	 */
	public function min($version = '1.0.0')
	{
		if ('*' === $version) {
			$version = '';
		}
		$this->set_dependency_version(0, $version);

		return $this;
	}

	/**
	 * Set maximum dependency version
	 *
	 * @param string $version Version dependency.
	 *
	 * @return \TInvWL_PluginExtend
	 */
	public function max($version = '1.0.0')
	{
		if ('*' === $version) {
			$version = '';
		}
		$this->set_dependency_version(1, $version);

		return $this;
	}

	/**
	 * Set need plugin dependency version
	 *
	 * @param string $version Version dependency. Can use '*' for check any version.
	 *
	 * @return \TInvWL_PluginExtend
	 */
	public function need($version = '*')
	{
		$this->set_dependency_version(2, $version);

		return $this;
	}

	/**
	 * Set conflict plugin dependency version
	 *
	 * @param string $version Version dependency. Can use '*' for check any version.
	 *
	 * @return \TInvWL_PluginExtend
	 */
	public function conflict($version = '*')
	{
		$this->set_dependency_version(3, $version);

		return $this;
	}

	/**
	 * Get dependency array
	 *
	 * @param string $plugin Plugin transient name.
	 *
	 * @return array
	 */
	private function get_dependency($plugin)
	{
		if (array_key_exists($plugin, $this->dependency)) {
			return $this->dependency[$plugin];
		}

		return array();
	}

	/**
	 * Get dependency version from array.
	 *
	 * @param string $plugin Plugin transient name.
	 * @param integer $index Index rules.
	 *                    0 Min version.
	 *                    1 Max version.
	 *                    2 Need plugin verion.
	 *                    3 Conflict plugin verion.
	 *
	 * @return array
	 */
	private function get_dep_ver($plugin, $index)
	{
		$dependency = $this->get_dependency($plugin);
		if (array_key_exists($index, $dependency)) {
			return $dependency[$index];
		}

		return null;
	}

	/**
	 * List dependency plugins
	 *
	 * @return array
	 */
	private function get_dependency_plugins()
	{
		return $this->dependency;
	}

	/**
	 * Check all dependency.
	 *
	 * @return boolean
	 */
	public function status_dependency()
	{
		$this->message = array();
		$plugins = $this->get_dependency_plugins();
		$status = true;

		foreach ($plugins as $plugin => $data) {
			if (is_plugin_active($plugin) && !$this->is_plugin_at_conflict_version($plugin)) {
				$status = $this->set_message('conflict', $data['nice_name']);
			} elseif (!is_plugin_active($plugin) || !$this->is_plugin_at_need_version($plugin)) {
				$status = $this->set_message('need', $data['nice_name']);
			} elseif (is_plugin_active($plugin) && !$this->is_plugin_at_min_version($plugin)) {
				$status = $this->set_message('upgrade', $data['nice_name']);
			} elseif (is_plugin_active($plugin) && !$this->is_plugin_at_max_version($plugin)) {
				$status = $this->set_message('downgrade', $plugin);
			} elseif (!is_plugin_active($plugin)) {
				$status = $this->set_message('activate', $data['nice_name']);
			}
		}

		return $status;
	}

	/**
	 * Check plugin minimal version dependency.
	 *
	 * @param string $plugin Plugin transient name.
	 *
	 * @return boolean
	 */
	private function is_plugin_at_min_version($plugin)
	{
		return $this->is_plugin_at_version($plugin, 0);
	}

	/**
	 * Check plugin maximal version dependency.
	 *
	 * @param string $plugin Plugin transient name.
	 *
	 * @return boolean
	 */
	private function is_plugin_at_max_version($plugin)
	{
		return $this->is_plugin_at_version($plugin, 1);
	}

	/**
	 * Check plugin need version dependency.
	 *
	 * @param string $plugin Plugin transient name.
	 *
	 * @return boolean
	 */
	private function is_plugin_at_need_version($plugin)
	{
		return $this->is_plugin_at_version($plugin, 2);
	}

	/**
	 * Check plugin conflict version dependency.
	 *
	 * @param string $plugin Plugin transient name.
	 *
	 * @return boolean
	 */
	private function is_plugin_at_conflict_version($plugin)
	{
		return $this->is_plugin_at_version($plugin, 3);
	}

	/**
	 * Check plugin version dependency.
	 *
	 * @param string $plugin Plugin transient name.
	 *
	 * @param integer $i Index rules.
	 *                    0 Min version.
	 *                    1 Max version.
	 *                    2 Need plugin verion.
	 *                    3 Conflict plugin verion.
	 *
	 * @return boolean
	 */
	private function is_plugin_at_version($plugin, $i = 0)
	{

		switch ($i) {
			case 3:
				$type = 'ne';
				$i = 3;
				break;
			case 2:
				$type = 'eq';
				$i = 2;
				break;
			case 1:
				$type = 'le';
				$i = 1;
				break;
			case 0:
			default:
				$type = 'ge';
				$i = 0;
		}
		$version = $this->get_dep_ver($plugin, $i);
		if (is_null($version)) {
			return true;
		}
		$version_plugin = $this->get_plugin_data($plugin, 'Version');
		if ('*' === $version) {
			if (3 === $i) {
				return empty($version_plugin);
			} else {
				return !empty($version_plugin);
			}
		}

		return version_compare($version_plugin, $version, $type);
	}

	/**
	 * Get error messages
	 *
	 * @param boolean $first Get first or all error messages.
	 *
	 * @return string
	 */
	public function get_messages($first = false)
	{
		if ($first) {
			$message = array_shift($this->message);
			$this->message = array();

			return $message;
		}

		$message = '<p>' . implode('</p><p>', $this->message) . '</p>';
		$this->message = array();

		return $message;
	}

	/**
	 * Deactivation plugin
	 *
	 * @param string $old_value Not used.
	 * @param string $value Not used.
	 *
	 * @return string
	 */
	public function maybe_deactivate($old_value, $value)
	{
		if (!$this->status_dependency()) {
			self::deactivate_self($this->transient);

			if (defined('WP_CLI') && WP_CLI) {
				$plugins = $this->get_dependency_plugins();
				$this->get_messages();

				foreach ($plugins as $plugin => $data) {
					if (!is_plugin_active($plugin) || !$this->is_plugin_at_need_version($plugin)) {
						return WP_CLI::error($this->get_message('deactivate', $data['nice_name']));
					} elseif (is_plugin_active($plugin) && !$this->is_plugin_at_min_version($plugin)) {
						return WP_CLI::error($this->get_message('deactivate', $data['nice_name']));
					} elseif (is_plugin_active($plugin) && !$this->is_plugin_at_max_version($plugin)) {
						return WP_CLI::error($this->get_message('deactivate', $data['nice_name']));
					}
				}
			}
		}
	}

	/**
	 * Deactivation plugin
	 *
	 * @param string $actions Not used.
	 *
	 * @return string
	 */
	public function plugin_action_links_maybe_deactivate($actions)
	{
		if (!$this->status_dependency()) {
			self::deactivate_self($this->transient);
		}

		return $actions;
	}

	/**
	 * Return message in plugin lists table
	 *
	 * @param string $file Plugin file path.
	 */
	public function plugin_row($file)
	{
		if (!$this->status_dependency()) {
			$wp_list_table = _get_list_table('WP_Plugins_List_Table');
			?>
			<tr class="plugin-update-tr installer-plugin-update-tr">
				<td colspan="<?php echo $wp_list_table->get_column_count(); // WPCS: xss ok. ?>"
					class="plugin-update colspanchange">
					<div class="notice inline notice-warning notice-alt">
						<p class="installer-q-icon">
							<?php echo $this->get_messages(true); // WPCS: xss ok. ?>
						</p>
					</div>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Deactivation plugin
	 *
	 * @param string $file Plugin file path.
	 * @param boolean $network_wide Network wide.
	 */
	public static function deactivate_self($file, $network_wide = false)
	{
		if (is_multisite()) {
			$network_wide = is_plugin_active_for_network($file);
		}

		deactivate_plugins($file, true, $network_wide);
	}

	/**
	 * Set message
	 *
	 * @param string $type Type error message.
	 * @param string $plugin Plugin transient name.
	 *
	 * @return boolean
	 */
	private function set_message($type, $plugin)
	{
		$current = $this->get_plugin_data('current', 'Name') ? $this->get_plugin_data('current', 'Name') : $this->transient;
		$version = $this->get_plugin_data('current', 'Version');
		$plugname = $this->get_plugin_data($plugin, 'Name') ? $this->get_plugin_data($plugin, 'Name') : $plugin;

		$message = '';
		switch ($type) {
			case 'deactivate':
				$version = $this->get_dep_ver($plugin, 0);
				$message = __('%2$s %3$s is required for %1$s. Deactivating %1$s.', 'ti-woocommerce-wishlist');
				if (empty($version)) {
					$version = $this->get_dep_ver($plugin, 1);
				}
				if (empty($version)) {
					$version = $this->get_dep_ver($plugin, 2);
				}
				if (empty($version)) {
					$version = $this->get_dep_ver($plugin, 3);
					$message = __('%1$s is confilcted with %2$s %3$s. Deactivating %1$s.', 'ti-woocommerce-wishlist');
				}
				if ('*' === $version) {
					$version = $this->get_plugin_data($plugin, 'Version');
				}
				break;
			case 'upgrade':
			case 'update':
				$version = $this->get_dep_ver($plugin, 0);
				$message = __('%2$s %3$s is required. Please update it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
			case 'downgrade':
			case 'downdate':
				$version = $this->get_dep_ver($plugin, 1);
				$message = __('%2$s %3$s is required. Please downgrade it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
			case 'need':
				$version = '*' === $this->get_dep_ver($plugin, 2) ? $this->get_plugin_data($plugin, 'Version') : $this->get_dep_ver($plugin, 2);
				$message = __('%2$s %3$s is required. Please activate it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
			case 'conflict':
				$version = '*' === $this->get_dep_ver($plugin, 3) ? $this->get_plugin_data($plugin, 'Version') : $this->get_dep_ver($plugin, 3);
				$message = __('%1$s is conflicted with %2$s %3$s. Please disable it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
			case 'activate':
				$version = $this->get_dep_ver($plugin, 1);
				$message = __('%1$s %3$s is required. Please activate it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
		} // End switch().
		if (empty($message)) {
			return true;
		}
		if (!empty($version)) {
			$version = '(v' . $version . ')';
		}
		$message = sprintf($message, $current, $plugname, $version);

		$this->message[] = $message;

		return false;
	}

	/**
	 * Get message
	 *
	 * @param string $type Type error message.
	 * @param string $plugin Plugin transient name.
	 *
	 * @return boolean
	 */
	private function get_message($type, $plugin)
	{
		$current = $this->get_plugin_data('current', 'Name') ? $this->get_plugin_data('current', 'Name') : $this->transient;
		$version = $this->get_plugin_data('current', 'Version');
		$plugname = $this->get_plugin_data($plugin, 'Name') ? $this->get_plugin_data($plugin, 'Name') : $plugin;

		$message = '';
		switch ($type) {
			case 'deactivate':
				$version = $this->get_dep_ver($plugin, 0);
				$message = __('%2$s %3$s is required for %1$s. Deactivating %1$s.', 'ti-woocommerce-wishlist');
				if (empty($version)) {
					$version = $this->get_dep_ver($plugin, 1);
				}
				if (empty($version)) {
					$version = $this->get_dep_ver($plugin, 2);
				}
				if (empty($version)) {
					$version = $this->get_dep_ver($plugin, 3);
					$message = __('%1$s is conflicted with %2$s %3$s. Deactivating %1$s.', 'ti-woocommerce-wishlist');
				}
				if ('*' === $version) {
					$version = $this->get_plugin_data($plugin, 'Version');
				}
				break;
			case 'upgrade':
			case 'update':
				$version = $this->get_dep_ver($plugin, 0);
				$message = __('%2$s %3$s is required. Please update it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
			case 'downgrade':
			case 'downdate':
				$version = $this->get_dep_ver($plugin, 1);
				$message = __('%2$s %3$s is required. Please downgrade it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
			case 'need':
				$version = '*' === $this->get_dep_ver($plugin, 2) ? $this->get_plugin_data($plugin, 'Version') : $this->get_dep_ver($plugin, 2);
				$message = __('%2$s %3$s is required. Please activate it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
			case 'conflict':
				$version = '*' === $this->get_dep_ver($plugin, 3) ? $this->get_plugin_data($plugin, 'Version') : $this->get_dep_ver($plugin, 3);
				$message = __('%1$s is conflicted with %2$s %3$s. Please disable it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
			case 'activate':
				$version = $this->get_dep_ver($plugin, 1);
				$message = __('%1$s %3$s is required. Please activate it before activating this plugin.', 'ti-woocommerce-wishlist');
				break;
		} // End switch().

		if (!empty($version)) {
			$version = '(v' . $version . ')';
		}

		return sprintf($message, $current, $plugname, $version);
	}

	/**
	 * Add error admin notice
	 */
	public function admin_notices()
	{
		if (!$this->status_dependency()) {
			printf('<div class="error is-dismissible">%s</div>', $this->get_messages()); // WPCS: xss ok.
		}
	}

	/**
	 * Get plugin info
	 *
	 * @param string $plugin Plugin transient name.
	 * @param string $attr Plugin attribute name.
	 *
	 * @return mixed
	 */
	public function get_plugin_data($plugin, $attr = null)
	{
		if ('current' === $plugin) {
			$plugin = $this->transient;
		}

		$plugin_path = $this->plugin_path . $plugin;

		if (!array_key_exists($plugin, $this->plugin_data)) {
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			$plugin_data = array_filter(@get_plugin_data($plugin_path, false, false)); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			if (empty($plugin_data)) {
				$plugin_data = null;
			}
			$this->plugin_data[$plugin] = $plugin_data;
		}

		if (empty($attr)) {
			return $this->plugin_data[$plugin];
		}
		if (array_key_exists($attr, (array)$this->plugin_data[$plugin])) {
			return $this->plugin_data[$plugin][$attr];
		}

		return null;
	}
}
