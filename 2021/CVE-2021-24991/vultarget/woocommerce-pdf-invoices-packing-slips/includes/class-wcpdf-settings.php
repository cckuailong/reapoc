<?php
namespace WPO\WC\PDF_Invoices;

use WPO\WC\PDF_Invoices\Documents\Sequential_Number_Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Settings' ) ) :

class Settings {
	public $options_page_hook;
	private $installed_templates = array();
	private $installed_templates_cache = array();
	
	function __construct()	{
		$this->callbacks = include( 'class-wcpdf-settings-callbacks.php' );

		// include settings classes
		$this->general = include( 'class-wcpdf-settings-general.php' );
		$this->documents = include( 'class-wcpdf-settings-documents.php' );
		$this->debug = include( 'class-wcpdf-settings-debug.php' );


		// Settings menu item
		add_action( 'admin_menu', array( $this, 'menu' ), 999 ); // Add menu
		// Links on plugin page
		add_filter( 'plugin_action_links_'.WPO_WCPDF()->plugin_basename, array( $this, 'add_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_support_links' ), 10, 2 );

		// settings capabilities
		add_filter( 'option_page_capability_wpo_wcpdf_general_settings', array( $this, 'settings_capabilities' ) );

		$this->general_settings		= get_option('wpo_wcpdf_settings_general');
		$this->debug_settings		= get_option('wpo_wcpdf_settings_debug');

		// admin notice for auto_increment_increment
		// add_action( 'admin_notices', array( $this, 'check_auto_increment_increment') );

		// AJAX set number store
		add_action( 'wp_ajax_wpo_wcpdf_set_next_number', array( $this, 'set_number_store' ) );

		// AJAX get header logo setting HTML
		add_action( 'wp_ajax_wpo_wcpdf_get_media_upload_setting_html', array( $this, 'get_media_upload_setting_html' ) );

		// refresh template path cache each time the general settings are updated
		add_action( "update_option_wpo_wcpdf_settings_general", array( $this, 'general_settings_updated' ), 10, 3 );
		// migrate old template paths to template IDs before loading settings page
		add_action( 'wpo_wcpdf_settings_output_general', array( $this, 'maybe_migrate_template_paths' ), 9, 1 );
	}

