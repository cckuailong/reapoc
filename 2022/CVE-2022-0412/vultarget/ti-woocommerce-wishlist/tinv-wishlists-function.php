<?php
/**
 * Basic function for plugin
 *
 * @since             1.0.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}


if (!function_exists('tinv_get_option')) {

	/**
	 * Extract options from database or default array settings.
	 *
	 * @param string $category Name category settings.
	 * @param string $option Name paremetr. If is empty string, then function return array category settings.
	 *
	 * @return mixed
	 */
	function tinv_get_option($category, $option = '')
	{
		$prefix = TINVWL_PREFIX . '-';
		$values = get_option($prefix . $category, array());
		if (empty($values)) {
			$values = tinv_get_option_defaults($category);
		}
		if (empty($option)) {
			return $values;
		} else {
			if (array_key_exists($option, (array)$values)) {
				return $values[$option];
			} else {
				$values = tinv_get_option_defaults($category);
				if (array_key_exists($option, (array)$values)) {
					return $values[$option];
				}
			}
		}

		return null;
	}
}

if (!function_exists('tinv_get_option_admin')) {

	/**
	 * Extract options from database or default array settings.
	 *
	 * @param string $category Name category settings.
	 * @param string $option Name paremetr. If is empty string, then function return array category settings.
	 *
	 * @return mixed
	 */
	function tinv_get_option_admin($category, $option = '')
	{
		$prefix = TINVWL_PREFIX . '-';
		$values = get_option($prefix . $category, array());
		if (empty($values)) {
			$values = array();
		}
		if (empty($option)) {
			return $values;
		} elseif (array_key_exists($option, $values)) {
			return $values[$option];
		}

		return null;
	}
}

if (!function_exists('tinv_style')) {

	/**
	 * Get style for custom style
	 *
	 * @param string $selector Selector style.
	 * @param string $element Attribute name.
	 *
	 * @return string
	 */
	function tinv_style($selector = '', $element = '')
	{
		$key = md5($selector . '||' . $element);
		$values = get_option(TINVWL_PREFIX . '-style_options', array());
		if (empty($values)) {
			return '';
		}
		if (array_key_exists($key, $values)) {
			return $values[$key];
		}

		return '';
	}
}

if (!function_exists('tinv_update_option')) {

	/**
	 * Update options in database.
	 *
	 * @param string $category Name category settings.
	 * @param string $option Name paremetr. If is empty string, then function update array category settings.
	 * @param mixed $value Value option.
	 *
	 * @return boolean
	 */
	function tinv_update_option($category, $option = '', $value = false)
	{
		$prefix = TINVWL_PREFIX . '-';
		if (empty($option)) {
			if (is_array($value)) {
				update_option($prefix . $category, $value);

				return true;
			}
		} else {
			$values = get_option($prefix . $category, array());

			$values[$option] = $value;
			update_option($prefix . $category, $values);

			return true;
		}

		return false;
	}
}

/**
 * Rename class
 */
class TInvWLRename
{

	/**
	 * Rename "wishlist" word across the plugin.
	 */
	function rename()
	{
		$this->rename = tinv_get_option('rename', 'rename');
		$this->rename_single = tinv_get_option('rename', 'rename_single');
		$this->rename_plural = tinv_get_option('rename', 'rename_plural');

		if ($this->rename && $this->rename_single) {
			add_filter('gettext', array($this, 'translations'), 999, 3);
			add_filter('ngettext', array($this, 'translations_n'), 999, 5);
		}
	}


	function translations_n($translation, $single, $plural, $number, $domain)
	{
		return $this->translation_update($translation, $domain);
	}

	function translations($translation, $text, $domain)
	{
		return $this->translation_update($translation, $domain);
	}

