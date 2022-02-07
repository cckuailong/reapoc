<?php
namespace WPO\WC\PDF_Invoices;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Settings_Debug' ) ) :

class Settings_Debug {

	function __construct()	{
		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_action( 'wpo_wcpdf_settings_output_debug', array( $this, 'output' ), 10, 1 );
		add_action( 'wpo_wcpdf_after_settings_page', array( $this, 'debug_tools' ), 10, 2 );

		// yes, we're hiring!
		if (defined('WP_DEBUG') && WP_DEBUG) {
			add_action( 'wpo_wcpdf_before_settings_page', array( $this, 'work_at_wpovernight' ), 10, 2 );
		} else {
			add_action( 'wpo_wcpdf_after_settings_page', array( $this, 'work_at_wpovernight' ), 30, 2 );			
		}

		add_action( 'wpo_wcpdf_after_settings_page', array( $this, 'dompdf_status' ), 20, 2 );
	}

	public function output( $section ) {
		settings_fields( "wpo_wcpdf_settings_debug" );
		do_settings_sections( "wpo_wcpdf_settings_debug" );

		submit_button();
	}

	public function debug_tools( $tab, $section ) {
		if ($tab !== 'debug') {
			return;
		}
		?>
		<div>
			<?php if( ! WPO_WCPDF()->main->get_random_string() ) : ?>
			<p>
				<form method="post">
					<?php wp_nonce_field( 'wpo_wcpdf_debug_tools_action', 'security' ); ?>
					<input type="hidden" name="wpo_wcpdf_debug_tools_action" value="generate_random_string">
					<input type="submit" name="submit" id="submit" class="button" value="<?php _e( 'Generate random temporary folder name', 'woocommerce-pdf-invoices-packing-slips' ); ?>">
					<?php
					if ( !empty($_POST) && isset($_POST['wpo_wcpdf_debug_tools_action']) && $_POST['wpo_wcpdf_debug_tools_action'] == 'generate_random_string' ) {
						// check permissions
						if ( !check_admin_referer( 'wpo_wcpdf_debug_tools_action', 'security' ) ) {
							return;
						}

						WPO_WCPDF()->main->generate_random_string();
						$old_path = WPO_WCPDF()->main->get_tmp_base( false );
						$new_path = WPO_WCPDF()->main->get_tmp_base();
						WPO_WCPDF()->main->copy_directory( $old_path, $new_path );
						/* translators: directory path */
						printf('<div class="notice notice-success"><p>%s</p></div>', sprintf( __( 'Temporary folder moved to %s', 'woocommerce-pdf-invoices-packing-slips' ), '<code>'.$new_path.'</code>' ) ); 
					}
					?>
				</form>
			</p>
			<?php endif; ?>
			<p>
				<form method="post">
					<?php wp_nonce_field( 'wpo_wcpdf_debug_tools_action', 'security' ); ?>
					<input type="hidden" name="wpo_wcpdf_debug_tools_action" value="install_fonts">
					<input type="submit" name="submit" id="submit" class="button" value="<?php _e( 'Reinstall fonts', 'woocommerce-pdf-invoices-packing-slips' ); ?>">
					<?php
					if ( !empty($_POST) && isset($_POST['wpo_wcpdf_debug_tools_action']) && $_POST['wpo_wcpdf_debug_tools_action'] == 'install_fonts' ) {
						// check permissions
						if ( !check_admin_referer( 'wpo_wcpdf_debug_tools_action', 'security' ) ) {
							return;
						}

						$font_path = WPO_WCPDF()->main->get_tmp_path( 'fonts' );

						// clear folder first
						if ( function_exists("glob") && $files = glob( $font_path.'/*.*' ) ) {
							$exclude_files = array( 'index.php', '.htaccess' );
							foreach($files as $file) {
								if( is_file($file) && !in_array( basename($file), $exclude_files ) ) {
									unlink($file);
								}
							}
						}

						WPO_WCPDF()->main->copy_fonts( $font_path );
						printf('<div class="notice notice-success"><p>%s</p></div>', __( 'Fonts reinstalled!', 'woocommerce-pdf-invoices-packing-slips' ) );
					}
					?>
				</form>
			</p>
			<p>
				<form method="post">
					<?php wp_nonce_field( 'wpo_wcpdf_debug_tools_action', 'security' ); ?>
					<input type="hidden" name="wpo_wcpdf_debug_tools_action" value="clear_tmp">
					<input type="submit" name="submit" id="submit" class="button" value="<?php _e( 'Remove temporary files', 'woocommerce-pdf-invoices-packing-slips' ); ?>">
					<?php
					if ( !empty($_POST) && isset($_POST['wpo_wcpdf_debug_tools_action']) && $_POST['wpo_wcpdf_debug_tools_action'] == 'clear_tmp' ) {
						// check permissions
						if ( !check_admin_referer( 'wpo_wcpdf_debug_tools_action', 'security' ) ) {
							return;
						}
						$tmp_path = WPO_WCPDF()->main->get_tmp_path('attachments');

						if ( !function_exists("glob") ) {
							// glob is disabled
							printf('<div class="notice notice-error"><p>%s<br><code>%s</code></p></div>', __( "Unable to read temporary folder contents!", 'woocommerce-pdf-invoices-packing-slips' ), $tmp_path);
						} else {
							$success = 0;
							$error = 0;
							if ( $files = glob($tmp_path.'*.pdf') ) { // get all pdf files
								foreach($files as $file) {
									if(is_file($file)) {
										// delete file
										if ( unlink($file) === true ) {
											$success++;
										} else {
											$error++;
										}
									}
								}

								if ($error > 0) {
									/* translators: 1,2. file count  */
									$message =  sprintf( __( 'Unable to delete %1$d files! (deleted %2$d)', 'woocommerce-pdf-invoices-packing-slips' ), $error, $success);
									printf('<div class="notice notice-error"><p>%s</p></div>', $message);
								} else {
									/* translators: file count */
									$message =  sprintf( __( 'Successfully deleted %d files!', 'woocommerce-pdf-invoices-packing-slips' ), $success );
									printf('<div class="notice notice-success"><p>%s</p></div>', $message);
								}
							} else {
								printf('<div class="notice notice-success"><p>%s</p></div>', __( 'Nothing to delete!', 'woocommerce-pdf-invoices-packing-slips' ) );
							}
						}
					}
					?>
				</form>
			</p>
			<p>
				<form method="post">
					<?php wp_nonce_field( 'wpo_wcpdf_debug_tools_action', 'security' ); ?>
					<input type="hidden" name="wpo_wcpdf_debug_tools_action" value="delete_legacy_settings">
					<input type="submit" name="submit" id="submit" class="button" value="<?php _e( 'Delete legacy (1.X) settings', 'woocommerce-pdf-invoices-packing-slips' ); ?>">
					<?php
					if ( !empty($_POST) && isset($_POST['wpo_wcpdf_debug_tools_action']) && $_POST['wpo_wcpdf_debug_tools_action'] == 'delete_legacy_settings' ) {
						// check permissions
						if ( !check_admin_referer( 'wpo_wcpdf_debug_tools_action', 'security' ) ) {
							return;
						}
						// delete options
						delete_option( 'wpo_wcpdf_general_settings' );
						delete_option( 'wpo_wcpdf_template_settings' );
						delete_option( 'wpo_wcpdf_debug_settings' );
						// and delete cache of these options, just in case...
						wp_cache_delete( 'wpo_wcpdf_general_settings','options' );
						wp_cache_delete( 'wpo_wcpdf_template_settings','options' );
						wp_cache_delete( 'wpo_wcpdf_debug_settings','options' );

						printf('<div class="notice notice-success"><p>%s</p></div>', __( 'Legacy settings deleted!', 'woocommerce-pdf-invoices-packing-slips' ) );
					}
					?>
				</form>
			</p>
		</div>
		<br>
		<?php
	}

