<?php
/**
 * IVE Loader.
 *
 * @package IVE
 */

if ( ! class_exists( 'IVE_Loader' ) ) {

	/**
	 * Class IVE_Loader.
	 */
	final class IVE_Loader {

		/**
		 * Member Variable
		 *
		 * @var ive_loader_instance
		 */
		private static $ive_loader_instance;

		/**
		 *  Initiator
		 */
		public static function ive_loader_get_instance() {
			if ( ! isset( self::$ive_loader_instance ) ) {
				self::$ive_loader_instance = new self();
			}
			return self::$ive_loader_instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'ive_load_plugin' ) );
		}


		/**
		 * Loads plugin files.
		 *
		 * @since 0.0.1
		 *
		 * @return void
		 */
		public function ive_load_plugin() {


			if ( !class_exists( 'IEPA_Loader' ) ) {
				/* TO DO */
				add_action( 'admin_notices', array( $this, 'ive_ive_fails_to_load' ) );
				add_action( 'network_admin_notices', array( $this, 'ive_ive_fails_to_load' ) );
			}
		}

		/**
		 * Fires admin notice when Ibtana Ecommerce Product Addons is not installed and activated.
		 *
		 * @since 0.0.1
		 *
		 * @return void
		 */
		public function ive_ive_fails_to_load() {

			$ive_ive_plugin = 'ibtana-ecommerce-product-addons/plugin.php';

			global $pagenow;
			if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
			  	if ('product' === get_post_type()) {

					$ive_notice_class = 'notice notice-error';
					/* translators: %s: html tags */
					$ive_message_notice = sprintf(
						__(
								'In order to use product page as a gutenberg editor install and activate %1$sIbtana Ecommerce Product Addons%1$s',
								'ibtana-visual-editor'
							),
						'<strong>',
						'</strong>'
					);

					if ( ive_is_ive_installed() ) {
						if ( ! current_user_can( 'activate_plugins' ) ) {
							return;
						}

						$ive_action_url   = wp_nonce_url(
							'plugins.php?action=activate&amp;plugin=' . $ive_ive_plugin . '&amp;plugin_status=all&amp;paged=1&amp;s',
							'activate-plugin_' . $ive_ive_plugin
						);
						$ive_button_label = __( 'Activate Ibtana – Ecommerce Product Addons', 'ibtana-visual-editor' );

					} else {
						if ( ! current_user_can( 'install_plugins' ) ) {
							return;
						}

						$ive_action_url   = wp_nonce_url(
							self_admin_url( 'update.php?action=install-plugin&plugin=ibtana-ecommerce-product-addons' ),
							'install-plugin_ibtana-ecommerce-product-addons'
						);
						$ive_button_label = __( 'Install Ibtana – Ecommerce Product Addons', 'ibtana-visual-editor' );
					}

					$ive_button = '<p><a href="' . $ive_action_url . '" class="button-primary ive-activation-addon-btn">' . $ive_button_label . '</a></p><p></p>';

					printf(
						'<div class="%1$s"><p>%2$s</p>%3$s</div>',
						esc_attr( $ive_notice_class ),
						wp_kses_post( $ive_message_notice ),
						wp_kses_post( $ive_button )
					);
				}
			}
		}

		public static function ive_sanitize_array( $var ) {
			if ( is_array( $var ) ) {
				return array_map( 'self::ive_sanitize_array', $var );
			} else {
				return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
			}
		}

	}

	/**
	 *  Prepare if class 'IVE_Loader' exist.
	 *  Kicking this off by calling 'ive_loader_get_instance()' method
	 */
	IVE_Loader::ive_loader_get_instance();
}

/**
 * Is Ibtana Ecommerce Product Addons plugin installed.
 */
if ( ! function_exists( 'ive_is_ive_installed' ) ) {

	/**
	 * Check if Ibtana Ecommerce Product Addons is installed
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	function ive_is_ive_installed() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$ive_ive_path    = 'ibtana-ecommerce-product-addons/plugin.php';
		$ive_get_plugins = get_plugins();

		return isset( $ive_get_plugins[ $ive_ive_path ] );
	}
}