	public function menu() {
		$parent_slug = 'woocommerce';
		
		$this->options_page_hook = add_submenu_page(
			$parent_slug,
			__( 'PDF Invoices', 'woocommerce-pdf-invoices-packing-slips' ),
			__( 'PDF Invoices', 'woocommerce-pdf-invoices-packing-slips' ),
			'manage_woocommerce',
			'wpo_wcpdf_options_page',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Add settings link to plugins page
	 */
	public function add_settings_link( $links ) {
		$action_links = array(
			'settings' => '<a href="admin.php?page=wpo_wcpdf_options_page">'. __( 'Settings', 'woocommerce' ) . '</a>',
		);
		
		return array_merge( $action_links, $links );
	}
	
	/**
	 * Add various support links to plugin page
	 * after meta (version, authors, site)
	 */
	public function add_support_links( $links, $file ) {
		if ( $file == WPO_WCPDF()->plugin_basename ) {
			$row_meta = array(
				'docs'    => '<a href="https://docs.wpovernight.com/topic/woocommerce-pdf-invoices-packing-slips/" target="_blank" title="' . __( 'Documentation', 'woocommerce-pdf-invoices-packing-slips' ) . '">' . __( 'Documentation', 'woocommerce-pdf-invoices-packing-slips' ) . '</a>',
				'support' => '<a href="https://wordpress.org/support/plugin/woocommerce-pdf-invoices-packing-slips" target="_blank" title="' . __( 'Support Forum', 'woocommerce-pdf-invoices-packing-slips' ) . '">' . __( 'Support Forum', 'woocommerce-pdf-invoices-packing-slips' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	function check_auto_increment_increment() {
		global $wpdb;
		$row = $wpdb->get_row("SHOW VARIABLES LIKE 'auto_increment_increment'");
		if ( !empty($row) && !empty($row->Value) && $row->Value != 1 ) {
			/* translators: database row value */
			$error = sprintf( __( "<strong>Warning!</strong> Your database has an AUTO_INCREMENT step size of %s, your invoice numbers may not be sequential. Enable the 'Calculate document numbers (slow)' setting in the Status tab to use an alternate method." , 'woocommerce-pdf-invoices-packing-slips' ), $row->Value );
			printf( '<div class="error"><p>%s</p></div>', $error );
		}
	}


	public function settings_page() {
		$settings_tabs = apply_filters( 'wpo_wcpdf_settings_tabs', array (
				'general'	=> __('General', 'woocommerce-pdf-invoices-packing-slips' ),
				'documents'	=> __('Documents', 'woocommerce-pdf-invoices-packing-slips' ),
			)
		);

		// add status tab last in row
		$settings_tabs['debug'] = __('Status', 'woocommerce-pdf-invoices-packing-slips' );

		$active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'general';
		$active_section = isset( $_GET[ 'section' ] ) ? sanitize_text_field( $_GET[ 'section' ] ) : '';

		include('views/wcpdf-settings-page.php');
	}

	public function add_settings_fields( $settings_fields, $page, $option_group, $option_name ) {
		foreach ( $settings_fields as $settings_field ) {
			if (!isset($settings_field['callback'])) {
				continue;
			} elseif ( is_callable( array( $this->callbacks, $settings_field['callback'] ) ) ) {
				$callback = array( $this->callbacks, $settings_field['callback'] );
			} elseif ( is_callable( $settings_field['callback'] ) ) {
				$callback = $settings_field['callback'];
			} else {
				continue;
			}

			if ( $settings_field['type'] == 'section' ) {
				add_settings_section(
					$settings_field['id'],
					$settings_field['title'],
					$callback,
					$page
				);
			} else {
				add_settings_field(
					$settings_field['id'],
					$settings_field['title'],
					$callback,
					$page,
					$settings_field['section'],
					$settings_field['args']
				);
				// register option separately for singular options
				if (is_string($settings_field['callback']) && $settings_field['callback'] == 'singular_text_element') {
					register_setting( $option_group, $settings_field['args']['option_name'], array( $this->callbacks, 'validate' ) );
				}
			}
		}
		// $page, $option_group & $option_name are all the same...
		register_setting( $option_group, $option_name, array( $this->callbacks, 'validate' ) );
		add_filter( 'option_page_capability_'.$page, array( $this, 'settings_capabilities' ) );

	}

	/**
	 * Set capability for settings page
	 */
	public function settings_capabilities() {
		return 'manage_woocommerce';
	}

	public function get_common_document_settings() {
		$common_settings = array(
			'paper_size'			=> isset( $this->general_settings['paper_size'] ) ? $this->general_settings['paper_size'] : '',
			'font_subsetting'		=> isset( $this->general_settings['font_subsetting'] ) || ( defined("DOMPDF_ENABLE_FONTSUBSETTING") && DOMPDF_ENABLE_FONTSUBSETTING === true ) ? true : false,
			'header_logo'			=> isset( $this->general_settings['header_logo'] ) ? $this->general_settings['header_logo'] : '',
			'header_logo_height'	=> isset( $this->general_settings['header_logo_height'] ) ? $this->general_settings['header_logo_height'] : '',
			'shop_name'				=> isset( $this->general_settings['shop_name'] ) ? $this->general_settings['shop_name'] : '',
			'shop_address'			=> isset( $this->general_settings['shop_address'] ) ? $this->general_settings['shop_address'] : '',
			'footer'				=> isset( $this->general_settings['footer'] ) ? $this->general_settings['footer'] : '',
			'extra_1'				=> isset( $this->general_settings['extra_1'] ) ? $this->general_settings['extra_1'] : '',
			'extra_2'				=> isset( $this->general_settings['extra_2'] ) ? $this->general_settings['extra_2'] : '',
			'extra_3'				=> isset( $this->general_settings['extra_3'] ) ? $this->general_settings['extra_3'] : '',
		);
		return $common_settings;
	}

	public function get_document_settings( $document_type ) {
		$documents = WPO_WCPDF()->documents->get_documents('all');
		foreach ($documents as $document) {
			if ( $document->get_type() == $document_type ) {
				return $document->settings;
			}
		}
		return false;
	}

	public function get_output_format( $document_type = null ) {
		if ( isset( $this->debug_settings['html_output'] ) ) {
			$output_format = 'html';
		} else {
			$output_format = 'pdf';
		}
		return apply_filters( 'wpo_wcpdf_output_format', $output_format, $document_type );
	}

	public function get_output_mode() {
		if ( isset( WPO_WCPDF()->settings->general_settings['download_display'] ) ) {
			switch ( WPO_WCPDF()->settings->general_settings['download_display'] ) {
				case 'display':
					$output_mode = 'inline';
					break;
				case 'download':
				default:
					$output_mode = 'download';
					break;
			}
		} else {
			$output_mode = 'download';
		}
		return $output_mode;
	}

	public function get_template_path() {
		// return default path if no template selected
		if ( empty( $this->general_settings['template_path'] ) ) {
			return $this->normalize_path( WPO_WCPDF()->plugin_path() . '/templates/Simple' );
		}

		$installed_templates = $this->get_installed_templates();
		$selected_template = $this->general_settings['template_path'];
		if ( in_array( $selected_template, $installed_templates ) ) {
			return array_search( $selected_template, $installed_templates );
		} else {
			// unknown template or full template path (legacy settings or filter override)
			$template_path = $this->normalize_path( $selected_template );
			
			// add base path, checking if it's not already there
			// alternative setups like Bedrock have WP_CONTENT_DIR & ABSPATH separated
			if ( defined('WP_CONTENT_DIR') && strpos( WP_CONTENT_DIR, ABSPATH ) !== false ) {
				$base_path = $this->normalize_path( ABSPATH );
			} else {
				$base_path = $this->normalize_path( WP_CONTENT_DIR );
			}
			
			if ( strpos( $template_path, $base_path ) === false ) {
				$template_path = $this->normalize_path( $base_path . $template_path );
			}
		}

		return $template_path;
	}

	public function get_installed_templates() {
		// because this method can be called (too) early we load from a cached list in those cases
		// this cache is updated each time the template settings are saved/updated
		if ( ! did_action( 'wpo_wcpdf_init_documents' ) && ( $cached_template_list = $this->get_template_list_cache() ) ) {
			return $cached_template_list;
		}

		// to save resources on the disk operations we only do this once
		if ( ! empty ( $this->installed_templates ) ) {
			return $this->installed_templates;
		}

		$installed_templates = array();

		// get base paths
		$template_base_path = ( function_exists( 'WC' ) && is_callable( 'WC', 'template_path' ) ) ? WC()->template_path() : 'woocommerce/';
		$template_base_path = untrailingslashit( $template_base_path );
		$template_paths = array (
			// note the order: theme before child-theme, so that child theme is always preferred (overwritten)
			'default'		=> WPO_WCPDF()->plugin_path() . '/templates/',
			'theme'			=> get_template_directory() . "/{$template_base_path}/pdf/",
			'child-theme'	=> get_stylesheet_directory() . "/{$template_base_path}/pdf/",
		);

		$template_paths = apply_filters( 'wpo_wcpdf_template_paths', $template_paths );

		foreach ($template_paths as $template_source => $template_path) {
			$dirs = (array) glob( $template_path . '*' , GLOB_ONLYDIR );
			
			foreach ( $dirs as $dir ) {
				$clean_dir = $this->normalize_path( $dir );
				$template_name = basename( $clean_dir );
				// let child theme override parent theme
				$group = ( $template_source == 'child-theme' ) ? 'theme' : $template_source; 
				$installed_templates[ $clean_dir ] = "{$group}/{$template_name}" ;
			}
		}

		if ( empty( $installed_templates ) ) {
			// fallback to Simple template for servers with glob() disabled
			$simple_template_path = $this->normalize_path( $template_paths['default'] . 'Simple' );
			$installed_templates[$simple_template_path] = 'default/Simple';
		}

		$installed_templates = apply_filters( 'wpo_wcpdf_installed_templates', $installed_templates );
		
		$this->installed_templates = $installed_templates;

		if ( ! empty( $this->template_list_cache ) && array_diff_assoc( $this->template_list_cache, $this->installed_templates ) ) {
			$this->set_template_list_cache( $this->installed_templates );
		}

		return $installed_templates;
	}

	public function get_template_list_cache() {
		$template_list = get_option( 'wpo_wcpdf_installed_template_paths', array() );
		if ( ! empty( $template_list ) ) {
			$checked_list = array();
			$outdated = false;
			// cache could be outdated, so we check whether the folders exist
			foreach ( $template_list as $path => $template_id ) {
				if ( @is_dir( $path ) ) {
					$checked_list[$path] = $template_id; // folder exists
					continue;
				}

				$outdated = true;
				// folder does not exist, try replacing base if we can locate wp-content
				$wp_content_folder = 'wp-content';
				if ( strpos( $path, $wp_content_folder ) !== false && defined( WP_CONTENT_DIR ) ) {
					// try wp-content
					$relative_path = substr( $path, strrpos( $path, $wp_content_folder ) + strlen( $wp_content_folder ) );
					$new_path = WP_CONTENT_DIR . $relative_path;
					if ( @is_dir( $new_path ) ) {
						$checked_list[$new_path] = $template_id;
					}
				}
			}

			if ( $outdated ) {
				$this->set_template_list_cache( $checked_list );
			}

			$this->installed_templates_cache = $checked_list;

			return $checked_list;
		} else {
			return array();
		}
	}

	public function set_template_list_cache( $template_list ) {
		$this->template_list_cache = $template_list;
		update_option( 'wpo_wcpdf_installed_template_paths', $template_list );
	}

	public function delete_template_list_cache() {
		delete_option( 'wpo_wcpdf_installed_template_paths' );
	}

	public function general_settings_updated( $old_settings, $settings, $option ) {
		if ( is_array( $settings ) && ! empty ( $settings['template_path'] ) ) {
			$this->delete_template_list_cache();
			$this->set_template_list_cache( $this->get_installed_templates() );
		}
	}

	public function get_relative_template_path( $absolute_path ) {
		if ( defined('WP_CONTENT_DIR') && strpos( WP_CONTENT_DIR, ABSPATH ) !== false ) {
			$base_path = $this->normalize_path( ABSPATH );
		} else {
			$base_path = $this->normalize_path( WP_CONTENT_DIR );
		}
		return str_replace( $base_path, '', $this->normalize_path( $absolute_path ) );
	}

	public function normalize_path( $path ) {
		return function_exists( 'wp_normalize_path' ) ? wp_normalize_path( $path ) : str_replace('\\','/', $path );
	}

	public function maybe_migrate_template_paths( $settings_section = null ) {
		$installed_templates = $this->get_installed_templates();
		$selected_template = $this->normalize_path( $this->general_settings['template_path'] );
		$template_match = '';
		if ( ! in_array( $selected_template, $installed_templates ) && substr_count( $selected_template, '/' ) > 1 ) {
			// search for path match
			foreach ( $installed_templates as $path => $template_id ) {
				$path = $this->normalize_path( $path );
				// check if the last part of the path matches
				if ( substr( $path, -strlen( $selected_template ) ) === $selected_template ) {
					$template_match = $template_id;
					break;
				}
			}

			// fallback to template name if no path match
			if ( empty( $template_match ) ) {
				$template_ids = array_flip( array_unique( array_combine( $installed_templates, array_map( 'basename', $installed_templates ) ) ) );
				$template_name = basename( $selected_template );
				if ( ! empty ( $template_ids[$template_name] ) ) {
					$template_match = $template_ids[$template_name];
				}
			}

			// migrate setting if we have a match
			if ( ! empty( $template_match ) ) {
				$this->general_settings['template_path'] = $template_match;
				update_option( 'wpo_wcpdf_settings_general', $this->general_settings );
				/* translators: 1. path, 2. template ID */
				wcpdf_log_error( sprintf( __( 'Template setting migrated from %1$s to %2$s', 'woocommerce-pdf-invoices-packing-slips' ), $path, $template_id ), 'info' );
			}
		}
	}

	public function set_number_store() {
		check_ajax_referer( "wpo_wcpdf_next_{$_POST['store']}", 'security' );
		// check permissions
		if ( !current_user_can('manage_woocommerce') ) {
			die(); 
		}

		$number = isset( $_POST['number'] ) ? (int) $_POST['number'] : 0;
		$number_store_method = $this->get_sequential_number_store_method();
		$number_store = new Sequential_Number_Store( $_POST['store'], $number_store_method );
		$number_store->set_next( $number );
		echo "next number ({$_POST['store']}) set to {$number}";
		die();
	}

	public function get_sequential_number_store_method() {
		global $wpdb;
		$method = isset( $this->debug_settings['calculate_document_numbers'] ) ? 'calculate' : 'auto_increment';

		// safety first - always use calculate when auto_increment_increment is not 1
		$row = $wpdb->get_row("SHOW VARIABLES LIKE 'auto_increment_increment'");
		if ( !empty($row) && !empty($row->Value) && $row->Value != 1 ) {
			$method = 'calculate';
		}

		return $method;		
	}

	public function get_media_upload_setting_html() {
		check_ajax_referer( 'wpo_wcpdf_get_media_upload_setting_html', 'security' );
		// check permissions
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error(); 
		}

		// get previous (default) args and preset current
		$args = $_POST['args'];
		$args['current'] = absint( $_POST['attachment_id'] );

		// get settings HTML
		ob_start();
		$this->callbacks->media_upload( $args );
		$html = ob_get_clean();

		return wp_send_json_success( $html );
	}

}

endif; // class_exists

return new Settings();