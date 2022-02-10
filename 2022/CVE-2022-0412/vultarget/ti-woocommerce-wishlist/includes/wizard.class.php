<?php
/**
 * Wizard installation plugin helper
 *
 * @since             1.0.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Wizard installation plugin helper
 */
class TInvWL_Wizard
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
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct($plugin_name, $version)
	{
		$this->_name = $plugin_name;
		$this->_version = $version;
		if (apply_filters('tinvwl_enable_wizard', true)) {
			$this->define_hooks();
		}
		update_option($this->_name . '_wizard', true);
	}

	/**
	 * Define hooks
	 */
	function define_hooks()
	{
		add_action('admin_menu', array($this, 'action_menu'));
		add_action('admin_init', array($this, 'wizard'));
	}

	/**
	 * Create admin page for wizard
	 */
	function action_menu()
	{
		add_dashboard_page('', '', 'manage_options', 'tinvwl-wizard', '');
	}

	/**
	 * Apply render wizard steps and save previous step
	 *
	 * @return void
	 */
	function wizard()
	{
		$page = filter_input(INPUT_GET, 'page');
		if ('tinvwl-wizard' !== $page) {
			return;
		}

		$this->page = filter_input(INPUT_GET, 'step', FILTER_VALIDATE_INT, array(
			'default' => 0,
			'min_range' => 0,
		));
		if (empty($this->page)) {
			$this->page = 'intro';
		}
		if (!method_exists($this, __FUNCTION__ . '_' . $this->page)) {
			$this->page = 'finish';
		}
		if (method_exists($this, __FUNCTION__ . '_' . $this->page)) {
			$this->method = __FUNCTION__ . '_' . $this->page;
		}

		// Run save form.
		$referer = wp_get_referer();
		if ($referer) {
			$url_attr = wp_parse_url($referer);
			if (array_key_exists('query', (array)$url_attr)) {
				parse_str($url_attr['query'], $url_attr);
			} else {
				$url_attr['step'] = 0;
			}
			$url_attr = filter_var_array($url_attr, array(
				'step' => array(
					'filter' => FILTER_VALIDATE_INT,
					'default' => 0,
					'min_range' => 0,
				),
			));
			if (empty($url_attr['step'])) {
				$url_attr['step'] = 'intro';
			}
			$method = __FUNCTION__ . '_' . $url_attr['step'] . '_save';
			if (!method_exists($this, $method)) {
				$method = __FUNCTION__ . '_finish_save';
			}
			if (method_exists($this, $method)) {
				$nonce = filter_input(0, '_wpnonce');
				if ($nonce && wp_verify_nonce($nonce, sprintf('%s-setup-%s', $this->_name, $url_attr['step']))) {
					$this->$method();
				}
			}
		}

		ob_start();
		$this->load_header();
		$this->load_content();
		$this->load_footer();
		exit;
	}

	/**
	 * Create index next page
	 *
	 * @return string
	 */
	private function next_page()
	{
		$index = $this->page;
		if ('finish' === $index) {
			return '';
		} elseif ('intro' === $index) {
			$index = 0;
		}
		$index++;

		return 'index.php?' . http_build_query(array(
				'page' => 'tinvwl-wizard',
				'step' => $index,
			));
	}

	/**
	 * Output header wizard page
	 */
	function load_header()
	{
		$this->enqueue_styles();
		$this->enqueue_scripts();
		$content = $title = '';
		$method = $this->method . '_title';
		if (method_exists($this, $method)) {
			$title = $this->$method();
		}
		$method = $this->method . '_header';
		if (method_exists($this, $method)) {
			ob_start();
			$this->$method();
			$content = ob_get_clean();
		}

		TInvWL_View::view('header', array(
			'title' => $title,
			'content' => $content,
			'page' => $this->page,
			'list_steps' => $this->get_list_steps(),
		), 'wizard');
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
		wp_enqueue_style($this->_name . '-setup', TINVWL_URL . 'assets/css/admin-setup' . $suffix . '.css', array(
			'dashicons',
		), $this->_version, 'all');
		wp_style_add_data($this->_name . '-setup', 'rtl', 'replace');
		wp_style_add_data($this->_name . '-setup', 'suffix', $suffix);
	}

	/**
	 * Load javascript
	 */
	function enqueue_scripts()
	{
		wp_enqueue_script($this->_name, TINVWL_URL . 'assets/js/admin.js', array('jquery'), $this->_version, 'all');
	}

	/**
	 * Output content wizard page
	 */
	function load_content()
	{
		?>
		<div class="<?php echo esc_attr(sprintf('%s-content', $this->_name)); ?>">
			<form method="POST" action="<?php echo esc_url(admin_url($this->next_page())) ?>">
				<?php
				$method = $this->method;
				if (method_exists($this, $method)) {
					$this->$method();
				}
				wp_nonce_field(sprintf('%s-setup-%s', $this->_name, $this->page));
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get titles steps
	 *
	 * @return array
	 */
	function get_list_steps()
	{
		$lists = get_class_methods($this);
		foreach ($lists as $key => $value) {
			if (!preg_match('/^wizard_[^_]+_title$/i', $value)) {
				unset($lists[$key]);
			}
		}
		sort($lists);

		$steps = array();
		foreach ($lists as $method) {
			$key = preg_replace('/(^wizard_|_title$)/i', '', $method);
			if ('intro' == $key) { // WPCS: loose comparison ok.
				$key = 0;
			} elseif ('finish' == $key) { // WPCS: loose comparison ok.
				$key = count($lists) - 1;
			}
			$steps[$key] = $this->$method();
		}
		ksort($steps);

		return $steps;
	}

	/**
	 * Output footer wizard page
	 */
	function load_footer()
	{
		$content = '';
		$method = $this->method . '_footer';
		if (method_exists($this, $method)) {
			ob_start();
			$this->$method();
			$content = ob_get_clean();
		}

		TInvWL_View::view('footer', array(
			'content' => $content,
			'page' => $this->page,
		), 'wizard');
	}

	/**
	 * Title intro
	 *
	 * @return string
	 */
	function wizard_intro_title()
	{
		return __('Introduction', 'ti-woocommerce-wishlist');
	}

	/**
	 * Content intro
	 */
	function wizard_intro()
	{
		TInvWL_View::view('intro', array(), 'wizard');
	}

	/**
	 * Title step 1
	 *
	 * @return string
	 */
	function wizard_1_title()
	{
		return __('Page Setup', 'ti-woocommerce-wishlist');
	}

	/**
	 * Content step 1
	 */
	function wizard_1()
	{
		$title_pages = array(
			'wishlist' => __('Wishlist', 'ti-woocommerce-wishlist'),
		);
		$lists = get_pages(array('number' => 9999999)); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.get_pages
		$page_list = array(
			'' => __('Create Automatically', 'ti-woocommerce-wishlist'),
			-100 => __('Create new Page', 'ti-woocommerce-wishlist'),
		);
		$page_name = array();
		foreach ($lists as $list) {
			$page_list[$list->ID] = $list->post_title;
			$page_name[$list->post_name] = $list->ID;
		}
		$data = array(
			'general_default_title_value' => apply_filters('tinvwl_default_wishlist_title', tinv_get_option('general', 'default_title')),
		);
		foreach ($title_pages as $key => $text) {
			$_data['options'] = $page_list;
			$_data['new_value'] = $text;
			$_data['value'] = tinv_get_option('page', $key) ? tinv_get_option('page', $key) : '';
			$_data['error'] = array_key_exists(('wishlist' === $key ? $key : 'wishlist-' . $key), $page_name);

			$data['page_pages'][$key] = $_data;
		}
		TInvWL_View::view('step-page', $data, 'wizard');
	}

	/**
	 * Save content step 1
	 */
	function wizard_1_save()
	{
		$title_pages = array(
			'wishlist' => __('Wishlist', 'ti-woocommerce-wishlist'),
		);
		$shortcode_pages = array(
			'wishlist' => '[ti_wishlistsview]',
		);
		$data = array(
			'general_default_title' => FILTER_SANITIZE_STRING,
		);
		foreach (array_keys($title_pages) as $key) {
			$data['page_' . $key] = FILTER_VALIDATE_INT;
			$data['page_' . $key . '_new'] = FILTER_SANITIZE_STRING;
			$data['page_' . $key . '_auto'] = FILTER_VALIDATE_BOOLEAN;
		}
		$data = filter_input_array(INPUT_POST, $data);
		if (!empty($data['general_default_title'])) {
			tinv_update_option('general', 'default_title', $data['general_default_title']);
		}

		$required = array('wishlist');
		$required_notsets = array();
		foreach ($title_pages as $key => $title) {
			$shortcode = $shortcode_pages[$key];
			$auto = $data[sprintf('page_%s_auto', $key)];
			$title_new = $data[sprintf('page_%s_new', $key)];
			$page = $data[sprintf('page_%s', $key)];

			$the_page_id = 0;
			if ((empty($page) && $auto) || (is_integer($page) && -100 === $page)) {
				if (-100 === $page) {
					$title = empty($title_new) ? $title : $title_new;
				}
				$title = apply_filters('tinvwl_create_new_page_post_title', $title, $key);

				$_page = array(
					'post_title' => $title,
					'post_content' => $shortcode,
					'post_status' => '',
					'post_name' => 'wishlist' === $key ? $key : 'wishlist-' . $key,
					'post_type' => 'page',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_category' => array(1),
				);
				if (-100 === $page) {
					unset($_page['post_name']);
				}
				$the_page_id = wp_insert_post($_page);
			} elseif (is_integer($page) && 0 < $page) {
				$the_page_id = $page;
			}

			if (0 < $the_page_id) {
				$the_page = get_post($the_page_id);
				$the_page->post_content = (strpos($the_page->post_content, $shortcode) !== false) ? $the_page->post_content : $shortcode . $the_page->post_content;
				$the_page->post_status = 'publish';
				$the_page_id = wp_update_post($the_page);
				tinv_update_option('page', $key, $the_page_id);
			} else {
				tinv_update_option('page', $key, '');
				if (in_array($key, $required)) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
					$required_notsets[] = $key;
				}
			}
		} // End foreach().
		if (!empty($required_notsets)) {
			wp_safe_redirect(wp_get_referer());
			exit;
		} else {
			TInvWL_Public_TInvWL::update_rewrite_rules();
		}
	}

	/**
	 * Title step 2
	 *
	 * @return string
	 */
	function wizard_2_title()
	{
		return __('Button', 'ti-woocommerce-wishlist');
	}

	/**
	 * Content step 2
	 */
	function wizard_2()
	{

		$data = array(
			'add_to_wishlist_position_value' => tinv_get_option('add_to_wishlist', 'position'),
			'add_to_wishlist_position_options' => array(
				'after' => __('After "Add to Cart" button', 'ti-woocommerce-wishlist'),
				'before' => __('Before "Add to Cart" button', 'ti-woocommerce-wishlist'),
				'shortcode' => __('Custom position with code', 'ti-woocommerce-wishlist'),
			),
			'add_to_wishlist_text_value' => tinv_get_option('add_to_wishlist', 'text'),
			'add_to_wishlist_catalog_show_in_loop_value' => tinv_get_option('add_to_wishlist_catalog', 'show_in_loop'),
			'add_to_wishlist_catalog_text_value' => tinv_get_option('add_to_wishlist_catalog', 'text'),
		);
		TInvWL_View::view('step-button', $data, 'wizard');
	}

	/**
	 * Save content step 2
	 */
	function wizard_2_save()
	{
		$data = filter_input_array(INPUT_POST, array(
			'add_to_wishlist_position' => FILTER_SANITIZE_STRING,
			'add_to_wishlist_text' => FILTER_SANITIZE_STRING,
			'add_to_wishlist_catalog_show_in_loop' => FILTER_VALIDATE_BOOLEAN,
			'add_to_wishlist_catalog_text' => FILTER_SANITIZE_STRING,
		));
		tinv_update_option('add_to_wishlist', 'position', $data['add_to_wishlist_position']);
		tinv_update_option('add_to_wishlist', 'text', $data['add_to_wishlist_text']);
		tinv_update_option('add_to_wishlist_catalog', 'show_in_loop', (bool)$data['add_to_wishlist_catalog_show_in_loop']);
		tinv_update_option('add_to_wishlist_catalog', 'text', $data['add_to_wishlist_catalog_text']);
	}

	/**
	 * Title step 3
	 *
	 * @return string
	 */
	function wizard_3_title()
	{
		return __('Processing', 'ti-woocommerce-wishlist');
	}

	/**
	 * Content step 3
	 */
	function wizard_3()
	{
		$data = array(
			'processing_autoremove_value' => tinv_get_option('processing', 'autoremove') ? 'auto' : 'manual',
			'processing_autoremove_options' => array(
				'auto' => __('Automatically', 'ti-woocommerce-wishlist'),
				'manual' => __('Manual', 'ti-woocommerce-wishlist'),
			),
		);
		TInvWL_View::view('step-processing', $data, 'wizard');
	}

	/**
	 * Save content step 3
	 */
	function wizard_3_save()
	{
		$data = filter_input_array(INPUT_POST, array(
			'processing_autoremove' => FILTER_SANITIZE_STRING,
		));
		$autoremove = 'auto' === $data['processing_autoremove'];
		tinv_update_option('processing', 'autoremove', $autoremove);
		tinv_update_option('processing', 'autoremove_status', 'tinvwl-addcart');
	}

	/**
	 * Title step 4
	 *
	 * @return string
	 */
	function wizard_4_title()
	{
		return __('Share', 'ti-woocommerce-wishlist');
	}

	/**
	 * Content step 4
	 */
	function wizard_4()
	{
		$data = array(
			'social_facebook_value' => tinv_get_option('social', 'facebook'),
			'social_twitter_value' => tinv_get_option('social', 'twitter'),
			'social_pinterest_value' => tinv_get_option('social', 'pinterest'),
			'social_whatsapp_value' => tinv_get_option('social', 'whatsapp'),
			'social_clipboard_value' => tinv_get_option('social', 'clipboard'),
			'social_email_value' => tinv_get_option('social', 'email'),
		);
		TInvWL_View::view('step-social', $data, 'wizard');
	}

	/**
	 * Save content step 4
	 */
	function wizard_4_save()
	{
		$data = filter_input_array(INPUT_POST, array(
			'social_facebook' => FILTER_VALIDATE_BOOLEAN,
			'social_twitter' => FILTER_VALIDATE_BOOLEAN,
			'social_pinterest' => FILTER_VALIDATE_BOOLEAN,
			'social_whatsapp' => FILTER_VALIDATE_BOOLEAN,
			'social_clipboard' => FILTER_VALIDATE_BOOLEAN,
			'social_email' => FILTER_VALIDATE_BOOLEAN,
		));
		tinv_update_option('social', 'facebook', (bool)$data['social_facebook']);
		tinv_update_option('social', 'twitter', (bool)$data['social_twitter']);
		tinv_update_option('social', 'pinterest', (bool)$data['social_pinterest']);
		tinv_update_option('social', 'whatsapp', (bool)$data['social_whatsapp']);
		tinv_update_option('social', 'clipboard', (bool)$data['social_clipboard']);
		tinv_update_option('social', 'email', (bool)$data['social_email']);
	}

	/**
	 * Title step 5
	 *
	 * @return string
	 */
	function wizard_5_title()
	{
		return __('Support', 'ti-woocommerce-wishlist');
	}

	/**
	 * Content step 5
	 */
	function wizard_5()
	{
		$data = array(
			'chat_enabled' => 'on',
		);
		TInvWL_View::view('step-support', $data, 'wizard');
	}

	/**
	 * Save content step 5
	 */
	function wizard_5_save()
	{
		$data = filter_input_array(INPUT_POST, array(
			'chat_enabled' => FILTER_VALIDATE_BOOLEAN,
		));
		tinv_update_option('chat', 'enabled', (bool)$data['chat_enabled']);
	}

	/**
	 * Title finish
	 *
	 * @return string
	 */
	function wizard_finish_title()
	{
		return __('Ready!', 'ti-woocommerce-wishlist');
	}

	/**
	 * Content finish
	 */
	function wizard_finish()
	{
		TInvWL_View::view('finish', array(), 'wizard');
	}
}
