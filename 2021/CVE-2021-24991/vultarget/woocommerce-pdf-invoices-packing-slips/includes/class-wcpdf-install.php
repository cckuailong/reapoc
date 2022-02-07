<?php
namespace WPO\WC\PDF_Invoices;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Install' ) ) :

class Install {
	
	function __construct()	{
		// run lifecycle methods
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			add_action( 'wp_loaded', array( $this, 'do_install' ) );
		}
	}

	/** Lifecycle methods *******************************************************
	 * Because register_activation_hook only runs when the plugin is manually
	 * activated by the user, we're checking the current version against the
	 * version stored in the database
	****************************************************************************/

	/**
	 * Handles version checking
	 */
	public function do_install() {
		// only install when woocommerce is active
		if ( !WPO_WCPDF()->is_woocommerce_activated() ) {
			return;
		}

		$version_setting = 'wpo_wcpdf_version';
		$installed_version = get_option( $version_setting );

		// installed version lower than plugin version?
		if ( version_compare( $installed_version, WPO_WCPDF_VERSION, '<' ) ) {

			if ( ! $installed_version ) {
				$this->install();
			} else {
				$this->upgrade( $installed_version );
			}

			// new version number
			update_option( $version_setting, WPO_WCPDF_VERSION );
		} elseif ( $installed_version && version_compare( $installed_version, WPO_WCPDF_VERSION, '>' ) ) {
			$this->downgrade( $installed_version );
			// downgrade version number
			update_option( $version_setting, WPO_WCPDF_VERSION );
		}
	}


	/**
	 * Plugin install method. Perform any installation tasks here
	 */
	protected function install() {
		// only install when php 5.6 or higher
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			return;
		}

		// check if upgrading from versionless (1.4.14 and older)
		if ( get_option('wpo_wcpdf_general_settings') ) {
			$this->upgrade( 'versionless' );
			return;
		}

		// Get tmp folders
		$tmp_base = WPO_WCPDF()->main->get_tmp_base();

		// check if tmp folder exists => if not, initialize 
		if ( ! @is_dir( $tmp_base ) || ! wp_is_writable( $tmp_base ) ) {
			WPO_WCPDF()->main->init_tmp();
		}

		// Unsupported currency symbols 
		$unsupported_symbols = array (
			'AED',
			'AFN',
			'BDT',
			'BHD',
			'BTC',
			'CRC',
			'DZD',
			'GEL',
			'GHS',
			'ILS',
			'INR',
			'IQD',
			'IRR',
			'IRT',
			'JOD',
			'KHR',
			'KPW',
			'KRW',
			'KWD',
			'LAK',
			'LBP',
			'LKR',
			'LYD',
			'MAD',
			'MNT',
			'MUR',
			'MVR',
			'NPR',
			'OMR',
			'PHP',
			'PKR',
			'PYG',
			'QAR',
			'RUB',
			'SAR',
			'SCR',
			'SDG',
			'SYP',
			'THB',
			'TND',
			'TRY',
			'UAH',
			'YER',
		);

		// set default settings
		$settings_defaults = array(
			'wpo_wcpdf_settings_general' => array(
				'download_display'			=> 'display',
				'template_path'				=> WPO_WCPDF()->plugin_path() . '/templates/Simple',
				'currency_font'				=> ( in_array( get_woocommerce_currency(), $unsupported_symbols ) ) ? 1 : '',
				'paper_size'				=> 'a4',
				// 'header_logo'				=> '',
				// 'shop_name'					=> array(),
				// 'shop_address'				=> array(),
				// 'footer'					=> array(),
				// 'extra_1'					=> array(),
				// 'extra_2'					=> array(),
				// 'extra_3'					=> array(),
			),
			'wpo_wcpdf_documents_settings_invoice' => array(
				'enabled'					=> 1,
				// 'attach_to_email_ids'		=> array(),
				// 'display_shipping_address'	=> '',
				// 'display_email'				=> '',
				// 'display_phone'				=> '',
				// 'display_date'				=> '',
				// 'display_number'			=> '',
				// 'number_format'				=> array(),
				// 'reset_number_yearly'		=> '',
				// 'my_account_buttons'		=> '',
				// 'invoice_number_column'		=> '',
				// 'disable_free'				=> '',
			),
			'wpo_wcpdf_documents_settings_packing-slip' => array(
				'enabled'					=> 1,
				// 'display_billing_address'	=> '',
				// 'display_email'				=> '',
				// 'display_phone'				=> '',
			),
			'wpo_wcpdf_settings_debug' => array(
				// 'legacy_mode'				=> '',
				// 'enable_debug'				=> '',
				// 'html_output'				=> '',
				// 'html_output'				=> '',
				'enable_cleanup'				=> 1,
				'cleanup_days'					=> 7,
			),
		);
		foreach ($settings_defaults as $option => $defaults) {
			add_option( $option, $defaults );
		}

		// set transient for wizard notification
		set_transient( 'wpo_wcpdf_new_install', 'yes', DAY_IN_SECONDS * 2 );
	}

	/**
	 * Plugin upgrade method.  Perform any required upgrades here
	 *
	 * @param string $installed_version the currently installed ('old') version
	 */
	protected function upgrade( $installed_version ) {
		// only upgrade when php 5.6 or higher
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			return;
		}

		// sync fonts on every upgrade!
		$tmp_base = WPO_WCPDF()->main->get_tmp_base();

		// get fonts folder path
		$font_path = WPO_WCPDF()->main->get_tmp_path( 'fonts' );

		// check if tmp folder exists => if not, initialize 
		if ( ! @is_dir( $tmp_base ) || ! wp_is_writable( $tmp_base ) || ! @is_dir( $font_path ) || ! wp_is_writable( $font_path ) ) {
			WPO_WCPDF()->main->init_tmp();
		} else {
			// don't try merging fonts with local when updating pre 2.0
			$pre_2 = ( $installed_version == 'versionless' || version_compare( $installed_version, '2.0-dev', '<' ) );
			$merge_with_local = $pre_2 ? false : true;
			WPO_WCPDF()->main->copy_fonts( $font_path, $merge_with_local );
		}
		
		// 1.5.28 update: copy next invoice number to separate setting
		if ( $installed_version == 'versionless' || version_compare( $installed_version, '1.5.28', '<' ) ) {
			$template_settings = get_option( 'wpo_wcpdf_template_settings' );
			$next_invoice_number = isset($template_settings['next_invoice_number'])?$template_settings['next_invoice_number']:'';
			update_option( 'wpo_wcpdf_next_invoice_number', $next_invoice_number );
		}

		// 2.0-dev update: reorganize settings
		if ( $installed_version == 'versionless' || version_compare( $installed_version, '2.0-dev', '<' ) ) {
			$old_settings = array(
				'wpo_wcpdf_general_settings'	=> get_option( 'wpo_wcpdf_general_settings' ),
				'wpo_wcpdf_template_settings'	=> get_option( 'wpo_wcpdf_template_settings' ),
				'wpo_wcpdf_debug_settings'		=> get_option( 'wpo_wcpdf_debug_settings' ),
			);

			// combine invoice number formatting in array
			$old_settings['wpo_wcpdf_template_settings']['invoice_number_formatting'] = array();
			$format_option_keys = array('padding','suffix','prefix');
			foreach ($format_option_keys as $format_option_key) {
				if (isset($old_settings['wpo_wcpdf_template_settings']["invoice_number_formatting_{$format_option_key}"])) {
					$old_settings['wpo_wcpdf_template_settings']['invoice_number_formatting'][$format_option_key] = $old_settings['wpo_wcpdf_template_settings']["invoice_number_formatting_{$format_option_key}"];
				}
			}

			// convert abbreviated email_ids
			if (isset($old_settings['wpo_wcpdf_general_settings']['email_pdf'])) {
				foreach ($old_settings['wpo_wcpdf_general_settings']['email_pdf'] as $email_id => $value) {
					if ($email_id == 'completed' || $email_id == 'processing') {
						$old_settings['wpo_wcpdf_general_settings']['email_pdf']["customer_{$email_id}_order"] = $value;
						unset($old_settings['wpo_wcpdf_general_settings']['email_pdf'][$email_id]);
					}
				}
			}

			// Migrate template path
			// forward slash for consistency/compatibility
			$template_path = str_replace('\\','/', $old_settings['wpo_wcpdf_template_settings']['template_path']);
			// strip abspath (forward slashed) if included
			$template_path = str_replace( str_replace('\\','/', ABSPATH), '', $template_path );
			// strip pdf subfolder from templates path
			$template_path = str_replace( '/templates/pdf/', '/templates/', $template_path );
			$old_settings['wpo_wcpdf_template_settings']['template_path'] = $template_path;

			// map new settings to old
			$settings_map = array(
				'wpo_wcpdf_settings_general' => array(
					'download_display'			=> array( 'wpo_wcpdf_general_settings' => 'download_display' ),
					'template_path'				=> array( 'wpo_wcpdf_template_settings' => 'template_path' ),
					'currency_font'				=> array( 'wpo_wcpdf_template_settings' => 'currency_font' ),
					'paper_size'				=> array( 'wpo_wcpdf_template_settings' => 'paper_size' ),
					'header_logo'				=> array( 'wpo_wcpdf_template_settings' => 'header_logo' ),
					'shop_name'					=> array( 'wpo_wcpdf_template_settings' => 'shop_name' ),
					'shop_address'				=> array( 'wpo_wcpdf_template_settings' => 'shop_address' ),
					'footer'					=> array( 'wpo_wcpdf_template_settings' => 'footer' ),
					'extra_1'					=> array( 'wpo_wcpdf_template_settings' => 'extra_1' ),
					'extra_2'					=> array( 'wpo_wcpdf_template_settings' => 'extra_2' ),
					'extra_3'					=> array( 'wpo_wcpdf_template_settings' => 'extra_3' ),
				),
				'wpo_wcpdf_documents_settings_invoice' => array(
					'attach_to_email_ids'		=> array( 'wpo_wcpdf_general_settings' => 'email_pdf' ),
					'display_shipping_address'	=> array( 'wpo_wcpdf_template_settings' => 'invoice_shipping_address' ),
					'display_email'				=> array( 'wpo_wcpdf_template_settings' => 'invoice_email' ),
					'display_phone'				=> array( 'wpo_wcpdf_template_settings' => 'invoice_phone' ),
					'display_date'				=> array( 'wpo_wcpdf_template_settings' => 'display_date' ),
					'display_number'			=> array( 'wpo_wcpdf_template_settings' => 'display_number' ),
					'number_format'				=> array( 'wpo_wcpdf_template_settings' => 'invoice_number_formatting' ),
					'reset_number_yearly'		=> array( 'wpo_wcpdf_template_settings' => 'yearly_reset_invoice_number' ),
					'my_account_buttons'		=> array( 'wpo_wcpdf_general_settings' => 'my_account_buttons' ),
					'invoice_number_column'		=> array( 'wpo_wcpdf_general_settings' => 'invoice_number_column' ),
					'disable_free'				=> array( 'wpo_wcpdf_general_settings' => 'disable_free' ),
				),
				'wpo_wcpdf_documents_settings_packing-slip' => array(
					'display_billing_address'	=> array( 'wpo_wcpdf_template_settings' => 'packing_slip_billing_address' ),
					'display_email'				=> array( 'wpo_wcpdf_template_settings' => 'packing_slip_email' ),
					'display_phone'				=> array( 'wpo_wcpdf_template_settings' => 'packing_slip_phone' ),
				),
				'wpo_wcpdf_settings_debug' => array(
					'enable_debug'				=> array( 'wpo_wcpdf_debug_settings' => 'enable_debug' ),
					'html_output'				=> array( 'wpo_wcpdf_debug_settings' => 'html_output' ),
				),
			);
			
			// walk through map
			foreach ($settings_map as $new_option => $new_settings_keys) {
				${$new_option} = array();
				foreach ($new_settings_keys as $new_key => $old_setting ) {
					$old_key = reset($old_setting);
					$old_option = key($old_setting);
					if (!empty($old_settings[$old_option][$old_key])) {
						// turn translatable fields into array
						$translatable_fields = array('shop_name','shop_address','footer','extra_1','extra_2','extra_3');
						if (in_array($new_key, $translatable_fields)) {
							${$new_option}[$new_key] = array( 'default' => $old_settings[$old_option][$old_key] );
						} else {
							${$new_option}[$new_key] = $old_settings[$old_option][$old_key];
						}
					}
				}

				// auto enable invoice & packing slip
				$enabled = array( 'wpo_wcpdf_documents_settings_invoice', 'wpo_wcpdf_documents_settings_packing-slip' );
				if ( in_array( $new_option, $enabled ) ) {
					${$new_option}['enabled'] = 1;
				}

				// auto enable legacy mode
				if ( $new_option == 'wpo_wcpdf_settings_debug' ) {
					${$new_option}['legacy_mode'] = 1;
				}

				// merge with existing settings
				${$new_option."_old"} = get_option( $new_option, ${$new_option} ); // second argument loads new as default in case the settings did not exist yet
				${$new_option} = (array) ${$new_option} + (array) ${$new_option."_old"}; // duplicate options take new options as default

				// store new option values
				update_option( $new_option, ${$new_option} );
			}
		}

		// 2.0-beta-2 update: copy next number to separate db store
		if ( version_compare( $installed_version, '2.0-beta-2', '<' ) ) {
			// load number store class (just in case)
			include_once( WPO_WCPDF()->plugin_path() . '/includes/documents/class-wcpdf-sequential-number-store.php' );

			$next_number = get_option( 'wpo_wcpdf_next_invoice_number' );
			if (!empty($next_number)) {
				$number_store = new \WPO\WC\PDF_Invoices\Documents\Sequential_Number_Store( 'invoice_number' );
				$number_store->set_next( (int) $next_number );
			}
			// we're not deleting this option yet to make downgrading possible
			// delete_option( 'wpo_wcpdf_next_invoice_number' ); // clean up after ourselves
		}

		// 2.1.9: set cleanup defaults
		if ( $installed_version == 'versionless' || version_compare( $installed_version, '2.1.9', '<' ) ) {
			$debug_settings = get_option( 'wpo_wcpdf_settings_debug', array() );
			$debug_settings['enable_cleanup'] = 1;
			$debug_settings['cleanup_days'] = 7;
			update_option( 'wpo_wcpdf_settings_debug', $debug_settings );
		}

		// 2.10.0-dev: migrate template path to template ID
		if ( version_compare( $installed_version, '2.10.0-dev', '<' ) ) {
			if ( ! empty( WPO_WCPDF()->settings ) && is_callable( array( WPO_WCPDF()->settings, 'maybe_migrate_template_paths' ) ) ) {
				WPO_WCPDF()->settings->maybe_migrate_template_paths();
			}
		}
	}

	/**
	 * Plugin downgrade method.  Perform any required downgrades here
	 * 
	 *
	 * @param string $installed_version the currently installed ('old') version (actually higher since this is a downgrade)
	 */
	protected function downgrade( $installed_version ) {
		// make sure fonts match with version: copy from plugin folder
		$tmp_base = WPO_WCPDF()->main->get_tmp_base();

		// make sure we have the fonts directory
		$font_path = WPO_WCPDF()->main->get_tmp_path( 'fonts' );

		// don't continue if we don't have an upload dir
		if ($tmp_base === false) {
			return false;
		}

		// check if tmp folder exists => if not, initialize 
		if ( ! @is_dir( $tmp_base ) || ! wp_is_writable( $tmp_base ) || ! @is_dir( $font_path ) || ! wp_is_writable( $font_path ) ) {
			WPO_WCPDF()->main->init_tmp();
		} else {
			WPO_WCPDF()->main->copy_fonts( $font_path );
		}
	}

}

endif; // class_exists

return new Install();