	private function translation_update($text, $domain)
	{
		if ('ti-woocommerce-wishlist' === $domain) {

			$translations = ['wishlist' => [$this->rename_single, $this->rename_plural ? $this->rename_plural : $this->rename_single . 's']];

			$text = preg_replace_callback('~\b[a-z]+(?:(?<=(s)))?~i', function ($m) use ($translations) {
				$lower = strtolower($m[0]);
				$rep = $m[0];
				if (isset($translations[$lower])) {
					$rep = is_array($translations[$lower]) ? $translations[$lower][0] : $translations[$lower];
				} elseif (isset($m[1])) {
					$sing = substr($lower, 0, -1);
					if (isset($translations[$sing]))
						$rep = is_array($translations[$sing]) ? $translations[$sing][1] : $translations[$sing] . 's';
				} else {
					return $rep;
				}

				if ($m[0] == $lower)
					return $rep;
				elseif ($m[0] == strtoupper($lower))
					return strtoupper($rep);
				elseif ($m[0] == ucfirst($lower))
					return ucfirst($rep);

				return $rep;
			}, $text);

		}
		return $text;
	}
}

$tinvwl_rename = new TInvWLRename();
$tinvwl_rename->rename();

if (!function_exists('tinv_wishlist_template')) {

	/**
	 * The function overwrites the method output templates woocommerce
	 *
	 * @param string $template_name Name file template.
	 * @param array $args Array variable in template.
	 * @param string $template_path Customization path.
	 */
	function tinv_wishlist_template($template_name, $args = array(), $template_path = '')
	{
		if (function_exists('wc_get_template')) {
			wc_get_template($template_name, $args, $template_path);
		} else {
			woocommerce_get_template($template_name, $args, $template_path);
		}
	}
}

if (!function_exists('tinv_wishlist_locate_template')) {

	/**
	 * Overwrites path for email and other template
	 *
	 * @param string $template_name Requered Template file.
	 * @param string $template_path Template path.
	 * @param string $default_path Template default path.
	 *
	 * @return mixed
	 */
	function tinv_wishlist_locate_template($template_name, $template_path = '', $default_path = '')
	{
		$prefix = 'ti-';

		if (substr(basename($template_name), 0, strlen($prefix)) !== $prefix) {
			return;
		}

		if (!$template_path) {
			$template_path = WC()->template_path();
		}

		if (!$default_path) {
			$default_path = TINVWL_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(array(
			trailingslashit($template_path) . $template_name,
			$template_name,
		));

		// Get default template.
		if (!$template && file_exists($default_path . $template_name)) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters('tinvwl_locate_template', $template, $template_name, $template_path);
	}
} // End if().

if (!function_exists('tinv_wishlist_template_html')) {

	/**
	 * The function overwrites the method return templates woocommerce
	 *
	 * @param string $template_name Name file template.
	 * @param array $args Array variable in template.
	 * @param string $template_path Customization path.
	 *
	 * @return string
	 */
	function tinv_wishlist_template_html($template_name, $args = array(), $template_path = '')
	{
		ob_start();
		tinv_wishlist_template($template_name, $args, $template_path);

		return ob_get_clean();
	}
}

if (!function_exists('tinv_wishlist_get_item_data')) {

	/**
	 * Extract meta attributes for product
	 *
	 * @param object $product Object selected product.
	 * @param array $wl_product Wishlist selected product.
	 * @param boolean $flat Return text or template.
	 *
	 * @return string
	 */
	function tinv_wishlist_get_item_data($product, $wl_product = array(), $flat = false)
	{
		$item_data = array();
		$variation_id = $product->is_type('variation') ? $product->get_id() : 0;
		$variation_data = $product->is_type('variation') ? wc_get_product_variation_attributes($product->get_id()) : array();
		if (!empty($variation_id) && is_array($variation_data) && is_array($wl_product)) {
			foreach ($variation_data as $name => $value) {
				if ('' === $value) {
					// Could be any value that saved to a custom meta.
					if (array_key_exists('meta', $wl_product) && array_key_exists($name, $wl_product['meta'])) {
						$value = $wl_product['meta'][$name];
					} else {
						continue;
					}
				}

				$taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));

				// If this is a term slug, get the term's nice name.
				if (taxonomy_exists($taxonomy)) {
					$term = get_term_by('slug', $value, $taxonomy); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.get_term_by
					if (!is_wp_error($term) && $term && $term->name) {
						$value = $term->name;
					}
					$label = wc_attribute_label($taxonomy);

					// If this is a custom option slug, get the options name.
				} else {
					$value = apply_filters('woocommerce_variation_option_name', $value);
					$product_attributes = $product->get_attributes();
					$_name = str_replace('attribute_', '', $name);
					if (isset($product_attributes[$_name])) {
						$label = wc_attribute_label($_name, $product);
					} else {
						$label = $name;
					}
				}
				if ('' === $value || wc_is_attribute_in_product_name($value, is_callable(array(
						$product,
						'get_name'
					)) ? $product->get_name() : $product->get_title())) {
					continue;
				}
				$item_data[] = array(
					'key' => $label,
					'value' => $value,
				);
			} // End foreach().
		} // End if().

		// Filter item data to allow 3rd parties to add more to the array.
		$item_data = apply_filters('tinvwl_wishlist_get_item_data', $item_data, $product);

		// Format item data ready to display.
		foreach ($item_data as $key => $data) {
			// Set hidden to true to not display meta on cart.
			if (!empty($data['hidden'])) {
				unset($item_data[$key]);
				continue;
			}
			$item_data[$key]['key'] = !empty($data['key']) ? $data['key'] : $data['name'];
			$item_data[$key]['display'] = !empty($data['display']) ? $data['display'] : $data['value'];
		}

		// Output flat or in list format.
		if (0 < count($item_data)) {
			ob_start();
			if ($flat) {
				foreach ($item_data as $data) {
					echo esc_html($data['key']) . ': ' . wp_kses_post($data['display']) . '<br>';
				}
			} else {
				tinv_wishlist_template('ti-wishlist-item-data.php', array('item_data' => $item_data));
			}

			return ob_get_clean();
		}

		return '';
	}
} // End if().

