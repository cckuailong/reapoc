<?php
/**
 * Admin pages class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Admin pages class
 */
class TInvWL_Admin_TInvWL extends TInvWL_Admin_Base
{

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct($plugin_name, $version)
	{
		$this->_name = $plugin_name;
		$this->_version = $version;
	}

	/**
	 * Load functions.
	 * Create Wishlist and Product class.
	 * Load settings classes.
	 */
	function load_function()
	{
		TInvWL_Includes_API_Yoasti18n::instance();

		$this->load_settings();

		$this->define_hooks();
	}

	/**
	 * Load settings classes.
	 *
	 * @return boolean
	 */
	function load_settings()
	{
		$dir = TINVWL_PATH . 'admin/settings/';
		if (!file_exists($dir) || !is_dir($dir)) {
			return false;
		}
		$files = scandir($dir);
		foreach ($files as $value) {
			if (preg_match('/\.class\.php$/i', $value)) {
				$file = preg_replace('/\.class\.php$/i', '', $value);
				$class = 'TInvWL_Admin_Settings_' . ucfirst($file);
				$class::instance($this->_name, $this->_version);
			}
		}

		return true;
	}

	/**
	 * Define hooks
	 */
	function define_hooks()
	{
		add_action('admin_menu', array($this, 'action_menu'));
		if ('skip' === filter_input(INPUT_GET, $this->_name . '-wizard')) {
			update_option($this->_name . '_wizard', true);
		}
		if (!get_option($this->_name . '_wizard')) {
			add_action('admin_notices', array($this, 'wizard_run_admin_notice'));
		} elseif (!tinv_get_option('page', 'wishlist')) {
			add_action('admin_notices', array($this, 'empty_page_admin_notice'));
		}
		if (!tinv_get_option('chat', 'enabled')) {
			add_action('admin_notices', array($this, 'enable_chat_admin_notice'));
		}
		add_action('wp_ajax_tinvwl_admin_chat_notice', array($this, 'tinvwl_admin_chat_notice'));
		add_action('woocommerce_system_status_report', array($this, 'system_report_templates'));

		add_action('switch_theme', array($this, 'admin_notice_outdated_templates'));
		add_action('tinvwl_updated', array($this, 'admin_notice_outdated_templates'));

		// Add a post display state for special WC pages.
		add_filter('display_post_states', array($this, 'add_display_post_states'), 10, 2);

		add_action('tinvwl_admin_promo_footer', array($this, 'promo_footer'));
		add_action('tinvwl_remove_without_author_wishlist', array($this, 'remove_old_wishlists'));
		$this->scheduled_remove_wishlist();
	}

	/**
	 * Error notice if wizard didn't run.
	 */
	function wizard_run_admin_notice()
	{
		printf('<div class="notice notice-error"><p>%1$s</p><p><a href="%2$s" class="button-primary">%3$s</a> <a href="%4$s" class="button-secondary">%5$s</a></p></div>',
			__('<strong>Welcome to WooCommerce Wishlist Plugin</strong> – You‘re almost ready to start :)', 'ti-woocommerce-wishlist'), // @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			esc_url(admin_url('index.php?page=tinvwl-wizard')),
			esc_html__('Run the Setup Wizard', 'ti-woocommerce-wishlist'),
			esc_url(admin_url('index.php?page=' . $this->_name . '&' . $this->_name . '-wizard=skip')),
			esc_html__('Skip Setup', 'ti-woocommerce-wishlist')
		);
	}

