<?php
if (!defined('ABSPATH')) die('No direct access.');

/**
 * Class UDP_Checkout_Embed
 *
 * Create links to embed an external checkout page
 */
if (!class_exists('Updraft_Checkout_Embed')) {
	class Updraft_Checkout_Embed {

		/**
		 * Class version
		 *
		 * @var string
		 */
		private static $version = '1.0.1';

		/**
		 * Products list
		 *
		 * @var array
		 */
		public $products = array();

		/**
		 * Construct
		 *
		 * @param string $plugin_name   Current plugin using the class
		 * @param string $return_url    The return URL after purchase is complete / canceled. Specially useful with paypal, that forces a redirect.
		 * @param array  $products_list The list of products. Array or object that can be converted to an array
		 * @param string $base_url      The plugin url, to where 'checkout-embed' is located. Used to enqueue scripts and styles.
		 * @param array  $load_in_pages Pages in which the scripts are included. Use to limit the inclusion if necessary. See $this->enqueue_scripts
		 */
		public function __construct($plugin_name, $return_url, $products_list, $base_url, $load_in_pages = null) {
			$this->plugin_name = sanitize_key($plugin_name);
			$this->return_url = $return_url;
			$this->products_list = $products_list;
			$this->load_in_pages = $load_in_pages;
			$this->base_url = $base_url;
			add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
			add_action('admin_footer', array($this, 'print_template'));
		}

		/**
		 * Get the product using its slug
		 *
		 * @param string $product_slug
		 * @param string $return_url
		 * @return string|bool
		 */
		public function get_product($product_slug, $return_url = '') {
			$products = $this->get_products();
			if (empty($products)) return false;

			if (is_object($products)) $products = get_object_vars($products);

			if (is_array($products) && array_key_exists($product_slug, $products)) {

				if (!$return_url) $return_url = $this->return_url;
				$return_url = add_query_arg($this->plugin_name.'_product', $product_slug, $return_url);

				return apply_filters(
					$this->plugin_name.'_return_url',
					add_query_arg(
						array(
							$this->plugin_name.'_return_url' => urlencode($return_url),
							'checkout_embed_product_slug' => $product_slug
						),
						$products[$product_slug]
					),
					$product_slug
				);
			}

			return false;
		}

		/**
		 * Get the products on the remote url
		 * Can return an object, if the products list given to the class is. (eg. json_decode gives an object if not specified otherwise)
		 *
		 * @return array|object
		 */
		private function get_products() {
			return apply_filters($this->plugin_name.'_checkout_embed_get_products', $this->products_list ? $this->products_list : array());
		}

		/**
		 * Enqueue the required scripts / styles
		 *
		 * @param string $hook
		 */
		public function enqueue_scripts($hook) {
			if (is_array($this->load_in_pages)) {
				if (!in_array($hook, $this->load_in_pages)) {
					return;
				}
			}
			wp_enqueue_script($this->plugin_name.'-checkout-embed', trailingslashit($this->base_url).'checkout-embed/assets/udp-checkout-embed.js', array('jquery'), self::$version, true);
			wp_enqueue_style($this->plugin_name.'-checkout-embed', trailingslashit($this->base_url).'checkout-embed/assets/udp-checkout-embed.css', null, self::$version);
		}

		/**
		 * Print the template for the modal
		 */
		public function print_template() {
			if (is_array($this->load_in_pages)) {
				$screen = get_current_screen();
				if (!in_array($screen->base, $this->load_in_pages)) {
					return;
				}
			}
			?>
			<div style="display: none;" id="udp-modal-template">
				<div class="udp-modal">
					<div class="udp-modal__overlay"></div>
					<div class="udp-modal__modal">
					</div>
				</div>
			</div>
		<?php }
	}
}