if (!function_exists('tinv_wishlist_get')) {

	/**
	 * Return Wishlist by id or share key
	 *
	 * @param mixed $id Integer wishlist ID, or Share Key wishlist.
	 * @param boolean $toend Switches to the extract the default or guest wishlist.
	 *
	 * @return array
	 */
	function tinv_wishlist_get($id = '', $toend = true)
	{
		$wl = new TInvWL_Wishlist();
		$wishlist = null;
		if (empty($id)) {
			$id = get_query_var('tinvwlID', null);
		}

		if (!empty($id)) {
			if (is_integer($id)) {
				$wishlist = $wl->get_by_id($id);
			}
			if (empty($wishlist)) {
				$wishlist = $wl->get_by_share_key($id);
			}

			if (is_array($wishlist)) {
				$wishlist['is_owner'] = false;
				if (is_user_logged_in()) {
					$wishlist['is_owner'] = get_current_user_id() == $wishlist['author']; // WPCS: loose comparison ok.
				} else {
					$wishlist['is_owner'] = $wl->get_sharekey() === $wishlist['share_key']; // WPCS: loose comparison ok.
				}
			}
		} elseif (is_user_logged_in() && $toend) {
			$wishlist = $wl->add_user_default();

			$wishlist['is_owner'] = true;
		} elseif ($toend) {
			$wishlist = $wl->get_by_sharekey_default();
			if (!empty($wishlist)) {
				$wishlist = array_shift($wishlist);
				$wishlist['is_owner'] = $wl->get_sharekey() === $wishlist['share_key'];
			}
		}

		return $wishlist;
	}
} // End if().

if (!function_exists('tinv_url_wishlist_default')) {

	/**
	 * Return the default wishlist url
	 *
	 * @return string
	 */
	function tinv_url_wishlist_default()
	{
		$page = apply_filters('wpml_object_id', tinv_get_option('page', 'wishlist'), 'page', true); // @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
		if (empty($page)) {
			return '';
		}
		$link = get_permalink($page);

		return $link;
	}
}

if (!function_exists('tinv_url_wishlist_by_key')) {

	/**
	 * Return the wishlist url by share key
	 *
	 * @param string $share_key Share Key wishlist.
	 * @param integer $paged Page.
	 *
	 * @return string
	 */
	function tinv_url_wishlist_by_key($share_key, $paged = 1)
	{
		$paged = absint($paged);
		$paged = 1 < $paged ? $paged : 1;
		$link = tinv_url_wishlist_default();
		if (empty($link)) {
			return $link;
		}

		if (1 < $paged) {
			$link = add_query_arg('wl_paged', $paged, $link);
		}

		if ($share_key) {
			if (get_option('permalink_structure')) {
				$suffix = '';
				if (preg_match('/([^\?]+)\?*?(.*)/i', $link, $_link)) {
					$link = $_link[1];
					$suffix = $_link[2];
				}
				if (!preg_match('/\/$/', $link)) {
					$link .= '/';
				}
				$link .= $share_key . '/' . $suffix;
			} else {
				$link = add_query_arg('tinvwlID', $share_key, $link);
			}
		}

		return $link;
	}
} // End if().

