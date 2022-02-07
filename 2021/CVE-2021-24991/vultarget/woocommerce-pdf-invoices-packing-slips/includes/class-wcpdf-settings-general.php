<?php
namespace WPO\WC\PDF_Invoices;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Settings_General' ) ) :

class Settings_General {

	protected $option_name = 'wpo_wcpdf_settings_general';

	function __construct()	{
		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_action( 'wpo_wcpdf_settings_output_general', array( $this, 'output' ), 10, 1 );
		add_action( 'wpo_wcpdf_before_settings', array( $this, 'attachment_settings_hint' ), 10, 2 );
	}

	public function output( $section ) {
		settings_fields( $this->option_name );
		do_settings_sections( $this->option_name );

		submit_button();
	}

	public function init_settings() {
		$page = $option_group = $option_name = $this->option_name;

		$template_base_path = ( defined( 'WC_TEMPLATE_PATH' ) ? WC_TEMPLATE_PATH : $GLOBALS['woocommerce']->template_url );
		$theme_template_path = get_stylesheet_directory() . '/' . $template_base_path;
		$wp_content_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
		$theme_template_path = substr($theme_template_path, strpos($theme_template_path, $wp_content_dir)) . 'pdf/yourtemplate';
		$plugin_template_path = "{$wp_content_dir}/plugins/woocommerce-pdf-invoices-packing-slips/templates/Simple";

		$settings_fields = array(
			array(
				'type'		=> 'section',
				'id'		=> 'general_settings',
				'title'		=> __( 'General settings', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'section',
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'download_display',
				'title'		=> __( 'How do you want to view the PDF?', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'select',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'download_display',
					'options' 		=> array(
						'download'	=> __( 'Download the PDF' , 'woocommerce-pdf-invoices-packing-slips' ),
						'display'	=> __( 'Open the PDF in a new browser tab/window' , 'woocommerce-pdf-invoices-packing-slips' ),
					),
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'template_path',
				'title'		=> __( 'Choose a template', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'select',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'template_path',
					'options' 		=> $this->get_installed_templates_list(),
					/* translators: 1,2. template paths */
					'description'	=> sprintf( __( 'Want to use your own template? Copy all the files from <code>%1$s</code> to your (child) theme in <code>%2$s</code> to customize them' , 'woocommerce-pdf-invoices-packing-slips' ), $plugin_template_path, $theme_template_path),
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'paper_size',
				'title'		=> __( 'Paper size', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'select',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'paper_size',
					'options' 		=> apply_filters( 'wpo_wcpdf_template_settings_paper_size', array(
						'a4'		=> __( 'A4' , 'woocommerce-pdf-invoices-packing-slips' ),
						'letter'	=> __( 'Letter' , 'woocommerce-pdf-invoices-packing-slips' ),
					) ),
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'test_mode',
				'title'		=> __( 'Test mode', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'checkbox',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'		=> $option_name,
					'id'				=> 'test_mode',
					'description'		=> __( 'With test mode enabled, any document generated will always use the latest settings, rather than using the settings as configured at the time the document was first created.' , 'woocommerce-pdf-invoices-packing-slips' ) . '<br>'. __( '<strong>Note:</strong> invoice numbers and dates are not affected by this setting and will still be generated.' , 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'currency_font',
				'title'		=> __( 'Extended currency symbol support', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'checkbox',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'		=> $option_name,
					'id'				=> 'currency_font',
					'description'		=> __( 'Enable this if your currency symbol is not displaying properly' , 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'font_subsetting',
				'title'		=> __( 'Enable font subsetting', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'checkbox',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'		=> $option_name,
					'id'				=> 'font_subsetting',
					'description'		=> __( "Font subsetting can reduce file size by only including the characters that are used in the PDF, but limits the ability to edit PDF files later. Recommended if you're using an Asian font." , 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'header_logo',
				'title'		=> __( 'Shop header/logo', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'media_upload',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'					=> $option_name,
					'id'							=> 'header_logo',
					'uploader_title'				=> __( 'Select or upload your invoice header/logo', 'woocommerce-pdf-invoices-packing-slips' ),
					'uploader_button_text'			=> __( 'Set image', 'woocommerce-pdf-invoices-packing-slips' ),
					'remove_button_text'			=> __( 'Remove image', 'woocommerce-pdf-invoices-packing-slips' ),
					//'description'					=> __( '...', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'header_logo_height',
				'title'		=> __( 'Logo height', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'text_input',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'		=> $option_name,
					'id'				=> 'header_logo_height',
					'size'				=> '5',
					'placeholder'		=> '3cm',
					'description'		=> __( 'Enter the total height of the logo in mm, cm or in and use a dot for decimals.<br/>For example: 1.15in or 40mm', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'shop_name',
				'title'		=> __( 'Shop Name', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'text_input',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'shop_name',
					'size'			=> '72',
					'translatable'	=> true,
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'shop_address',
				'title'		=> __( 'Shop Address', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'textarea',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'shop_address',
					'width'			=> '72',
					'height'		=> '8',
					'translatable'	=> true,
					//'description'			=> __( '...', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),	
			array(
				'type'		=> 'setting',
				'id'		=> 'footer',
				'title'		=> __( 'Footer: terms & conditions, policies, etc.', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'textarea',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'footer',
					'width'			=> '72',
					'height'		=> '4',
					'translatable'	=> true,
					//'description'			=> __( '...', 'woocommerce-pdf-invoices-packing-slips' ),
				)
			),
			array(
				'type'		=> 'section',
				'id'		=> 'extra_template_fields',
				'title'		=> __( 'Extra template fields', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'custom_fields_section',
			),	
			array(
				'type'		=> 'setting',
				'id'		=> 'extra_1',
				'title'		=> __( 'Extra field 1', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'textarea',
				'section'	=> 'extra_template_fields',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'extra_1',
					'width'			=> '72',
					'height'		=> '8',
					'description'	=> __( 'This is footer column 1 in the <i>Modern (Premium)</i> template', 'woocommerce-pdf-invoices-packing-slips' ),
					'translatable'	=> true,
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'extra_2',
				'title'		=> __( 'Extra field 2', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'textarea',
				'section'	=> 'extra_template_fields',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'extra_2',
					'width'			=> '72',
					'height'		=> '8',
					'description'	=> __( 'This is footer column 2 in the <i>Modern (Premium)</i> template', 'woocommerce-pdf-invoices-packing-slips' ),
					'translatable'	=> true,
				)
			),
			array(
				'type'		=> 'setting',
				'id'		=> 'extra_3',
				'title'		=> __( 'Extra field 3', 'woocommerce-pdf-invoices-packing-slips' ),
				'callback'	=> 'textarea',
				'section'	=> 'extra_template_fields',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'extra_3',
					'width'			=> '72',
					'height'		=> '8',
					'description'	=> __( 'This is footer column 3 in the <i>Modern (Premium)</i> template', 'woocommerce-pdf-invoices-packing-slips' ),
					'translatable'	=> true,
				)
			),
		);

		// allow plugins to alter settings fields
		$settings_fields = apply_filters( 'wpo_wcpdf_settings_fields_general', $settings_fields, $page, $option_group, $option_name );
		WPO_WCPDF()->settings->add_settings_fields( $settings_fields, $page, $option_group, $option_name );
		return;
	}

	public function attachment_settings_hint( $active_tab, $active_section ) {
		// save or check option to hide attachments settings hint
		if ( isset( $_GET['wpo_wcpdf_hide_attachments_hint'] ) ) {
			update_option( 'wpo_wcpdf_hide_attachments_hint', true );
			$hide_hint = true;
		} else {
			$hide_hint = get_option( 'wpo_wcpdf_hide_attachments_hint' );
		}

		if ( $active_tab == 'general' && !$hide_hint ) {
			$documents = WPO_WCPDF()->documents->get_documents();

			foreach ($documents as $document) {
				if ( $document->get_type() == 'invoice' ) {
					$invoice_email_ids = $document->get_attach_to_email_ids();
					if (empty($invoice_email_ids)) {
						include_once( WPO_WCPDF()->plugin_path() . '/includes/views/attachment-settings-hint.php' );
					}
				}
			}
		}
	}

	public function get_installed_templates_list() {
		$installed_templates = WPO_WCPDF()->settings->get_installed_templates();
		$template_list = array();
		foreach ( $installed_templates as $path => $template_id ) {
			$template_name = basename( $template_id );
			$group = dirname( $template_id );
			switch ( $group ) {
				case 'default':
				case 'premium_plugin':
					// no suffix
					break;
				case 'theme':
				default:
					$template_name = sprintf( '%s (%s)', $template_name, __( 'Custom', 'woocommerce-pdf-invoices-packing-slips' ) );
					break;
			}
			$template_list[$template_id] = $template_name;
		}
		return $template_list;
	}

	/**
	 * List templates in plugin folder, theme folder & child theme folder
	 * @return array		template path => template name
	 */
	public function find_templates() {
		$installed_templates = array();

		// get base paths
		$template_base_path = ( function_exists( 'WC' ) && is_callable( 'WC', 'template_path' ) ) ? WC()->template_path() : 'woocommerce/';
		$template_base_path = untrailingslashit( $template_base_path );
		$template_paths = array (
			// note the order: child-theme before theme, so that array_unique filters out parent doubles
			'default'		=> WPO_WCPDF()->plugin_path() . '/templates/',
			'child-theme'	=> get_stylesheet_directory() . "/{$template_base_path}/pdf/",
			'theme'			=> get_template_directory() . "/{$template_base_path}/pdf/",
		);

		$template_paths = apply_filters( 'wpo_wcpdf_template_paths', $template_paths );

		if ( defined('WP_CONTENT_DIR') && strpos( WP_CONTENT_DIR, ABSPATH ) !== false ) {
			$forwardslash_basepath = str_replace('\\','/', ABSPATH);
		} else {
			$forwardslash_basepath = str_replace('\\','/', WP_CONTENT_DIR);
		}

		foreach ($template_paths as $template_source => $template_path) {
			$dirs = (array) glob( $template_path . '*' , GLOB_ONLYDIR);
			
			foreach ($dirs as $dir) {
				// we're stripping abspath to make the plugin settings more portable
				$forwardslash_dir = str_replace('\\','/', $dir);
				$installed_templates[ str_replace( $forwardslash_basepath, '', $forwardslash_dir ) ] = basename($dir);
			}
		}

		// remove parent doubles
		$installed_templates = array_unique($installed_templates);

		if (empty($installed_templates)) {
			// fallback to Simple template for servers with glob() disabled
			$simple_template_path = str_replace( ABSPATH, '', $template_paths['default'] . 'Simple' );
			$installed_templates[$simple_template_path] = 'Simple';
		}

		return apply_filters( 'wpo_wcpdf_templates', $installed_templates );
	}

}

endif; // class_exists

return new Settings_General();