	/**
	 * Error notice if wishlist page not set.
	 */
	function empty_page_admin_notice()
	{
		printf('<div class="notice notice-error is-dismissible" style="position: relative;"><h4>%1$s</h4><p>%2$s</p><ol><li>%3$s</li><li>%4$s</li><li>%5$s</li></ol><p><a href="%6$s">%7$s</a>%8$s<a href="%9$s">%10$s</a></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss', 'ti-woocommerce-wishlist') . '</span></button></div>', // @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			esc_html__('WooCommerce Wishlist Plugin is misconfigured!', 'ti-woocommerce-wishlist'),
			esc_html__('Since the Setup Wizard was skipped, the Wishlist may function improperly.', 'ti-woocommerce-wishlist'),
			esc_html__('Create a New Page or open to edit a page where the Wishlist should be displayed.', 'ti-woocommerce-wishlist'),
			__('Add <code>[ti_wishlistsview]</code> shortcode into a page content.', 'ti-woocommerce-wishlist'),
			esc_html__('In a plugin General Settings section apply this page as a "Wishlist" page.', 'ti-woocommerce-wishlist'),
			esc_url($this->admin_url('') . '#general'),
			esc_html__('Please apply the Wishlist page', 'ti-woocommerce-wishlist'),
			esc_html__(' or ', 'ti-woocommerce-wishlist'),
			esc_url(admin_url('index.php?page=tinvwl-wizard')),
			esc_html__('Run the Setup Wizard', 'ti-woocommerce-wishlist')
		);
	}

	/**
	 * Notice to enable support chat.
	 */
	function enable_chat_admin_notice()
	{
		if (!isset($_GET['page']) || substr($_GET['page'], 0, 6) !== 'tinvwl') {
			return;
		}

		$hide_notice = get_option('tinvwl_hide_chat_notice');

		if ($hide_notice) {
			return;
		}

		printf('<div class="notice notice-warning  is-dismissible tinvwl-chat-notice"><p>%1$s</p><p><a href="%2$s" class="button-primary">%3$s</a></p></div>',
			__('The Support Chat is disabled by default for the plugin setting pages. Enable it to get the most from our service!', 'ti-woocommerce-wishlist'), // @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			esc_url(admin_url('admin.php?page=tinvwl#chat')),
			esc_html__('Enable Support Chat', 'ti-woocommerce-wishlist')
		);
	}

	function tinvwl_admin_chat_notice()
	{
		update_option('tinvwl_hide_chat_notice', '1');
	}

	/**
	 * Creation mune and sub-menu
	 */
	function action_menu()
	{
		global $wp_roles;
		$page = add_menu_page(__('TI Wishlist', 'ti-woocommerce-wishlist'), __('TI Wishlist', 'ti-woocommerce-wishlist'), 'tinvwl_general_settings', $this->_name, null, TINVWL_URL . 'assets/img/icon_menu.png', '55.888');
		add_action("load-$page", array($this, 'onload'));
		add_action('admin_enqueue_scripts', array($this, 'add_inline_scripts'));
		$menu = apply_filters('tinvwl_admin_menu', array());
		foreach ($menu as $item) {
			if (!array_key_exists('page_title', $item)) {
				$item['page_title'] = $item['title'];
			}
			if (!array_key_exists('parent', $item)) {
				$item['parent'] = $this->_name;
			}
			if (!array_key_exists('capability', $item)) {
				$item['capability'] = 'manage_woocommerce';
			}

			if (!array_key_exists('roles', $item)) {
				$item['roles'] = array('administrator');
			}

			foreach ($item['roles'] as $role) {
				$wp_roles->add_cap($role, $item['capability']);
			}

			$item['slug'] = implode('-', array_filter(array($this->_name, $item['slug'])));

			$page = add_submenu_page($item['parent'], $item['page_title'], $item['title'], $item['capability'], $item['slug'], $item['method']);
			add_action("load-$page", array($this, 'onload'));
		}
	}

	/**
	 * Load style and javascript
	 */
	function onload()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_filter('admin_footer_text', array($this, 'footer_admin'));
		add_filter('screen_options_show_screen', array($this, 'screen_options_hide_screen'), 10, 2);

		add_filter('tinvwl_view_panelstatus', array($this, 'status_panel'), 9999);
	}

	/**
	 * Load style
	 */
	function enqueue_styles()
	{
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if (apply_filters('tinvwl_load_webfont_admin', true)) {
			wp_enqueue_style($this->_name . '-gfonts', (is_ssl() ? 'https' : 'http') . '://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800', '', null, 'all');
			wp_enqueue_style($this->_name . '-webfont', TINVWL_URL . 'assets/css/webfont' . $suffix . '.css', array(), $this->_version, 'all');
			wp_style_add_data($this->_name . '-webfont', 'rtl', 'replace');
			wp_style_add_data($this->_name . '-webfont', 'suffix', $suffix);
		}

		wp_enqueue_style($this->_name, TINVWL_URL . 'assets/css/admin' . $suffix . '.css', array(), $this->_version, 'all');
		wp_style_add_data($this->_name, 'rtl', 'replace');
		wp_style_add_data($this->_name, 'suffix', $suffix);
		wp_enqueue_style($this->_name . '-form', TINVWL_URL . 'assets/css/admin-form' . $suffix . '.css', array(), $this->_version, 'all');
		wp_style_add_data($this->_name . '-form', 'rtl', 'replace');
		wp_style_add_data($this->_name . '-form', 'suffix', $suffix);
	}

	/**
	 * Load javascript
	 */
	function add_inline_scripts()
	{
		wp_add_inline_script('jquery-blockui', 'jQuery(function(c){c("body").on("click.woo",\'a[href*="//woocommerce.com"]\',function(o){var e=(((o||{}).originalEvent||{}).target||{}).href||!1,r=((o||{}).currentTarget||{}).href||!1,t="&";e&&r&&(o.currentTarget.href=e.split("?")[0]+"?aff=3955",setTimeout(function(){o.originalEvent.target.href=e},1)),c("body").off("click.woo",\'a[href*="woocommerce.com"]\')})});');
	}

	/**
	 * Load javascript
	 */
	function enqueue_scripts()
	{
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script($this->_name . '-bootstrap', TINVWL_URL . 'assets/js/bootstrap' . $suffix . '.js', array('jquery'), $this->_version, 'all');
		wp_register_script($this->_name, TINVWL_URL . 'assets/js/admin' . $suffix . '.js', array(
			'jquery',
			'wp-color-picker',
		), $this->_version, 'all');
		wp_localize_script($this->_name, 'tinvwl_comfirm', array(
			'text_comfirm_reset' => __('Are you sure you want to reset the settings?', 'ti-woocommerce-wishlist'),
			'ajax_url' => WC()->ajax_url(),
		));
		wp_enqueue_script($this->_name);

		if (tinv_get_option('chat', 'enabled')) {

			$geo = new WC_Geolocation(); // Get WC_Geolocation instance object
			$user_ip = $geo->get_ip_address(); // Get user IP
			$user_geo = $geo->geolocate_ip($user_ip); // Get geolocated user data.
			$country_code = $user_geo['country']; // Get the country code
			$restricted_codes = array('BD', 'PK', 'IN', 'NG', 'KE');

			if (!in_array($country_code, $restricted_codes)) {

				$user_id = get_current_user_id();
				$user_info = get_userdata($user_id);
				$current_theme = wp_get_theme();

				$parent_theme = $current_theme->parent();

				wp_add_inline_script($this->_name, 'window.intercomSettings = {
					app_id: "wj7rirzi",
					"Website": "' . get_site_url() . '",
					"Plugin name": "WooCommerce Wishlist Plugin",
					"Plugin version":"' . TINVWL_FVERSION . '",
					"Theme name":"' . $current_theme->get('Name') . '",
					"Theme version":"' . $current_theme->get('Version') . '",
					"Theme URI":"' . $current_theme->get('ThemeURI') . '",
					"Theme author":"' . $current_theme->get('Author') . '",
					"Theme author URI":"' . $current_theme->get('AuthorURI') . '",
					"Parent theme name":"' . (($parent_theme) ? $parent_theme->get('Name') : '') . '",
					"Parent theme version":"' . (($parent_theme) ? $parent_theme->get('Version') : '') . '",
					"Parent theme URI":"' . (($parent_theme) ? $parent_theme->get('ThemeURI') : '') . '",
					"Parent theme author":"' . (($parent_theme) ? $parent_theme->get('Author') : '') . '",
					"Parent theme author URI":"' . (($parent_theme) ? $parent_theme->get('AuthorURI') : '') . '",
					};
					(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic("reattach_activator");ic("update",intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement("script");s.type="text/javascript";s.async=true;s.src="https://widget.intercom.io/widget/zyh6v0pc";var x=d.getElementsByTagName("script")[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent("onload",l);}else{w.addEventListener("load",l,false);}}})();
					Intercom("trackEvent", "wishlist-free-install", {
						theme_name:"' . (($parent_theme) ? $parent_theme->get('Name') : $current_theme->get('Name')) . '",
						theme_uri:"' . (($parent_theme) ? $parent_theme->get('ThemeURI') : $current_theme->get('ThemeURI')) . '",
						theme_author:"' . (($parent_theme) ? $parent_theme->get('Author') : $current_theme->get('Author')) . '",
						theme_author_uri:"' . (($parent_theme) ? $parent_theme->get('AuthorURI') : $current_theme->get('AuthorURI')) . '",
						theme_version:"' . (($parent_theme) ? $parent_theme->get('Version') : $current_theme->get('Version')) . '",
						website:"' . get_site_url() . '",
						user:"' . $user_info->user_email . '",
						user_name:"' . $user_info->user_nicename . '",
						plugin_name:"WooCommerce Wishlist Plugin",
						plugin_version:"' . TINVWL_FVERSION . '",
						partner:"' . TINVWL_UTM_SOURCE . '"
					});
			');
			}
		}
	}

	/**
	 * Add plugin footer copywriting
	 */
	function footer_admin()
	{
		do_action('tinvwl_admin_promo_footer');
	}

	/**
	 * Promo in footer for wishlist
	 */
	function promo_footer()
	{
		echo 'Made with <i class="ftinvwl ftinvwl-heart2"></i> by <a href="https://templateinvaders.com/?utm_source=' . TINVWL_UTM_SOURCE . '&utm_campaign=' . TINVWL_UTM_CAMPAIGN . '&utm_medium=' . TINVWL_UTM_MEDIUM . '&utm_content=made_by&partner=' . TINVWL_UTM_SOURCE . '">TemplateInvaders</a><br />If you like WooCommerce Wishlist Plugin please leave us a <a href="https://wordpress.org/support/plugin/ti-woocommerce-wishlist/reviews/#new-post"><span><i class="ftinvwl ftinvwl-star"></i><i class="ftinvwl ftinvwl-star"></i><i class="ftinvwl ftinvwl-star"></i><i class="ftinvwl ftinvwl-star"></i><i class="ftinvwl ftinvwl-star"></i></span></a> rating.'; // WPCS: xss ok.
	}

	/**
	 * Create Upgrade button
	 *
	 * @param array $panel Panel Button.
	 *
	 * @return array
	 */
	function status_panel($panel)
	{
		array_unshift($panel, sprintf('<a class="tinvwl-btn red w-icon smaller-txt" href="%s"><i class="ftinvwl ftinvwl-star"></i><span class="tinvwl-txt">%s</span></a>', 'https://templateinvaders.com/product/ti-woocommerce-wishlist-wordpress-plugin/?utm_source=' . TINVWL_UTM_SOURCE . '&utm_campaign=' . TINVWL_UTM_CAMPAIGN . '&utm_medium=' . TINVWL_UTM_MEDIUM . '&utm_content=header_upgrade&partner=' . TINVWL_UTM_SOURCE, __('Upgrade to Premium', 'ti-woocommerce-wishlist')));

		return $panel;
	}

	/**
	 * Templates overriding status check.
	 *
	 * @param boolean $outdated Out date status.
	 *
	 * @return string
	 */
	function templates_status_check($outdated = false)
	{

		$found_files = array();

		$scanned_files = WC_Admin_Status::scan_template_files(TINVWL_PATH . '/templates/');

		foreach ($scanned_files as $file) {
			if (file_exists(get_stylesheet_directory() . '/' . $file)) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif (file_exists(get_stylesheet_directory() . '/woocommerce/' . $file)) {
				$theme_file = get_stylesheet_directory() . '/woocommerce/' . $file;
			} elseif (file_exists(get_template_directory() . '/' . $file)) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif (file_exists(get_template_directory() . '/woocommerce/' . $file)) {
				$theme_file = get_template_directory() . '/woocommerce/' . $file;
			} else {
				$theme_file = false;
			}

			if (!empty($theme_file)) {
				$core_version = WC_Admin_Status::get_file_version(TINVWL_PATH . '/templates/' . $file);
				$theme_version = WC_Admin_Status::get_file_version($theme_file);

				if ($core_version && (empty($theme_version) || version_compare($theme_version, $core_version, '<'))) {
					if ($outdated) {
						return 'outdated';
					}
					$found_files[] = sprintf(__('<code>%1$s</code> version <strong style="color:red">%2$s</strong> is out of date. The core version is <strong style="color:red">%3$s</strong>', 'ti-woocommerce-wishlist'), str_replace(WP_CONTENT_DIR . '/themes/', '', $theme_file), $theme_version ? $theme_version : '-', $core_version);
				} else {
					$found_files[] = str_replace(WP_CONTENT_DIR . '/themes/', '', $theme_file);
				}
			}
		}

		return $found_files;
	}

	/**
	 * Templates overriding status for WooCommerce Status report page.
	 */
	function system_report_templates()
	{

		TInvWL_View::view('templates-status', array('found_files' => $this->templates_status_check()));
	}

	/**
	 * Outdated templates notice.
	 */
	function admin_notice_outdated_templates()
	{
		if ('outdated' === $this->templates_status_check(true)) {

			$theme = wp_get_theme();

			$html = sprintf(__('<strong>Your theme (%1$s) contains outdated copies of some WooCommerce Wishlist Plugin template files.</strong><br> These files may need updating to ensure they are compatible with the current version of WooCommerce Wishlist Plugin.<br> You can see which files are affected from the <a href="%2$s">system status page</a>.<br> If in doubt, check with the author of the theme.', 'ti-woocommerce-wishlist'), esc_html($theme['Name']), esc_url(admin_url('admin.php?page=wc-status')));

			WC_Admin_Notices::add_custom_notice('outdated_templates', $html);
		} else {
			WC_Admin_Notices::remove_notice('outdated_templates');
		}
	}

	/**
	 * Disable screen option on plugin pages
	 *
	 * @param boolean $show_screen Show screen.
	 * @param \WP_Screen $_this Screen option page.
	 *
	 * @return boolean
	 */
	function screen_options_hide_screen($show_screen, $_this)
	{
		if ($this->_name === $_this->parent_base || $this->_name === $_this->parent_file) {
			return false;
		}

		return $show_screen;
	}

	/**
	 * Check if there is a hook in the cron
	 */
	function scheduled_remove_wishlist()
	{
		$timestamp = wp_next_scheduled('tinvwl_remove_without_author_wishlist');
		if (!$timestamp) {
			$time = strtotime('00:00 today +1 HOURS');
			wp_schedule_event($time, 'daily', 'tinvwl_remove_without_author_wishlist');
		}
	}

	/**
	 * Removing old wishlist without a user older than 34 days
	 */
	public function remove_old_wishlists()
	{
		$wl = new TInvWL_Wishlist();
		$wishlists = $wl->get(array(
			'author' => 0,
			'type' => 'default',
			'sql' => 'SELECT * FROM {table} {where} AND `date` < DATE_SUB( CURDATE(), INTERVAL 34 DAY)',
		));
		foreach ($wishlists as $wishlist) {
			$wl->remove($wishlist['ID']);
		}
	}

	/**
	 * Add a post display state for special WC pages in the page list table.
	 *
	 * @param array $post_states An array of post display states.
	 * @param WP_Post $post The current post object.
	 *
	 * @return array
	 */
	public function add_display_post_states($post_states, $post)
	{
		if (tinv_get_option('page', 'wishlist') === $post->ID) {
			$post_states['tinvwl_page_for_wishlist'] = __('Wishlist Page', 'ti-woocommerce-wishlist');
		}

		return $post_states;
	}
}
