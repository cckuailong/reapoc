<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DLM_Product_Manager {

	/**
	 * @var DLM_Product_Manager
	 */
	private static $instance = null;

	/**
	 * @var array<DLM_Product>
	 */
	private $products = array();

	/**
	 * @var DLM_Product_Error_Handler
	 */
	private $error_handler;
	
	/**
	 * Private constructor
	 */
	private function __construct() {
		$this->error_handler = DLM_Product_Error_Handler::get();
	}

	/**
	 * Singleton get method
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return DLM_Product_Manager
	 */
	public static function get() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return DLM_Product_Error_Handler
	 */
	public function error_handler() {
		return $this->error_handler;
	}

	/**
	 * Setup Product Manager
	 */
	public function setup() {
		add_action( 'admin_init', array( $this, 'load_extensions' ) );
		add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
	}

	/**
	 * Load extensions
	 * @hooked admin_init
	 */
	public function load_extensions() {
		// Load the registered extensions
		$registered_extensions = apply_filters( 'dlm_extensions', array() );

		// Check if we've got extensions
		if ( count( $registered_extensions ) > 0 ) {

			// Don't block local requests
			add_filter( 'block_local_requests', '__return_false' );

			// Load products
			$this->load_products( $registered_extensions );

		}
	}


	/**
	 * Load Products
	 *
	 * @param array $extensions
	 */
	private function load_products( $extensions ) {

		// Check
		if ( count( $extensions ) > 0 ) {

			// Loop
			foreach ( $extensions as $extension ) {

				// backwards compat
				if ( ! is_array( $extension ) ) {
					$extension = array(
						'file'    => $extension,
						'version' => false,
						'name'    => "",
					);
				}

				// Setup new Product
				$product = new DLM_Product( $extension['file'], $extension['version'], $extension['name'] );

				// Setup plugin actions and filters
				add_action( 'pre_set_site_transient_update_plugins', array( $product, 'check_for_updates' ) );
				add_filter( 'plugins_api', array( $product, 'plugins_api' ), 10, 3 );
				add_action( 'after_plugin_row_' . $product->get_plugin_name(), array( $product, 'after_plugin_row' ), 10, 2 );

				// Add product to products property
				$this->products[ $extension['file'] ] = $product;
			}

		}

	}

	/**
	 * Get products
	 *
	 * @return array<DLM_Product>
	 */
	public function get_products() {
		return $this->products;
	}

	public function display_admin_notices() {

		// get products
		$products = $this->get_products();

		// loop products
		if ( count( $products ) > 0 ) {
			foreach ( $products as $product ) {

				// check if product is correctly activated
				if ( true !== $product->get_license()->is_active() ) {

					$is_dlm_page = ( isset( $_GET['post_type'] ) && 'dlm_download' == $_GET['post_type'] ) ? true : false;

					$notice_id = "extension-" . esc_attr( $product->get_product_id() );

					if ( 1 != get_option( 'dlm_hide_notice-'.$notice_id, 0 ) || $is_dlm_page ) {

						$message = '<b>Warning!</b> Your %s license is inactive which means you\'re missing out on updates and support! <a href="%s">Activate your license</a> or <a href="%s" target="_blank">get a license here</a>.';
						$message = sprintf( __( $message, 'download-monitor' ), $product->get_product_name(), admin_url( 'edit.php?post_type=dlm_download&page=dlm-extensions#installed-extensions' ), $product->get_tracking_url( 'activate-license-notice' ) );

						?>
						<div class="notice notice-warning dlm-notice<?php echo (!$is_dlm_page?" is-dismissible":""); ?>"
						     id="<?php echo $notice_id; ?>"
						     data-nonce="<?php echo esc_attr( wp_create_nonce( 'dlm_hide_notice-'.$notice_id ) ); ?>">
							<p><?php echo $message; ?></p>
						</div>
						<?php
					}

				}

			}
		}

	}
}