if (!function_exists('tinv_url_wishlist')) {

	/**
	 * Return the wishlist url by id or share key
	 *
	 * @param mixed $id Integer wishlist ID, or Share Key wishlist.
	 * @param integer $paged Page.
	 * @param boolean $full Return full url or shroted url for logged in user.
	 *
	 * @return string
	 */
	function tinv_url_wishlist($id = '', $paged = 1, $full = true)
	{
		$share_key = $id;
		if (!(is_string($id) && preg_match('/^[A-Fa-f0-9]{6}$/', $id))) {
			$wishlist = tinv_wishlist_get($id, false);
			$share_key = $wishlist['share_key'];
		}

		return tinv_url_wishlist_by_key($share_key, $paged);
	}
}

if (!function_exists('tinv_wishlist_status')) {

	/**
	 * Check status free or premium plugin and disable free
	 *
	 * @param string $transient Plugin transient name.
	 *
	 * @return string
	 * @global string $s
	 *
	 * @global string $status
	 * @global string $page
	 */
	function tinv_wishlist_status($transient)
	{
		if (TINVWL_LOAD_FREE === $transient) {
			TInvWL_PluginExtend::deactivate_self(TINVWL_LOAD_FREE);

			return 'plugins.php';
		}
		if (TINVWL_LOAD_PREMIUM === $transient) {
			if (is_plugin_active(TINVWL_LOAD_FREE)) {
				TInvWL_PluginExtend::deactivate_self(TINVWL_LOAD_FREE);
				if (!function_exists('wp_create_nonce')) {
					return 'plugins.php';
				}

				global $status, $page, $s;
				$redirect = 'plugins.php?';
				$redirect .= http_build_query(array(
					'action' => 'activate',
					'plugin' => $transient,
					'plugin_status' => $status,
					'paged' => $page,
					's' => $s,
				));
				$redirect = esc_url_raw(add_query_arg('_wpnonce', wp_create_nonce('activate-plugin_' . $transient), $redirect));

				return $redirect;
			}
		}

		return false;
	}
} // End if().

if (!function_exists('tinvwl_body_classes')) {

	/**
	 * Add custom class
	 *
	 * @param array $classes Current classes.
	 *
	 * @return array
	 */
	function tinvwl_body_classes($classes)
	{
		if (tinv_get_option('style', 'customstyle')) {
			$classes[] = 'tinvwl-theme-style';
		} else {
			$classes[] = 'tinvwl-custom-style';
		}

		return $classes;
	}

	add_filter('body_class', 'tinvwl_body_classes');
}

if (!function_exists('tinvwl_shortcode_addtowishlist')) {

	/**
	 * Shortcode Add To Wishlist
	 *
	 * @param array $atts Array parameter from shortcode.
	 *
	 * @return string
	 */
	function tinvwl_shortcode_addtowishlist($atts = array())
	{
		$class = TInvWL_Public_AddToWishlist::instance();

		return $class->shortcode($atts);
	}

	add_shortcode('ti_wishlists_addtowishlist', 'tinvwl_shortcode_addtowishlist');
}

if (!function_exists('tinvwl_shortcode_view')) {

	/**
	 * Shortcode view Wishlist
	 *
	 * @param array $atts Array parameter from shortcode.
	 *
	 * @return string
	 */
	function tinvwl_shortcode_view($atts = array())
	{
		$class = TInvWL_Public_Wishlist_View::instance();

		return $class->shortcode($atts);
	}

	add_shortcode('ti_wishlistsview', 'tinvwl_shortcode_view');
}

if (!function_exists('tinvwl_shortcode_products_counter')) {

	/**
	 * Shortcode view Wishlist
	 *
	 * @param array $atts Array parameter from shortcode.
	 *
	 * @return string
	 */
	function tinvwl_shortcode_products_counter($atts = array())
	{
		$class = TInvWL_Public_WishlistCounter::instance();

		return $class->shortcode($atts);
	}

	add_shortcode('ti_wishlist_products_counter', 'tinvwl_shortcode_products_counter');
}

