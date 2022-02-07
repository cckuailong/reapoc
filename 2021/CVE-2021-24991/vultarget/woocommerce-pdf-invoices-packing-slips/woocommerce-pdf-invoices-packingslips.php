<?php
/**
 * Plugin Name: WooCommerce PDF Invoices & Packing Slips
 * Plugin URI: http://www.wpovernight.com
 * Description: Create, print & email PDF invoices & packing slips for WooCommerce orders.
 * Version: 2.10.4
 * Author: Ewout Fernhout
 * Author URI: http://www.wpovernight.com
 * License: GPLv2 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: woocommerce-pdf-invoices-packing-slips
 * WC requires at least: 2.2.0
 * WC tested up to: 5.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'WPO_WCPDF' ) ) :

class WPO_WCPDF {

	public $version = '2.10.4';
	public $plugin_basename;
	public $legacy_mode;
	public $legacy_textdomain;

	protected static $_instance = null;

	/**
	 * Main Plugin Instance
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_basename = plugin_basename(__FILE__);

		$this->define( 'WPO_WCPDF_VERSION', $this->version );

		// load the localisation & classes
		add_action( 'plugins_loaded', array( $this, 'translations' ) );
		add_action( 'plugins_loaded', array( $this, 'load_classes' ), 9 );
		add_action( 'in_plugin_update_message-'.$this->plugin_basename, array( $this, 'in_plugin_update_message' ) );
		add_action( 'admin_notices', array( $this, 'nginx_detected' ) );
		add_action( 'admin_notices', array( $this, 'mailpoet_mta_detected' ) );

		// legacy textdomain fallback
		if ( $this->legacy_textdomain_enabled() === true ) {
			add_filter( 'load_textdomain_mofile', array( $this, 'textdomain_fallback' ), 10, 2 );
		}
	}

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}


	/**
	 * Load the translation / textdomain files
	 * 
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public function translations() {
		if ( function_exists( 'determine_locale' ) ) { // WP5.0+
			$locale = determine_locale();
		} else {
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		}
		$locale = apply_filters( 'plugin_locale', $locale, 'woocommerce-pdf-invoices-packing-slips' );
		$dir    = trailingslashit( WP_LANG_DIR );

		$textdomains = array( 'woocommerce-pdf-invoices-packing-slips' );
		if ( $this->legacy_mode_enabled() === true ) {
			$textdomains[] = 'wpo_wcpdf';
		}

		/**
		 * Frontend/global Locale. Looks in:
		 *
		 * 		- WP_LANG_DIR/woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packing-slips-LOCALE.mo
		 * 	 	- WP_LANG_DIR/plugins/woocommerce-pdf-invoices-packing-slips-LOCALE.mo
		 * 	 	- woocommerce-pdf-invoices-packing-slips/languages/woocommerce-pdf-invoices-packing-slips-LOCALE.mo (which if not found falls back to:)
		 * 	 	- WP_LANG_DIR/plugins/woocommerce-pdf-invoices-packing-slips-LOCALE.mo
		 */
		foreach ( $textdomains as $textdomain ) {
			unload_textdomain( $textdomain );
			load_textdomain( $textdomain, $dir . 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packing-slips-' . $locale . '.mo' );
			load_textdomain( $textdomain, $dir . 'plugins/woocommerce-pdf-invoices-packing-slips-' . $locale . '.mo' );
			load_plugin_textdomain( $textdomain, false, dirname( plugin_basename(__FILE__) ) . '/languages' );
		}
	}

	/**
	 * Maintain backwards compatibility with old translation files
	 * Uses old .mo file if it exists in any of the override locations
	 */
	public function textdomain_fallback( $mo, $textdomain ) {
		$plugin_domain = 'woocommerce-pdf-invoices-packing-slips';
		$old_domain = 'wpo_wcpdf';

		if ( $textdomain !== $plugin_domain && $textdomain !== $old_domain ) {
			return $mo;
		}

		$mopath = trailingslashit( dirname( $mo ) );
		$mofile = basename( $mo );

		if ( $textdomain == $old_domain ) {
			$textdomain = $plugin_domain;
			$mofile = str_replace( $old_domain, $textdomain, $mofile );
		}

		if ( $textdomain === $plugin_domain ) {
			$old_mofile = str_replace( $textdomain, $old_domain, $mofile );
			if ( file_exists( $mopath.$old_mofile ) ) {
				// we have an old override - use it
				return $mopath.$old_mofile;
			}

			// prevent loading outdated language packs
			$pofile = str_replace( '.mo', '.po', $mofile );
			if ( file_exists( $mopath.$pofile ) ) {
				// load po file
				$podata = file_get_contents( $mopath.$pofile );
				// set revision date threshold
				$block_before = strtotime( '2017-05-15' );
				// read revision date
				preg_match( '~PO-Revision-Date: (.*?)\\\n~s', $podata, $matches );
				if ( isset( $matches[1] ) ) {
					$revision_date = $matches[1];
					if ( $revision_timestamp = strtotime( $revision_date ) ) {
						// check if revision is before threshold date
						if ( $revision_timestamp < $block_before ) {
							// try bundled
							$bundled_file = $this->plugin_path() . '/languages/'. $mofile;
							if ( file_exists( $bundled_file ) ) {
								return $bundled_file;
							} else {
								return '';
							}
							// delete po & mo file if possible
							// @unlink($pofile);
							// @unlink($mofile);
						}
					}
				}
			}
		}

		return $mopath.$mofile;
	}

	/**
	 * Load the main plugin classes and functions
	 */
	public function includes() {
		// WooCommerce compatibility classes
		include_once( $this->plugin_path() . '/includes/compatibility/abstract-wc-data-compatibility.php' );
		include_once( $this->plugin_path() . '/includes/compatibility/class-wc-date-compatibility.php' );
		include_once( $this->plugin_path() . '/includes/compatibility/class-wc-core-compatibility.php' );
		include_once( $this->plugin_path() . '/includes/compatibility/class-wc-order-compatibility.php' );
		include_once( $this->plugin_path() . '/includes/compatibility/class-wc-product-compatibility.php' );
		include_once( $this->plugin_path() . '/includes/compatibility/wc-datetime-functions-compatibility.php' );

		// Third party compatibility
		include_once( $this->plugin_path() . '/includes/compatibility/class-wcpdf-compatibility-third-party-plugins.php' );

		// Plugin classes
		include_once( $this->plugin_path() . '/includes/wcpdf-functions.php' );
		$this->settings = include_once( $this->plugin_path() . '/includes/class-wcpdf-settings.php' );
		$this->documents = include_once( $this->plugin_path() . '/includes/class-wcpdf-documents.php' );
		$this->main = include_once( $this->plugin_path() . '/includes/class-wcpdf-main.php' );
		include_once( $this->plugin_path() . '/includes/class-wcpdf-assets.php' );
		include_once( $this->plugin_path() . '/includes/class-wcpdf-admin.php' );
		include_once( $this->plugin_path() . '/includes/class-wcpdf-frontend.php' );
		include_once( $this->plugin_path() . '/includes/class-wcpdf-install.php' );

		// Backwards compatibility with self
		include_once( $this->plugin_path() . '/includes/legacy/class-wcpdf-legacy.php' );
		include_once( $this->plugin_path() . '/includes/legacy/class-wcpdf-legacy-deprecated-hooks.php' );
	}
	

	/**
	 * Instantiate classes when woocommerce is activated
	 */
	public function load_classes() {
		if ( $this->is_woocommerce_activated() === false ) {
			add_action( 'admin_notices', array ( $this, 'need_woocommerce' ) );
			return;
		}

		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			add_action( 'admin_notices', array ( $this, 'required_php_version' ) );
			return;
		}

		if ( has_filter( 'wpo_wcpdf_pdf_maker' ) === false && version_compare( PHP_VERSION, '7.1', '<' ) ) {
			add_filter( 'wpo_wcpdf_document_is_allowed', '__return_false', 99999 );
			add_action( 'admin_notices', array ( $this, 'required_php_version' ) );
		}

		// all systems ready - GO!
		$this->includes();
	}

	/**
	 * Check if legacy mode is enabled
	 */
	public function legacy_mode_enabled() {
		if (!isset($this->legacy_mode)) {
			$debug_settings = get_option( 'wpo_wcpdf_settings_debug', array() );
			$this->legacy_mode = isset($debug_settings['legacy_mode']);
		}
		return $this->legacy_mode;
	}

	/**
	 * Check if legacy textdomain fallback is enabled
	 */
	public function legacy_textdomain_enabled() {
		if (!isset($this->legacy_textdomain)) {
			$debug_settings = get_option( 'wpo_wcpdf_settings_debug', array() );
			$this->legacy_textdomain = isset($debug_settings['legacy_textdomain']);
		}
		return $this->legacy_textdomain;
	}

	/**
	 * Check if woocommerce is activated
	 */
	public function is_woocommerce_activated() {
		$blog_plugins = get_option( 'active_plugins', array() );
		$site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option('active_sitewide_plugins' ) ) : array();

		if ( in_array( 'woocommerce/woocommerce.php', $blog_plugins ) || isset( $site_plugins['woocommerce/woocommerce.php'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * WooCommerce not active notice.
	 *
	 * @return string Fallack notice.
	 */
	public function need_woocommerce() {
		/* translators: <a> tags */
		$error = sprintf( __( 'WooCommerce PDF Invoices & Packing Slips requires %1$sWooCommerce%2$s to be installed & activated!' , 'woocommerce-pdf-invoices-packing-slips' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>' );
		
		$message = '<div class="error"><p>' . $error . '</p></div>';
	
		echo $message;
	}

	/**
	 * PHP version requirement notice
	 */
	public function required_php_version() {
		$error_message	= __( 'WooCommerce PDF Invoices & Packing Slips requires PHP 7.1 (7.4 or higher recommended).', 'woocommerce-pdf-invoices-packing-slips' );
		/* translators: <a> tags */
		$php_message	= __( 'We strongly recommend to %1$supdate your PHP version%2$s.', 'woocommerce-pdf-invoices-packing-slips' );
		/* translators: <a> tags */
		$add_on_message	= __( 'If you cannot upgrade your PHP version, you can download %1$sthis addon%2$s to enable backwards compatibility with PHP5.6.', 'woocommerce-pdf-invoices-packing-slips' );

		$message = '<div class="error">';
		$message .= sprintf( '<p>%s</p>', $error_message );
		$message .= sprintf( '<p>'.$php_message.'</p>', '<a href="https://docs.wpovernight.com/general/how-to-update-your-php-version/" target="_blank">', '</a>' );
		if ( version_compare( PHP_VERSION, '5.6', '>' ) ) {
			$message .= sprintf( '<p>'.$add_on_message.'</p>', '<a href="https://docs.wpovernight.com/woocommerce-pdf-invoices-packing-slips/backwards-compatibility-with-php-5-6/" target="_blank">', '</a>' );
		}
		$message .= '</div>';

		echo $message;
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 */
	public function in_plugin_update_message( $args ) {
		$transient_name = 'wpo_wcpdf_upgrade_notice_' . $args['Version'];

		if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {
			$response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/woocommerce-pdf-invoices-packing-slips/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$upgrade_notice = self::parse_update_notice( $response['body'], $args['new_version'] );
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		echo wp_kses_post( $upgrade_notice );
	}

	/**
	 * Parse update notice from readme file.
	 *
	 * @param  string $content
	 * @param  string $new_version
	 * @return string
	 */
	private function parse_update_notice( $content, $new_version ) {
		// Output Upgrade Notice.
		$matches        = null;
		$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $new_version ) . '\s*=|$)~Uis';
		$upgrade_notice = '';


		if ( preg_match( $regexp, $content, $matches ) ) {
			$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

			// Convert the full version strings to minor versions.
			$notice_version_parts  = explode( '.', trim( $matches[1] ) );
			$current_version_parts = explode( '.', $this->version );

			if ( 3 !== sizeof( $notice_version_parts ) ) {
				return;
			}

			$notice_version  = $notice_version_parts[0] . '.' . $notice_version_parts[1];
			$current_version = $current_version_parts[0] . '.' . $current_version_parts[1];

			// Check the latest stable version and ignore trunk.
			if ( version_compare( $current_version, $notice_version, '<' ) ) {

				$upgrade_notice .= '</p><p class="wpo_wcpdf_upgrade_notice">';

				foreach ( $notices as $index => $line ) {
					$upgrade_notice .= preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line );
				}
			}
		}

		return wp_kses_post( $upgrade_notice );
	}

	public function nginx_detected()
	{
		if ( empty( $this->main ) ) {
			return;
		}
		$tmp_path = $this->main->get_tmp_path('attachments');
		$server_software   = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
		$random_string = $this->main->get_random_string();

		if ( stristr( $server_software, 'nginx' ) && ( current_user_can( 'manage_shop_settings' ) || current_user_can( 'manage_woocommerce' ) ) && ! get_option('wpo_wcpdf_hide_nginx_notice') && ! $random_string ) {
			ob_start();
			?>
			<div class="error">
				<img src="<?php echo $this->plugin_url() . "/assets/images/nginx.svg"; ?>" style="margin-top:10px;">
				<?php /* translators: directory path */ ?>
				<p><?php printf( __( 'The PDF files in %s are not currently protected due to your site running on <strong>NGINX</strong>.', 'woocommerce-pdf-invoices-packing-slips' ), '<strong>' . $tmp_path . '</strong>' ); ?></p>
				<p><?php _e( 'To protect them, you must click the button below.', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
				<p><a class="button" href="<?php echo esc_url( add_query_arg( 'wpo_wcpdf_protect_pdf_directory', 'true' ) ); ?>"><?php _e( 'Generate random temporary folder name', 'woocommerce-pdf-invoices-packing-slips' ); ?></a></p>
				<p><a href="<?php echo esc_url( add_query_arg( 'wpo_wcpdf_hide_nginx_notice', 'true' ) ); ?>"><?php _e( 'Hide this message', 'woocommerce-pdf-invoices-packing-slips' ); ?></a></p>
			</div>
			<?php
			echo ob_get_clean();
		}

		// protect PDF directory
		if ( isset( $_GET['wpo_wcpdf_protect_pdf_directory'] ) ) {
			$this->main->generate_random_string();
			$old_path = $this->main->get_tmp_base( false );
			$new_path = $this->main->get_tmp_base();
			$this->main->copy_directory( $old_path, $new_path );
			// save option to hide nginx notice
			update_option( 'wpo_wcpdf_hide_nginx_notice', true );
			wp_redirect( 'admin.php?page=wpo_wcpdf_options_page' );
			exit;
		}

		// save option to hide nginx notice
		if ( isset( $_GET['wpo_wcpdf_hide_nginx_notice'] ) ) {
			update_option( 'wpo_wcpdf_hide_nginx_notice', true );
			wp_redirect( 'admin.php?page=wpo_wcpdf_options_page' );
			exit;
		}
	}

	/**
	 * Detect MailPoet.
	 * @return void
	 */
	public function mailpoet_mta_detected() {
		if( is_callable( array( '\\MailPoet\\Settings\\SettingsController', 'getInstance' ) ) ) {
			$settings = \MailPoet\Settings\SettingsController::getInstance();
			if( empty($settings) ) return;
			$send_transactional = $settings->get( 'send_transactional_emails', false );

			if( $send_transactional && ! get_option('wpo_wcpdf_hide_mailpoet_notice') ) {
				ob_start();
				?>
				<div class="error">
					<img src="<?php echo $this->plugin_url() . "/assets/images/mailpoet.svg"; ?>" style="margin-top:10px;">
					<p><?php _e( 'When sending emails with MailPoet 3 and the active sending method is <strong>MailPoet Sending Service</strong> or <strong>Your web host / web server</strong>, MailPoet does not include the <strong>WooCommerce PDF Invoices & Packing Slips</strong> attachments in the emails.', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
					<p><?php _e( 'To fix this you should select <strong>The default WordPress sending method (default)</strong> on the <strong>Advanced tab</strong>.', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
					<p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=mailpoet-settings#/advanced' ) ); ?>"><?php _e( 'Change MailPoet sending method to WordPress (default)', 'woocommerce-pdf-invoices-packing-slips' ); ?></a></p>
					<p><a href="<?php echo esc_url( add_query_arg( 'wpo_wcpdf_hide_mailpoet_notice', 'true' ) ); ?>"><?php _e( 'Hide this message', 'woocommerce-pdf-invoices-packing-slips' ); ?></a></p>
				</div>
				<?php
				echo ob_get_clean();
			}
		}

		// save option to hide mailpoet notice
		if ( isset( $_GET['wpo_wcpdf_hide_mailpoet_notice'] ) ) {
			update_option( 'wpo_wcpdf_hide_mailpoet_notice', true );
			wp_redirect( 'admin.php?page=wpo_wcpdf_options_page' );
			exit;
		}
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

} // class WPO_WCPDF

endif; // class_exists

/**
 * Returns the main instance of WooCommerce PDF Invoices & Packing Slips to prevent the need to use globals.
 *
 * @since  1.6
 * @return WPO_WCPDF
 */
function WPO_WCPDF() {
	return WPO_WCPDF::instance();
}

WPO_WCPDF(); // load plugin

// legacy class for plugin detecting
if ( !class_exists( 'WooCommerce_PDF_Invoices' ) ) {
	class WooCommerce_PDF_Invoices{
		public static $version;

		public function __construct() {
			self::$version = WPO_WCPDF()->version;
		}
	}
	new WooCommerce_PDF_Invoices();
}
