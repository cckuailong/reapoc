<?php
/**
 * Action buttons for Wishlist
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Action buttons for Wishlist
 */
class TInvWL_Public_Wishlist_Buttons
{

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	static $_name;
	/**
	 * Basic event
	 *
	 * @var string
	 */
	static $event;

	/**
	 * First run function
	 *
	 * @param string $plugin_name Plugin name.
	 */
	public static function init($plugin_name = TINVWL_PREFIX)
	{
		self::$_name = $plugin_name;
		self::$event = 'tinvwl_after_wishlist_table';
		self::htmloutput();
	}

	/**
	 * Defined buttons
	 *
	 * @return array
	 */
	private static function prepare()
	{

		// WP Multilang string translations.
		if (function_exists('wpm_translate_string')) {
			add_filter('tinvwl_add_selected_to_cart_text', 'wpm_translate_string');
			add_filter('tinvwl_add_all_to_cart_text', 'wpm_translate_string');
		}

		$buttons = array();
		if (tinv_get_option('table', 'colm_checkbox') && tinv_get_option('table', 'colm_actions')) {
			$buttons[] = array(
				'name' => 'product_apply',
				'title' => sprintf(__('Apply %s', 'ti-woocommerce-wishlist'), "<span class='tinvwl-mobile'>" . __('Action', 'ti-woocommerce-wishlist') . '</span>'),
				'method' => array(__CLASS__, 'apply_action'),
				'before' => array(__CLASS__, 'apply_action_before'),
				'priority' => 10,
				'condition' => '$a["is_owner"]',
			);
			add_filter('tinvwl_prepare_attr__button_product_apply', array(
				__CLASS__,
				'add_break_class_input',
			));
			add_filter('tinvwl_prepare_attr__button_product_apply', array(
				__CLASS__,
				'add_break_class_checkbox',
			));
		}
		if (tinv_get_option('table', 'colm_checkbox') && tinv_get_option('table', 'add_select_to_cart')) {
			$buttons[] = array(
				'name' => 'product_selected',
				'title' => apply_filters('tinvwl_add_selected_to_cart_text', tinv_get_option('table', 'text_add_select_to_cart')),
				'method' => array(__CLASS__, 'apply_action_add_selected'),
				'priority' => 25,
			);
			add_filter('tinvwl_prepare_attr_button_product_selected', array(
				__CLASS__,
				'add_break_class_checkbox',
			));
		}
		if (tinv_get_option('table', 'add_all_to_cart')) {
			$buttons[] = array(
				'name' => 'product_all',
				'title' => apply_filters('tinvwl_add_all_to_cart_text', tinv_get_option('table', 'text_add_all_to_cart')),
				'method' => array(__CLASS__, 'add_all'),
				'priority' => 30,
			);
			add_filter('tinvwl_prepare_attr_button_product_selected', array(__CLASS__, 'class_action'));
			add_filter('tinvwl_prepare_attr_button_product_all', array(__CLASS__, 'class_action'));
		}
		$buttons = apply_filters('tinvwl_manage_buttons_create', $buttons);

		return $buttons;
	}

	/**
	 * Output buttons
	 */
	public static function htmloutput()
	{
		$buttons = self::prepare();
		foreach ($buttons as $button) {
			self::addbutton($button);
		}
	}

	/**
	 * Add break class
	 *
	 * @param array $attr Attributes.
	 *
	 * @return array
	 */
	public static function add_break_class_input($attr)
	{
		if (array_key_exists('class', $attr)) {
			$attr['class'] .= ' tinvwl-break-input';
		} else {
			$attr['class'] = 'tinvwl-break-input';
		}

		return $attr;
	}

	/**
	 * Add break class
	 *
	 * @param array $attr Attributes.
	 *
	 * @return array
	 */
	public static function add_break_class_checkbox($attr)
	{
		if (array_key_exists('class', $attr)) {
			$attr['class'] .= ' tinvwl-break-checkbox';
		} else {
			$attr['class'] = 'tinvwl-break-checkbox';
		}

		return $attr;
	}