if (!function_exists('tinvwl_view_addto_html')) {

	/**
	 * Show button Add to Wishlsit
	 */
	function tinvwl_view_addto_html()
	{
		$class = TInvWL_Public_AddToWishlist::instance();
		$class->htmloutput();
	}
}

if (!function_exists('tinvwl_view_addto_htmlout')) {

	/**
	 * Show button Add to Wishlsit, if product is not purchasable
	 */
	function tinvwl_view_addto_htmlout()
	{
		$class = TInvWL_Public_AddToWishlist::instance();
		$class->htmloutput_out();
	}
}

if (!function_exists('tinvwl_view_addto_htmlloop')) {

	/**
	 * Show button Add to Wishlsit, in loop
	 */
	function tinvwl_view_addto_htmlloop()
	{
		$class = TInvWL_Public_AddToWishlist::instance();
		$class->htmloutput_loop();
	}
}

if (!function_exists('tinvwl_clean_url')) {

	/**
	 * Clear esc_url to original
	 *
	 * @param string $good_protocol_url Cleared URL.
	 * @param string $original_url Original URL.
	 *
	 * @return string
	 */
	function tinvwl_clean_url($good_protocol_url, $original_url)
	{
		return $original_url;
	}
}

if (!function_exists('tinvwl_add_to_cart_need_redirect')) {

	/**
	 * Check if the product is third-party, or has another link added to the cart then redirect to the product page.
	 *
	 * @param boolean $redirect Default value to redirect.
	 * @param \WC_Product $_product Product data.
	 * @param string $redirect_url Current url for redirect.
	 *
	 * @return boolean
	 */
	function tinvwl_add_to_cart_need_redirect($redirect, $_product, $redirect_url)
	{
		if ($redirect) {
			return true;
		}

		if ('external' === $_product->get_type()) {
			return true;
		}

		$need_url_data = array_merge(array(
			'variation_id' => $_product->is_type('variation') ? $_product->get_id() : 0,
			'add-to-cart' => $_product->is_type('variation') ? $_product->get_parent_id() : $_product->get_id(),
		), array_map('urlencode', array()));

		$need_url_data = array_filter($need_url_data);

		$need_url = apply_filters('tinvwl_product_add_to_cart_redirect_slug_original', remove_query_arg('added-to-cart', (version_compare(WC_VERSION, '3.8.0', '<') ? add_query_arg($need_url_data) : add_query_arg($need_url_data, ''))), $_product);
		$need_url_full = apply_filters('tinvwl_product_add_to_cart_redirect_url_original', remove_query_arg('added-to-cart', add_query_arg($need_url_data, $_product->get_permalink())), $_product);

		global $product;
		// store global product data.
		$_product_tmp = $product;
		// override global product data.
		$product = $_product;

		add_filter('clean_url', 'tinvwl_clean_url', 10, 2);
		do_action('before_get_redirect_url');
		$_redirect_url = apply_filters('tinvwl_product_add_to_cart_redirect_url', $_product->add_to_cart_url(), $_product);
		do_action('after_get_redirect_url');
		remove_filter('clean_url', 'tinvwl_clean_url', 10);

		// restore global product data.
		$product = $_product_tmp;

		if ($_redirect_url !== $need_url && $_redirect_url !== $need_url_full) {
			return true;
		}

		return $redirect;
	}

	add_filter('tinvwl_product_add_to_cart_need_redirect', 'tinvwl_add_to_cart_need_redirect', 10, 3);
} // End if().