	public function work_at_wpovernight( $tab, $section ) {
		if ($tab === 'debug') {
			include( WPO_WCPDF()->plugin_path() . '/includes/views/work-at-wpovernight.php' );		
		}
	}

	public function dompdf_status( $tab, $section ) {
		if ($tab === 'debug') {
			include( WPO_WCPDF()->plugin_path() . '/includes/views/dompdf-status.php' );
		}
	}

	public function init_settings() {
		// Register settings.
		$page = $option_group = $option_name = 'wpo_wcpdf_settings_debug';

		$settings_fields = array(
			array(
				'type'			=> 'section',
				'id'			=> 'debug_settings',
				'title'			=> __( 'Debug settings', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'section',
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'legacy_mode',
				'title'			=> __( 'Legacy mode', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'legacy_mode',
					'description'	=> __( "Legacy mode ensures compatibility with templates and filters from previous versions.", 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'legacy_textdomain',
				'title'			=> __( 'Legacy textdomain fallback', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'legacy_textdomain',
					'description'	=> __( "Legacy textdomain fallback ensures compatibility with translation files from versions prior to 2017-05-15.", 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'guest_access',
				'title'			=> __( 'Allow guest access', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'		=> $option_name,
					'id'				=> 'guest_access',
					'description'		=> __( 'Enable this to allow customers that purchase without an account to access their PDF with a unique key', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'calculate_document_numbers',
				'title'			=> __( 'Calculate document numbers (slow)', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'calculate_document_numbers',
					'description'	=> __( "Document numbers (such as invoice numbers) are generated using AUTO_INCREMENT by default. Use this setting if your database auto increments with more than 1.", 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'enable_debug',
				'title'			=> __( 'Enable debug output', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'enable_debug',
					'description'	=> __( "Enable this option to output plugin errors if you're getting a blank page or other PDF generation issues", 'woocommerce-pdf-invoices-packing-slips' ) . '<br>' .
									   __( '<b>Caution!</b> This setting may reveal errors (from other plugins) in other places on your site too, therefor this is not recommended to leave it enabled on live sites.', 'woocommerce-pdf-invoices-packing-slips' ) . ' ' .
					                   __( 'You can also add <code>&debug=true</code> to the URL to apply this on a per-order basis.', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'enable_cleanup',
				'title'			=> __( 'Enable automatic cleanup', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox_text_input',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'		=> $option_name,
					'id'				=> 'enable_cleanup',
					'disabled'			=> ( !function_exists("glob") || !function_exists('filemtime') ) ? 1 : NULL,
					/* translators: number of days */
					'text_input_wrap'	=> __( "every %s days", 'woocommerce-pdf-invoices-packing-slips' ),
					'text_input_size'	=> 4,
					'text_input_id'		=> 'cleanup_days',
					'text_input_default'=> 7,
					'description'		=> ( function_exists("glob") && function_exists('filemtime') ) ?
										   __( "Automatically clean up PDF files stored in the temporary folder (used for email attachments)", 'woocommerce-pdf-invoices-packing-slips' ) :
										   __( '<b>Disabled:</b> The PHP functions glob and filemtime are required for automatic cleanup but not enabled on your server.', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'html_output',
				'title'			=> __( 'Output to HTML', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'html_output',
					'description'	=> __( 'Send the template output as HTML to the browser instead of creating a PDF.', 'woocommerce-pdf-invoices-packing-slips' ) . ' ' .
					                   __( 'You can also add <code>&output=html</code> to the URL to apply this on a per-order basis.', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'use_html5_parser',
				'title'			=> __( 'Use alternative HTML5 parser to parse HTML', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'use_html5_parser',
				)
			),
			array(
				'type'			=> 'setting',
				'id'			=> 'log_to_order_notes',
				'title'			=> __( 'Log to order notes', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'		=> 'checkbox',
				'section'		=> 'debug_settings',
				'args'			=> array(
					'option_name'	=> $option_name,
					'id'			=> 'log_to_order_notes',
					'description'	=> __( 'Log PDF document creation to order notes.', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
		);

		// allow plugins to alter settings fields
		$settings_fields = apply_filters( 'wpo_wcpdf_settings_fields_debug', $settings_fields, $page, $option_group, $option_name );
		WPO_WCPDF()->settings->add_settings_fields( $settings_fields, $page, $option_group, $option_name );
		return;
	}

}

endif; // class_exists

return new Settings_Debug();