	/**
	 * Create button and action
	 *
	 * @param array $button Structure for button.
	 *
	 * @return boolean
	 */
	public static function addbutton($button)
	{
		if (!array_key_exists('name', $button)) {
			return false;
		}
		if (!array_key_exists('priority', $button)) {
			$button['priority'] = 10;
		}
		if (!array_key_exists('method', $button)) {
			$button['method'] = array(__CLASS__, 'null_action');
		}
		if (!array_key_exists('event', $button)) {
			$button['event'] = self::$event;
		}
		if (!array_key_exists('condition', $button)) {
			$button['condition'] = 'true';
		}
		if (array_key_exists('submit', $button)) {
			$button['submit'] = $button['submit'] ? 'submit' : 'button';
		} else {
			$button['submit'] = 'submit';
		}

		if (array_key_exists('before', $button)) {
			add_filter('tinvwl_before__button_' . $button['name'], $button['before']);
		}
		if (array_key_exists('after', $button)) {
			add_filter('tinvwl_after__button_' . $button['name'], $button['after']);
		}

		add_action($button['event'], function () use ($button) {
			if ($button['condition']) {
				self::button($button['name'], __($button['title']), $button['submit']);
			}
		}, $button['priority']);
		add_action('tinvwl_action_' . $button['name'], $button['method'], 10, 4);
	}

	/**
	 * Create html button
	 *
	 * @param string $value Vaule for tinvwl-action.
	 * @param string $title HTML title for button.
	 * @param string $submit Type button.
	 * @param boolean $echo Retun or echo.
	 *
	 * @return string
	 */
	public static function button($value, $title, $submit, $echo = true)
	{
		$html = apply_filters('tinvwl_before__button_' . $value, '');
		$attr = array(
			'type' => $submit,
			'class' => 'button',
			'name' => 'tinvwl-action',
			'value' => $value,
			'title' => esc_attr(wp_strip_all_tags($title)),
		);
		$attr = apply_filters('tinvwl_prepare_attr__button_' . $value, $attr);
		foreach ($attr as $key => &$value) {
			$value = sprintf('%s="%s"', $key, esc_attr($value));
		}
		$attr = implode(' ', $attr);

		$html .= apply_filters('tinvwl_button_' . $value, sprintf('<button %s>%s</button>', $attr, $title));
		$html .= apply_filters('tinvwl_after_button_' . $value, '');

		if ($echo) {
			echo $html; // WPCS: xss ok.
		} else {
			return $html;
		}
	}

	/**
	 * Default action for button
	 *
	 * @return boolean
	 */
	public static function null_action()
	{
		return false;
	}

	/**
	 * Add class 'alt' to button
	 *
	 * @param array $attr Attributes for button.
	 *
	 * @return array
	 */
	public static function class_action($attr)
	{
		if (array_key_exists('class', $attr)) {
			$attr['class'] .= ' alt';
		} else {
			$attr['class'] = 'alt';
		}

		return $attr;
	}

	/**
	 * Get all products fix offset issue when paged argument exists.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function get_all_products_fix_offset($data)
	{
		$data['offset'] = 0;

		return $data;
	}

	/**
	 * Apply action for product_all
	 *
	 * @param array $wishlist Wishlist object.
	 * @param array $selected Not used.
	 * @param array $_quantity Not used.
	 * @param boolean $owner Owner this wishlist.
	 *
	 * @return boolean
	 */
	public static function add_all($wishlist, $selected = array(), $_quantity = array(), $owner = false)
	{
		add_filter('tinvwl_before_get_current_product', array(__CLASS__, 'get_all_products_fix_offset'));
		$products = self::get_current_products($wishlist, 9999999);
		$result = $errors = array();
		foreach ($products as $_product) {
			$product_data = wc_get_product($_product['variation_id'] ? $_product['variation_id'] : $_product['product_id']);

			if (!$product_data || 'trash' === $product_data->get_status()) {
				continue;
			}

			global $product;
			// store global product data.
			$_product_tmp = $product;
			// override global product data.
			$product = $product_data;

			add_filter('clean_url', 'tinvwl_clean_url', 10, 2);
			$redirect_url = $product_data->add_to_cart_url();
			remove_filter('clean_url', 'tinvwl_clean_url', 10);

			// restore global product data.
			$product = $_product_tmp;

			$quantity = apply_filters('tinvwl_product_add_to_cart_quantity', array_key_exists($_product['ID'], (array)$_quantity) ? $_quantity[$_product['ID']] : 1, $product_data);
			$_product['quantity'] = $quantity;
			if (apply_filters('tinvwl_product_add_to_cart_need_redirect', false, $product_data, $redirect_url, $_product)) {
				$errors[] = $_product['product_id'];
				continue;
			}

			$_product = $_product['ID'];

			$add = TInvWL_Public_Cart::add($wishlist, $_product, $quantity);

			if ($add) {
				$result = tinv_array_merge($result, $add);
			} else {
				$errors[] = $product_data->get_id();
			}
		}

		if (!empty($errors)) {
			$titles = array();
			foreach ($errors as $product_id) {
				$titles[] = sprintf(_x('&ldquo;%s&rdquo;', 'Item name in quotes', 'ti-woocommerce-wishlist'), strip_tags(get_the_title($product_id)));
			}
			$titles = array_filter($titles);
			wc_add_notice(sprintf(_n('Product %s could not be added to cart because some requirements are not met.', 'Products: %s could not be added to cart because some requirements are not met.', count($titles), 'ti-woocommerce-wishlist'), wc_format_list_of_items($titles)), 'error');
		}
		if (!empty($result)) {
			wc_add_to_cart_message($result, true);

			if (tinv_get_option('processing', 'redirect_checkout')) {
				wp_safe_redirect(wc_get_checkout_url());
				exit;
			}

			if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
				wp_safe_redirect(wc_get_cart_url());
				exit;
			}

			return true;
		}