if (!function_exists('tinvwl_meta_validate_cart_add')) {

	/**
	 * Checks the ability to add a product
	 *
	 * @param boolean $redirect Default value to redirect.
	 * @param \WC_Product $product Product data.
	 * @param string $redirect_url Current url for redirect.
	 * @param array $wl_product Wishlist Product.
	 *
	 * @return boolean
	 */
	function tinvwl_meta_validate_cart_add($redirect, $product, $redirect_url, $wl_product)
	{
		if ($redirect && array_key_exists('meta', $wl_product) && !empty($wl_product['meta'])) {

			$wl_product = apply_filters('tinvwl_addproduct_tocart', $wl_product);

			TInvWL_Public_Cart::prepare_post($wl_product);

			$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($wl_product['product_id']));
			$quantity = empty($wl_product['quantity']) ? 1 : wc_stock_amount($wl_product['quantity']);
			$variation_id = $wl_product['variation_id'];
			$variations = $product->is_type('variation') ? wc_get_product_variation_attributes($product->get_id()) : array();
			$passed_validation = $product->is_purchasable() && ($product->is_in_stock() || $product->backorders_allowed()) && 'external' !== $product->get_type();
			ob_start();
			if (function_exists('wc_clear_notices')) {
				wc_clear_notices();
			}
			$passed_validation = apply_filters('woocommerce_add_to_cart_validation', $passed_validation, $product_id, $quantity, $variation_id, $variations);
			if (function_exists('wc_get_notices')) {
				$wc_errors = wc_get_notices('error');
			}
			$wc_output = ob_get_clean();
			if ($passed_validation && empty($wc_errors) && empty($wc_output)) {
				$redirect = false;
			}

			TInvWL_Public_Cart::unprepare_post();
		}

		return $redirect;
	}

	add_filter('tinvwl_product_add_to_cart_need_redirect', 'tinvwl_meta_validate_cart_add', 90, 4);
} // End if().

if (!function_exists('tinv_wishlist_print_meta')) {

	/**
	 * Print meta data for wishlist form
	 *
	 * @param array $meta Meta Array.
	 * @param boolean $flat Return text or template.
	 *
	 * @return string
	 */
	function tinv_wishlist_print_meta($meta = array(), $flat = false)
	{
		if (!is_array($meta)) {
			$meta = array();
		}
		$product_id = $variation_id = 0;
		if (array_key_exists('product_id', $meta)) {
			$product_id = $meta['product_id'];
		}
		if (array_key_exists('variation_id', $meta)) {
			$variation_id = $meta['variation_id'];
		}
		foreach (array('add-to-cart', 'product_id', 'variation_id', 'quantity', 'action', 'variation') as $field) {
			if (array_key_exists($field, $meta)) {
				unset($meta[$field]);
			}
		}
		$meta = array_filter($meta);
		if (empty($meta)) {
			return '';
		}
		$item_data = array();
		foreach ($meta as $key => $value) {
			if (!preg_match('/^\_/', $key)) {
				$item_data[$key] = array(
					'key' => $key,
					'display' => $value,
				);
			}
		}

		foreach (array_keys($item_data) as $key) {
			if (strpos($key, 'attribute_') === 0) {
				unset($item_data[$key]);
			}
		}

		$item_data = apply_filters('tinvwl_wishlist_item_meta_post', $item_data, $product_id, $variation_id);
		foreach ($item_data as $key => $data) {
			if (is_object($data['display']) || is_array($data['display'])) {
				$item_data[$key]['display'] = json_encode($data['display']);
			}
		}
		ob_start();
		if ($flat) {
			foreach ($item_data as $data) {
				echo esc_html($data['key']) . ': ' . wp_kses_post($data['display']) . '<br>';
			}
		} else {
			if ($item_data) {
				tinv_wishlist_template('ti-wishlist-item-data.php', array('item_data' => $item_data));
			}
		}

		return apply_filters('tinvwl_wishlist_item_meta_wishlist', ob_get_clean());
	}
} // End if().

