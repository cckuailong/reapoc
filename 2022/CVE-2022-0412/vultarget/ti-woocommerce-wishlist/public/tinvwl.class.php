<?php
/**
 * Public pages class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Public pages class
 */
class TInvWL_Public_TInvWL
{

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $_version;

	/**
	 * Raw rewrites
	 *
	 * @var array
	 */
	public static $rules_raw;
	/**
	 * This class
	 *
	 * @var \TInvWL_Public_TInvWL
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 *
	 * @return \TInvWL_Public_TInvWL
	 */
	public static function instance($plugin_name = TINVWL_PREFIX, $version = TINVWL_VERSION)
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self($plugin_name, $version);
		}

		return self::$_instance;
	}

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
		$this->pre_load_function();
	}

	/**
	 * Create all object and shortcode
	 */
	function pre_load_function()
	{

		add_action('init', array(__CLASS__, 'add_rewrite_rules'));

		add_action('tinvwl_flush_rewrite_rules', array(__CLASS__, 'apply_rewrite_rules'));

		add_filter('rewrite_rules_array', array($this, 'add_rewrite_rules_raw'), 9999999);

		add_filter('tinvwl_update_wishlists_data', array($this, 'update_local_wishlists_data'));

		add_filter('query_vars', array($this, 'add_query_var'));
		add_action('wp', array($this, 'analytics_referer'));
		add_action('deleted_user', array($this, 'delete_user_wishlist'));

		add_action('wp_ajax_nopriv_' . $this->_name . '_css', array($this, 'dynaminc_css'));
		add_action('wp_ajax_' . $this->_name . '_css', array($this, 'dynaminc_css'));
		add_action('widgets_init', array($this, 'register_widgets'));

		add_filter('woocommerce_locate_core_template', array($this, 'locate_template'), 10, 3);
		add_filter('woocommerce_locate_template', array($this, 'locate_template'), 10, 3);

		$this->addto = TInvWL_Public_AddToWishlist::instance($this->_name);
		$this->view = TInvWL_Public_Wishlist_View::instance($this->_name);
		$this->cart = TInvWL_Public_Cart::instance($this->_name);
		$this->topwishlist = TInvWL_Public_WishlistCounter::instance($this->_name);
	}

	function update_local_wishlists_data($state)
	{
		if (get_transient('_tinvwl_update_wishlists_data')) {
			delete_transient('_tinvwl_update_wishlists_data');

			return true;
		}

		return $state;
	}

	/**
	 * @param $rules
	 *
	 * @return mixed
	 */
	function add_rewrite_rules_raw($rules)
	{
		if (is_array(self::$rules_raw) && tinv_get_option('permalinks', 'force')) {
			self::add_rewrite_rules();
			$rules = self::$rules_raw + $rules;
		}

		return $rules;
	}

	/**
	 * Define hooks
	 */
	function define_hooks()
	{
		if (tinv_get_option('social', 'facebook')) {
			add_filter('language_attributes', array($this, 'add_ogp'), 100);
		}

		if (tinv_get_option('general', 'link_in_myaccount')) {
			add_filter('woocommerce_account_menu_items', array($this, 'account_menu_items'));
			add_filter('woocommerce_get_endpoint_url', array($this, 'account_menu_endpoint'), 4, 10);
		}

		add_action('wp_enqueue_scripts', array($this, 'enqueue_header'));
		add_action('wp_login', array($this, 'transfert_local_to_user'), 10, 2);
		add_action('wp_logout', array($this, 'logout'), 10, 2);
		add_action('user_register', array($this, 'transfert_local_to_user_register'));
		add_action('init', array($this, 'legacy_transfer'), 90);
		add_action('clear_auth_cookie', array($this, 'set_user_sharekey'));

		add_action('tinvwl_after_wishlist_table', array($this, 'wishlist_button_action_before'), 0);
		add_action('tinvwl_after_wishlist_table', array($this, 'wishlist_button_action_after'), 15);
		add_action('tinvwl_after_wishlist_table', array($this, 'wishlist_button_updcart_before'), 15);
		add_action('tinvwl_after_wishlist_table', array($this, 'wishlist_button_action_after'), 100);
	}

	/**
	 * Add analytic click from wishlist
	 */
	function analytics_referer()
	{
		$product_id = absint(get_query_var('tiwp'));
		if (empty($product_id)) {
			return false;
		}

		$wlp = new TInvWL_Product();

		$items = $wlp->get(array('ID' => $product_id));
		$item = array_shift($items);
		if (empty($item)) {
			return false;
		}
		if (empty($item['data'])) {
			return false;
		}
		$wishlist = tinv_wishlist_get($item['wishlist_id']);

		$wla = new TInvWL_Analytics($wishlist, $this->_name);
		if ($wishlist['is_owner']) {
			$wla->click_author_product_from_wl($item['product_id'], $item['variation_id']);
		} else {
			$wla->click_product_from_wl($item['product_id'], $item['variation_id']);
		}
		wp_redirect(remove_query_arg('tiwp')); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.wp_redirect
	}

	/**
	 * Left class button
	 */
	function wishlist_button_action_before()
	{
		echo '<div class="tinvwl-to-left look_in">';
	}

	/**
	 * Right class button
	 */
	function wishlist_button_updcart_before()
	{
		echo '<div class="tinvwl-to-right look_in">';
	}

	/**
	 * Close class button
	 */
	function wishlist_button_action_after()
	{
		echo '</div>';
	}

	/**
	 * Register Widgets
	 */
	function register_widgets()
	{
		$paths = glob(TINVWL_PATH . 'public' . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . '*.class.php');
		foreach ($paths as $path) {
			$path = ucfirst(str_replace('.class.php', '', basename($path)));
			register_widget('TInvWL_Public_Widget_' . $path);
		}
	}

	/**
	 * Overwrites path for email and other template
	 *
	 * @param string $core_file Absolute path.
	 * @param string $template Requered Template file.
	 * @param string $template_base Template path.
	 *
	 * @return string
	 */
	function locate_template($core_file, $template, $template_base)
	{
		$_core_file = tinv_wishlist_locate_template($template, $template_base);
		if (empty($_core_file)) {
			return $core_file;
		}

		return $_core_file;
	}

	/**
	 * Update rewrite url for wishlist
	 */
	public static function update_rewrite_rules()
	{
		wp_schedule_single_event(time(), 'tinvwl_flush_rewrite_rules');
	}

	/**
	 * Apply rewrite url for wishlist
	 */
	public static function apply_rewrite_rules()
	{
		self::add_rewrite_rules();
		flush_rewrite_rules();
	}

	/**
	 * Create rewrite url for wishlist
	 */
	public static function add_rewrite_rules()
	{
		$id = tinv_get_option('page', 'wishlist');
		$pages = array($id);
		$language_codes = array();
		if (function_exists('pll_languages_list')) {
			$language_codes = implode('|', pll_languages_list());
			$translations = PLL()->model->post->get_translations($id);
			$pages = array_merge($pages, array_values($translations));
		} else {
			$languages = apply_filters('wpml_active_languages', array(), array(
				'skip_missing' => 0,
				'orderby' => 'code',
			));
			if (!empty($languages)) {
				foreach ($languages as $l) {
					$pages[] = apply_filters('wpml_object_id', $id, 'page', true, $l['language_code']);
					$language_codes[] = $l['language_code'];
				}
				$pages = array_unique($pages);
				$language_codes = implode('|', array_unique($language_codes));
			}
		}

		$pages = array_filter($pages);
		if (!empty($pages)) {
			foreach ($pages as $page) {
				$page = get_post($page);

				if (!$page) {
					continue;
				}

				$page_slug = $page->post_name;

				if ($language_codes && (defined('POLYLANG_VERSION') || defined('ICL_PLUGIN_PATH'))) {
					add_rewrite_rule('^(' . $language_codes . ')/(([^/]+/)*' . $page_slug . ')/([A-Fa-f0-9]{6})?/wl_page/([0-9]{1,})/{0,1}$', 'index.php?pagename=$matches[2]&tinvwlID=$matches[4]&wl_paged=$matches[5]&lang=$matches[1]', 'top');
					self::$rules_raw['^(' . $language_codes . ')/(([^/]+/)*' . $page_slug . ')/([A-Fa-f0-9]{6})?/wl_page/([0-9]{1,})/{0,1}$'] = 'index.php?pagename=$matches[2]&tinvwlID=$matches[4]&wl_paged=$matches[5]&lang=$matches[1]';
					add_rewrite_rule('^(' . $language_codes . ')/(([^/]+/)*' . $page_slug . ')/([A-Fa-f0-9]{6})?/{0,1}$', 'index.php?pagename=$matches[2]&tinvwlID=$matches[4]&lang=$matches[1]', 'top');
					self::$rules_raw['^(' . $language_codes . ')/(([^/]+/)*' . $page_slug . ')/([A-Fa-f0-9]{6})?/{0,1}$'] = 'index.php?pagename=$matches[2]&tinvwlID=$matches[4]&lang=$matches[1]';
				}

				// Wishlist on frontpage.
				$page_on_front = absint(get_option('page_on_front'));
				if ($page_on_front && 'page' === get_option('show_on_front') && $page->ID === $page_on_front) {
					add_filter('redirect_canonical', array(
						'TInvWL_Public_TInvWL',
						'disable_canonical_redirect_for_front_page',
					));
					// Match the front page and pass item value as a query var.
					add_rewrite_rule('^([A-Fa-f0-9]{6})?/{0,1}$', 'index.php?page_id=' . $page_on_front . '&tinvwlID=$matches[1]', 'top');
					self::$rules_raw['^([A-Fa-f0-9]{6})?/{0,1}$'] = 'index.php?page_id=' . $page_on_front . '&tinvwlID=$matches[1]';
					add_rewrite_rule('^([A-Fa-f0-9]{6})?/wl_page/([0-9]{1,})/{0,1}$', 'index.php?page_id=' . $page_on_front . '&tinvwlID=$matches[3]&wl_paged=$matches[4]', 'top');
					self::$rules_raw['^([A-Fa-f0-9]{6})?/wl_page/([0-9]{1,})/{0,1}$'] = 'index.php?page_id=' . $page_on_front . '&tinvwlID=$matches[3]&wl_paged=$matches[4]';
				}

				add_rewrite_rule('(([^/]+/)*' . $page_slug . ')/([A-Fa-f0-9]{6})?/wl_page/([0-9]{1,})/{0,1}$', 'index.php?pagename=$matches[1]&tinvwlID=$matches[3]&wl_paged=$matches[4]', 'top');
				self::$rules_raw['(([^/]+/)*' . $page_slug . ')/([A-Fa-f0-9]{6})?/wl_page/([0-9]{1,})/{0,1}$'] = 'index.php?pagename=$matches[1]&tinvwlID=$matches[3]&wl_paged=$matches[4]';
				add_rewrite_rule('(([^/]+/)*' . $page_slug . ')/([A-Fa-f0-9]{6})?/{0,1}$', 'index.php?pagename=$matches[1]&tinvwlID=$matches[3]', 'top');
				self::$rules_raw['(([^/]+/)*' . $page_slug . ')/([A-Fa-f0-9]{6})?/{0,1}$'] = 'index.php?pagename=$matches[1]&tinvwlID=$matches[3]';

				// Wishlist on shop page.
				$shop_page_id = wc_get_page_id('shop');
				if ($shop_page_id && $page->ID === $shop_page_id) {
					$shop = get_post($shop_page_id);
					$shop_slug = $shop->post_name;
					add_rewrite_rule('(([^/]+/)*' . $shop_slug . ')/([A-Fa-f0-9]{6})?/{0,1}$', 'index.php?post_type=product&tinvwlID=$matches[3]', 'top');
					self::$rules_raw['(([^/]+/)*' . $shop_slug . ')/([A-Fa-f0-9]{6})?/{0,1}$'] = 'index.php?post_type=product&tinvwlID=$matches[3]';
					add_rewrite_rule('(([^/]+/)*' . $shop_slug . ')/([A-Fa-f0-9]{6})?/wl_page/([0-9]{1,})/{0,1}$', 'index.php?post_type=product&tinvwlID=$matches[3]&wl_paged=$matches[4]', 'top');
					self::$rules_raw['(([^/]+/)*' . $shop_slug . ')/([A-Fa-f0-9]{6})?/wl_page/([0-9]{1,})/{0,1}$'] = 'index.php?post_type=product&tinvwlID=$matches[3]&wl_paged=$matches[4]';
					add_rewrite_rule('(([^/]+/)*' . $shop_slug . ')/([A-Fa-f0-9]{6})?/page/([0-9]{1,})/{0,1}$', 'index.php?post_type=product&tinvwlID=$matches[3]&paged=$matches[4]', 'top');
					self::$rules_raw['(([^/]+/)*' . $shop_slug . ')/([A-Fa-f0-9]{6})?/page/([0-9]{1,})/{0,1}$'] = 'index.php?post_type=product&tinvwlID=$matches[3]&paged=$matches[4]';
				}
			}
		}
	}

	/**
	 *  Disable the front page redirect.
	 *
	 * @param bool $redirect Allow redirect.
	 *
	 * @return bool
	 */
	public static function disable_canonical_redirect_for_front_page($redirect)
	{
		$page_on_front = absint(get_option('page_on_front'));
		if (is_page() && 'page' === get_option('show_on_front') && $page_on_front) {
			if (is_page($page_on_front)) {
				$redirect = false;
			}
		}

		return $redirect;
	}

	/**
	 * Add new POST variable
	 *
	 * @param array $public_var WordPress Public variable.
	 *
	 * @return array
	 */
	function add_query_var($public_var)
	{
		$public_var[] = 'tinvwlID';
		$public_var[] = 'tiws';
		$public_var[] = 'tiwp';
		$public_var[] = 'wl_paged';

		return $public_var;
	}

	/**
	 * Create ogp namespace
	 *
	 * @param string $text A space-separated list of language attributes.
	 *
	 * @return string
	 */
	function add_ogp($text)
	{
		global $wp_query;
		if (isset($wp_query) && is_page(apply_filters('wpml_object_id', tinv_get_option('page', 'wishlist'), 'page', true))) {
			if (!preg_match('/prefix\=/i', $text)) {
				$text .= ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# product: http://ogp.me/ns/product#"';
			}
			if (!preg_match('/itemscope/i', $text)) {
				$text .= ' itemscope';
			}
			if (!preg_match('/itemtype\=/i', $text)) {
				$text .= ' itemtype="http://schema.org/Offer"';
			}
		}

		return $text;
	}

	/**
	 * Check if is plugin page
	 *
	 * @return boolean
	 */
	function is_pluginpage()
	{
		$pages = tinv_get_option('page');
		$pages = array_filter($pages);
		foreach ($pages as $page) {
			if (is_page(apply_filters('wpml_object_id', $page, 'page', true))) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Load style and javascript
	 */
	function enqueue_header()
	{
		if ($this->is_pluginpage()) {
			$this->enqueue_wc_styles();
		}
		$this->enqueue_scripts();
		$this->enqueue_styles();
	}

	/**
	 * Load style
	 */
	function enqueue_styles()
	{
		if (apply_filters('tinvwl_load_webfont', true)) {
			wp_enqueue_style($this->_name . '-webfont-font', TINVWL_URL . 'assets/fonts/tinvwl-webfont.woff2', array(), 'xu2uyi');
			wp_enqueue_style($this->_name . '-webfont', TINVWL_URL . 'assets/css/webfont.min.css', array(), $this->_version, 'all');
			wp_style_add_data($this->_name . '-webfont', 'rtl', 'replace');
			wp_style_add_data($this->_name . '-webfont', 'suffix', '.min');
			add_filter('style_loader_tag', array($this, 'font_loader_tag_filter'), 100, 2);
		}
		wp_enqueue_style('tinvwl', TINVWL_URL . 'assets/css/public.min.css', array(), $this->_version, 'all');
		wp_style_add_data('tinvwl', 'rtl', 'replace');
		wp_style_add_data('tinvwl', 'suffix', '.min');

		if (!tinv_get_option('style', 'customstyle')) {
			wp_enqueue_style('tinvwl-theme', TINVWL_URL . 'assets/css/theme.min.css', array(), $this->_version, 'all');
			wp_style_add_data('tinvwl-theme', 'rtl', 'replace');
			wp_style_add_data('tinvwl-theme', 'suffix', '.min');
		}
		if (!tinv_get_option('style', 'customstyle') || (tinv_get_option('style_plain', 'allow') && tinv_get_option('style_plain', 'css'))) {
			$newcss = $this->dynaminc_css();
			if ($newcss) {
				$name_style = tinv_get_option('style', 'customstyle') ? 'tinvwl' : 'tinvwl-theme';
				wp_add_inline_style($name_style, $newcss);
			}
		}
	}

	/* Preload Icons font */
	function font_loader_tag_filter($html, $handle)
	{
		if ($handle === $this->_name . '-webfont-font') {
			$html = str_replace("type='text/css'", '', $html);
			return str_replace("rel='stylesheet'", "rel='preload' as='font' type='font/woff2' crossorigin='anonymous'", $html);
		}

		return $html;
	}

	/**
	 * Compress CSS
	 *
	 * @param string $css CSS Content.
	 *
	 * @return string
	 */
	function compress_css($css)
	{
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', ' ', $css);
		$css = preg_replace('/(\r|\n|\t| {2,})/', ' ', $css);

		return $css;
	}

	/**
	 * Generate dynaminc css
	 */
	function dynaminc_css()
	{
		$css = get_transient(TINVWL_PREFIX . '_dynamic_');
		if (!$css) {
			$css = '';
			if (!tinv_get_option('style', 'customstyle')) {
				$newcss = tinv_get_option('style_options', 'css');
				if ($newcss) {
					$newcss = $this->compress_css($newcss);
					$css .= $newcss;
				}
			}
			if (tinv_get_option('style_plain', 'allow')) {
				$newcss = tinv_get_option('style_plain', 'css');
				if ($newcss) {
					$newcss = $this->compress_css($newcss);
					$css .= $newcss;
				}
			}
			$image_url = TINVWL_URL . 'assets/img/';
			$css = str_replace('../img/', $image_url, $css);
			set_transient(TINVWL_PREFIX . '_dynamic_', $css, DAY_IN_SECONDS);
		}

		return $css;
	}

	/**
	 * Add woocommerce style
	 */
	function enqueue_wc_styles()
	{
		if ($enqueue_styles = WC_Frontend_Scripts::get_styles()) {
			foreach ($enqueue_styles as $handle => $args) {
				wp_register_style($handle, $args['src'], $args['deps'], $args['version'], $args['media']);
				wp_enqueue_style($handle);
			}
		}
	}

	/**
	 * Load javascript
	 */
	function enqueue_scripts()
	{
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script($this->_name . '-clipboard', TINVWL_URL . 'assets/js/clipboard.min.js', array('jquery'), $this->_version, true);
		wp_register_script($this->_name, TINVWL_URL . 'assets/js/public' . $suffix . '.js', array(
			'jquery',
			'js-cookie',
			apply_filters('tinvwl_wc_cart_fragments_enabled', true) ? 'wc-cart-fragments' : 'jquery',
		), $this->_version, true);

		$args = array(
			'text_create' => __('Create New', 'ti-woocommerce-wishlist'),
			'text_already_in' => apply_filters('tinvwl_already_in_wishlist_text', tinv_get_option('general', 'text_already_in')),
			'simple_flow' => tinv_get_option('general', 'simple_flow'),
			'hide_zero_counter' => tinv_get_option('topline', 'hide_zero_counter'),
			'i18n_make_a_selection_text' => esc_attr__('Please select some product options before adding this product to your wishlist.', 'ti-woocommerce-wishlist'),
			'tinvwl_break_submit' => esc_attr__('No items or actions are selected.', 'ti-woocommerce-wishlist'),
			'tinvwl_clipboard' => esc_attr__('Copied!', 'ti-woocommerce-wishlist'),
			'allow_parent_variable' => apply_filters('tinvwl_allow_add_parent_variable_product', false),
			'block_ajax_wishlists_data' => apply_filters('tinvwl_block_ajax_wishlists_data', false),
			'update_wishlists_data' => apply_filters('tinvwl_update_wishlists_data', false),
			'hash_key' => 'ti_wishlist_data_' . md5(get_current_blog_id() . '_' . get_site_url(get_current_blog_id(), '/') . get_template()),
			'nonce' => wp_create_nonce('wp_rest'),
			'rest_root' => esc_url_raw(get_rest_url()),
			'plugin_url' => esc_url_raw(TINVWL_URL),
		);

		if (function_exists('wpml_get_current_language')) {

			global $sitepress;

			if ($sitepress && $sitepress instanceof SitePress) {
				$wpml_settings = $sitepress->get_settings();
				if (isset($wpml_settings['custom_posts_sync_option']) && isset($wpml_settings['custom_posts_sync_option']['product']) && '1' == $wpml_settings['custom_posts_sync_option']['product']) {
					$args['wpml'] = wpml_get_current_language();
				}
			}
		}

		wp_localize_script($this->_name, 'tinvwl_add_to_wishlist', $args);

		if (wp_script_is('woocommerce', 'enqueued')) {
			wp_enqueue_script('tinvwl');
		}
	}

	/**
	 * Load function
	 */
	function load_function()
	{
		$this->define_hooks();
	}

	/**
	 * Transfer Cookie Wishlist when login user
	 *
	 * @param string $user_login Not used.
	 * @param object $user User object.
	 *
	 * @return boolean
	 */
	function transfert_local_to_user($user_login, $user)
	{
		return $this->transfert_local_to_user_register($user->ID);
	}

	function logout($user_id)
	{
		set_transient('_tinvwl_update_wishlists_data', '1');
	}

	/**
	 * Transfer Cookie Wishlist when register user
	 *
	 * @param integer $user_id New user id.
	 */
	function transfert_local_to_user_register($user_id)
	{

		set_transient('_tinvwl_update_wishlists_data', '1');

		$wl = new TInvWL_Wishlist($this->_name);
		$wishlist = $wl->get_by_sharekey_default();
		if (!empty($wishlist)) {
			$wishlist = array_shift($wishlist);
			if (empty($wishlist['author'])) {
				$wlpl = new TInvWL_Product($wishlist);
				$wl->user = $user_id;
				$_wishlist = $wl->get_by_user_default($user_id);
				if (empty($_wishlist)) {
					$wishlist['author'] = $user_id;
					unset($wishlist['title']);
					$wl->update($wishlist['ID'], $wishlist);
					$wlp = new TInvWL_Product($wishlist, $this->_name);
					$products = $wlp->get_wishlist(array('external' => false));
					foreach ($products as $product) {
						$product['author'] = $user_id;
						$wlp->update($product);
					}
				} else {
					$_wishlist = array_shift($_wishlist);
					if ($wishlist['ID'] != $_wishlist['ID']) {
						$wlp = new TInvWL_Product($_wishlist, $this->_name);
						$products = $wlpl->get_wishlist(array('external' => false));
						$added = true;
						foreach ($products as $product) {
							unset($product['author']);
							unset($product['wishlist_id']);
							$added = $added && $wlp->add_product($product);
						}
						if ($added) {
							$wlpl->remove_product_from_wl();
						}
					}
					$wl->set_sharekey($_wishlist['share_key']);
				}
			}
		}
	}

	/**
	 * Set the default wishlist key if the user loguot
	 */
	public function set_user_sharekey()
	{
		$wl = new TInvWL_Wishlist($this->_name);
		$wishlist = $wl->get_by_user_default();
		if (!empty($wishlist)) {
			$wishlist = array_shift($wishlist);
			$wl->set_sharekey($wishlist['share_key']);
		}
	}

	/**
	 * Add link to wishlist in WooCommerce My Account page.
	 *
	 * @param array $items Menu items links in my accounts.
	 *
	 * @return array
	 */
	function account_menu_items($items)
	{
		$index_position = apply_filters('tinvwl_myaccount_position_wishlist', -1, $items);
		$items = array_merge(
			array_slice($items, 0, $index_position, true),
			array(
				'tinv_wishlist' => __('Wishlist', 'ti-woocommerce-wishlist'),
			),
			array_slice($items, $index_position, null, true)
		);
		flush_rewrite_rules();

		return $items;
	}

	/**
	 * Create end point for wishlist url
	 *
	 * @param string $url URL from wishlist.
	 * @param string $endpoint End point name.
	 * @param string $value Not used.
	 * @param string $permalink Not used.
	 *
	 * @return string
	 */
	function account_menu_endpoint($url, $endpoint, $value, $permalink)
	{
		if ('tinv_wishlist' === $endpoint) {
			$url = tinv_url_wishlist_default();
		}

		return $url;
	}

	/**
	 * Remove Wishlist a user when the user is deleted
	 *
	 * @param integer $id Removed userid.
	 */
	function delete_user_wishlist($id)
	{
		$wl = new TInvWL_Wishlist($this->_name);
		$wishlists = $wl->get(array(
			'author' => $id,
			'count' => 9999999,
		));
		if (!empty($wishlists)) {
			foreach ($wishlists as $wishlist) {
				$wl->remove($wishlist['ID']);
			}
		}
	}

	/**
	 * Export cookies wishlist to database
	 */
	function legacy_transfer()
	{
		$wlpl = TInvWL_Product_Legacy::instance($this->_name);
		$products = $wlpl->get_wishlist(array('external' => false));
		if (!empty($products) && is_array($products)) {
			$wl = new TInvWL_Wishlist($this->_name);
			$wishlist = $wl->add_user_default();

			$wlp = new TInvWL_Product($wishlist, $this->_name);

			$added = true;
			foreach ($products as $product) {
				unset($product['author']);
				if (!$wlp->add_product($product)) {
					$added = false;
				}
			}
			if ($added) {
				$wlpl->remove_product_from_wl();
			}
		}
	}
}