		return false;
	}

	/**
	 * Create select for custom action
	 *
	 * @return string
	 */
	public static function apply_action_before()
	{
		$options = array(
			'' => __('Actions', 'ti-woocommerce-wishlist'),
		);

		if (tinv_get_option('table', 'add_select_to_cart')) {
			$options['add_selected'] = apply_filters('tinvwl_add_to_cart_text', tinv_get_option('product_table', 'text_add_to_cart'));
		}

		$wishlist_curent = TInvWL_Public_Wishlist_View::instance()->get_current_wishlist();
		if ($wishlist_curent['is_owner']) {
			$options['remove'] = __('Remove', 'ti-woocommerce-wishlist');
		}

		return TInvWL_Form::_select('product_actions', '', array('class' => 'tinvwl-break-input-filed'), $options);
	}

	/**
	 * Apply action for product_actions
	 *
	 * @param array $wishlist Wishlist object.
	 * @param array $selected Array selected products.
	 * @param array $_quantity Array quantity products.
	 * @param boolean $owner Owner this wishlist.
	 *
	 * @return boolean
	 */
	public static function apply_action($wishlist, $selected = array(), $_quantity = array(), $owner = false)
	{
		if (empty($selected) || !is_array($selected)) {
			return false;
		}
		$action = filter_input(INPUT_POST, 'product_actions', FILTER_SANITIZE_STRING);
		switch ($action) {
			case 'add_selected':
				self::apply_action_add_selected($wishlist, $selected, $_quantity, $owner);
				break;
			case 'remove':
				self::apply_action_remove($wishlist, $selected, $_quantity, $owner);
				break;
		}
	}

	/**
	 * Apply action for product_actions add_selected
	 *
	 * @param array $wishlist Wishlist object.
	 * @param array $selected Array selected products.
	 * @param array $_quantity Array quantity products.
	 * @param boolean $owner Owner this wishlist.
	 *
	 * @return boolean
	 */
	public static function apply_action_add_selected($wishlist, $selected = array(), $_quantity = array(), $owner = false)
	{
		if (!empty($selected)) {
			$result = $errors = array();
			foreach ($selected as $id) {
				$wlp = null;
				if (0 === $wishlist['ID']) {
					$wlp = TInvWL_Product_Local::instance();
				} else {
					$wlp = new TInvWL_Product($wishlist);
				}
				$_product = $wlp->get_wishlist(array('ID' => $id));
				$_product = array_shift($_product);

				$product_data = wc_get_product($_product['variation_id'] ? $_product['variation_id'] : $_product['product_id']);

				if (!$product_data || 'trash' === $product_data->get_status()) {
					continue;
				}

				global $product;
				// store global product data.
				$_product_tmp = $product;
				// override global product data.
				$product = $product_data;

				add_filter('clean_url', 'tinvwl_clean_url', 10, 2);
				$redirect_url = $product_data->add_to_cart_url();
				remove_filter('clean_url', 'tinvwl_clean_url', 10);

				// restore global product data.
				$product = $_product_tmp;

				$quantity = apply_filters('tinvwl_product_add_to_cart_quantity', array_key_exists($id, (array)$_quantity) ? $_quantity[$id] : 1, $product_data);
				$_product['quantity'] = $quantity;

				if (apply_filters('tinvwl_product_add_to_cart_need_redirect', false, $product_data, $redirect_url, $_product)) {
					$errors[] = $_product['product_id'];
					continue;
				}

				$add = TInvWL_Public_Cart::add($wishlist, $id, $quantity);
				if ($add) {
					$result = tinv_array_merge($result, $add);
				} else {
					$errors[] = $_product['variation_id'] ? $_product['variation_id'] : $_product['product_id'];
				}
			}
			if (!empty($errors)) {
				$titles = array();
				foreach ($errors as $product_id) {
					$titles[] = sprintf(_x('&ldquo;%s&rdquo;', 'Item name in quotes', 'ti-woocommerce-wishlist'), strip_tags(get_the_title($product_id)));
				}
				$titles = array_filter($titles);
				wc_add_notice(sprintf(_n('Product %s could not be added to cart because some requirements are not met.', 'Products: %s could not be added to cart because some requirements are not met.', count($titles), 'ti-woocommerce-wishlist'), wc_format_list_of_items($titles)), 'error');
			}
			if (!empty($result)) {
				wc_add_to_cart_message($result, true);

				if (tinv_get_option('processing', 'redirect_checkout')) {
					wp_safe_redirect(wc_get_checkout_url());
					exit;
				}

				if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
					wp_safe_redirect(wc_get_cart_url());
					exit;
				}

				return true;
			}
		} // End if().

		return false;
	}

	/**
	 * Apply action for product_actions remove
	 *
	 * @param array $wishlist Wishlist object.
	 * @param array $selected Array selected products.
	 * @param array $_quantity Not used.
	 * @param boolean $owner Owner this wishlist.
	 *
	 * @return boolean
	 */
	public static function apply_action_remove($wishlist, $selected = array(), $_quantity = array(), $owner = false)
	{
		if (!$owner) {
			return false;
		}
		$wlp = null;
		if (0 === $wishlist['ID']) {
			$wlp = TInvWL_Product_Local::instance(self::$_name);
		} else {
			$wlp = new TInvWL_Product($wishlist, self::$_name);
		}
		if (empty($wlp)) {
			return false;
		}

		$products = $wlp->get_wishlist(array(
			'ID' => $selected,
			'count' => 100,
		));

		$titles = array();
		foreach ($products as $product) {
			if ($wlp->remove_product_from_wl($product['wishlist_id'], $product['product_id'], $product['variation_id'], $product['meta'])) {
				$titles[] = sprintf(__('&ldquo;%s&rdquo;', 'ti-woocommerce-wishlist'), is_callable(array(
					$product['data'],
					'get_name'
				)) ? $product['data']->get_name() : $product['data']->get_title());
			}
		}

		if (!empty($titles)) {
			add_action('tinvwl_before_wishlist', array(
				'TInvWL_Public_Wishlist_View',
				'check_cart_hash',
			), 99, 1);
			add_action('woocommerce_set_cart_cookies', array(
				'TInvWL_Public_Wishlist_View',
				'reset_cart_hash',
			), 199, 1);
			wc_add_notice(sprintf(_n('%s has been successfully removed from wishlist.', '%s have been successfully removed from wishlist.', count($titles), 'ti-woocommerce-wishlist'), wc_format_list_of_items($titles)));
		}

		return true;
	}

	/**
	 * Get product bu wishlist
	 *
	 * @param array $wishlist Wishlist object.
	 *
	 * @return array
	 */
	public static function get_current_products($wishlist = null, $per_page = null)
	{
		if (empty($wishlist)) {
			return array();
		}
		$wlp = null;
		if (0 === $wishlist['ID']) {
			$wlp = TInvWL_Product_Local::instance();
		} else {
			$wlp = new TInvWL_Product($wishlist);
		}
		if (empty($wlp)) {
			return array();
		}

		$paged = absint(get_query_var('wl_paged', 1));
		$paged = 1 < $paged ? $paged : 1;

		if (!$per_page) {
			$per_page = apply_filters('tinvwl_wishlist_products_per_page', filter_input(INPUT_POST, 'lists_per_page', FILTER_VALIDATE_INT, array(
				'options' => array(
					'default' => 10,
					'min_range' => 1,
				),
			)));

		}

		$product_data = array(
			'count' => $per_page,
			'offset' => $per_page * ($paged - 1),
			'external' => false,
		);

		$product_data = apply_filters('tinvwl_before_get_current_product', $product_data);
		$products = $wlp->get_wishlist($product_data);
		$products = apply_filters('tinvwl_after_get_current_product', $products);

		return $products;
	}
}