if (!function_exists('tinv_wishlistmeta')) {

	/**
	 * Show new meta data
	 *
	 * @param string $meta Print meta.
	 * @param array $wl_product Wishlist product.
	 * @param \WC_Product $product Woocommerce product.
	 *
	 * @return string
	 */
	function tinv_wishlistmeta($meta, $wl_product, $product)
	{
		if (array_key_exists('meta', $wl_product)) {
			$wlmeta = apply_filters('tinvwl_wishlist_item_meta_wishlist_output', tinv_wishlist_print_meta($wl_product['meta']), $wl_product, $product);
		}
		$meta .= $wlmeta;

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_data', 'tinv_wishlistmeta', 10, 3);
}

if (!function_exists('tinvwl_add_to_cart_item_meta_post')) {

	/**
	 * Save post data to cart item
	 *
	 * @param array $cart_item_data Array with cart imet information.
	 * @param string $cart_item_key Cart item key.
	 *
	 * @return array
	 */
	function tinvwl_add_to_cart_item_meta_post($cart_item_data, $cart_item_key)
	{
		$postdata = $_POST; // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected

		$postdata = apply_filters('tinvwl_product_prepare_meta', $postdata);

		if (array_key_exists('variation_id', $postdata) && !empty($postdata['variation_id'])) {
			foreach ($postdata as $key => $field) {
				if (preg_match('/^attribute\_/', $key)) {
					unset($postdata[$key]);
				}
			}
		}
		foreach (array('add-to-cart', 'product_id', 'variation_id', 'quantity') as $field) {
			if (array_key_exists($field, $postdata)) {
				unset($postdata[$field]);
			}
		}
		$postdata = array_filter($postdata);
		if (empty($postdata)) {
			return $cart_item_data;
		}
		ksort($postdata);

		$cart_item_data['tinvwl_formdata'] = $postdata;

		return $cart_item_data;
	}

	add_action('woocommerce_add_cart_item', 'tinvwl_add_to_cart_item_meta_post', 10, 2);
} // End if().

if (!function_exists('tinvwl_set_utm')) {

	/**
	 * Set UTM sources.
	 */
	function tinvwl_set_utm()
	{

		// Forcing partners UTM.
		if (class_exists('Ocean_Extra') && !defined('TINVWL_PARTNER') && !defined('TINVWL_CAMPAIGN')) {
			define('TINVWL_PARTNER', 'oceanwporg');
			define('TINVWL_CAMPAIGN', 'oceanwp_theme');
		}

		// Set a source.
		$source = get_option(TINVWL_PREFIX . '_utm_source');
		if (!$source || $source !== defined('TINVWL_PARTNER')) {
			$source = defined('TINVWL_PARTNER') ? TINVWL_PARTNER : 'wordpress_org';
			update_option(TINVWL_PREFIX . '_utm_source', $source);
		}

		define('TINVWL_UTM_SOURCE', $source);

		// Set a medium.
		$medium = get_option(TINVWL_PREFIX . '_utm_medium');
		if (!$medium || ('organic' === $medium && defined('TINVWL_PARTNER'))) {
			$medium = defined('TINVWL_PARTNER') ? 'integration' : 'organic';
			update_option(TINVWL_PREFIX . '_utm_medium', $medium);
		}

		define('TINVWL_UTM_MEDIUM', $medium);

		// Set a campaign.
		$campaign = get_option(TINVWL_PREFIX . '_utm_campaign');
		if (!$campaign || $campaign !== defined('TINVWL_CAMPAIGN')) {
			$campaign = defined('TINVWL_PARTNER') ? (defined('TINVWL_CAMPAIGN') ? TINVWL_CAMPAIGN : TINVWL_PARTNER) : 'organic';
			update_option(TINVWL_PREFIX . '_utm_campaign', $campaign);
		}

		define('TINVWL_UTM_CAMPAIGN', $campaign);
	}
} // End if().

if (!function_exists('is_wishlist')) {

	/**
	 * is_wishlist - Returns true when viewing the wishlist page.
	 *
	 * @return bool
	 */
	function is_wishlist()
	{
		return (is_page(apply_filters('wpml_object_id', tinv_get_option('page', 'wishlist'), 'page', true)));
	}
}

if (!function_exists('tinvwl_get_wishlist_products')) {
	/**
	 * Get wishlist products for default user wishlist or by ID or SHAREKEY
	 *
	 * @param int $wishlist_id by ID or SHAREKEY, 0 = default wishlist of current user
	 * @param array $data query parameters for get() method of TInvWL_Product() class.
	 *
	 * @return array|bool
	 */
	function tinvwl_get_wishlist_products($wishlist_id = 0, $data = array())
	{
		$wishlist = tinv_wishlist_get($wishlist_id);
		if (empty($wishlist)) {
			return false;
		}
		$wlp = new TInvWL_Product($wishlist);
		$products = $wlp->get_wishlist($data);

		if (empty($products)) {
			return false;
		}

		return $products;
	}
}

add_action('init', function () {
	if (!is_user_logged_in()) {
		add_filter('nonce_user_logged_out', function ($uid, $action = -1) {
			if ($action === 'wp_rest') {
				return get_current_user_id();
			}

			return $uid;
		}, 99, 2);
	}
});
