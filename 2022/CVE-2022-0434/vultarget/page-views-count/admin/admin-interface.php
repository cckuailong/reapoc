<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\PageViewsCount\FrameWork {

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------
A3rev Plugin Admin Interface

TABLE OF CONTENTS

- __construct()
- get_success_message()
- get_error_message()
- get_reset_message()
- admin_includes()
- get_font_weights()
- get_border_styles()
- admin_script_load()
- admin_css_load()
- get_settings_default()
- get_settings()
- save_settings()
- reset_settings()
- settings_get_option()
- admin_forms()
- admin_stripslashes()
- generate_border_css()
- generate_border_style_css()
- generate_border_corner_css()
- generate_shadow_css()

-----------------------------------------------------------------------------------*/

class Admin_Interface extends Admin_UI
{

	/*-----------------------------------------------------------------------------------*/
	/* Admin Interface Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {

		parent::__construct();

		$this->admin_includes();

		add_action( 'init', array( $this, 'init_scripts' ) );
		add_action( 'init', array( $this, 'init_styles' ) );

		// AJAX hide yellow message dontshow
		add_action( 'wp_ajax_'.$this->plugin_name.'_a3_admin_ui_event', array( $this, 'a3_admin_ui_event' ) );
		add_action( 'wp_ajax_nopriv_'.$this->plugin_name.'_a3_admin_ui_event', array( $this, 'a3_admin_ui_event' ) );

	}

	/*-----------------------------------------------------------------------------------*/
	/* Init scripts */
	/*-----------------------------------------------------------------------------------*/
	public function init_scripts() {
		$admin_pages = $this->admin_pages();
		
		if ( is_admin() && isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $admin_pages ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_script_load' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_modal_scripts' ), 0 );
			do_action( $this->plugin_name . '_init_scripts' );

			add_action( 'admin_print_scripts', array( $this, 'admin_localize_printed_scripts' ), 5 );
			add_action( 'admin_print_footer_scripts', array( $this, 'admin_localize_printed_scripts' ), 5 );
		}
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Init styles */
	/*-----------------------------------------------------------------------------------*/
	public function init_styles() {
		$admin_pages = $this->admin_pages();
		
		if ( is_admin() && isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $admin_pages ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'register_fontawesome_style' ), 0 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_css_load' ) );
			do_action( $this->plugin_name . '_init_styles' );
		}
	}

	public function register_fontawesome_style() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_register_style( 'font-awesome-styles', $this->admin_plugin_url() . '/assets/css/font-awesome' . $suffix . '.css', array(), '4.5.0', 'all' );
	}

	public function register_modal_scripts() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_register_style( 'bootstrap-modal', $this->admin_plugin_url() . '/assets/css/modal' . $suffix . '.css', array(), '4.1.1', 'all' );

		if ( ! wp_script_is( 'bootstrap-util', 'registered' ) ) {
			wp_register_script( 'bootstrap-util', $this->admin_plugin_url() . '/assets/js/bootstrap/util' . $suffix . '.js', array( 'jquery' ), '4.1.1', false );
		}

		wp_register_script( 'bootstrap-modal', $this->admin_plugin_url() . '/assets/js/bootstrap/modal' . $suffix . '.js', array( 'jquery', 'bootstrap-util' ), '4.1.1', false );
	}

	public function register_popover_scripts() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'bootstrap-popover', $this->admin_plugin_url() . '/assets/css/popover' . $suffix . '.css', array(), '4.1.1', 'all' );

		wp_register_script( 'bootstrap-popper', $this->admin_plugin_url() . '/assets/js/bootstrap/popper.min.js', array( 'jquery' ), '4.1.1', false );

		if ( ! wp_script_is( 'bootstrap-tooltip', 'registered' ) ) {
			wp_register_script( 'bootstrap-tooltip', $this->admin_plugin_url() . '/assets/js/bootstrap/tooltip' . $suffix . '.js', array( 'jquery' ), '4.1.1', false );
		}

		if ( ! wp_script_is( 'bootstrap-util', 'registered' ) ) {
			wp_register_script( 'bootstrap-util', $this->admin_plugin_url() . '/assets/js/bootstrap/util' . $suffix . '.js', array( 'jquery' ), '4.1.1', false );
		}

		wp_register_script( 'bootstrap-popover', $this->admin_plugin_url() . '/assets/js/bootstrap/popover' . $suffix . '.js', array( 'jquery', 'bootstrap-popper', 'bootstrap-util', 'bootstrap-tooltip' ), '4.1.1', false );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* admin_script_load */
	/*-----------------------------------------------------------------------------------*/

	public function admin_script_load() {
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$rtl = is_rtl() ? '.rtl' : '';

		$this->register_popover_scripts();
		
		wp_register_script( 'chosen', $this->admin_plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array( 'jquery' ), true, false );
		wp_register_script( 'a3rev-chosen-new', $this->admin_plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array( 'jquery' ), $this->framework_version, false );
		wp_register_script( 'a3rev-chosen-ajaxify', $this->admin_plugin_url() . '/assets/js/chosen/chosen.ajaxify.js', array( 'jquery', 'a3rev-chosen-new' ), $this->framework_version, false );
		wp_register_script( 'a3rev-style-checkboxes', $this->admin_plugin_url() . '/assets/js/iphone-style-checkboxes' . $rtl . '.js', array('jquery'), $this->framework_version, false );
		wp_register_script( 'jquery-ui-slider-rtl', $this->admin_plugin_url() . '/assets/js/ui-slider/jquery.ui.slider.rtl' . $suffix . '.js', array('jquery'), true, true );
		
		wp_register_script( 'a3rev-admin-ui-script', $this->admin_plugin_url() . '/assets/js/admin-ui-script.js', array('jquery', 'bootstrap-popover' ), $this->framework_version, true );
		wp_register_script( 'a3rev-typography-preview', $this->admin_plugin_url() . '/assets/js/a3rev-typography-preview.js',  array('jquery'), $this->framework_version, true );
		wp_register_script( 'a3rev-settings-preview', $this->admin_plugin_url() . '/assets/js/a3rev-settings-preview.js',  array('jquery'), $this->framework_version, true );
		wp_register_script( 'a3rev-metabox-ui', $this->admin_plugin_url() . '/assets/js/data-meta-boxes.js', array( 'jquery' ), $this->framework_version, true );
		wp_register_script( 'jquery-rwd-image-maps', $this->admin_plugin_url() . '/assets/js/rwdImageMaps/jquery.rwdImageMaps.min.js', array( 'jquery' ), true, true );
		wp_register_script( 'jquery-datetime-picker', $this->admin_plugin_url() . '/assets/js/datetimepicker/jquery.datetimepicker.js', array( 'jquery' ), true, true );
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-datetime-picker' );
		if ( is_rtl() ) {
			wp_enqueue_script( 'jquery-ui-slider-rtl' );
		} else {
			wp_enqueue_script( 'jquery-ui-slider' );
		}
		wp_enqueue_script( 'chosen' );
		wp_enqueue_script( 'a3rev-chosen-ajaxify' );
		wp_enqueue_script( 'a3rev-style-checkboxes' );
		wp_enqueue_script( 'a3rev-admin-ui-script' );
		wp_enqueue_script( 'a3rev-typography-preview' );
		wp_enqueue_script( 'a3rev-settings-preview' );
		wp_enqueue_script( 'a3rev-metabox-ui' );

	} // End admin_script_load()

	/*-----------------------------------------------------------------------------------*/
	/* admin_localize_printed_scripts: Localize scripts only when enqueued */
	/*-----------------------------------------------------------------------------------*/

	public function admin_localize_printed_scripts() {
		$rtl	= is_rtl() ? 1 : 0;

		if ( wp_script_is( 'a3rev-admin-ui-script' ) ) {
			wp_localize_script( 'a3rev-admin-ui-script', 'a3_admin_ui_script_params', apply_filters( 'a3_admin_ui_script_params', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'plugin'   => $this->plugin_name,
				'security' => wp_create_nonce( $this->plugin_name . '_a3_admin_ui_event' ),
				'rtl'      => $rtl,
			) ) );
		}

	} // End admin_localize_printed_scripts()

	public function a3_admin_ui_event() {
		check_ajax_referer( $this->plugin_name. '_a3_admin_ui_event', 'security' );
		if ( isset( $_REQUEST['type'] ) ) {
			switch ( trim( $_REQUEST['type'] ) ) {
				case 'open_close_panel_box':
					$form_key = sanitize_key( $_REQUEST['form_key'] );
					$box_id   = sanitize_text_field( $_REQUEST['box_id'] );
					$is_open  = $_REQUEST['is_open'];

					$user_id = get_current_user_id();
					$opened_box = get_user_meta( $user_id, $this->plugin_name . '-' . trim( $form_key ), true );
					if ( empty( $opened_box ) || ! is_array( $opened_box ) ) {
						$opened_box = array();
					}
					if ( 1 == $is_open && ! in_array( $box_id, $opened_box ) ) {
						$opened_box[] = $box_id;
					} elseif ( 0 == $is_open && in_array( $box_id, $opened_box ) ) {
						$opened_box = array_diff( $opened_box, array( $box_id ) );
					}
					update_user_meta( $user_id, $this->plugin_name . '-' . trim( $form_key ), $opened_box );
					break;

				case 'check_new_version':
					$transient_name = sanitize_key( $_REQUEST['transient_name'] );
					delete_transient( $transient_name );

					$new_version = '';

					$version_message = $this->get_version_message();
					$has_new_version = 1;
					if ( '' == trim( $version_message ) ) {
						$version_message = __( 'Great! You have the latest version installed.', 'page-views-count' );
						$has_new_version = 0;
					} else {
						delete_option( $this->plugin_name . '_clean_on_deletion');
						if ( is_array( $new_version ) && 'valid' == $new_version['is_valid_key'] ) {
							$current_update_plugins = get_site_transient( 'update_plugins' );
							if ( isset( $current_update_plugins->response ) ) {
								if ( empty( $current_update_plugins->response[$this->plugin_path] ) ) {
									$current_update_plugins->response[$this->plugin_path] = new \stdClass();
								}
								$current_update_plugins->response[$this->plugin_path]->url = "http://www.a3rev.com";
								$current_update_plugins->response[$this->plugin_path]->slug = $this->plugin_name;
								$current_update_plugins->response[$this->plugin_path]->package = $new_version["url"];
								$current_update_plugins->response[$this->plugin_path]->new_version = $new_version['version'];
								$current_update_plugins->response[$this->plugin_path]->upgrade_notice = $new_version['upgrade_notice'];
								$current_update_plugins->response[$this->plugin_path]->id = "0";
								set_site_transient( 'update_plugins', $current_update_plugins );
							}
						}
					}

					$response_data = array(
						'has_new_version' => $has_new_version,
						'version_message' => $version_message,
					);
					echo json_encode( $response_data );
					break;

				case 'validate_google_api_key':
					$g_key      = sanitize_text_field( $_REQUEST['g_key'] );
					$g_key_type = sanitize_text_field( $_REQUEST['g_key_type'] );

					$is_valid = false;
					if ( ! empty( $g_key ) ) {
						if ( 'font' == $g_key_type ) {
							$response_fonts = $GLOBALS[$this->plugin_prefix.'fonts_face']->validate_google_api_key( $g_key );
							if ( ! isset( $response_fonts['error'] ) ) {
								$is_valid = true;
							}

							if ( $is_valid ) {
								$google_api_key_status = 'valid';
							} else {
								$google_api_key_status = 'invalid';
							}

							//caching google api status for 24 hours
							set_transient( $this->google_api_key_option . '_status', $google_api_key_status, 86400 );

							update_option( $this->google_api_key_option . '_enable', 1 );
							update_option( $this->google_api_key_option, trim( $g_key ) );
						} else {
							$is_valid = $this->validate_google_map_api_key( $g_key );
							update_option( $this->google_map_api_key_option . '_enable', 1 );
							update_option( $this->google_map_api_key_option, trim( $g_key ) );
						}
					}

					if ( $is_valid ) {
						$is_valid = 1;
					} else {
						$is_valid = 0;
					}

					$response_data = array(
						'is_valid' => $is_valid,
					);
					echo json_encode( $response_data );

					break;
			}

		}
		die();
	}


	/*-----------------------------------------------------------------------------------*/
	/* admin_css_load */
	/*-----------------------------------------------------------------------------------*/

	public function admin_css_load () {
		global $wp_version;
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		
		wp_enqueue_style( 'a3rev-admin-ui-style', $this->admin_plugin_url() . '/assets/css/admin-ui-style' . $suffix . '.css', array(), $this->framework_version );
		
		if ( version_compare( $wp_version, '3.8', '>=' ) ) {
			wp_enqueue_style( 'a3rev-admin-flat-ui-style', $this->admin_plugin_url() . '/assets/css/admin-flat-ui-style' . $suffix . '.css', array(), $this->framework_version );
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'jquery-datetime-picker', $this->admin_plugin_url() . '/assets/css/jquery.datetimepicker.css' );
		wp_enqueue_style( 'a3rev-chosen-new-style', $this->admin_plugin_url() . '/assets/js/chosen/chosen' . $suffix . '.css', array(), $this->framework_version );
		wp_enqueue_style( 'a3rev-metabox-ui-style', $this->admin_plugin_url() . '/assets/css/a3_admin_metabox.css', array(), $this->framework_version );

		if ( is_rtl() ) {
			wp_enqueue_style( 'a3rev-admin-ui-style-rtl', $this->admin_plugin_url() . '/assets/css/admin-ui-style.rtl' . $suffix . '.css', array(), $this->framework_version );
			wp_enqueue_style( 'a3rev-metabox-ui-style-rtl', $this->admin_plugin_url() . '/assets/css/a3_admin_metabox.rtl' . $suffix . '.css', array(), $this->framework_version );
		}
		
	} // End admin_css_load()
	
	/*-----------------------------------------------------------------------------------*/
	/* get_success_message */
	/*-----------------------------------------------------------------------------------*/
	public function get_success_message( $message = '' ) {
		if ( trim( $message ) == '' ) $message = __( 'Settings successfully saved.' , 'page-views-count' ); 
		return '<div class="updated" id=""><p>' . $message . '</p></div>';
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_error_message */
	/*-----------------------------------------------------------------------------------*/
	public function get_error_message( $message = '' ) {
		if ( trim( $message ) == '' ) $message = __( 'Error: Settings can not save.' , 'page-views-count' ); 
		return '<div class="error" id=""><p>' . $message . '</p></div>';
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_reset_message */
	/*-----------------------------------------------------------------------------------*/
	public function get_reset_message( $message = '' ) {
		if ( trim( $message ) == '' ) $message = __( 'Settings successfully reseted.' , 'page-views-count' ); 
		return '<div class="updated" id=""><p>' . $message . '</p></div>';
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* admin_includes */
	/* Include required core files used in admin UI.
	/*-----------------------------------------------------------------------------------*/
	public function admin_includes() {
		// Includes Font Face Lib
		$GLOBALS[$this->plugin_prefix.'fonts_face'] = new Fonts_Face();
		
		// Includes Uploader Lib
		$GLOBALS[$this->plugin_prefix.'uploader'] = new Uploader();
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Get Font Weights */
	/*-----------------------------------------------------------------------------------*/
	public function get_font_weights() {
		$font_weights = array (
			'300'				=> __( 'Thin', 'page-views-count' ),
			'300 italic'		=> __( 'Thin/Italic', 'page-views-count' ),
			'normal'			=> __( 'Normal', 'page-views-count' ),
			'italic'			=> __( 'Italic', 'page-views-count' ),
			'bold'				=> __( 'Bold', 'page-views-count' ),
			'bold italic'		=> __( 'Bold/Italic', 'page-views-count' ),
		);
		return apply_filters( $this->plugin_name . '_font_weights', $font_weights );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Get Border Styles */
	/*-----------------------------------------------------------------------------------*/
	public function get_border_styles() {
		$border_styles = array (
			'solid'				=> __( 'Solid', 'page-views-count' ),
			'double'			=> __( 'Double', 'page-views-count' ),
			'dashed'			=> __( 'Dashed', 'page-views-count' ),
			'dotted'			=> __( 'Dotted', 'page-views-count' ),
			'groove'			=> __( 'Groove', 'page-views-count' ),
			'ridge'				=> __( 'Ridge', 'page-views-count' ),
			'inset'				=> __( 'Inset', 'page-views-count' ),
			'outset'			=> __( 'Outset', 'page-views-count' ),
		);
		return apply_filters( $this->plugin_name . '_border_styles', $border_styles );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Get Settings Default Function - get_settings_default */
	/* Just called for when option values is an array and it's in single option name for all settings
	/*-----------------------------------------------------------------------------------*/

	public function get_settings_default( $options, $option_name = '' ) {
		
		$default_settings = array();
		
		if ( !is_array( $options ) || count( $options ) < 1 ) return $default_settings;
		
		foreach ( $options as $value ) {
			if ( ! isset( $value['type'] ) ) continue;
			if ( in_array( $value['type'], array( 'row', 'column', 'heading', 'ajax_submit', 'ajax_multi_submit' ) ) ) continue;
			if ( ! isset( $value['id'] ) || trim( $value['id'] ) == '' ) continue;
			if ( ! isset( $value['default'] ) ) $value['default'] = '';

			if ( 'array_textfields' === $value['type'] ) {
				// Array textfields
				if ( !isset( $value['ids'] ) || !is_array( $value['ids'] ) || count( $value['ids'] ) < 1 ) continue;
				
				foreach ( $value['ids'] as $text_field ) {
					if ( ! isset( $text_field['id'] ) || trim( $text_field['id'] ) == '' ) continue;
					if ( ! isset( $text_field['default'] ) ) $text_field['default'] = '';
					
					// Do not include when it's separate option
					if ( isset( $text_field['separate_option'] ) && $text_field['separate_option'] != false ) continue;
					
					// Remove [, ] characters from id argument
					if ( strstr( $text_field['id'], '[' ) ) {
						parse_str( esc_attr( $text_field['id'] ), $option_array );
			
						// Option name is first key
						$option_keys = array_keys( $option_array );
						$first_key = current( $option_keys );
							
						$id_attribute		= $first_key;
					} else {
						$id_attribute		= esc_attr( $text_field['id'] );
					}
					
					$default_settings[$id_attribute] = $text_field['default'];
				}
			} else {
				// Do not include when it's separate option
				if ( isset( $value['separate_option'] ) && $value['separate_option'] != false ) continue;
				
				// Remove [, ] characters from id argument
				if ( strstr( $value['id'], '[' ) ) {
					parse_str( esc_attr( $value['id'] ), $option_array );
		
					// Option name is first key
					$option_keys = array_keys( $option_array );
					$first_key = current( $option_keys );
						
					$id_attribute		= $first_key;
				} else {
					$id_attribute		= esc_attr( $value['id'] );
				}

				// Backward compatibility to old settings don't have line_height option for typography
				if ( 'typography' == $value['type'] && ! isset( $value['default']['line_height'] ) ) {
					$value['default']['line_height'] = '1.4em';
				}

				$default_settings[$id_attribute] = $value['default'];
			}
		}
		
		if ( trim( $option_name ) != '' ) $default_settings = apply_filters( $this->plugin_name . '_' . $option_name . '_default_settings' , $default_settings );
		if ( ! is_array( $default_settings ) ) $default_settings = array();
		
		return $default_settings;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Get Settings Function - get_settings */
	/*-----------------------------------------------------------------------------------*/

	public function get_settings( $options, $option_name = '' ) {
				
		if ( !is_array( $options ) || count( $options ) < 1 ) return;
		
		$new_settings = array(); $new_single_setting = ''; // :)
		
		// Get settings for option values is an array and it's in single option name for all settings
		if ( trim( $option_name ) != '' ) {			
			$default_settings = $this->get_settings_default( $options, $option_name );
			
			$current_settings = get_option( $option_name );
			if ( ! is_array( $current_settings ) ) $current_settings = array();
			$current_settings = array_merge( $default_settings, $current_settings );
			
			$current_settings = array_map( array( $this, 'admin_stripslashes' ), $current_settings );
			$current_settings = apply_filters( $this->plugin_name . '_' . $option_name . '_get_settings' , $current_settings );
			
			$GLOBALS[$option_name] = $current_settings;
			
		}
		
		// Get settings for option value is stored as a record or it's spearate option
		foreach ( $options as $value ) {
			if ( ! isset( $value['type'] ) ) continue;
			if ( in_array( $value['type'], array( 'row', 'column', 'heading', 'ajax_submit', 'ajax_multi_submit' ) ) ) continue;
			if ( ! isset( $value['id'] ) || trim( $value['id'] ) == '' ) continue;
			if ( ! isset( $value['default'] ) ) $value['default'] = '';
			
			// For way it has an option name
			if ( ! isset( $value['separate_option'] ) ) $value['separate_option'] = false;
			
			// Remove [, ] characters from id argument
			if ( strstr( $value['id'], '[' ) ) {
				parse_str( esc_attr( $value['id'] ), $option_array );
	
				// Option name is first key
				$option_keys = array_keys( $option_array );
				$first_key = current( $option_keys );
					
				$id_attribute		= $first_key;
			} else {
				$id_attribute		= esc_attr( $value['id'] );
			}
			
			if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
				
				$current_setting = get_option( $id_attribute, $value['default'] );
				
				switch ( $value['type'] ) {
				
					// Array textfields
					case 'wp_editor' :
						if ( is_array( $current_setting ) )
							$current_setting = array_map( array( $this, 'stripslashes' ), $current_setting );
						elseif ( ! is_null( $current_setting ) )
							$current_setting = stripslashes( $current_setting );
					break;
					
					default:
				
						if ( is_array( $current_setting ) )
							$current_setting = array_map( array( $this, 'admin_stripslashes' ), $current_setting );
						elseif ( ! is_null( $current_setting ) )
							$current_setting = esc_attr( stripslashes( $current_setting ) );

						// Backward compatibility to old settings don't have line_height option for typography
						if ( 'typography' == $value['type'] && ! isset( $current_setting['line_height'] ) ) {
							$current_setting['line_height'] = '1.4em';
						}

					break;
				}
				
				$current_setting = apply_filters( $this->plugin_name . '_' . $id_attribute . '_get_setting' , $current_setting );
				
				$GLOBALS[$id_attribute] = $current_setting;
			}
		}
		
		// :)
		if ( ! isset( $this->is_free_plugin ) || ! $this->is_free_plugin ) {
			$fs = array( 0 => 'c', 1 => 'p', 2 => 'h', 3 => 'i', 4 => 'e', 5 => 'n', 6 => 'k', 7 => '_' );
			$cs = array( 0 => 'U', 1 => 'g', 2 => 'p', 3 => 'r', 4 => 'd', 5 => 'a', 6 => 'e', 7 => '_' );
			$check_settings_save = true;
			if ( isset( $this->class_name ) && ! class_exists( $this->class_name . $cs[7] . $cs[0] . $cs[2] . $cs[1] . $cs[3] . $cs[5] . $cs[4] . $cs[6] ) ) {
				$check_settings_save = false;
			}
			if ( ! function_exists( $this->plugin_name . $fs[7] . $fs[0] . $fs[2] . $fs[4] . $fs[0] . $fs[6] . $fs[7] . $fs[1] . $fs[3] . $fs[5] ) ) {
				$check_settings_save = false;
			}
			if ( ! $check_settings_save ) {

				if ( trim( $option_name ) != '' ) {
					update_option( $option_name, $new_settings );
					$GLOBALS[$option_name] = $new_settings;
				}
				
				foreach ( $options as $value ) {
					if ( ! isset( $value['type'] ) ) continue;
					if ( in_array( $value['type'], array( 'row', 'column', 'heading', 'ajax_submit', 'ajax_multi_submit' ) ) ) continue;
					if ( ! isset( $value['id'] ) || trim( $value['id'] ) == '' ) continue;
					if ( ! isset( $value['default'] ) ) $value['default'] = '';
					if ( ! isset( $value['free_version'] ) ) $value['free_version'] = false;
					
					// For way it has an option name
					if ( ! isset( $value['separate_option'] ) ) $value['separate_option'] = false;
					
					// Remove [, ] characters from id argument
					if ( strstr( $value['id'], '[' ) ) {
						parse_str( esc_attr( $value['id'] ), $option_array );
			
						// Option name is first key
						$option_keys = array_keys( $option_array );
						$first_key = current( $option_keys );
							
						$id_attribute		= $first_key;
					} else {
						$id_attribute		= esc_attr( $value['id'] );
					}
					
					if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
						update_option( $id_attribute,  $new_single_setting );
						$GLOBALS[$id_attribute] = $new_single_setting;
					}
				}
			}
		}
				
		return true;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Save Settings Function - save_settings */
	/*-----------------------------------------------------------------------------------*/

	public function save_settings( $options, $option_name = '' ) {
				
		if ( !is_array( $options ) || count( $options ) < 1 ) return;
		
		if ( empty( $_POST ) ) return false;
		
		$update_options = array();
		$update_separate_options = array();
		//var_dump($_POST);
		
		// Get settings for option value is stored as a record or it's spearate option
		foreach ( $options as $value ) {
			if ( ! isset( $value['type'] ) ) continue;
			if ( in_array( $value['type'], array( 'row', 'column', 'heading', 'ajax_submit', 'ajax_multi_submit' ) ) ) continue;

			// Save for global settings of plugin framework
			switch ( $value['type'] ) {

				// Toggle Box Open
				case 'onoff_toggle_box' :

					if ( isset( $_POST[ $this->toggle_box_open_option ] ) ) {
						$option_value = 1;
					} else {
						$option_value = 0;
					}

					update_option( $this->toggle_box_open_option, $option_value );

				break;

			}

			if ( ! isset( $value['id'] ) || trim( $value['id'] ) == '' ) continue;
			if ( ! isset( $value['default'] ) ) $value['default'] = '';
			
			// For way it has an option name
			if ( ! isset( $value['separate_option'] ) ) $value['separate_option'] = false;
			
			// Remove [, ] characters from id argument
			$key = false;
			if ( strstr( $value['id'], '[' ) ) {
				parse_str( esc_attr( $value['id'] ), $option_array );
	
				// Option name is first key
				$option_keys = array_keys( $option_array );
				$first_key = current( $option_keys );
					
				$id_attribute		= $first_key;
				
				$key = key( $option_array[ $id_attribute ] );
			} else {
				$id_attribute		= esc_attr( $value['id'] );
			}
			
			// Get the option name
			$option_value = null;

			if ( in_array( $value['type'], array( 'checkbox', 'onoff_checkbox', 'switcher_checkbox' ) ) ) {
				if ( ! isset( $value['checked_value'] ) ) $value['checked_value'] = 1;
				if ( ! isset( $value['unchecked_value'] ) ) $value['unchecked_value'] = 0;
				
				if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
					if ( $key != false ) {
						if ( isset( $_POST[ $id_attribute ][ $key ] ) ) {
							$option_value = $value['checked_value'];
						} else {
							$option_value = $value['unchecked_value'];
						}	
					} else {
						if ( isset( $_POST[ $id_attribute ] ) ) {
							$option_value = $value['checked_value'];
						} else {
							$option_value = $value['unchecked_value'];
						}
					}
						
				} else {
					if ( $key != false ) {
						if ( isset( $_POST[ $option_name ][ $id_attribute ][ $key ] ) ) {
							$option_value = $value['checked_value'];
						} else {
							$option_value = $value['unchecked_value'];
						}	
					} else {
						if ( isset( $_POST[ $option_name ][ $id_attribute ] ) ) {
							$option_value = $value['checked_value'];
						} else {
							$option_value = $value['unchecked_value'];
						}
					}
				}
			} elseif ( 'array_textfields' === $value['type'] ) {
				if ( !isset( $value['ids'] ) || !is_array( $value['ids'] ) || count( $value['ids'] ) < 1 ) continue;
					
				foreach ( $value['ids'] as $text_field ) {
					if ( ! isset( $text_field['id'] ) || trim( $text_field['id'] ) == '' ) continue;
					if ( ! isset( $text_field['default'] ) ) $text_field['default'] = '';
					
					// Remove [, ] characters from id argument
					$key = false;
					if ( strstr( $text_field['id'], '[' ) ) {
						parse_str( esc_attr( $text_field['id'] ), $option_array );
			
						// Option name is first key
						$option_keys = array_keys( $option_array );
						$first_key = current( $option_keys );
							
						$id_attribute		= $first_key;
						
						$key = key( $option_array[ $id_attribute ] );
					} else {
						$id_attribute		= esc_attr( $text_field['id'] );
					}
					
					// Get the option name
					$option_value = null;
					
					if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
						if ( $key != false ) {
							if ( isset( $_POST[ $id_attribute ][ $key ] ) ) {
								$option_value = sanitize_text_field( $_POST[ $id_attribute ][ $key ] );
							} else {
								$option_value = '';
							}	
						} else {
							if ( isset( $_POST[ $id_attribute ] ) ) {
								$option_value = sanitize_text_field( $_POST[ $id_attribute ] );
							} else {
								$option_value = '';
							}
						}
							
					} else {
						if ( $key != false ) {
							if ( isset( $_POST[ $option_name ][ $id_attribute ][ $key ] ) ) {
								$option_value = sanitize_text_field( $_POST[ $option_name ][ $id_attribute ][ $key ] );
							} else {
								$option_value = '';
							}	
						} else {
							if ( isset( $_POST[ $option_name ][ $id_attribute ] ) ) {
								$option_value = sanitize_text_field( $_POST[ $option_name ][ $id_attribute ] );
							} else {
								$option_value = '';
							}
						}
					}

					// Set Default value if this field is required and has default value and option value is empty
					if ( isset ( $text_field['required'] ) && $text_field['required'] && empty( $option_value ) && ! empty( $text_field['default'] ) ) {
						$option_value = $text_field['default'];
					}
					
					if ( strstr( $text_field['id'], '[' ) ) {
						// Set keys and value
    					$key = key( $option_array[ $id_attribute ] );
		
						$update_options[ $id_attribute ][ $key ] = $option_value;
						
						if ( trim( $option_name ) != '' && $value['separate_option'] != false ) {
							$update_separate_options[ $id_attribute ][ $key ] = $option_value;
						}
						
					} else {
						$update_options[ $id_attribute ] = $option_value;
						
						if ( trim( $option_name ) != '' && $value['separate_option'] != false ) {
							$update_separate_options[ $id_attribute ] = $option_value;
						}
					}
				}
			} else {
				if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
					if ( $key != false ) {
						if ( isset( $_POST[ $id_attribute ][ $key ] ) ) {

							// sanitize content for wp_editor type
							if ( 'wp_editor' === $value['type'] ) {
								$option_value = wp_kses_post_deep( $_POST[ $id_attribute ][ $key ] );
							} elseif ( 'email' === $value['type'] ) {
								if ( is_array( $_POST[ $id_attribute ][ $key ] ) ) {
									$option_value = array_map( 'sanitize_email', $_POST[ $id_attribute ][ $key ] );
								} else {
									$option_value = sanitize_email( $_POST[ $id_attribute ][ $key ] );
								}
							} elseif ( 'color' === $value['type'] ) {
								if ( is_array( $_POST[ $id_attribute ][ $key ] ) ) {
									$option_value = array_map( 'sanitize_hex_color', $_POST[ $id_attribute ][ $key ] );
								} else {
									$option_value = sanitize_hex_color( $_POST[ $id_attribute ][ $key ] );
								}
							} elseif ( 'textarea' === $value['type'] ) {
								if ( is_array( $_POST[ $id_attribute ][ $key ] ) ) {
									$option_value = array_map( 'sanitize_textarea_field', $_POST[ $id_attribute ][ $key ] );
								} else {
									$option_value = sanitize_textarea_field( $_POST[ $id_attribute ][ $key ] );
								}
							} else {
								if ( is_array( $_POST[ $id_attribute ][ $key ] ) ) {
									$option_value = array_map( 'sanitize_text_field', $_POST[ $id_attribute ][ $key ] );
								} else {
									$option_value = sanitize_text_field( $_POST[ $id_attribute ][ $key ] );
								}
							}

						} else {
							$option_value = '';
						}	
					} else {
						if ( isset( $_POST[ $id_attribute ] ) ) {

							// sanitize content for wp_editor type
							if ( 'wp_editor' === $value['type'] ) {
								$option_value = wp_kses_post_deep( $_POST[ $id_attribute ] );
							} elseif ( 'email' === $value['type'] ) {
								if ( is_array( $_POST[ $id_attribute ] ) ) {
									$option_value = array_map( 'sanitize_email', $_POST[ $id_attribute ] );
								} else {
									$option_value = sanitize_email( $_POST[ $id_attribute ] );
								}
							} elseif ( 'color' === $value['type'] ) {
								if ( is_array( $_POST[ $id_attribute ] ) ) {
									$option_value = array_map( 'sanitize_hex_color', $_POST[ $id_attribute ] );
								} else {
									$option_value = sanitize_hex_color( $_POST[ $id_attribute ] );
								}
							} elseif ( 'textarea' === $value['type'] ) {
								if ( is_array( $_POST[ $id_attribute ] ) ) {
									$option_value = array_map( 'sanitize_textarea_field', $_POST[ $id_attribute ] );
								} else {
									$option_value = sanitize_textarea_field( $_POST[ $id_attribute ] );
								}
							} else {
								if ( is_array( $_POST[ $id_attribute ] ) ) {
									$option_value = array_map( 'sanitize_text_field', $_POST[ $id_attribute ] );
								} else {
									$option_value = sanitize_text_field( $_POST[ $id_attribute ] );
								}
							}

						} else {
							$option_value = '';
						}
					}
						
				} else {
					if ( $key != false ) {
						if ( isset( $_POST[ $option_name ][ $id_attribute ][ $key ] ) ) {

							// sanitize content for wp_editor type
							if ( 'wp_editor' === $value['type'] ) {
								$option_value = wp_kses_post_deep( $_POST[ $option_name ][ $id_attribute ][ $key ] );
							} elseif ( 'email' === $value['type'] ) {
								if ( is_array( $_POST[ $option_name ][ $id_attribute ][ $key ] ) ) {
									$option_value = array_map( 'sanitize_email', $_POST[ $option_name ][ $id_attribute ][ $key ] );
								} else {
									$option_value = sanitize_email( $_POST[ $option_name ][ $id_attribute ][ $key ] );
								}
							} elseif ( 'color' === $value['type'] ) {
								if ( is_array( $_POST[ $option_name ][ $id_attribute ][ $key ] ) ) {
									$option_value = array_map( 'sanitize_hex_color', $_POST[ $option_name ][ $id_attribute ][ $key ] );
								} else {
									$option_value = sanitize_hex_color( $_POST[ $option_name ][ $id_attribute ][ $key ] );
								}
							} elseif ( 'textarea' === $value['type'] ) {
								if ( is_array( $_POST[ $option_name ][ $id_attribute ][ $key ] ) ) {
									$option_value = array_map( 'sanitize_textarea_field', $_POST[ $option_name ][ $id_attribute ][ $key ] );
								} else {
									$option_value = sanitize_textarea_field( $_POST[ $option_name ][ $id_attribute ][ $key ] );
								}
							} else {
								if ( is_array( $_POST[ $option_name ][ $id_attribute ][ $key ] ) ) {
									$option_value = array_map( 'sanitize_text_field', $_POST[ $option_name ][ $id_attribute ][ $key ] );
								} else {
									$option_value = sanitize_text_field( $_POST[ $option_name ][ $id_attribute ][ $key ] );
								}
							}

						} else {
							$option_value = '';
						}	
					} else {
						if ( isset( $_POST[ $option_name ][ $id_attribute ] ) ) {

							// sanitize content for wp_editor type
							if ( 'wp_editor' === $value['type'] ) {
								$option_value = wp_kses_post_deep( $_POST[ $option_name ][ $id_attribute ] );
							} elseif ( 'email' === $value['type'] ) {
								if ( is_array( $_POST[ $option_name ][ $id_attribute ] ) ) {
									$option_value = array_map( 'sanitize_email', $_POST[ $option_name ][ $id_attribute ] );
								} else {
									$option_value = sanitize_email( $_POST[ $option_name ][ $id_attribute ] );
								}
							} elseif ( 'color' === $value['type'] ) {
								if ( is_array( $_POST[ $option_name ][ $id_attribute ] ) ) {
									$option_value = array_map( 'sanitize_hex_color', $_POST[ $option_name ][ $id_attribute ] );
								} else {
									$option_value = sanitize_hex_color( $_POST[ $option_name ][ $id_attribute ] );
								}
							} elseif ( 'textarea' === $value['type'] ) {
								if ( is_array( $_POST[ $option_name ][ $id_attribute ] ) ) {
									$option_value = array_map( 'sanitize_textarea_field', $_POST[ $option_name ][ $id_attribute ] );
								} else {
									$option_value = sanitize_textarea_field( $_POST[ $option_name ][ $id_attribute ] );
								}
							} else {
								if ( is_array( $_POST[ $option_name ][ $id_attribute ] ) ) {
									$option_value = array_map( 'sanitize_text_field', $_POST[ $option_name ][ $id_attribute ] );
								} else {
									$option_value = sanitize_text_field( $_POST[ $option_name ][ $id_attribute ] );
								}
							}

						} else {
							$option_value = '';
						}
					}
				}

				// Just for Color type
				if ( 'color' == $value['type'] && '' == trim( $option_value ) ) {
					$option_value = 'transparent';
				}
				// Just for Background Color type
				elseif ( 'bg_color' == $value['type'] && '' == trim( $option_value['color'] ) ) {
					$option_value['color'] = 'transparent';
				} elseif ( 'upload' == $value['type'] ) {
					// Uploader: Set key and value for attachment id of upload type
					if ( strstr( $value['id'], '[' ) ) {
						$key = key( $option_array[ $id_attribute ] );

						if ( trim( $option_name ) != '' && $value['separate_option'] != false ) {
							if ( isset( $_POST[ $id_attribute ][ $key . '_attachment_id' ] ) ) {
								$attachment_id = intval( $_POST[ $id_attribute ][ $key . '_attachment_id' ] );
							} else {
								$attachment_id = 0;
							}

							$update_separate_options[ $id_attribute ][ $key . '_attachment_id' ] = $attachment_id;
						} else {
							if ( isset( $_POST[ $option_name ][ $id_attribute ][ $key . '_attachment_id' ] ) ) {
								$attachment_id = intval( $_POST[ $option_name ][ $id_attribute ][ $key . '_attachment_id' ] );
							} else {
								$attachment_id = 0;
							}

							$update_options[ $id_attribute ][ $key . '_attachment_id' ] = $attachment_id;
						}
					} else {
						if ( trim( $option_name ) != '' && $value['separate_option'] != false ) {
							if ( isset( $_POST[ $id_attribute . '_attachment_id' ] ) ) {
								$attachment_id = intval( $_POST[ $id_attribute . '_attachment_id' ] );
							} else {
								$attachment_id = 0;
							}
							$update_separate_options[ $id_attribute . '_attachment_id' ] = $attachment_id;
						} else {
							if ( isset( $_POST[ $option_name ][ $id_attribute . '_attachment_id' ] ) ) {
								$attachment_id = intval( $_POST[ $option_name ][ $id_attribute . '_attachment_id' ] );
							} else {
								$attachment_id = 0;
							}
							$update_options[ $id_attribute . '_attachment_id' ] = $attachment_id;
						}
					}
				}
			}
			
			if ( !in_array( $value['type'], array( 'array_textfields' ) ) ) {

				// Set Default value if this field is required and has default value and option value is empty
				if ( isset ( $value['required'] ) && $value['required'] && empty( $option_value ) && ! empty( $value['default'] ) ) {
					$option_value = $value['default'];
				}

				if ( strstr( $value['id'], '[' ) ) {
					// Set keys and value
					$key = key( $option_array[ $id_attribute ] );
					
					if ( trim( $option_name ) != '' && $value['separate_option'] != false ) {
						$update_separate_options[ $id_attribute ][ $key ] = $option_value;
					} else {
						$update_options[ $id_attribute ][ $key ] = $option_value;
					}
					
				} else {
					
					if ( trim( $option_name ) != '' && $value['separate_option'] != false ) {
						$update_separate_options[ $id_attribute ] = $option_value;
					} else {
						$update_options[ $id_attribute ] = $option_value;
					}
				}
			}
			
		}
		
		// Save settings for option values is an array and it's in single option name for all settings
		if ( trim( $option_name ) != '' ) {
			update_option( $option_name, $update_options );
		}
		
		// Save options if each option save in a row
		if ( count( $update_options ) > 0 && trim( $option_name ) == '' ) {
			foreach ( $update_options as $name => $value ) {
				update_option( $name, $value );
			}
		}
		
		// Save separate options
		if ( count( $update_separate_options ) > 0 ) {
			foreach ( $update_separate_options as $name => $value ) {
				update_option( $name, $value );
			}
		}
				
		return true;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Reset Function - reset_settings */
	/*-----------------------------------------------------------------------------------*/

	public function reset_settings( $options, $option_name = '', $reset = false, $free_version = false ) {
		
		if ( !is_array( $options ) || count( $options ) < 1 ) return;
		
		// Update settings default for option values is an array and it's in single option name for all settings
		if ( trim( $option_name ) != '' ) {

			$default_settings = $this->get_settings_default( $options, $option_name );

			$current_settings = get_option( $option_name );
			if ( ! is_array( $current_settings ) ) $current_settings = array();
			$current_settings = array_merge( $default_settings, $current_settings );

			if ( $reset && !$free_version ) {
				update_option( $option_name, $default_settings );
			} else {
				if ( $free_version ) {
					foreach ( $options as $value ) {
						if ( ! isset( $value['type'] ) ) continue;
						if ( in_array( $value['type'], array( 'row', 'column', 'heading', 'ajax_submit', 'ajax_multi_submit' ) ) ) continue;
						if ( ! isset( $value['id'] ) || trim( $value['id'] ) == '' ) continue;

						if ( 'array_textfields' === $value['type'] ) {
							// Array textfields
							if ( !isset( $value['ids'] ) || !is_array( $value['ids'] ) || count( $value['ids'] ) < 1 ) continue;
								
								foreach ( $value['ids'] as $text_field ) {
									if ( ! isset( $text_field['id'] ) || trim( $text_field['id'] ) == '' ) continue;
									if ( ! isset( $text_field['default'] ) ) $text_field['default'] = '';
									if ( ! isset( $text_field['free_version'] ) ) {
										if ( ! isset( $value['free_version'] ) ) 
											$text_field['free_version'] = false;
										else
											$text_field['free_version'] = $value['free_version'];
									}
									if ( $text_field['free_version'] ) unset( $default_settings[ $text_field['id']] );
								}
						} else {
							if ( ! isset( $value['default'] ) ) $value['default'] = '';
							if ( ! isset( $value['free_version'] ) ) $value['free_version'] = false;
							if ( $value['free_version'] ) unset( $default_settings[ $value['id']] );
						}
					}
					
					$current_settings = array_merge( $current_settings, $default_settings );
					update_option( $option_name, $current_settings );
				} else {
					update_option( $option_name, $current_settings );
				}
			}
			
		}
		
		// Update settings default for option value is stored as a record or it's spearate option
		foreach ( $options as $value ) {
			if ( ! isset( $value['type'] ) ) continue;
			if ( in_array( $value['type'], array( 'row', 'column', 'heading', 'ajax_submit', 'ajax_multi_submit' ) ) ) continue;
			if ( ! isset( $value['id'] ) || trim( $value['id'] ) == '' ) continue;
			if ( ! isset( $value['default'] ) ) $value['default'] = '';
			if ( ! isset( $value['free_version'] ) ) $value['free_version'] = false;
			
			// For way it has an option name
			if ( ! isset( $value['separate_option'] ) ) $value['separate_option'] = false;

			if ( 'array_textfields' === $value['type'] ) {
				// Array textfields
				if ( !isset( $value['ids'] ) || !is_array( $value['ids'] ) || count( $value['ids'] ) < 1 ) continue;
								
				foreach ( $value['ids'] as $text_field ) {
					if ( ! isset( $text_field['id'] ) || trim( $text_field['id'] ) == '' ) continue;
					if ( ! isset( $text_field['default'] ) ) $text_field['default'] = '';
					if ( ! isset( $text_field['free_version'] ) ) {
						if ( ! isset( $value['free_version'] ) ) 
							$text_field['free_version'] = false;
						else
							$text_field['free_version'] = $value['free_version'];
					}
					
					// Remove [, ] characters from id argument
					$key = false;
					if ( strstr( $text_field['id'], '[' ) ) {
						parse_str( esc_attr( $text_field['id'] ), $option_array );
			
						// Option name is first key
						$option_keys = array_keys( $option_array );
						$first_key = current( $option_keys );
							
						$id_attribute		= $first_key;

						$key = key( $option_array[ $id_attribute ] );
					} else {
						$id_attribute		= esc_attr( $text_field['id'] );
					}
					
					if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
						if ( $reset && $text_field['free_version'] && !$free_version ) {
							if ( $key != false ) {
								$current_settings = get_option( $id_attribute, array() );
								if ( ! is_array( $current_settings) ) {
									$current_settings = array();
								}
								$current_settings[$key] = $text_field['default'];
								update_option( $id_attribute,  $current_settings );
							} else {
								update_option( $id_attribute,  $text_field['default'] );
							}
						} elseif ( $reset && !$text_field['free_version'] ) {
							if ( $key != false ) {
								$current_settings = get_option( $id_attribute, array() );
								if ( ! is_array( $current_settings) ) {
									$current_settings = array();
								}
								$current_settings[$key] = $text_field['default'];
								update_option( $id_attribute,  $current_settings );
							} else {
								update_option( $id_attribute,  $text_field['default'] );
							}
						} else {
							if ( $key != false ) {
							$current_settings = get_option( $id_attribute, array() );
							if ( ! is_array( $current_settings) ) {
								$current_settings = array();
							}
							if ( ! isset( $current_settings[$key] ) ) {
								$current_settings[$key] = $text_field['default'];
								update_option( $id_attribute,  $current_settings );
							}
							} else {
								add_option( $id_attribute,  $text_field['default'] );
							}
						}
					}
				}
			} else {
				// Remove [, ] characters from id argument
				$key = false;
				if ( strstr( $value['id'], '[' ) ) {
					parse_str( esc_attr( $value['id'] ), $option_array );
		
					// Option name is first key
					$option_keys = array_keys( $option_array );
					$first_key = current( $option_keys );
						
					$id_attribute		= $first_key;

					$key = key( $option_array[ $id_attribute ] );
				} else {
					$id_attribute		= esc_attr( $value['id'] );
				}
				
				if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
					if ( $reset && $value['free_version'] && !$free_version ) {
						if ( $key != false ) {
							$current_settings = get_option( $id_attribute, array() );
							if ( ! is_array( $current_settings) ) {
								$current_settings = array();
							}
							$current_settings[$key] = $value['default'];
							update_option( $id_attribute,  $current_settings );
						} else {
							update_option( $id_attribute,  $value['default'] );
						}
					} elseif ( $reset && !$value['free_version'] ) {
						if ( $key != false ) {
							$current_settings = get_option( $id_attribute, array() );
							if ( ! is_array( $current_settings) ) {
								$current_settings = array();
							}
							$current_settings[$key] = $value['default'];
							update_option( $id_attribute,  $current_settings );
						} else {
							update_option( $id_attribute,  $value['default'] );
						}
					} else {
						if ( $key != false ) {
							$current_settings = get_option( $id_attribute, array() );
							if ( ! is_array( $current_settings) ) {
								$current_settings = array();
							}
							if ( ! isset( $current_settings[$key] ) ) {
								$current_settings[$key] = $value['default'];
								update_option( $id_attribute,  $current_settings );
							}
						} else {
							add_option( $id_attribute,  $value['default'] );
						}
					}
				}
			}
			
		}
				
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Get Option Value Function - settings_get_option */
	/* Just called for when each option has an option value for settings
	/*-----------------------------------------------------------------------------------*/
	
	public function settings_get_option( $option_name, $default = '' ) {
		// Array value
		if ( strstr( $option_name, '[' ) ) {
	
			parse_str( $option_name, $option_array );
	
			// Option name is first key
			$option_keys = array_keys( $option_array );
			$option_name = current( $option_keys );
	
			// Get value
			$option_values = get_option( $option_name, '' );
	
			$key = key( $option_array[ $option_name ] );
	
			if ( isset( $option_values[ $key ] ) )
				$option_value = $option_values[ $key ];
			else
				$option_value = null;
	
		// Single value
		} else {
			$option_value = get_option( $option_name, null );
		}
	
		if ( is_array( $option_value ) )
			$option_value = array_map( 'stripslashes', $option_value );
		elseif ( ! is_null( $option_value ) )
			$option_value = stripslashes( $option_value );
	
		return $option_value === null ? $default : $option_value;
	}
	
	/**
	 * Output admin fields.
	 *
	 *
	 * @access public
	 * @param array $options : Opens array to output
	 * @param text $form_key : It's unique key for form to get correct save and reset action for this form
	 * @param text $option_name : Save all settings as array into database for a single option name
	 * @param array $form_messages : { 'success_message' => '', 'error_message' => '', 'reset_message' => '' }
	 * @return void
	 * ========================================================================
	 * Option Array Structure :
	 * type					=> row | column | heading | ajax_submit | ajax_multi_submit | google_api_key | google_map_api_key | onoff_toggle_box 
	 * 						   | text | email | number | password | color | bg_color | textarea | select | multiselect | radio | onoff_radio | checkbox | onoff_checkbox 
	 *						   | switcher_checkbox | image_size | single_select_page | typography | border | border_styles | border_corner | box_shadow 
	 *						   | slider | upload | wp_editor | array_textfields | time_picker
	 *
	 * id					=> text
	 * name					=> text
	 * free_version			=> true | false : if Yes then when save settings with $free_version = true, it does reset this option
	 * class				=> text
	 * css					=> text
	 * default				=> text : apply for other types
	 * 						   array( 'enable' => 1, 'color' => '#515151' ) : apply bg_color only
	 *						   array( 'width' => '125', 'height' => '125', 'crop' => 1 ) : apply image_size only
	 *						   array( 'size' => '9px', line_height => '1.4em', 'face' => 'Arial', 'style' => 'normal', 'color' => '#515151' ) : apply for typography only 
	 * required 			=> true | false : apply for all types
	 *						   array( 'width' => '1px', 'style' => 'normal', 'color' => '#515151', 'corner' => 'rounded' | 'square' , 'top_left_corner' => 3, 
	 *									'top_right_corner' => 3, 'bottom_left_corner' => 3, 'bottom_right_corner' => 3 ) : apply for border only
	  *						   array( 'width' => '1px', 'style' => 'normal', 'color' => '#515151' ) : apply for border_styles only
	 *						   array( 'corner' => 'rounded' | 'square' , 'top_left_corner' => 3, 'top_right_corner' => 3, 'bottom_left_corner' => 3, 
	 *									'bottom_right_corner' => 3 ) : apply for border_corner only
	 *						   array( 'enable' => 1|0, 'h_shadow' => '5px' , 'v_shadow' => '5px', 'blur' => '2px' , 'spread' => '2px', 'color' => '#515151', 
	 *									'inset' => '' | 'insert' ) : apply for box_shadow only
	 *
	 * desc					=> text
	 * desc_tip				=> text
	 * separate_option		=> true | false
	 * custom_attributes	=> array
	 * view_doc				=> allowed html code : apply for heading only
	 * placeholder			=> text : apply for input, email, number, password, textarea, select, multiselect and single_select_page
	 * hide_if_checked		=> true | false : apply for checkbox only
	 * show_if_checked		=> true | false : apply for checkbox only
	 * checkboxgroup		=> start | end : apply for checkbox only
	 * checked_value		=> text : apply for checkbox, onoff_checkbox, switcher_checkbox only ( it's value set to database when checkbox is checked )
	 * unchecked_value		=> text : apply for checkbox, onoff_checkbox, switcher_checkbox only ( it's value set to database when checkbox is unchecked )
	 * checked_label		=> text : apply for onoff_checkbox, switcher_checkbox only ( set it to show the text instead ON word default )
	 * unchecked_label		=> text : apply for onoff_checkbox, switcher_checkbox only ( set it to show the text instead OFF word default  )
	 * options				=> array : apply for select, multiselect, radio types
	 * options_url		 	=> url : apply for select, multiselect
	 *
	 * onoff_options		=> array : apply for onoff_radio only
	 *						   ---------------- example ---------------------
	 *							array( 
	 *								array(  'val' 				=> 1,
	 *										'text' 				=> 'Top',
	 *										'checked_label' 	=> 'ON',
	 *										'unchecked_value'	=> 'OFF' ),
	 *
	 *								array(  'val' 				=> 2,
	 *										'text' 				=> 'Bottom',
	 *										'checked_label' 	=> 'ON',
	 *										'unchecked_value'	=> 'OFF' ),
	 *							)
	 *							---------------------------------------------
	 *
	 * args					=> array : apply for single_select_page only
	 * min					=> number : apply for slider, border, border_corner types only
	 * max					=> number : apply for slider, border, border_corner types only
	 * increment			=> number : apply for slider, border, border_corner types only
	 * textarea_rows		=> number : apply for wp_editor type only
	 *
	 * ids					=> array : apply for array_textfields only
	 *						   ---------------- example ---------------------
	 *							array( 
	 *								array(  'id' 		=> 'box_margin_top',
	 *										'name' 		=> 'Top',
	 *										'class' 	=> '',
	 *										'css'		=> 'width:40px;',
	 *										'default'	=> '10px' ),
	 *
	 *								array(  'id' 		=> 'box_margin_top',
	 *										'name' 		=> 'Top',
	 *										'class' 	=> '',
	 *										'css'		=> 'width:40px;',
	 *										'default'	=> '10px' ),
	 *							)
	 *							---------------------------------------------
	 *
	 * strip_methods		=> true | false : apply for upload type only
	 *
	 * submit_data 			=> array : apply for ajax_submit only
	 *						   ---------------- example ---------------------
	 * 							array(
	 *								'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
	 *								'ajax_type' => 'POST',
	 *								'data'      => array(
	 *									'action'   => 'action_name',
	 *								),
	 *							),
	 * button_name      	=> text : apply for ajax_submit, ajax_multi_submit only
	 * progressing_text     => text : apply for ajax_submit, ajax_multi_submit only
	 * completed_text      	=> text : apply for ajax_submit, ajax_multi_submit only
	 * successed_text      	=> text : apply for ajax_submit, ajax_multi_submit only
	 *
	 * statistic_column     => number : apply for ajax_multi_submit only
	 * resubmit      		=> true | false : apply for ajax_multi_submit only
	 *
	 * multi_submit			=> array : apply for ajax_multi_submit only
	 *						   ---------------- example ---------------------
	 * 							array(
	 *								array(
	 *									'item_id'          => 'item_ajax_id',
	 *									'item_name'        => 'Item Ajax Name',
	 *									'current_items'    => 20,
	 *									'total_items'      => 20,
	 *									'progressing_text' => 'Processing,
	 *									'completed_text'   => 'Completed',
	 *									'submit_data'      => array(
	 *										'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
	 *										'ajax_type' => 'POST',
	 *										'data'      => array(
	 *											'action'   => 'action_name',
	 *										)
	 *									),
	 *									'show_statistic'       => true,
	 *									'statistic_customizer' => array(
	 *										'current_color' => '#96587d',
	 *									),
	 *								),
	 * 								array(
	 * 									...
	 * 								),
	 *								...
	 *							)
	 * 
	 * time_step			=> number : apply for time_picker only
	 * time_min				=> text : apply for time_picker only
	 * time_max				=> text : apply for time_picker only
	 * time_allow			=> text : apply for time_picker only
	 *						   ---------------- example ---------------------
	 * 							[ '9:00', '11:00', '12:00', '21:00' ]
	 *
	 */
	 
	public function admin_forms( $options, $form_key, $option_name = '', $form_messages = array() ) {
		global $current_subtab;
		
		$new_settings = array(); $new_single_setting = ''; // :)
		$admin_message = '';
		
		if ( isset( $_POST['form_name_action'] ) && $_POST['form_name_action'] == $form_key ) {
			
			do_action( $this->plugin_name . '_before_settings_save_reset' );
			do_action( $this->plugin_name . '-' . trim( $form_key ) . '_before_settings_save' );
			
			// Save settings action
			if ( isset( $_POST['bt_save_settings'] ) ) {
				$this->save_settings( $options, $option_name );
				$admin_message = $this->get_success_message( ( isset( $form_messages['success_message'] ) ) ? $form_messages['success_message'] : ''  );
			} 
			// Reset settings action
			elseif ( isset( $_POST['bt_reset_settings'] ) ) {
				$this->reset_settings( $options, $option_name, true );
				$admin_message = $this->get_success_message( ( isset( $form_messages['reset_message'] ) ) ? $form_messages['reset_message'] : ''  );
			}
			
			do_action( $this->plugin_name . '-' . trim( $form_key ) . '_after_settings_save' );
			do_action( $this->plugin_name . '_after_settings_save_reset' );
		}
		do_action( $this->plugin_name . '-' . trim( $form_key ) . '_settings_init' );
		do_action( $this->plugin_name . '_settings_init' );
		
		$option_values = array();
		if ( trim( $option_name ) != '' ) {
			$option_values = get_option( $option_name, array() );
			if ( is_array( $option_values ) )
				$option_values = array_map( array( $this, 'admin_stripslashes' ), $option_values );
			else
				$option_values = array();
			
			$default_settings = $this->get_settings_default( $options, $option_name );
			
			$option_values = array_merge($default_settings, $option_values);
		}
						
		if ( !is_array( $options ) || count( $options ) < 1 ) return '';
		?>
        
        <?php echo $admin_message; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
		<div class="a3rev_panel_container" style="visibility:hidden; height:0; overflow:hidden;" >
        <form action="" method="post">
		<?php do_action( $this->plugin_name . '-' . trim( $form_key ) . '_settings_start' ); ?>
		<div class="a3rev_panel_row"> <!-- Open Panel Row -->
		<?php
		$had_first_row = false;
		$had_first_column = false;
		$closed_panel_inner = false;
		$count_heading = 0;
		$end_heading_id = false;
		$header_box_opening = false;
		$header_sub_box_opening = false;

		$user_id = get_current_user_id();
		$opened_box = get_user_meta( $user_id, $this->plugin_name . '-' . trim( $form_key ), true );
		if ( empty( $opened_box ) || ! is_array( $opened_box ) ) {
			$opened_box = array();
		}

		$toggle_box_open = $this->settings_get_option( $this->toggle_box_open_option, 0 );
		if ( ! isset( $_POST['bt_save_settings'] ) && 0 == $toggle_box_open ) {
			delete_user_meta( $user_id, $this->plugin_name . '-' . trim( $form_key ) );
		}

		foreach ( $options as $value ) {
			if ( ! isset( $value['type'] ) ) continue;
			if ( ! isset( $value['id'] ) ) $value['id'] = '';
			if ( ! isset( $value['name'] ) ) $value['name'] = '';
			if ( ! isset( $value['class'] ) ) $value['class'] = '';
			if ( ! isset( $value['css'] ) ) $value['css'] = '';
			if ( ! isset( $value['default'] ) ) $value['default'] = '';
			if ( ! isset( $value['desc'] ) ) $value['desc'] = '';
			if ( ! isset( $value['desc_tip'] ) ) $value['desc_tip'] = false;
			if ( ! isset( $value['placeholder'] ) ) $value['placeholder'] = '';
			
			// For way it has an option name
			if ( ! isset( $value['separate_option'] ) ) $value['separate_option'] = false;
	
			// Custom attribute handling
			$custom_attributes = array();
	
			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) )
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value )
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
	
			// Description handling
			if ( $value['desc_tip'] === true ) {
				$description = '';
				$tip = $value['desc'];
			} elseif ( ! empty( $value['desc_tip'] ) ) {
				$description = $value['desc'];
				$tip = $value['desc_tip'];
			} elseif ( ! empty( $value['desc'] ) ) {
				$description = $value['desc'];
				$tip = '';
			} else {
				$description = $tip = '';
			}
	
			if ( $description && in_array( $value['type'], array( 'manual_check_version', 'ajax_submit', 'ajax_multi_submit', 'textarea', 'radio', 'onoff_radio', 'typography', 'border', 'border_styles', 'array_textfields', 'wp_editor', 'upload', 'google_api_key', 'google_map_api_key' ) ) ) {
				$description = '<div class="desc" style="margin-bottom:5px;">' . wptexturize( $description ) . '</div>';
			} elseif ( $description ) {
				$description = '<span class="description" style="margin-left:5px;">' . wptexturize( $description ) . '</span>';
			}
			
			/**
			 * Add Default value into description and description tip if it has shortcode :
			 * [default_value] 				: apply for normal types
			 *
			 * [default_value_width] 		: apply for image_size type
			 * [default_value_height] 		: apply for image_size type
			 *
			 * [default_value_size]			: apply for typography type
			 * [default_value_line_height]	: apply for typography type
			 * [default_value_face]			: apply for typography type
			 * [default_value_style]		: apply for typography, border, border_styles types
			 * [default_value_color]		: apply for typography, border, border_styles types
			 *
			 * [default_value_width]		: apply for border, border_styles types
			 * [default_value_rounded_value]: apply for border, border_corner types
			 * [default_value_top_left_corner]: apply for border, border_corner types
			 * [default_value_top_right_corner]: apply for border, border_corner types
			 * [default_value_bottom_left_corner]: apply for border, border_corner types
			 * [default_value_bottom_right_corner]: apply for border, border_corner types
			 */
			if ( $value['type'] == 'image_size' ) {
				if ( ! is_array( $value['default'] ) ) $value['default'] = array();
				if ( ! isset( $value['default']['width'] ) ) $value['default']['width'] = '';
				if ( ! isset( $value['default']['height'] ) ) $value['default']['height'] = '';
				if ( ! isset( $value['default']['crop'] ) ) $value['default']['crop'] = 1;
				
				$description = str_replace( '[default_value_width]', $value['default']['width'], $description );
				$description = str_replace( '[default_value_height]', $value['default']['height'], $description );
			} elseif ( $value['type'] == 'typography' ) {
				if ( ! is_array( $value['default'] ) ) $value['default'] = array();
				if ( ! isset( $value['default']['size'] ) ) $value['default']['size'] = '';
				if ( ! isset( $value['default']['line_height'] ) ) $value['default']['line_height'] = '';
				if ( ! isset( $value['default']['face'] ) ) $value['default']['face'] = '';
				if ( ! isset( $value['default']['style'] ) ) $value['default']['style'] = '';
				if ( ! isset( $value['default']['color'] ) || trim( $value['default']['color'] ) == '' ) $value['default']['color'] = '#515151';
				
				$description = str_replace( '[default_value_size]', $value['default']['size'], $description );
				$description = str_replace( '[default_value_face]', $value['default']['face'], $description );
				$description = str_replace( '[default_value_style]', $value['default']['style'], $description );
				$description = str_replace( '[default_value_color]', $value['default']['color'], $description );
			} elseif ( in_array( $value['type'], array( 'border', 'border_styles', 'border_corner' ) ) ) {
				if ( ! is_array( $value['default'] ) ) $value['default'] = array();
				
				if ( ! isset( $value['default']['width'] ) ) $value['default']['width'] = '';
				if ( ! isset( $value['default']['style'] ) ) $value['default']['style'] = '';
				if ( ! isset( $value['default']['color'] ) || trim( $value['default']['color'] ) == '' ) $value['default']['color'] = '#515151';
					
				if ( ! isset( $value['default']['corner'] ) ) $value['default']['corner'] = 'rounded';
				if ( ! isset( $value['default']['rounded_value'] ) ) $value['default']['rounded_value'] = '';
				if ( ! isset( $value['default']['top_left_corner'] ) ) $value['default']['top_left_corner'] = $value['default']['rounded_value'];
				if ( ! isset( $value['default']['top_right_corner'] ) ) $value['default']['top_right_corner'] = $value['default']['rounded_value'];
				if ( ! isset( $value['default']['bottom_left_corner'] ) ) $value['default']['bottom_left_corner'] = $value['default']['rounded_value'];
				if ( ! isset( $value['default']['bottom_right_corner'] ) ) $value['default']['bottom_right_corner'] = $value['default']['rounded_value'];
				
				$description = str_replace( '[default_value_width]', $value['default']['width'], $description );
				$description = str_replace( '[default_value_style]', $value['default']['style'], $description );
				$description = str_replace( '[default_value_color]', $value['default']['color'], $description );
				$description = str_replace( '[default_value_rounded_value]', $value['default']['rounded_value'], $description );
				$description = str_replace( '[default_value_top_left_corner]', $value['default']['top_left_corner'], $description );
				$description = str_replace( '[default_value_top_right_corner]', $value['default']['top_right_corner'], $description );
				$description = str_replace( '[default_value_bottom_left_corner]', $value['default']['bottom_left_corner'], $description );
				$description = str_replace( '[default_value_bottom_right_corner]', $value['default']['bottom_right_corner'], $description );
			} elseif ( $value['type'] == 'box_shadow' ) {
				if ( ! is_array( $value['default'] ) ) $value['default'] = array();
				if ( ! isset( $value['default']['enable'] ) || trim( $value['default']['enable'] ) == '' ) $value['default']['enable'] = 0;
				if ( ! isset( $value['default']['color'] ) || trim( $value['default']['color'] ) == '' ) $value['default']['color'] = '#515151';
				if ( ! isset( $value['default']['h_shadow'] ) || trim( $value['default']['h_shadow'] ) == '' ) $value['default']['h_shadow'] = '0px';
				if ( ! isset( $value['default']['v_shadow'] ) || trim( $value['default']['v_shadow'] ) == '' ) $value['default']['v_shadow'] = '0px';
				if ( ! isset( $value['default']['blur'] ) || trim( $value['default']['blur'] ) == '' ) $value['default']['blur'] = '0px';
				if ( ! isset( $value['default']['spread'] ) || trim( $value['default']['spread'] ) == '' ) $value['default']['spread'] = '0px';
				if ( ! isset( $value['default']['inset'] ) || trim( $value['default']['inset'] ) == '' ) $value['default']['inset'] = '';
				
				$description = str_replace( '[default_value_color]', $value['default']['color'], $description );
				$description = str_replace( '[default_value_h_shadow]', $value['default']['h_shadow'], $description );
				$description = str_replace( '[default_value_v_shadow]', $value['default']['v_shadow'], $description );
				$description = str_replace( '[default_value_blur]', $value['default']['blur'], $description );
				$description = str_replace( '[default_value_spread]', $value['default']['spread'], $description );
				
			} elseif ( $value['type'] == 'bg_color' ) {
				if ( ! is_array( $value['default'] ) ) $value['default'] = array();
				if ( ! isset( $value['default']['enable'] ) || trim( $value['default']['enable'] ) == '' ) $value['default']['enable'] = 0;
				if ( ! isset( $value['default']['color'] ) || trim( $value['default']['color'] ) == '' ) $value['default']['color'] = '#515151';

				$description = str_replace( '[default_value_color]', $value['default']['color'], $description );
			} elseif ( $value['type'] != 'multiselect' ) {
				$description = str_replace( '[default_value]', $value['default'], $description );
			}
	
			if ( $tip && in_array( $value['type'], array( 'checkbox' ) ) ) {
	
				$tip = '<p class="description">' . esc_html( $tip ) . '</p>';
	
			} elseif ( $tip ) {
	
				$tip = '<div class="help_tip a3-plugin-ui-icon a3-plugin-ui-help-icon" data-trigger="hover" data-content="' . esc_attr( $tip ) . '"></div>';
	
			}
			
			// Remove [, ] characters from id argument
			$child_key = false;
			if ( strstr( $value['id'], '[' ) ) {
				parse_str( esc_attr( $value['id'] ), $option_array );
	
				// Option name is first key
				$option_keys = array_keys( $option_array );
				$first_key = current( $option_keys );
					
				$id_attribute		= $first_key;
				
				$child_key = key( $option_array[ $id_attribute ] );
			} else {
				$id_attribute		= esc_attr( $value['id'] );
			}
			
			// Get option value when option name is not parse or when it's spearate option
			if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
				$option_value		= $this->settings_get_option( $value['id'], $value['default'] );
			}
			// Get option value when it's an element from option array 
			else {
				if ( $child_key != false ) {
					$option_value 		= ( isset( $option_values[ $id_attribute ][ $child_key ] ) ) ? $option_values[ $id_attribute ][ $child_key ] : $value['default'];
				} else {
					$option_value 		= ( isset( $option_values[ $id_attribute ] ) ) ? $option_values[ $id_attribute ] : $value['default'];
				}
			}
					
			// Generate name and id attributes
			if ( trim( $option_name ) == '' ) {
				$name_attribute		= esc_attr( $value['id'] );
			} elseif ( $value['separate_option'] != false ) {
				$name_attribute		= esc_attr( $value['id'] );
				$id_attribute		= esc_attr( $option_name ) . '_' . $id_attribute;
			} else {
				// Array value
				if ( strstr( $value['id'], '[' ) ) {
					$name_attribute	= esc_attr( $option_name ) . '[' . $id_attribute . ']' . str_replace( $id_attribute . '[', '[', esc_attr( $value['id'] ) );
				} else {
					$name_attribute	= esc_attr( $option_name ) . '[' . esc_attr( $value['id'] ) . ']';
				}
				$id_attribute		= esc_attr( $option_name ) . '_' . $id_attribute;
			}

			// Update id attribute if current element is child of array
			if ( $child_key != false ) {
				$id_attribute .= '_' . $child_key;
			}

			// Switch based on type
			switch( $value['type'] ) {

				// Row
				case 'row':

					if ( $end_heading_id !== false && ! $closed_panel_inner ) {
						if ( trim( $end_heading_id ) != '' ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $end_heading_id ) . '_end' );
						echo '</table>' . "\n\n";
						echo '</div>' . "\n\n";
						if ( trim( $end_heading_id ) != '' ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $end_heading_id ) . '_after' );

						$closed_panel_inner = true;
					}

					if ( $header_sub_box_opening ) {
						$header_sub_box_opening = false;

						// close box inside
						echo '</div>' . "\n\n";

						// close panel box
						echo '</div>' . "\n\n";
					}

					if ( $header_box_opening ) {
						$header_box_opening = false;

						// close box inside
						echo '</div>' . "\n\n";

						// close panel box
						echo '</div>' . "\n\n";
					}

					if ( $had_first_column ) {
						// close panel column
						echo '</div>' . "\n\n";
					}

					if ( $had_first_row ) {
						// close panel row
						echo '</div>' . "\n\n";

						// open panel column
						echo '<div class="a3rev_panel_row">' . "\n\n";
					}

					$had_first_column = false;
					$had_first_row    = true;

				break;

				// Column
				case 'column':

					if ( $end_heading_id !== false && ! $closed_panel_inner ) {
						if ( trim( $end_heading_id ) != '' ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $end_heading_id ) . '_end' );
						echo '</table>' . "\n\n";
						echo '</div>' . "\n\n";
						if ( trim( $end_heading_id ) != '' ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $end_heading_id ) . '_after' );

						$closed_panel_inner = true;
					}

					if ( $header_sub_box_opening ) {
						$header_sub_box_opening = false;

						// close box inside
						echo '</div>' . "\n\n";

						// close panel box
						echo '</div>' . "\n\n";
					}

					if ( $header_box_opening ) {
						$header_box_opening = false;

						// close box inside
						echo '</div>' . "\n\n";

						// close panel box
						echo '</div>' . "\n\n";
					}

					if ( $had_first_column ) {
						// close panel column
						echo '</div>' . "\n\n";

						// open panel column
						echo '<div class="a3rev_panel_column">' . "\n\n";
					} else {
						// open panel column
						echo '<div class="a3rev_panel_column">' . "\n\n";
					}

					$had_first_column = true;
					$had_first_row    = true;

				break;

				// Heading
				case 'heading':

					$is_box = false;
					if ( isset( $value['is_box'] ) && true == $value['is_box'] ) {
						$is_box = true;
					}

					$is_sub = false;
					if ( isset( $value['is_sub'] ) && true == $value['is_sub'] ) {
						$is_sub = true;
					}

					$count_heading++;
					if ( $count_heading > 1 && ! $closed_panel_inner )  {
						if ( trim( $end_heading_id ) != '' ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $end_heading_id ) . '_end' );
						echo '</table>' . "\n\n";
						echo '</div>' . "\n\n";
						if ( trim( $end_heading_id ) != '' ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $end_heading_id ) . '_after' );
					}
					if ( ! empty( $value['id'] ) )
						$end_heading_id = $value['id'];
					else
						$end_heading_id = '';

					if ( $header_sub_box_opening ) {
						$header_sub_box_opening = false;

						// close box inside
						echo '</div>' . "\n\n";

						// close panel box
						echo '</div>' . "\n\n";
					}

					if ( $is_box && $header_box_opening && ! $is_sub ) {
						$header_box_opening = false;

						// close box inside
						echo '</div>' . "\n\n";

						// close panel box
						echo '</div>' . "\n\n";
					}

					$view_doc = ( isset( $value['view_doc'] ) ) ? $value['view_doc'] : '';

					if ( ! empty( $value['id'] ) ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $value['id'] ) . '_before' );

					if ( $is_box ) {
						$heading_box_id = $count_heading;
						if ( ! empty( $value['id'] ) ) {
							$heading_box_id = $value['id'];
						}

						$toggle_box_class = 'enable_toggle_box_save';

						$opened_class = '';
						if ( in_array( $heading_box_id, $opened_box ) && 1 == $toggle_box_open ) {
							$opened_class = 'box_open';
						}

						if ( isset( $_POST['bt_save_settings']) && in_array( $heading_box_id, $opened_box ) ) {
							$opened_class = 'box_open';
						}

						// Change to open box for the heading set alway_open = true
						if ( isset( $value['alway_open'] ) && true == $value['alway_open'] ) {
							$opened_class = 'box_open';
						}

						// Change to close box for the heading set alway_close = true
						if ( isset( $value['alway_close'] ) && true == $value['alway_close'] ) {
							$opened_class = '';
						}

						// Make the box open on first load with this argument first_open = true
						if ( isset( $value['first_open'] ) && true == $value['first_open'] ) {
							$this_box_is_opened = get_user_meta( $user_id, $this->plugin_name . '-' . trim( $heading_box_id ) . '-' . 'opened', true );
							if ( empty( $this_box_is_opened ) ) {
								$opened_class = 'box_open';
								add_user_meta( $user_id, $this->plugin_name . '-' . trim( $heading_box_id ) . '-' . 'opened', 1 );
							}
						}

						$box_handle_class = '';
						if ( isset( $value['is_active'] ) && true == $value['is_active'] ) {
							$box_handle_class .= 'box_active';
						}

						if ( isset( $_GET['box_open'] ) && sanitize_text_field( $_GET['box_open'] ) == $value['id'] ) {
							$opened_class = 'box_open';
						}

						// open panel box
						echo '<div id="'. esc_attr( $value['id'] ) . '" class="a3rev_panel_box '. esc_attr( $value['class'] ) .'" style="'. esc_attr( $value['css'] ) .'">' . "\n\n";

						// open box handle
						echo '<div data-form-key="'. esc_attr( trim( $form_key ) ) .'" data-box-id="'. esc_attr( $heading_box_id ) .'" class="a3rev_panel_box_handle ' . $box_handle_class .'" >' . "\n\n";

						echo ( ! empty( $value['name'] ) ) ? '<h3 class="a3-plugin-ui-panel-box '. $toggle_box_class . ' ' . $opened_class . '">'. esc_html( $value['name'] ) .' '. wptexturize( $view_doc ) .'</h3>' : '';

						if ( stristr( $value['class'], 'pro_feature_fields' ) !== false && ! empty( $value['id'] ) ) $this->upgrade_top_message( true, sanitize_title( $value['id'] ) );
						elseif ( stristr( $value['class'], 'pro_feature_fields' ) !== false ) $this->upgrade_top_message( true );

						// close box handle
						echo '</div>' . "\n\n";

						// open box inside
						echo '<div id="'. esc_attr( $value['id'] ) . '_box_inside" class="a3rev_panel_box_inside '.$opened_class.'" >' . "\n\n";

						echo '<div class="a3rev_panel_inner">' . "\n\n";

						if ( $is_sub ) {
							// Mark this heading as a sub box is openning to check for close it on next header box
							$header_sub_box_opening = true;
						} else {
							// Mark this heading as a box is openning to check for close it on next header box
							$header_box_opening = true;
						}

					} else {
						echo '<div id="'. esc_attr( $value['id'] ) . '" class="a3rev_panel_inner '. esc_attr( $value['class'] ) .'" style="'. esc_attr( $value['css'] ) .'">' . "\n\n";
						if ( stristr( $value['class'], 'pro_feature_fields' ) !== false && ! empty( $value['id'] ) ) $this->upgrade_top_message( true, sanitize_title( $value['id'] ) );
						elseif ( stristr( $value['class'], 'pro_feature_fields' ) !== false ) $this->upgrade_top_message( true );

						echo ( ! empty( $value['name'] ) ) ? '<h3>'. esc_html( $value['name'] ) .' '. wptexturize( $view_doc ) .'</h3>' : '';
					}

					if ( ! empty( $value['desc'] ) ) {
						echo '<div class="a3rev_panel_box_description" >' . "\n\n";
						echo wpautop( wptexturize( $value['desc'] ) );
						echo '</div>' . "\n\n";
					}

					$closed_panel_inner = false;

					echo '<table class="form-table">' . "\n\n";

					if ( ! empty( $value['id'] ) ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $value['id'] ) . '_start' );
				break;

				// Google API Key input
				case 'google_api_key':

					$google_api_key        = $this->settings_get_option( $this->google_api_key_option );
					$google_api_key_enable = $this->settings_get_option( $this->google_api_key_option . '_enable', 0 );
					if ( ! isset( $value['checked_label'] ) ) $value['checked_label'] = __( 'ON', 'page-views-count' );
					if ( ! isset( $value['unchecked_label'] ) ) $value['unchecked_label'] = __( 'OFF', 'page-views-count' );

					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $this->google_api_key_option ); ?>"><?php echo __( 'Google Fonts API', 'page-views-count' ); ?></label>
						</th>
						<td class="forminp forminp-onoff_checkbox forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
								name="<?php echo esc_attr( $this->google_api_key_option ); ?>_enable"
                                id="<?php echo esc_attr( $this->google_api_key_option ); ?>_enable"
								class="a3rev-ui-onoff_checkbox a3rev-ui-onoff_google_api_key_enable"
                                checked_label="<?php echo esc_html( $value['checked_label'] ); ?>"
                                unchecked_label="<?php echo esc_html( $value['unchecked_label'] ); ?>"
                                type="checkbox"
								value="1"
								<?php checked( $google_api_key_enable, 1 ); ?>
								/> <span class="description" style="margin-left:5px;"><?php echo __( 'ON to connect to Google Fonts API and have auto font updates direct from Google.', 'page-views-count' ); ?></span>

							<div>&nbsp;</div>
							<div class="a3rev-ui-google-api-key-container" style="<?php if( 1 != $google_api_key_enable ) { echo 'display: none;'; } ?>">
								<?php 
									if ( ! empty( $description ) ) { 
										echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ 
									} else {
								?>
								<div class="a3rev-ui-google-api-key-description"><?php echo sprintf( __( "Enter your existing Google Fonts API Key below. Don't have a key? Visit <a href='%s' target='_blank'>Google Developer API</a> to create a key" ), 'https://developers.google.com/fonts/docs/developer_api#APIKey' ); ?></div>
								<?php } ?>
								<div class="a3rev-ui-google-api-key-inside 
									<?php
									if ( $GLOBALS[$this->plugin_prefix.'fonts_face']->is_valid_google_api_key() ) {
										echo 'a3rev-ui-google-valid-key';
									} elseif ( '' != $google_api_key ) {
										echo 'a3rev-ui-google-unvalid-key';
									}
									?>
									">
									<input
										data-type="font"
										name="<?php echo esc_attr( $this->google_api_key_option ); ?>"
										id="<?php echo esc_attr( $this->google_api_key_option ); ?>"
										type="text"
										style="<?php echo esc_attr( $value['css'] ); ?>"
										value="<?php echo esc_attr( $google_api_key ); ?>"
										class="a3rev-ui-text a3rev-ui-google-api-key a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> <?php echo esc_attr( $value['class'] ); ?>"
		                                placeholder="<?php echo __( 'Google Fonts API Key' ); ?>"
										<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
										/>
									<button
									name="<?php echo esc_attr( $this->google_api_key_option ); ?>_validate_bt"
									id="<?php echo esc_attr( $this->google_api_key_option ); ?>_validate_bt"
									type="button"
									class="a3rev-ui-google-api-key-validate-button a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-button"><?php echo __( 'Validate' ); ?></button>
									<p class="a3rev-ui-google-valid-key-message"><?php echo __( 'Your Google API Key is valid and automatic font updates are enabled.' ); ?></p>
									<p class="a3rev-ui-google-unvalid-key-message"><?php echo __( 'Please enter a valid Google API Key.' ); ?></p>
								</div>
							</div>
						</td>
					</tr><?php

				break;

				// Google Map API Key input
				case 'google_map_api_key':

					$google_map_api_key        = $this->settings_get_option( $this->google_map_api_key_option );
					$google_map_api_key_enable = $this->settings_get_option( $this->google_map_api_key_option . '_enable', 0 );
					if ( ! isset( $value['checked_label'] ) ) $value['checked_label'] = __( 'ON', 'page-views-count' );
					if ( ! isset( $value['unchecked_label'] ) ) $value['unchecked_label'] = __( 'OFF', 'page-views-count' );

					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $this->google_map_api_key_option ); ?>"><?php echo __( 'Google Maps API', 'page-views-count' ); ?></label>
						</th>
						<td class="forminp forminp-onoff_checkbox forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
								name="<?php echo esc_attr( $this->google_map_api_key_option ); ?>_enable"
                                id="<?php echo esc_attr( $this->google_map_api_key_option ); ?>_enable"
								class="a3rev-ui-onoff_checkbox a3rev-ui-onoff_google_api_key_enable"
                                checked_label="<?php echo esc_html( $value['checked_label'] ); ?>"
                                unchecked_label="<?php echo esc_html( $value['unchecked_label'] ); ?>"
                                type="checkbox"
								value="1"
								<?php checked( $google_map_api_key_enable, 1 ); ?>
								/> <span class="description" style="margin-left:5px;"><?php echo __( 'Switch ON to connect to Google Maps API', 'page-views-count' ); ?></span>

							<div>&nbsp;</div>
							<div class="a3rev-ui-google-api-key-container" style="<?php if( 1 != $google_map_api_key_enable ) { echo 'display: none;'; } ?>">
							<?php 
								if ( ! empty( $description ) ) { 
									echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ 
								} else {
							?>
								<div class="a3rev-ui-google-api-key-description" style="margin-bottom:5px;"><?php echo sprintf( __( "Enter your Google Maps API Key and save changes, or go to <a href='%s' target='_blank'>Google Maps API</a> to create a new key. The key must have the Geocoding API, Maps Embed API and Maps JavaScript API as a minimum." ), 'https://developers.google.com/maps/documentation/javascript/get-api-key' ); ?></div>
							<?php } ?>
								<div class="a3rev-ui-google-api-key-inside 
									<?php
									if ( $this->is_valid_google_map_api_key() ) {
										echo 'a3rev-ui-google-valid-key';
									} elseif ( '' != $google_map_api_key ) {
										echo 'a3rev-ui-google-unvalid-key';
									}
									?>
									">
									<input
										data-type="map"
										name="<?php echo esc_attr( $this->google_map_api_key_option ); ?>"
										id="<?php echo esc_attr( $this->google_map_api_key_option ); ?>"
										type="text"
										style="<?php echo esc_attr( $value['css'] ); ?>"
										value="<?php echo esc_attr( $google_map_api_key ); ?>"
										class="a3rev-ui-text a3rev-ui-google-api-key a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> <?php echo esc_attr( $value['class'] ); ?>"
		                                placeholder="<?php echo __( 'Google Map API Key' ); ?>"
										<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
										/>
									<button
									name="<?php echo esc_attr( $this->google_map_api_key_option ); ?>_validate_bt"
									id="<?php echo esc_attr( $this->google_map_api_key_option ); ?>_validate_bt"
									type="button"
									class="a3rev-ui-google-api-key-validate-button a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-button"><?php echo __( 'Validate' ); ?></button>
									<p class="a3rev-ui-google-valid-key-message"><?php echo __( 'Your Google API Key is valid.' ); ?></p>
									<p class="a3rev-ui-google-unvalid-key-message"><?php echo __( 'Please enter a valid Google API Key.' ); ?></p>
								</div>
							</div>
						</td>
					</tr><?php

				break;

				// Manual Check New Version when click on the button instead of wait for daily
				case 'manual_check_version':

					$check_version_class = 'a3rev-ui-new-version-message';

					if ( is_multisite() ) {
						$version_message = __( 'Sorry, this feature just for Network Admin.', 'page-views-count' );
					} else {
						global $a3_dashboard_plugin_requirement;

						if ( ! $a3_dashboard_plugin_requirement->is_installed() ) {
							$version_message = sprintf( __( 'You need to install and activate the <a title="" href="%s" target="_parent">a3rev Dashboard plugin</a> for manage version and get auto upgrade to latest version.', 'page-views-count' ), $a3_dashboard_plugin_requirement->install_url() );
						} elseif ( ! $a3_dashboard_plugin_requirement->is_activated() ) {
							$version_message = sprintf( __( 'You need to activate the <a title="" href="%s" target="_parent">a3rev Dashboard plugin</a> for manage version and get auto upgrade to latest version.', 'page-views-count' ), $a3_dashboard_plugin_requirement->activate_url() );
						} elseif ( function_exists( 'is_a3_club_membership' ) && ! is_a3_club_membership() ) {
							$version_message = sprintf( __( 'You need to go to <a title="" href="%s">a3 Dashboard Main page</a> for login before check for Update. Use your account creds on <a href="https://a3rev.com" target="_parent">a3rev.com</a> to login.', 'page-views-count' ), self_admin_url( 'admin.php?page=a3rev-dashboard' ) );
						} else {
							$check_version_class = 'a3rev-ui-latest-version-message';
							$version_message = sprintf( __( 'Go to <a href="%s">a3 Dashboard Main page</a> and hit on <strong>CHECK NOW</strong> button to manual check for Update.', 'page-views-count' ), self_admin_url( 'admin.php?page=a3rev-dashboard' ) );
						}
					}

					?><tr valign="top">
						<td colspan="2">
							<p class="a3rev-ui-check-version-message <?php echo esc_attr( $check_version_class ); ?>"><?php echo $version_message; ?></p>
						</td>
					</tr><?php

				break;

				// Ajax Submit type
				case 'ajax_submit' :
					$button_name      = $value['button_name'];
					$progressing_text = $value['progressing_text'];
					$completed_text   = $value['completed_text'];
					$successed_text   = $value['successed_text'];
					$errors_text      = $value['errors_text'];
					$submit_data      = json_encode( $value['submit_data'] );

					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp">

                            <div class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-control">

								<button
									name="<?php echo $name_attribute; // XSS ok ?>"
									id="<?php echo esc_attr( $id_attribute ); ?>"
									data-submit_data="<?php echo esc_attr( $submit_data ); ?>"
									type="button"
									class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-button <?php echo esc_attr( $value['class'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								><?php echo esc_html( $button_name ); ?></button>
								<span class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-successed"><?php echo esc_html( $successed_text ); ?></span>
								<span class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-errors"><?php echo esc_html( $errors_text ); ?></span>

								<!-- Progress Bar -->
								<div class="a3rev-ui-progress-bar-wrap">
									<div class="a3rev-ui-progress-inner"></div>
									<div class="a3rev-ui-progressing-text"><?php echo esc_html( $progressing_text ); ?></div>
									<div class="a3rev-ui-completed-text"><?php echo esc_html( $completed_text ); ?></div>
								</div>

                           </div>
                           <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
						</td>
					</tr><?php

				break;

				// Ajax Multi Submit type
				case 'ajax_multi_submit' :
					$resubmit         = $value['resubmit'];
					$button_name      = $value['button_name'];
					$progressing_text = $value['progressing_text'];
					$completed_text   = $value['completed_text'];
					$successed_text   = $value['successed_text'];
					$errors_text      = $value['errors_text'];
					$statistic_column = isset( $value['statistic_column'] ) ? $value['statistic_column'] : 1;

					$notice          = isset( $value['notice'] ) ? $value['notice'] : '';
					$confirm_message = '';
					if ( isset( $value['confirm_run'] ) && $value['confirm_run']['allow'] ) {
						$confirm_message = isset( $value['confirm_run']['message'] ) ? $value['confirm_run']['message'] : '';
					}

					$multi_current_items = 0;
					$multi_total_items   = 0;

					$multi_submit = $value['multi_submit'];
					$multi_ajax  = array();
					if ( is_array( $multi_submit ) && count( $multi_submit ) > 0 ) {
						$number_ajax = 0;
						$old_item_id = '';
						foreach ( $multi_submit as $single_submit ) {
							$multi_current_items += (int) $single_submit['current_items'];
							$multi_total_items += (int) $single_submit['total_items'];

							$single_submit['next_item_id'] = '';
							$multi_ajax[$single_submit['item_id']] = $single_submit;

							if ( $number_ajax > 0 ) {
								$multi_ajax[$old_item_id]['next_item_id'] = $single_submit['item_id'];
							}
							$old_item_id = $single_submit['item_id'];

							$number_ajax++;
						}
					}
					$multi_ajax = json_encode( $multi_ajax );

					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp">

                            <div class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-control">
								<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
								<button
									data-resubmit="<?php echo $resubmit ? 1 : 0 ; ?>"
									name="<?php echo $name_attribute; // XSS ok ?>"
									id="<?php echo esc_attr( $id_attribute ); ?>"
									data-multi_ajax="<?php echo esc_attr( $multi_ajax ); ?>"
									type="button"
									class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-button <?php echo esc_attr( $value['class'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								<?php if ( ! empty( $confirm_message ) ) { ?>
									data-confirm_message="<?php echo esc_attr( $confirm_message ); ?>"
								<?php } ?> 
								><?php echo esc_html( $button_name ); ?></button>
								<span class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-successed"><?php echo esc_html( $successed_text ); ?></span>
								<span class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-errors"><?php echo esc_html( $errors_text ); ?></span>

								<!-- Progress Bar -->
								<?php if ( ! empty( $notice ) ) { ?>
								<div class="a3rev-ui-progress-notice"><?php echo esc_html( $notice ); ?></div>
								<?php } ?>
								<div class="a3rev-ui-progress-bar-wrap">
									<div class="a3rev-ui-progress-inner" data-current="<?php echo esc_attr( $multi_current_items ); ?>" data-total="<?php echo esc_attr( $multi_total_items ); ?>" ></div>
									<div class="a3rev-ui-progressing-text"><?php echo esc_html( $progressing_text ); ?></div>
									<div class="a3rev-ui-completed-text"><?php echo esc_html( $completed_text ); ?></div>
								</div>
								<div style="clear: both;"></div>

								<!-- Status Object -->
								<div class="a3rev-ui-statistics-wrap">
								<?php if ( $multi_total_items > 0 ) {
									$column_width = round( (100 / $statistic_column ), 2, PHP_ROUND_HALF_DOWN );
									foreach ( $multi_submit as $single_submit ) {

										$current_items = (int) $single_submit['current_items'];
										$total_items   = (int) $single_submit['total_items'];

										// Calculate deg value for cirlce
										$current_deg = 360;
										$left_deg    = 360;
										$right_deg   = 180;
										$pie_class   = 'pie-more-50';
										if ( $current_items < $total_items ) {
											$current_deg = round( ( $current_items / $total_items ) * 360 );
										}
										if ( $current_deg <= 180 ) {
											$left_deg = $right_deg = $current_deg;
											$pie_class = '';
										} else {
											$right_deg = 180;
											$left_deg = $current_deg;
										}

										$statistic_customizer = isset ( $single_submit['statistic_customizer'] ) ? $single_submit['statistic_customizer'] : false;
										if ( $statistic_customizer ) {
											$current_color = isset( $statistic_customizer['current_color'] ) ? $statistic_customizer['current_color'] : '';
										}
								?>
									<div style="<?php echo ( isset( $single_submit['show_statistic'] ) && ! $single_submit['show_statistic'] ) ? 'display:none;' : ''; ?> width: <?php echo esc_attr( $column_width ); ?>%;" class="a3rev-ui-statistic-item a3rev-ui-statistic-<?php echo esc_attr( $single_submit['item_id'] ); ?>">
										<div class="a3rev-ui-pie-wrap">
											<div class="a3rev-ui-pie <?php echo esc_attr( $pie_class); ?>">
												<div class="a3rev-ui-pie-left-side a3rev-ui-pie-half-circle" style="transform: rotate(<?php echo esc_attr( $left_deg ); ?>deg); <?php echo ( ! empty( $current_color ) ? 'border-color:' . esc_attr( $current_color ) : '' ); ?>"></div>
												<div class="a3rev-ui-pie-right-side a3rev-ui-pie-half-circle" style="transform: rotate(<?php echo esc_attr( $right_deg ); ?>deg); <?php echo ( ! empty( $current_color ) ? 'border-color:' . esc_attr( $current_color ) : '' ); ?>"></div>
											</div>
											<div class="a3rev-ui-pie-shadow"></div>
										</div>
										<div class="a3rev-ui-statistic-text">
											<span class="a3rev-ui-statistic-current-item" data-current="<?php echo esc_attr( $current_items ); ?>" ><?php echo esc_html( $current_items ); ?></span>
											<span class="a3rev-ui-statistic-separate">/</span>
											<span class="a3rev-ui-statistic-total-item"><?php echo esc_html( $total_items ); ?></span>
											<br />
											<span class="a3rev-ui-statistic-item-name"><?php echo esc_html( $single_submit['item_name'] ); ?></span>
										</div>
									</div>
								<?php
									}
								} ?>
								</div>
								<div style="clear: both;"></div>
                           </div>
						</td>
					</tr><?php

				break;

				// Toggle Box Open type
				case 'onoff_toggle_box' :

					$option_value = $this->settings_get_option( $this->toggle_box_open_option, 0 );
					if ( ! isset( $value['checked_label'] ) ) $value['checked_label'] = __( 'ON', 'page-views-count' );
					if ( ! isset( $value['unchecked_label'] ) ) $value['unchecked_label'] = __( 'OFF', 'page-views-count' );

					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $this->toggle_box_open_option ); ?>"><?php echo __( 'Open Box Display', 'page-views-count' ); ?></label>
						</th>
						<td class="forminp forminp-onoff_checkbox forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
								name="<?php echo esc_attr( $this->toggle_box_open_option ); ?>"
                                id="<?php echo esc_attr( $this->toggle_box_open_option ); ?>"
								class="a3rev-ui-onoff_checkbox a3rev-ui-onoff_toggle_box <?php echo esc_attr( $value['class'] ); ?>"
                                checked_label="<?php echo esc_html( $value['checked_label'] ); ?>"
                                unchecked_label="<?php echo esc_html( $value['unchecked_label'] ); ?>"
                                type="checkbox"
								value="1"
								<?php checked( $option_value, 1 ); ?>
								<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/> <span class="description" style="margin-left:5px;"><?php echo __( 'ON and each admin panel setting box OPEN | CLOSED position are saved each time changes are SAVED.', 'page-views-count' ); ?></span>
                        </td>
					</tr><?php
				break;

				// Standard text inputs and subtypes like 'number'
				case 'text':
				case 'email':
				case 'number':
				case 'password' :
	
					$type 			= $value['type'];
	
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
								name="<?php echo $name_attribute; // XSS ok ?>"
								id="<?php echo esc_attr( $id_attribute ); ?>"
								type="<?php echo esc_attr( $type ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> <?php echo esc_attr( $value['class'] ); ?>"
                                placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
						</td>
					</tr><?php
				break;
				
				// Color
				case 'color' :
					
					if ( trim( $value['default'] ) == '' ) $value['default'] = '#515151';
					$default_color = ' data-default-color="' . esc_attr( $value['default'] ) . '"';
					if ( '' == trim( $option_value ) ) $option_value = 'transparent';

					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
								name="<?php echo $name_attribute; // XSS ok ?>"
								id="<?php echo esc_attr( $id_attribute ); ?>"
								type="text"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="a3rev-color-picker"
								<?php echo $default_color // XSS ok; ?>
								/> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
						</td>
					</tr><?php

				break;

				// Background Color
				case 'bg_color' :

					if ( ! isset( $option_value['enable'] ) ) $option_value['enable'] = 0;
					$enable		= $option_value['enable'];

					if ( trim( $value['default']['color'] ) == '' ) $value['default']['color'] = '#515151';
					$default_color = ' data-default-color="' . esc_attr( $value['default']['color'] ) . '"';

					$color = $option_value['color'];
					if ( '' == trim( $color ) ) $color = 'transparent';

					?><tr valign="top">
						<th scope="row" class="titledesc">
							<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
									name="<?php echo $name_attribute; ?>[enable]"
									id="<?php echo esc_attr( $id_attribute ); ?>"
									class="a3rev-ui-bg_color-enable a3rev-ui-onoff_checkbox <?php echo esc_attr( $value['class'] ); ?>"
									checked_label="<?php _e( 'ON', 'page-views-count' ); ?>"
									unchecked_label="<?php _e( 'OFF', 'page-views-count' ); ?>"
									type="checkbox"
									value="1"
									<?php checked( 1, $enable ); ?>
									<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<div style="clear:both;"></div>
							<div class="a3rev-ui-bg_color-enable-container">
							<input
								name="<?php echo $name_attribute; ?>[color]"
								id="<?php echo esc_attr( $id_attribute ); ?>-color"
								type="text"
								value="<?php echo esc_attr( $color ); ?>"
								class="a3rev-color-picker"
								<?php echo $default_color; // XSS ok ?>
								/>
							</div>
						</td>
					</tr><?php

				break;

				// Textarea
				case 'textarea':

					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
	
							<textarea
								name="<?php echo $name_attribute; // XSS ok ?>"
								id="<?php echo esc_attr( $id_attribute ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> <?php echo esc_attr( $value['class'] ); ?>"
                                placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								><?php echo esc_textarea( $option_value );  ?></textarea>
						</td>
					</tr><?php
				break;
	
				// Select boxes
				case 'select' :
				case 'multiselect' :
				
					if ( trim( $value['class'] ) == '' ) $value['class'] = 'chzn-select';
					if ( is_rtl() ) {
						$value['class'] .= ' chzn-rtl';
					}
					if ( ! isset( $value['options'] ) ) $value['options'] = array();

					$is_ajax = false;
					if ( isset( $value['options_url'] ) && ! empty( $value['options_url'] ) ) {
						$is_ajax = true;
						$value['class'] .= ' chzn-select-ajaxify';
					}
		
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<select
								name="<?php echo $name_attribute; // XSS ok ?><?php if ( $value['type'] == 'multiselect' ) echo '[]'; ?>"
								id="<?php echo esc_attr( $id_attribute ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> <?php echo esc_attr( $value['class'] ); ?>"
								data-placeholder="<?php echo esc_html( $value['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								<?php if ( $value['type'] == 'multiselect' ) echo 'multiple="multiple"'; ?>
								<?php if ( $is_ajax ) {
									echo 'options_url="'.esc_url( $value['options_url'] ).'"';
									echo 'data-no_results_text="Please enter 3 or more characters"';
								}
								?>
								>
								<?php
								if ( is_array( $value['options'] ) && count( $value['options'] ) > 0 ) {
									foreach ( $value['options'] as $key => $val ) {
										if ( is_array( $val ) ) {
										?>
										<optgroup label="<?php echo esc_attr( $key ); ?>">
										<?php
											foreach ( $val as $sub_key => $sub_val ) {
										?>
											<option value="<?php echo esc_attr( $sub_key ); ?>" <?php
	
												if ( is_array( $option_value ) )
													selected( in_array( $sub_key, $option_value ), true );
												else
													selected( $option_value, $sub_key );
		
											?>><?php echo esc_html( $sub_val ); ?></option>
										<?php
											}
										?>
										</optgroup>
										<?php
										} else {
										?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php
	
											if ( is_array( $option_value ) )
												selected( in_array( $key, $option_value ), true );
											else
												selected( $option_value, $key );
	
										?>><?php echo esc_html( $val ); ?></option>
										<?php
										}
									}
								}
								?>
						   </select> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
						</td>
					</tr><?php
				break;
	
				// Radio inputs
				case 'radio' :
				
					if ( ! isset( $value['options'] ) ) $value['options'] = array();
	
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<fieldset>
								<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
								<ul>
								<?php
								if ( is_array( $value['options'] ) && count( $value['options'] ) > 0 ) {
									foreach ( $value['options'] as $val => $text ) {
										?>
										<li>
											<label><input
												name="<?php echo $name_attribute; // XSS ok ?>"
												value="<?php echo esc_attr( $val ); ?>"
												type="radio"
												style="<?php echo esc_attr( $value['css'] ); ?>"
												class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> <?php echo esc_attr( $value['class'] ); ?>"
												<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
												<?php checked( $val, $option_value ); ?>
												/> <span class="description" style="margin-left:5px;"><?php echo esc_html( $text ); ?></span></label>
										</li>
										<?php
									}
								}
								?>
								</ul>
							</fieldset>
						</td>
					</tr><?php
				break;
				
				// OnOff Radio inputs
				case 'onoff_radio' :
				
					if ( ! isset( $value['onoff_options'] ) ) $value['onoff_options'] = array();
	
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<fieldset>
								<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
								<ul>
								<?php
								if ( is_array( $value['onoff_options'] ) && count( $value['onoff_options'] ) > 0 ) {
									foreach ( $value['onoff_options'] as $i_option ) {
										if ( ! isset( $i_option['checked_label'] ) ) $i_option['checked_label'] = __( 'ON', 'page-views-count' );
										if ( ! isset( $i_option['unchecked_label'] ) ) $i_option['unchecked_label'] = __( 'OFF', 'page-views-count' );
										if ( ! isset( $i_option['val'] ) ) $i_option['val'] = 1;
										if ( ! isset( $i_option['text'] ) ) $i_option['text'] = '';
										?>
										<li>
                                            <input
                                                name="<?php echo $name_attribute; // XSS ok ?>"
                                                <?php if ( $i_option['val'] == $option_value ) echo ' checkbox-disabled="true" ' ; ?>
                                                class="a3rev-ui-onoff_radio <?php echo esc_attr( $value['class'] ); ?>"
                                                checked_label="<?php echo esc_html( $i_option['checked_label'] ); ?>"
                                                unchecked_label="<?php echo esc_html( $i_option['unchecked_label'] ); ?>"
                                                type="radio"
                                                value="<?php echo esc_attr( stripslashes( $i_option['val'] ) ); ?>"
                                                <?php checked( esc_attr( stripslashes( $i_option['val'] ) ), $option_value ); ?>
                                                <?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
                                                /> <span class="description" style="margin-left:5px;"><?php echo wptexturize( $i_option['text'] ); ?></span>
										</li>
										<?php
									}
								}
								?>
								</ul>
							</fieldset>
						</td>
					</tr><?php
				break;
	
				// Checkbox input
				case 'checkbox' :
		
					if ( ! isset( $value['checked_value'] ) ) $value['checked_value'] = 1;
					if ( ! isset( $value['hide_if_checked'] ) ) $value['hide_if_checked'] = false;
					if ( ! isset( $value['show_if_checked'] ) ) $value['show_if_checked'] = false;
	
					if ( ! isset( $value['checkboxgroup'] ) || ( isset( $value['checkboxgroup'] ) && $value['checkboxgroup'] == 'start' ) ) {
						?>
						<tr valign="top" class="<?php
							if ( $value['hide_if_checked'] == 'yes' || $value['show_if_checked']=='yes') echo 'hidden_option';
							if ( $value['hide_if_checked'] == 'option' ) echo 'hide_options_if_checked';
							if ( $value['show_if_checked'] == 'option' ) echo 'show_options_if_checked';
						?>">
						<th scope="row" class="titledesc">
                        	<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
                        </th>
						<td class="forminp forminp-checkbox">
							<fieldset>
						<?php
					} else {
						?>
						<fieldset class="<?php
							if ( $value['hide_if_checked'] == 'yes' || $value['show_if_checked'] == 'yes') echo 'hidden_option';
							if ( $value['hide_if_checked'] == 'option') echo 'hide_options_if_checked';
							if ( $value['show_if_checked'] == 'option') echo 'show_options_if_checked';
						?>">
						<?php
					}
	
					?>
						<legend class="screen-reader-text"><span><?php echo esc_html( $value['name'] ); ?></span></legend>
	
						<label for="<?php echo esc_attr( $id_attribute ); ?>">
						<input
							name="<?php echo $name_attribute; // XSS ok ?>"
							id="<?php echo esc_attr( $id_attribute ); ?>"
							type="checkbox"
							value="<?php echo esc_attr( stripslashes( $value['checked_value'] ) ); ?>"
							<?php checked( $option_value, esc_attr( stripslashes( $value['checked_value'] ) ) ); ?>
							<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
						/> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label> <?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
					<?php
	
					if ( ! isset( $value['checkboxgroup'] ) || ( isset( $value['checkboxgroup'] ) && $value['checkboxgroup'] == 'end' ) ) {
						?>
							</fieldset>
						</td>
						</tr>
						<?php
					} else {
						?>
						</fieldset>
						<?php
					}
	
				break;
				
				// OnOff Checkbox input
				case 'onoff_checkbox' :
				
					if ( ! isset( $value['checked_value'] ) ) $value['checked_value'] = 1;
					if ( ! isset( $value['checked_label'] ) ) $value['checked_label'] = __( 'ON', 'page-views-count' );
					if ( ! isset( $value['unchecked_label'] ) ) $value['unchecked_label'] = __( 'OFF', 'page-views-count' );
		
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
								name="<?php echo $name_attribute; // XSS ok ?>"
                                id="<?php echo esc_attr( $id_attribute ); ?>"
								class="a3rev-ui-onoff_checkbox <?php echo esc_attr( $value['class'] ); ?>"
                                checked_label="<?php echo esc_html( $value['checked_label'] ); ?>"
                                unchecked_label="<?php echo esc_html( $value['unchecked_label'] ); ?>"
                                type="checkbox"
								value="<?php echo esc_attr( stripslashes( $value['checked_value'] ) ); ?>"
								<?php checked( $option_value, esc_attr( stripslashes( $value['checked_value'] ) ) ); ?>
								<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                        </td>
					</tr><?php
	
				break;
				
				// Switcher Checkbox input
				case 'switcher_checkbox' :
				
					if ( ! isset( $value['checked_value'] ) ) $value['checked_value'] = 1;
					if ( ! isset( $value['checked_label'] ) ) $value['checked_label'] = __( 'ON', 'page-views-count' );
					if ( ! isset( $value['unchecked_label'] ) ) $value['unchecked_label'] = __( 'OFF', 'page-views-count' );
		
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
							<input
								name="<?php echo $name_attribute; // XSS ok ?>"
                                id="<?php echo esc_attr( $id_attribute ); ?>"
								class="a3rev-ui-onoff_checkbox <?php echo esc_attr( $value['class'] ); ?>"
                                checked_label="<?php echo esc_html( $value['checked_label'] ); ?>"
                                unchecked_label="<?php echo esc_html( $value['unchecked_label'] ); ?>"
                                type="checkbox"
								value="<?php echo esc_attr( stripslashes( $value['checked_value'] ) ); ?>"
								<?php checked( $option_value, esc_attr( stripslashes( $value['checked_value'] ) ) ); ?>
								<?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                        </td>
					</tr><?php
	
				break;
	
				// Image size settings
				case 'image_size' :
	
					$width 	= $option_value['width'];
					$height = $option_value['height'];
					$crop 	= checked( 1, $option_value['crop'], false );
	
					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
	
							<label><?php _e( 'Width', 'page-views-count' ); ?> <input name="<?php echo $name_attribute; ?>[width]" id="<?php echo esc_attr( $id_attribute ); ?>-width" type="text" class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-width" value="<?php echo esc_attr( $width ); ?>" /></label>
	
							<label><?php _e( 'Height', 'page-views-count' ); ?> <input name="<?php echo $name_attribute; ?>[height]" id="<?php echo esc_attr( $id_attribute ); ?>-height" type="text" class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-height" value="<?php echo esc_attr( $height ); ?>" /></label>
	
							<label><?php _e( 'Hard Crop', 'page-views-count' ); ?> <input name="<?php echo $name_attribute; ?>[crop]" id="<?php echo esc_attr( $id_attribute ); ?>-crop" type="checkbox" class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-crop" <?php echo $crop; ?> /></label>
	
							</td>
					</tr><?php
				break;
	
				// Single page selects
				case 'single_select_page' :
	
					if ( trim( $value['class'] ) == '' ) $value['class'] = 'chzn-select-deselect';
					if ( is_rtl() ) {
						$value['class'] .= ' chzn-rtl';
					}
					
					$args = array( 'name'				=> $name_attribute,
								   'id'					=> $id_attribute,
								   'sort_column' 		=> 'menu_order',
								   'sort_order'			=> 'ASC',
								   'show_option_none' 	=> ' ',
								   'class'				=> 'a3rev-ui-' . sanitize_title( $value['type'] ) . ' ' . $value['class'],
								   'echo' 				=> false,
								   'selected'			=> absint( $option_value )
								   );
	
					if( isset( $value['args'] ) )
						$args = wp_parse_args( $value['args'], $args );
	
					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp">
							<?php echo str_replace(' id=', " data-placeholder='" . esc_html( $value['placeholder'] ) .  "' style='" . esc_attr( $value['css'] ) . "' class='" . esc_attr( $value['class'] ) . "' id=", wp_dropdown_pages( $args ) ); ?> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
						</td>
					</tr><?php
				break;

				// Font Control
				case 'typography':

					$default_color = ' data-default-color="' . esc_attr( $value['default']['color'] ) . '"';

					if ( ! isset( $option_value['line_height'] ) ) {
						$option_value['line_height'] = '1.4em';
					}

					$size        = $option_value['size'];
					$line_height = $option_value['line_height'];
					$face        = $option_value['face'];
					$style       = $option_value['style'];
					$color       = $option_value['color'];

					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp">
                        	<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                            <div class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-control">
                        	<!-- Font Size -->
							<select
								name="<?php echo $name_attribute; ?>[size]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-size"
								class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-size chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
								>
								<?php
									for ( $i = 6; $i <= 70; $i++ ) {
										?>
										<option value="<?php echo esc_attr( $i ); ?>px" <?php
												selected( $size, $i.'px' );
										?>><?php echo esc_html( $i ); ?>px</option>
										<?php
									}
								?>
						   </select>
						   <!-- Line Height -->
							<select
								name="<?php echo $name_attribute; ?>[line_height]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-line_height"
								class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-line_height chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
								>
								<?php
									for ( $i = 0.6; $i <= 3.1; $i = $i + 0.1 ) {
										?>
										<option value="<?php echo esc_attr( $i ); ?>em" <?php
												selected( $line_height, $i.'em' );
										?>><?php echo esc_html( $i ); ?>em</option>
										<?php
									}
								?>
						   </select>
                           <!-- Font Face -->
							<select
								name="<?php echo $name_attribute; ?>[face]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-face"
								class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-face chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
								>
								<optgroup label="<?php _e( '-- Default Fonts --', 'page-views-count' ); ?>">
                                <?php
									foreach ( $GLOBALS[$this->plugin_prefix.'fonts_face']->get_default_fonts() as $val => $text ) {
										?>
                                        <option value="<?php echo esc_attr( $val ); ?>" <?php
												selected( esc_attr( $val ), esc_attr( $face ) );
										?>><?php echo esc_html( $text ); ?></option>
                                        <?php
									}
								?>
                                </optgroup>
                                <optgroup label="<?php _e( '-- Google Fonts --', 'page-views-count' ); ?>">
                                <?php
									foreach ( $GLOBALS[$this->plugin_prefix.'fonts_face']->get_google_fonts() as $font ) {
										?>
                                        <option value="<?php echo esc_attr( $font['name'] ); ?>" <?php
												selected( esc_attr( $font['name'] ), esc_attr( $face ) );
										?>><?php echo esc_html( $font['name'] ); ?></option>
                                        <?php
									}
								?>
                                </optgroup>
						   </select> 
                           
                           <!-- Font Weight -->
                           <select
								name="<?php echo $name_attribute; ?>[style]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-style"
								class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-style chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
								>
								<?php
									foreach ( $this->get_font_weights() as $val => $text ) {
										?>
										<option value="<?php echo esc_attr( $val ); ?>" <?php
												selected( esc_attr( $val ), esc_attr( $style ) );
										?>><?php echo esc_html( $text ); ?></option>
                                        <?php
									}
								?>
						   </select>
                           
                           <!-- Font Color -->
                           <input
								name="<?php echo $name_attribute; ?>[color]"
								id="<?php echo esc_attr( $id_attribute ); ?>-color"
								type="text"
								value="<?php echo esc_attr( $color ); ?>"
								class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>-color a3rev-color-picker"
								<?php echo $default_color; // XSS ok ?>
								/> 
                                
                           <!-- Preview Button -->
                           <div class="a3rev-ui-typography-preview"><a href="#" class="a3rev-ui-typography-preview-button button submit-button" title="<?php _e( 'Preview your customized typography settings', 'page-views-count'); ?>"><span>&nbsp;</span></a></div>
                           
                           </div>
                           
						</td>
					</tr><?php

				break;
				
				// Border Styles & Corner Control
				case 'border':
				
					if ( ! is_array( $value['default'] ) ) $value['default'] = array();
					
					// For Border Styles
					$default_color = ' data-default-color="' . esc_attr( $value['default']['color'] ) . '"';
					
					$width	= $option_value['width'];
					$style	= $option_value['style'];
					$color	= $option_value['color'];
					
					// For Border Corner
					if ( ! isset( $value['min'] ) ) $value['min'] = 0;
					if ( ! isset( $value['max'] ) ) $value['max'] = 100;
					if ( ! isset( $value['increment'] ) ) $value['increment'] = 1;
					
					if ( ! isset( $option_value['corner'] ) ) $option_value['corner'] = '';
					$corner					= $option_value['corner'];
					
					if ( ! isset( $option_value['rounded_value'] ) ) $option_value['rounded_value'] = 3;
					$rounded_value			= $option_value['rounded_value'];
					
					if ( ! isset( $option_value['top_left_corner'] ) ) $option_value['top_left_corner'] = 3;
					$top_left_corner		= $option_value['top_left_corner'];
					
					if ( ! isset( $option_value['top_right_corner'] ) ) $option_value['top_right_corner'] = 3;
					$top_right_corner		= $option_value['top_right_corner'];
					
					if ( ! isset( $option_value['bottom_left_corner'] ) ) $option_value['bottom_left_corner'] = 3;
					$bottom_left_corner		= $option_value['bottom_left_corner'];
					
					if ( ! isset( $option_value['bottom_right_corner'] ) ) $option_value['bottom_right_corner'] = 3;
					$bottom_right_corner	= $option_value['bottom_right_corner'];
					
					if ( trim( $rounded_value ) == '' || trim( $rounded_value ) <= 0  ) $rounded_value = $value['min'];
					$rounded_value = intval( $rounded_value );
					
					if ( trim( $top_left_corner ) == '' || trim( $top_left_corner ) <= 0  ) $top_left_corner = $rounded_value;
					$top_left_corner = intval( $top_left_corner );
					
					if ( trim( $top_right_corner ) == '' || trim( $top_right_corner ) <= 0  ) $top_right_corner = $rounded_value;
					$top_right_corner = intval( $top_right_corner );
					
					if ( trim( $bottom_left_corner ) == '' || trim( $bottom_left_corner ) <= 0  ) $bottom_left_corner = $rounded_value;
					$bottom_left_corner = intval( $bottom_left_corner );
					
					if ( trim( $bottom_right_corner ) == '' || trim( $bottom_right_corner ) <= 0  ) $bottom_right_corner = $rounded_value;
					$bottom_right_corner = intval( $bottom_right_corner );
				
					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp forminp-border_corner">
							<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                            <div class="a3rev-ui-settings-control">
                        	<!-- Border Width -->
							<select
								name="<?php echo $name_attribute; ?>[width]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-width"
								class="a3rev-ui-border_styles-width chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
								>
								<?php
									for ( $i = 0; $i <= 20; $i++ ) {
										?>
										<option value="<?php echo esc_attr( $i ); ?>px" <?php
												selected( $width, $i.'px' );
										?>><?php echo esc_html( $i ); ?>px</option>
										<?php
									}
								?>
						   </select> 
                           
                           <!-- Border Style -->
                           <select
								name="<?php echo $name_attribute; ?>[style]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-style"
								class="a3rev-ui-border_styles-style chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
								>
								<?php
									foreach ( $this->get_border_styles() as $val => $text ) {
										?>
										<option value="<?php echo esc_attr( $val ); ?>" <?php
												selected( esc_attr( $val ), esc_attr( $style ) );
										?>><?php echo esc_html( $text ); ?></option>
                                        <?php
									}
								?>
						   </select>
                           
                           <!-- Border Color -->
                           <input
								name="<?php echo $name_attribute; ?>[color]"
								id="<?php echo esc_attr( $id_attribute ); ?>-color"
								type="text"
								value="<?php echo esc_attr( $color ); ?>"
								class="a3rev-ui-border_styles-color a3rev-color-picker"
								<?php echo $default_color; // XSS ok ?>
								/>
                           
                           <!-- Preview Button -->
                           <div class="a3rev-ui-settings-preview"><a href="#" class="a3rev-ui-border-preview-button a3rev-ui-settings-preview-button button submit-button" title="<?php _e( 'Preview your customized border settings', 'page-views-count' ); ?>"><span>&nbsp;</span></a></div>
                           <span class="description" style="margin-left:5px;"><?php echo __( '0px = No Border', 'page-views-count' ); ?></span>
                           <div style="clear:both; margin-bottom:10px"></div>
                           
                           <!-- Border Corner : Rounded or Square -->
								<input
                                    name="<?php echo $name_attribute; ?>[corner]"
                                    id="<?php echo esc_attr( $id_attribute ); ?>"
                                    class="a3rev-ui-border-corner a3rev-ui-onoff_checkbox <?php echo esc_attr( $value['class'] ); ?>"
                                    checked_label="<?php _e( 'Rounded', 'page-views-count' ); ?>"
                                    unchecked_label="<?php _e( 'Square', 'page-views-count' ); ?>"
                                    type="checkbox"
                                    value="rounded"
                                    <?php checked( 'rounded', $corner ); ?>
                                    <?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/> 
                                
							<!-- Border Rounded Value -->
								<div class="a3rev-ui-border-corner-value-container">
                                	<div class="a3rev-ui-border_corner-top_left">
                                        <span class="a3rev-ui-border_corner-span"><?php _e( 'Top Left Corner', 'page-views-count' ); ?></span>
                                        <div class="a3rev-ui-slide-container">
                                            <div class="a3rev-ui-slide-container-start">
                                                <div class="a3rev-ui-slide-container-end">
                                                    <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>-top_left_corner_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                                                </div>
                                            </div>
                                            <div class="a3rev-ui-slide-result-container">
                                            <input
                                                readonly="readonly"
                                                name="<?php echo $name_attribute; ?>[top_left_corner]"
                                                id="<?php echo esc_attr( $id_attribute ); ?>-top_left_corner"
                                                type="text"
                                                value="<?php echo esc_attr( $top_left_corner ); ?>"
                                                class="a3rev-ui-border_top_left_corner a3rev-ui-slider"
                                            /> <span class="a3rev-ui-border_corner-px">px</span>
                                            </div>
                                		</div>
                                    </div>
                                    <div class="a3rev-ui-border_corner-top_right">
                                        <span class="a3rev-ui-border_corner-span"><?php _e( 'Top Right Corner', 'page-views-count' ); ?></span> 
                                        <div class="a3rev-ui-slide-container">
                                            <div class="a3rev-ui-slide-container-start">
                                                <div class="a3rev-ui-slide-container-end">
                                                    <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>-top_right_corner_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                                                </div>
                                            </div>
                                            <div class="a3rev-ui-slide-result-container">
                                            <input
                                                readonly="readonly"
                                                name="<?php echo $name_attribute; ?>[top_right_corner]"
                                                id="<?php echo esc_attr( $id_attribute ); ?>-top_right_corner"
                                                type="text"
                                                value="<?php echo esc_attr( $top_right_corner ); ?>"
                                                class="a3rev-ui-border_top_right_corner a3rev-ui-slider"
                                            /> <span class="a3rev-ui-border_corner-px">px</span>
                                            </div>
                                		</div>
                                    </div>
                                    <div class="a3rev-ui-border_corner-bottom_right">
                                        <span class="a3rev-ui-border_corner-span"><?php _e( 'Bottom Right Corner', 'page-views-count' ); ?></span> 
                                        <div class="a3rev-ui-slide-container">
                                            <div class="a3rev-ui-slide-container-start">
                                                <div class="a3rev-ui-slide-container-end">
                                                    <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>-bottom_right_corner_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                                                </div>
                                            </div>
                                            <div class="a3rev-ui-slide-result-container">
                                            <input
                                                readonly="readonly"
                                                name="<?php echo $name_attribute; ?>[bottom_right_corner]"
                                                id="<?php echo esc_attr( $id_attribute ); ?>-bottom_right_corner"
                                                type="text"
                                                value="<?php echo esc_attr( $bottom_right_corner ); ?>"
                                                class="a3rev-ui-border_bottom_right_corner a3rev-ui-slider"
                                            /> <span class="a3rev-ui-border_corner-px">px</span>
                                            </div>
                                		</div>
                                    </div>
                                    <div class="a3rev-ui-border_corner-bottom_left">
                                        <span class="a3rev-ui-border_corner-span"><?php _e( 'Bottom Left Corner', 'page-views-count' ); ?></span>
                                        <div class="a3rev-ui-slide-container"> 
                                            <div class="a3rev-ui-slide-container-start">
                                                <div class="a3rev-ui-slide-container-end">
                                                    <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>-bottom_left_corner_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                                                </div>
                                            </div>
                                            <div class="a3rev-ui-slide-result-container">
                                            <input
                                                readonly="readonly"
                                                name="<?php echo $name_attribute; ?>[bottom_left_corner]"
                                                id="<?php echo esc_attr( $id_attribute ); ?>-bottom_left_corner"
                                                type="text"
                                                value="<?php echo esc_attr( $bottom_left_corner ); ?>"
                                                class="a3rev-ui-border_bottom_left_corner a3rev-ui-slider"
                                            /> <span class="a3rev-ui-border_corner-px">px</span>
                                            </div>
                                		</div>
                                    </div>
                                </div>
                                <div style="clear:both"></div>
							</div>
                        
                        </td>
					</tr><?php

				break;
				
				// Border Style Control
				case 'border_styles':
				
					$default_color = ' data-default-color="' . esc_attr( $value['default']['color'] ) . '"';
					
					$width	= $option_value['width'];
					$style	= $option_value['style'];
					$color	= $option_value['color'];
				
					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp">
							<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                            <div class="a3rev-ui-settings-control">
                        	<!-- Border Width -->
							<select
								name="<?php echo $name_attribute; ?>[width]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-width"
								class="a3rev-ui-border_styles-width chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
								>
								<?php
									for ( $i = 0; $i <= 20; $i++ ) {
										?>
										<option value="<?php echo esc_attr( $i ); ?>px" <?php
												selected( $width, $i.'px' );
										?>><?php echo esc_html( $i ); ?>px</option>
										<?php
									}
								?>
						   </select> 
                           
                           <!-- Border Style -->
                           <select
								name="<?php echo $name_attribute; ?>[style]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-style"
								class="a3rev-ui-border_styles-style chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
								>
								<?php
									foreach ( $this->get_border_styles() as $val => $text ) {
										?>
										<option value="<?php echo esc_attr( $val ); ?>" <?php
												selected( esc_attr( $val ), esc_attr( $style ) );
										?>><?php echo esc_html( $text ); ?></option>
                                        <?php
									}
								?>
						   </select>
                           
                           <!-- Border Color -->
                           <input
								name="<?php echo $name_attribute; ?>[color]"
								id="<?php echo esc_attr( $id_attribute ); ?>-color"
								type="text"
								value="<?php echo esc_attr( $color ); ?>"
								class="a3rev-ui-border_styles-color a3rev-color-picker"
								<?php echo $default_color; // XSS ok ?>
								/>
                           
                           <!-- Preview Button -->
                           <div class="a3rev-ui-settings-preview"><a href="#" class="a3rev-ui-border-preview-button a3rev-ui-settings-preview-button button submit-button" title="<?php _e( 'Preview your customized border styles settings', 'page-views-count' ); ?>"><span>&nbsp;</span></a></div>
                           <span class="description" style="margin-left:5px;"><?php echo __( '0px = No Border', 'page-views-count' ); ?></span>
                           </div>
                           
						</td>
					</tr><?php

				break;
				
				// Border Rounded Corners Control
				case 'border_corner':
					
					if ( ! isset( $value['min'] ) ) $value['min'] = 0;
					if ( ! isset( $value['max'] ) ) $value['max'] = 100;
					if ( ! isset( $value['increment'] ) ) $value['increment'] = 1;
					
					if ( ! isset( $option_value['corner'] ) ) $option_value['corner'] = '';
					$corner					= $option_value['corner'];
					
					if ( ! isset( $option_value['rounded_value'] ) ) $option_value['rounded_value'] = 3;
					$rounded_value			= $option_value['rounded_value'];
					
					if ( ! isset( $option_value['top_left_corner'] ) ) $option_value['top_left_corner'] = 3;
					$top_left_corner		= $option_value['top_left_corner'];
					
					if ( ! isset( $option_value['top_right_corner'] ) ) $option_value['top_right_corner'] = 3;
					$top_right_corner		= $option_value['top_right_corner'];
					
					if ( ! isset( $option_value['bottom_left_corner'] ) ) $option_value['bottom_left_corner'] = 3;
					$bottom_left_corner		= $option_value['bottom_left_corner'];
					
					if ( ! isset( $option_value['bottom_right_corner'] ) ) $option_value['bottom_right_corner'] = 3;
					$bottom_right_corner	= $option_value['bottom_right_corner'];
					
					if ( trim( $rounded_value ) == '' || trim( $rounded_value ) <= 0  ) $rounded_value = $value['min'];
					$rounded_value = intval( $rounded_value );
					
					if ( trim( $top_left_corner ) == '' || trim( $top_left_corner ) <= 0  ) $top_left_corner = $rounded_value;
					$top_left_corner = intval( $top_left_corner );
					
					if ( trim( $top_right_corner ) == '' || trim( $top_right_corner ) <= 0  ) $top_right_corner = $rounded_value;
					$top_right_corner = intval( $top_right_corner );
					
					if ( trim( $bottom_left_corner ) == '' || trim( $bottom_left_corner ) <= 0  ) $bottom_left_corner = $rounded_value;
					$bottom_left_corner = intval( $bottom_left_corner );
					
					if ( trim( $bottom_right_corner ) == '' || trim( $bottom_right_corner ) <= 0  ) $bottom_right_corner = $rounded_value;
					$bottom_right_corner = intval( $bottom_right_corner );
				
					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                            <div class="a3rev-ui-settings-control">	
                                <!-- Border Corner : Rounded or Square -->
                                <input
                                    name="<?php echo $name_attribute; ?>[corner]"
                                    id="<?php echo esc_attr( $id_attribute ); ?>"
                                    class="a3rev-ui-border-corner a3rev-ui-onoff_checkbox <?php echo esc_attr( $value['class'] ); ?>"
                                    checked_label="<?php _e( 'Rounded', 'page-views-count' ); ?>"
                                    unchecked_label="<?php _e( 'Square', 'page-views-count' ); ?>"
                                    type="checkbox"
                                    value="rounded"
                                    <?php checked( 'rounded', $corner ); ?>
                                    <?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/> 
                                
                                <!-- Preview Button -->
                               	<div class="a3rev-ui-settings-preview"><a href="#" class="a3rev-ui-border-preview-button a3rev-ui-settings-preview-button button submit-button" title="<?php _e( 'Preview your customized border settings', 'page-views-count' ); ?>"><span>&nbsp;</span></a></div>
                                <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                               	<!-- Border Rounded Value -->
								<div class="a3rev-ui-border-corner-value-container">
                                	<div class="a3rev-ui-border_corner-top_left">
                                        <span class="a3rev-ui-border_corner-span"><?php _e( 'Top Left Corner', 'page-views-count' ); ?></span>
                                        <div class="a3rev-ui-slide-container">
                                            <div class="a3rev-ui-slide-container-start">
                                                <div class="a3rev-ui-slide-container-end">
                                                    <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>-top_left_corner_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                                                </div>
                                            </div>
                                            <div class="a3rev-ui-slide-result-container">
                                            <input
                                                readonly="readonly"
                                                name="<?php echo $name_attribute; ?>[top_left_corner]"
                                                id="<?php echo esc_attr( $id_attribute ); ?>-top_left_corner"
                                                type="text"
                                                value="<?php echo esc_attr( $top_left_corner ); ?>"
                                                class="a3rev-ui-border_top_left_corner a3rev-ui-slider"
                                            /> <span class="a3rev-ui-border_corner-px">px</span>
                                            </div>
                                		</div>
                                    </div>
                                    <div class="a3rev-ui-border_corner-top_right">
                                        <span class="a3rev-ui-border_corner-span"><?php _e( 'Top Right Corner', 'page-views-count' ); ?></span>
                                        <div class="a3rev-ui-slide-container"> 
                                            <div class="a3rev-ui-slide-container-start">
                                                <div class="a3rev-ui-slide-container-end">
                                                    <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>-top_right_corner_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                                                </div>
                                            </div>
                                            <div class="a3rev-ui-slide-result-container">
                                            <input
                                                readonly="readonly"
                                                name="<?php echo $name_attribute; ?>[top_right_corner]"
                                                id="<?php echo esc_attr( $id_attribute ); ?>-top_right_corner"
                                                type="text"
                                                value="<?php echo esc_attr( $top_right_corner ); ?>"
                                                class="a3rev-ui-border_top_right_corner a3rev-ui-slider"
                                            /> <span class="a3rev-ui-border_corner-px">px</span>
                                            </div>
                                		</div>
                                    </div>
                                    <div class="a3rev-ui-border_corner-bottom_right">
                                        <span class="a3rev-ui-border_corner-span"><?php _e( 'Bottom Right Corner', 'page-views-count' ); ?></span>
                                        <div class="a3rev-ui-slide-container"> 
                                            <div class="a3rev-ui-slide-container-start">
                                                <div class="a3rev-ui-slide-container-end">
                                                    <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>-bottom_right_corner_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                                                </div>
                                            </div>
                                            <div class="a3rev-ui-slide-result-container">
                                            <input
                                                readonly="readonly"
                                                name="<?php echo $name_attribute; ?>[bottom_right_corner]"
                                                id="<?php echo esc_attr( $id_attribute ); ?>-bottom_right_corner"
                                                type="text"
                                                value="<?php echo esc_attr( $bottom_right_corner ); ?>"
                                                class="a3rev-ui-border_bottom_right_corner a3rev-ui-slider"
                                            /> <span class="a3rev-ui-border_corner-px">px</span>
                                            </div>
                                		</div>
                                    </div>
                                    <div class="a3rev-ui-border_corner-bottom_left">
                                        <span class="a3rev-ui-border_corner-span"><?php _e( 'Bottom Left Corner', 'page-views-count' ); ?></span> 
                                        <div class="a3rev-ui-slide-container">
                                            <div class="a3rev-ui-slide-container-start">
                                                <div class="a3rev-ui-slide-container-end">
                                                    <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>-bottom_left_corner_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                                                </div>
                                            </div>
                                            <div class="a3rev-ui-slide-result-container">
                                            <input
                                                readonly="readonly"
                                                name="<?php echo $name_attribute; ?>[bottom_left_corner]"
                                                id="<?php echo esc_attr( $id_attribute ); ?>-bottom_left_corner"
                                                type="text"
                                                value="<?php echo esc_attr( $bottom_left_corner ); ?>"
                                                class="a3rev-ui-border_bottom_left_corner a3rev-ui-slider"
                                            /> <span class="a3rev-ui-border_corner-px">px</span>
                                            </div>
                                		</div>
                                    </div>
                                </div>
                                <div style="clear:both"></div>
                            </div>
							<div style="clear:both"></div>
						</td>
					</tr><?php

				break;
				
				// Box Shadow Control
				case 'box_shadow':
				
					$default_color = ' data-default-color="' . esc_attr( $value['default']['color'] ) . '"';
					
					if ( ! isset( $option_value['enable'] ) ) $option_value['enable'] = 0;
					$enable		= $option_value['enable'];
					if ( ! isset( $option_value['inset'] ) ) $option_value['inset'] = '';
					$h_shadow	= $option_value['h_shadow'];
					$v_shadow	= $option_value['v_shadow'];
					$blur		= $option_value['blur'];
					$spread		= $option_value['spread'];
					$color		= $option_value['color'];
					$inset		= $option_value['inset'];
				
					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo esc_html( $value['name'] ); ?></th>
						<td class="forminp forminp-box_shadow">
                            <input
                                    name="<?php echo $name_attribute; ?>[enable]"
                                    id="<?php echo esc_attr( $id_attribute ); ?>"
                                    class="a3rev-ui-box_shadow-enable a3rev-ui-onoff_checkbox <?php echo esc_attr( $value['class'] ); ?>"
                                    checked_label="<?php _e( 'ON', 'page-views-count' ); ?>"
                                    unchecked_label="<?php _e( 'OFF', 'page-views-count' ); ?>"
                                    type="checkbox"
                                    value="1"
                                    <?php checked( 1, $enable ); ?>
                                    <?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/>
							<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                            <div style="clear:both;"></div>    
                            <div class="a3rev-ui-box_shadow-enable-container">
                            <div class="a3rev-ui-settings-control">
                        	<!-- Box Horizontal Shadow Size -->
							<select
								name="<?php echo $name_attribute; ?>[h_shadow]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-h_shadow"
								class="a3rev-ui-box_shadow-h_shadow chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
                                data-placeholder="<?php _e( 'Horizontal Shadow', 'page-views-count' ); ?>"
								>
								<?php
									for ( $i = -20; $i <= 20; $i++ ) {
										?>
										<option value="<?php echo esc_attr( $i ); ?>px" <?php
												selected( $h_shadow, $i.'px' );
										?>><?php echo esc_html( $i ); ?>px</option>
										<?php
									}
								?>
						   </select> 
                           
                        	<!-- Box Vertical Shadow Size -->
							<select
								name="<?php echo $name_attribute; ?>[v_shadow]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-v_shadow"
								class="a3rev-ui-box_shadow-v_shadow chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
                                data-placeholder="<?php _e( 'Vertical Shadow', 'page-views-count' ); ?>"
								>
								<?php
									for ( $i = -20; $i <= 20; $i++ ) {
										?>
										<option value="<?php echo esc_attr( $i ); ?>px" <?php
												selected( $v_shadow, $i.'px' );
										?>><?php echo esc_html( $i ); ?>px</option>
										<?php
									}
								?>
						   </select> 
                           
                           <!-- Box Blur Distance -->
							<select
								name="<?php echo $name_attribute; ?>[blur]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-blur"
								class="a3rev-ui-box_shadow-blur chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
                                data-placeholder="<?php _e( 'Blur Distance', 'page-views-count' ); ?>"
								>
								<?php
									for ( $i = 0; $i <= 20; $i++ ) {
										?>
										<option value="<?php echo esc_attr( $i ); ?>px" <?php
												selected( $blur, $i.'px' );
										?>><?php echo esc_html( $i ); ?>px</option>
										<?php
									}
								?>
						   </select> 
                           
                           <!-- Box Spread -->
							<select
								name="<?php echo $name_attribute; ?>[spread]"
                                id="<?php echo esc_attr( $id_attribute ); ?>-spread"
								class="a3rev-ui-box_shadow-spread chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>"
                                data-placeholder="<?php _e( 'Spread Size', 'page-views-count' ); ?>"
								>
								<?php
									for ( $i = 0; $i <= 20; $i++ ) {
										?>
										<option value="<?php echo esc_attr( $i ); ?>px" <?php
												selected( $spread, $i.'px' );
										?>><?php echo esc_html( $i ); ?>px</option>
										<?php
									}
								?>
						   </select> 
                           
                           <!-- Box Shadow Inset -->
                                <input
                                    name="<?php echo $name_attribute; ?>[inset]"
                                    id="<?php echo esc_attr( $id_attribute ); ?>"
                                    class="a3rev-ui-box_shadow-inset a3rev-ui-onoff_checkbox"
                                    checked_label="<?php _e( 'INNER', 'page-views-count' ); ?>"
                                    unchecked_label="<?php _e( 'OUTER', 'page-views-count' ); ?>"
                                    type="checkbox"
                                    value="inset"
                                    <?php checked( 'inset', $inset ); ?>
                                    <?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
								/> 
                           
                           <!-- Box Shadow Color -->
                           <input
								name="<?php echo $name_attribute; ?>[color]"
								id="<?php echo esc_attr( $id_attribute ); ?>-color"
								type="text"
								value="<?php echo esc_attr( $color ); ?>"
								class="a3rev-ui-box_shadow-color a3rev-color-picker"
								<?php echo $default_color; // XSS ok ?>
								/>
                        	
                            <!-- Preview Button -->
                           <div class="a3rev-ui-settings-preview"><a href="#" class="a3rev-ui-box_shadow-preview-button a3rev-ui-settings-preview-button button submit-button" title="<?php _e( 'Preview your customized box shadow settings', 'page-views-count'); ?>"><span>&nbsp;</span></a></div>   
                           </div>
                           <div style="clear:both;"></div>
                           </div>
						</td>
					</tr><?php

				break;
				
				// Slider Control
				case 'slider':
				
					if ( ! isset( $value['min'] ) ) $value['min'] = 0;
					if ( ! isset( $value['max'] ) ) $value['max'] = 100;
					if ( ! isset( $value['increment'] ) ) $value['increment'] = 1;
					if ( trim( $option_value ) == '' || trim( $option_value ) <= 0  ) $option_value = $value['min'];
					$option_value = intval( $option_value );
				
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                        <div class="a3rev-ui-slide-container">
                            <div class="a3rev-ui-slide-container-start"><div class="a3rev-ui-slide-container-end">
                                <div class="a3rev-ui-slide" id="<?php echo esc_attr( $id_attribute ); ?>_div" min="<?php echo esc_attr( $value['min'] ); ?>" max="<?php echo esc_attr( $value['max'] ); ?>" inc="<?php echo esc_attr( $value['increment'] ); ?>"></div>
                            </div></div>
                            <div class="a3rev-ui-slide-result-container">
                                <input
                                    readonly="readonly"
                                    name="<?php echo $name_attribute; // XSS ok ?>"
                                    id="<?php echo esc_attr( $id_attribute ); ?>"
                                    type="text"
                                    value="<?php echo esc_attr( $option_value ); ?>"
                                    class="a3rev-ui-slider"
                                    <?php echo implode( ' ', $custom_attributes );	// XSS ok ?>
                                    /> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							</div>
                        </div>
                        </td>
					</tr><?php
					
				break;
				
				// Upload Control
				case 'upload':
				
					$class = 'a3rev-ui-' . sanitize_title( $value['type'] ) . ' ' . esc_attr( $value['class'] );

					$strip_methods = true;
					if ( isset( $value['strip_methods'] ) ) {
						$strip_methods = $value['strip_methods'];
					}

					if ( strstr( $name_attribute, ']' ) ) {
						$attachment_id_name_attribute = substr_replace( $name_attribute, '_attachment_id', -1, 0 );
					} else {
						$attachment_id_name_attribute = $name_attribute.'_attachment_id';
					}
					$attachment_id = $this->settings_get_option( $attachment_id_name_attribute, 0 );
				
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                        	<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                        	<?php echo $GLOBALS[$this->plugin_prefix.'uploader']->upload_input( $name_attribute, $id_attribute, $option_value, $attachment_id, $value['default'], $value['name'], $class, esc_attr( $value['css'] ) , '', $strip_methods );?>
						</td>
					</tr><?php
									
				break;
				
				// WP Editor Control
				case 'wp_editor':
				
					if ( ! isset( $value['textarea_rows'] ) ) $value['textarea_rows'] = 15;
					
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                        	<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                            <?php remove_all_filters('mce_external_plugins'); ?>
                        	<?php wp_editor( 	$option_value, 
												$id_attribute, 
												array( 	'textarea_name' => $name_attribute, 
														'wpautop' 		=> true, 
														'editor_class'	=> 'a3rev-ui-' . sanitize_title( $value['type'] ) . ' ' . esc_attr( $value['class'] ), 
														'textarea_rows' => $value['textarea_rows'] ) ); ?> 
						</td>
					</tr><?php
					
				break;
				
				// Array Text Field Control
				case 'array_textfields':
					
					if ( !isset( $value['ids'] ) || !is_array( $value['ids'] ) || count( $value['ids'] ) < 1 ) break;
					
					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                        	<?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                        	<div class="a3rev-ui-array_textfields-container">
                           	<?php
							foreach ( $value['ids'] as $text_field ) {
						
								if ( ! isset( $text_field['id'] ) ) $text_field['id'] = '';
								if ( ! isset( $text_field['name'] ) ) $text_field['name'] = '';
								if ( ! isset( $text_field['class'] ) ) $text_field['class'] = '';
								if ( ! isset( $text_field['css'] ) ) $text_field['css'] = '';
								if ( ! isset( $text_field['default'] ) ) $text_field['default'] = '';
								
								// Remove [, ] characters from id argument
								$key = false;
								if ( strstr( $text_field['id'], '[' ) ) {
									parse_str( esc_attr( $text_field['id'] ), $option_array );
						
									// Option name is first key
									$option_keys = array_keys( $option_array );
									$first_key = current( $option_keys );
										
									$id_attribute		= $first_key;
									
									$key = key( $option_array[ $id_attribute ] );
								} else {
									$id_attribute		= esc_attr( $text_field['id'] );
								}
								
								// Get option value when option name is not parse or when it's spearate option
								if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
									$option_value		= $this->settings_get_option( $text_field['id'], $text_field['default'] );
								}
								// Get option value when it's an element from option array 
								else {
									if ( $key != false ) {
										$option_value	= ( isset( $option_values[ $id_attribute ][ $key ] ) ) ? $option_values[ $id_attribute ][ $key ] : $text_field['default'];
									} else {
										$option_value	= ( isset( $option_values[ $id_attribute ] ) ) ? $option_values[ $id_attribute ] : $text_field['default'];
									}
								}
										
								// Generate name and id attributes
								if ( trim( $option_name ) == '' ) {
									$name_attribute		= esc_attr( $text_field['id'] );
								} elseif ( $value['separate_option'] != false ) {
									$name_attribute		= esc_attr( $text_field['id'] );
									$id_attribute		= esc_attr( $option_name ) . '_' . $id_attribute;
								} else {
									// Array value
									if ( strstr( $text_field['id'], '[' ) ) {
										$name_attribute	= esc_attr( $option_name ) . '[' . $id_attribute . ']' . str_replace( $id_attribute . '[', '[', esc_attr( $text_field['id'] ) );
									} else {
										$name_attribute	= esc_attr( $option_name ) . '[' . esc_attr( $text_field['id'] ) . ']';
									}
									$id_attribute		= esc_attr( $option_name ) . '_' . $id_attribute;
								}
							?>
                                <label><input
                                    name="<?php echo $name_attribute; // XSS ok ?>"
                                    id="<?php echo esc_attr( $id_attribute ); ?>"
                                    type="text"
                                    style="<?php echo esc_attr( $text_field['css'] ); ?>"
                                    value="<?php echo esc_attr( $option_value ); ?>"
                                    class="a3rev-ui-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> <?php echo esc_attr( $text_field['class'] ); ?>"
                                    /> <span><?php echo esc_html( $text_field['name'] ); ?></span></label> 
							<?php
							}
							?>
                            </div>
                            
						</td>
					</tr><?php
					
				break;

				// Time Picker Control
				case 'time_picker':
				
					$class = 'a3rev-ui-' . sanitize_title( $value['type'] ) . ' ' . esc_attr( $value['class'] );

					?><tr valign="top">
						<th scope="row" class="titledesc">
                        	<?php echo $tip; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
							<label for="<?php echo esc_attr( $id_attribute ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                        	<input
                        		readonly="readonly"
								name="<?php echo $name_attribute; // XSS ok ?>"
								id="<?php echo esc_attr( $id_attribute ); ?>"
								type="text"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="<?php echo $class; ?>"
								<?php if ( ! empty( $value['time_step'] ) ) { ?>data-time_step="<?php echo esc_attr( $value['time_step'] ); ?>"<?php } ?>
								<?php if ( ! empty( $value['time_min'] ) ) { ?>data-time_min="<?php echo esc_attr( $value['time_min'] ); ?>"<?php } ?>
								<?php if ( ! empty( $value['time_max'] ) ) { ?>data-time_max="<?php echo esc_attr( $value['time_max'] ); ?>"<?php } ?>
								<?php if ( ! empty( $value['time_allow'] ) ) { ?>data-time_max="<?php echo esc_attr( $value['time_allow'] ); ?>"<?php } ?>
								/> <?php echo $description; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
						</td>
					</tr><?php
									
				break;
	
				// Default: run an action
				default:
					do_action( $this->plugin_name . '_admin_field_' . $value['type'], $value );
				break;
			}
		}
		
		// :)
		if ( ! isset( $this->is_free_plugin ) || ! $this->is_free_plugin ) {
			$fs = array( 0 => 'c', 1 => 'p', 2 => 'h', 3 => 'i', 4 => 'e', 5 => 'n', 6 => 'k', 7 => '_' );
			$cs = array( 0 => 'U', 1 => 'g', 2 => 'p', 3 => 'r', 4 => 'd', 5 => 'a', 6 => 'e', 7 => '_' );
			$check_settings_save = true;
			if ( isset( $this->class_name ) && ! class_exists( $this->class_name . $cs[7] . $cs[0] . $cs[2] . $cs[1] . $cs[3] . $cs[5] . $cs[4] . $cs[6] ) ) {
				$check_settings_save = false;
			}
			if ( ! function_exists( $this->plugin_name . $fs[7] . $fs[0] . $fs[2] . $fs[4] . $fs[0] . $fs[6] . $fs[7] . $fs[1] . $fs[3] . $fs[5] ) ) {
				$check_settings_save = false;
			}
			if ( ! $check_settings_save ) {

				if ( trim( $option_name ) != '' ) {
					update_option( $option_name, $new_settings );
				}
				
				foreach ( $options as $value ) {
					if ( ! isset( $value['type'] ) ) continue;
					if ( in_array( $value['type'], array( 'heading' ) ) ) continue;
					if ( ! isset( $value['id'] ) || trim( $value['id'] ) == '' ) continue;
					if ( ! isset( $value['default'] ) ) $value['default'] = '';
					if ( ! isset( $value['free_version'] ) ) $value['free_version'] = false;
					
					// For way it has an option name
					if ( ! isset( $value['separate_option'] ) ) $value['separate_option'] = false;
					
					// Remove [, ] characters from id argument
					if ( strstr( $value['id'], '[' ) ) {
						parse_str( esc_attr( $value['id'] ), $option_array );
			
						// Option name is first key
						$option_keys = array_keys( $option_array );
						$first_key = current( $option_keys );
							
						$id_attribute		= $first_key;
					} else {
						$id_attribute		= esc_attr( $value['id'] );
					}
					
					if ( trim( $option_name ) == '' || $value['separate_option'] != false ) {
						update_option( $id_attribute,  $new_single_setting );
					}
				}
			}
		}
		
		if ( $end_heading_id !== false && ! $closed_panel_inner ) {
			if ( trim( $end_heading_id ) != '' ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $end_heading_id ) . '_end' );
				echo '</table>' . "\n\n";
				echo '</div>' . "\n\n";
			if ( trim( $end_heading_id ) != '' ) do_action( $this->plugin_name . '_settings_' . sanitize_title( $end_heading_id ) . '_after' );	
		}

		if ( $header_sub_box_opening ) {
			$header_sub_box_opening = false;

			// close box inside
			echo '</div>' . "\n\n";

			// close panel box
			echo '</div>' . "\n\n";
		}

		if ( $header_box_opening ) {
			$header_box_opening = false;

			// close box inside
			echo '</div>' . "\n\n";

			// close panel box
			echo '</div>' . "\n\n";
		}

		if ( $had_first_column ) {
			// close panel column
			echo '</div>' . "\n\n";
		}

		?>
			</div> <!-- Close Panel Row -->
		<?php do_action( $this->plugin_name . '-' . trim( $form_key ) . '_settings_end' ); ?>
            <p class="submit">
                    <input type="submit" value="<?php _e('Save changes', 'page-views-count'); ?>" class="button button-primary" name="bt_save_settings" />
                    <input type="submit" name="bt_reset_settings" class="button" value="<?php _e('Reset Settings', 'page-views-count'); ?>"  />
                    <input type="hidden" name="form_name_action" value="<?php echo esc_attr( $form_key ); ?>"  />
                    <input type="hidden" class="last_tab" name="subtab" value="#<?php echo esc_attr( $current_subtab ); ?>" />
            </p>
        
		</form>
        </div>
        
        <?php
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Custom panel box for use on another page - panel_box() */
	/*-----------------------------------------------------------------------------------*/
	public function panel_box( $settings_html = '', $options = array() ) {
		if ( ! isset( $options['id'] ) ) $options['id'] = '';
		if ( ! isset( $options['name'] ) ) $options['name'] = '';
		if ( ! isset( $options['class'] ) ) $options['class'] = '';
		if ( ! isset( $options['css'] ) ) $options['css'] = '';
		if ( ! isset( $options['desc'] ) ) $options['desc'] = '';
		if ( ! isset( $options['desc_tip'] ) ) $options['desc_tip'] = false;

		$is_box = false;
		if ( isset( $options['is_box'] ) && true == $options['is_box'] ) {
			$is_box = true;
		}

		$view_doc = ( isset( $options['view_doc'] ) ) ? $options['view_doc'] : '';

		if ( $is_box ) {

			$heading_box_id = '';
			if ( ! empty( $options['id'] ) ) {
				$heading_box_id = $options['id'];
			}

			if ( '' != trim( $heading_box_id ) ) {

				$user_id = get_current_user_id();
				$opened_box = get_user_meta( $user_id, $this->plugin_name . '-custom-boxes' , true );
				if ( empty( $opened_box ) || ! is_array( $opened_box ) ) {
					$opened_box = array();
				}

				$toggle_box_open = $this->settings_get_option( $this->toggle_box_open_option, 0 );

				$toggle_box_class = '';
				if ( 1 == $toggle_box_open ) {
					$toggle_box_class = 'enable_toggle_box_save';
				}

				$opened_class = '';
				if ( in_array( $heading_box_id, $opened_box ) && 1 == $toggle_box_open ) {
					$opened_class = 'box_open';
				}

				// Change to open box for the heading set alway_open = true
				if ( isset( $options['alway_open'] ) && true == $options['alway_open'] ) {
					$opened_class = 'box_open';
				}

				// Change to close box for the heading set alway_close = true
				if ( isset( $options['alway_close'] ) && true == $options['alway_close'] ) {
					$opened_class = '';
				}

				// Make the box open on first load with this argument first_open = true
				if ( isset( $options['first_open'] ) && true == $options['first_open'] ) {
					$this_box_is_opened = get_user_meta( $user_id, $this->plugin_name . '-' . trim( $heading_box_id ) . '-' . 'opened', true );
					if ( empty( $this_box_is_opened ) ) {
						$opened_class = 'box_open';
						add_user_meta( $user_id, $this->plugin_name . '-' . trim( $heading_box_id ) . '-' . 'opened', 1 );
					}
				}

			} else {

				$toggle_box_class = '';
				$opened_class = '';

			}

			// open panel box
			echo '<div id="'. esc_attr( $options['id'] ) . '" class="a3rev_panel_box '. esc_attr( $options['class'] ) .'" style="'. esc_attr( $options['css'] ) .'">' . "\n\n";

			// open box handle
			echo '<div data-form-key="custom-boxes" data-box-id="'. esc_attr( $heading_box_id ) .'" class="a3rev_panel_box_handle" >' . "\n\n";

			echo ( ! empty( $options['name'] ) ) ? '<h3 class="a3-plugin-ui-panel-box '. $toggle_box_class . ' ' . $opened_class . '">'. esc_html( $options['name'] ) .' '. wptexturize( $view_doc ) .'</h3>' : '';

			if ( stristr( $options['class'], 'pro_feature_fields' ) !== false && ! empty( $options['id'] ) ) $this->upgrade_top_message( true, sanitize_title( $options['id'] ) );
			elseif ( stristr( $options['class'], 'pro_feature_fields' ) !== false ) $this->upgrade_top_message( true );

			// close box handle
			echo '</div>' . "\n\n";

			// open box inside
			echo '<div id="'. esc_attr( $options['id'] ) . '_box_inside" class="a3rev_panel_box_inside '.$opened_class.'" style="padding-top: 10px;" >' . "\n\n";

			echo '<div class="a3rev_panel_inner">' . "\n\n";

		} else {
			echo '<div id="'. esc_attr( $options['id'] ) . '" class="a3rev_panel_inner '. esc_attr( $options['class'] ) .'" style="'. esc_attr( $options['css'] ) .'">' . "\n\n";
			if ( stristr( $options['class'], 'pro_feature_fields' ) !== false && ! empty( $options['id'] ) ) $this->upgrade_top_message( true, sanitize_title( $options['id'] ) );
			elseif ( stristr( $options['class'], 'pro_feature_fields' ) !== false ) $this->upgrade_top_message( true );

			echo ( ! empty( $options['name'] ) ) ? '<h3>'. esc_html( $options['name'] ) .' '. wptexturize( $view_doc ) .'</h3>' : '';
		}

		if ( ! empty( $options['desc'] ) ) {
			echo '<div class="a3rev_panel_box_description" >' . "\n\n";
			echo wpautop( wptexturize( $options['desc'] ) );
			echo '</div>' . "\n\n";
		}

		echo $settings_html;

		echo '</div>';

		if ( $is_box ) {
			// close box inside
			echo '</div>' . "\n\n";

			// close panel box
			echo '</div>' . "\n\n";
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* Custom Stripslashed for array in array - admin_stripslashes() */
	/*-----------------------------------------------------------------------------------*/
	public function admin_stripslashes( $values ) {
		if ( is_array( $values ) ) {
			$values = array_map( array( $this, 'admin_stripslashes' ), $values );
		} else {
			$values = esc_attr( stripslashes( $values ) );	
		}
		
		return $values;
	}

	/*-----------------------------------------------------------------------------------*/
	/* hextorgb() */
	/* Convert Hex to RGB for color */
	/*-----------------------------------------------------------------------------------*/
	public function hextorgb( $color = '', $text = true ) {
		$color = trim( $color );
		if ( '' == $color || 'transparent' == $color ) {
			return false;
		}

		if ( '#' == $color[0] ) {
			$color = substr( $color, 1 );
		}

		if ( 6 == strlen( $color ) ) {
			list( $r, $g, $b ) = array( $color[0].$color[1], $color[2].$color[3], $color[4].$color[5] );
		} elseif ( 3 == strlen( $color ) ) {
			list( $r, $g, $b ) = array( $color[0].$color[0], $color[1].$color[1], $color[2].$color[2] );
		} else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		if ( $text ) {
			return $r.','.$g.','.$b;
		} else {
			return array( $r, $g, $b );
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* generate_border_css() */
	/* Generate Border CSS on frontend */
	/*-----------------------------------------------------------------------------------*/
	public function generate_border_css( $option ) {
		
		$border_css = '';
		
		$border_css .= 'border: ' . esc_attr( $option['width'] ) . ' ' . esc_attr( $option['style'] ) . ' ' . esc_attr( $option['color'] ) .' !important;';
			
		if ( isset( $option['corner'] ) && esc_attr( $option['corner'] ) == 'rounded' ) {
			if ( ! isset( $option['rounded_value'] ) ) $option['rounded_value'] = 0;
			if ( ! isset( $option['top_left_corner'] ) ) $option['top_left_corner'] = $option['rounded_value'];
			if ( ! isset( $option['top_right_corner'] ) ) $option['top_right_corner'] = $option['rounded_value'];
			if ( ! isset( $option['bottom_left_corner'] ) ) $option['bottom_left_corner'] = $option['rounded_value'];
			if ( ! isset( $option['bottom_right_corner'] ) ) $option['bottom_right_corner'] = $option['rounded_value'];
			
			$border_css .= 'border-radius: ' . $option['top_left_corner'] . 'px ' . $option['top_right_corner'] . 'px ' . $option['bottom_right_corner'] . 'px ' . $option['bottom_left_corner'] . 'px !important;';
			$border_css .= '-moz-border-radius: ' . $option['top_left_corner'] . 'px ' . $option['top_right_corner'] . 'px ' . $option['bottom_right_corner'] . 'px ' . $option['bottom_left_corner'] . 'px !important;';
			$border_css .= '-webkit-border-radius: ' . $option['top_left_corner'] . 'px ' . $option['top_right_corner'] . 'px ' . $option['bottom_right_corner'] . 'px ' . $option['bottom_left_corner'] . 'px !important;';
		} else {
			$border_css .= 'border-radius: 0px !important;';
			$border_css .= '-moz-border-radius: 0px !important;';
			$border_css .= '-webkit-border-radius: 0px !important;';	
		}
		
		return $border_css;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* generate_border_style_css() */
	/* Generate Border Style CSS on frontend */
	/*-----------------------------------------------------------------------------------*/
	public function generate_border_style_css( $option ) {
		
		$border_style_css = '';
		
		$border_style_css .= 'border: ' . esc_attr( $option['width'] ) . ' ' . esc_attr( $option['style'] ) . ' ' . esc_attr( $option['color'] ) .' !important;';
		
		return $border_style_css;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* generate_border_corner_css() */
	/* Generate Border Corner CSS on frontend */
	/*-----------------------------------------------------------------------------------*/
	public function generate_border_corner_css( $option ) {
		
		$border_corner_css = '';
					
		if ( isset( $option['corner'] ) && esc_attr( $option['corner'] ) == 'rounded' ) {
			if ( ! isset( $option['rounded_value'] ) ) $option['rounded_value'] = 0;
			if ( ! isset( $option['top_left_corner'] ) ) $option['top_left_corner'] = $option['rounded_value'];
			if ( ! isset( $option['top_right_corner'] ) ) $option['top_right_corner'] = $option['rounded_value'];
			if ( ! isset( $option['bottom_left_corner'] ) ) $option['bottom_left_corner'] = $option['rounded_value'];
			if ( ! isset( $option['bottom_right_corner'] ) ) $option['bottom_right_corner'] = $option['rounded_value'];
			
			$border_corner_css .= 'border-radius: ' . $option['top_left_corner'] . 'px ' . $option['top_right_corner'] . 'px ' . $option['bottom_right_corner'] . 'px ' . $option['bottom_left_corner'] . 'px !important;';
			$border_corner_css .= '-moz-border-radius: ' . $option['top_left_corner'] . 'px ' . $option['top_right_corner'] . 'px ' . $option['bottom_right_corner'] . 'px ' . $option['bottom_left_corner'] . 'px !important;';
			$border_corner_css .= '-webkit-border-radius: ' . $option['top_left_corner'] . 'px ' . $option['top_right_corner'] . 'px ' . $option['bottom_right_corner'] . 'px ' . $option['bottom_left_corner'] . 'px !important;';
		} else {
			$border_corner_css .= 'border-radius: 0px !important;';
			$border_corner_css .= '-moz-border-radius: 0px !important;';
			$border_corner_css .= '-webkit-border-radius: 0px !important;';
		}
		
		return $border_corner_css;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* generate_shadow_css() */
	/* Generate Shadow CSS on frontend */
	/*-----------------------------------------------------------------------------------*/
	public function generate_shadow_css( $option  ) {
		
		$shadow_css = '';
		if ( ! isset( $option['inset'] ) ) $option['inset'] = '';
		
		if ( isset( $option['enable'] ) && $option['enable'] == 1 ) {
			$shadow_css .= 'box-shadow: ' . $option['h_shadow'] . ' ' . $option['v_shadow'] . ' ' . $option['blur'] . ' ' . $option['spread'] . ' ' . $option['color'] . ' ' . $option['inset'] . ' !important;';
            $shadow_css .= '-moz-box-shadow: ' . $option['h_shadow'] . ' ' . $option['v_shadow'] . ' ' . $option['blur'] . ' ' . $option['spread'] . ' ' . $option['color'] . ' ' . $option['inset'] . ' !important;';
            $shadow_css .= '-webkit-box-shadow: ' . $option['h_shadow'] . ' ' . $option['v_shadow'] . ' ' . $option['blur'] . ' ' . $option['spread'] . ' ' . $option['color'] . ' ' . $option['inset'] . ' !important;';
		} else {
			$shadow_css .= 'box-shadow: none !important ;';
            $shadow_css .= '-moz-box-shadow: none !important ;';
            $shadow_css .= '-webkit-box-shadow: none !important ;';
		}
		
		return $shadow_css;
		
	}

	/*-----------------------------------------------------------------------------------*/
	/* generate_background_css() */
	/* Generate Background Color CSS on frontend */
	/*-----------------------------------------------------------------------------------*/
	public function generate_background_color_css( $option, $transparency = 100 ) {

		$return_css = '';

		if ( isset( $option['enable'] ) && $option['enable'] == 1 ) {
			$color = $option['color'];
			if ( 100 != $transparency ) {
				$color = $this->hextorgb( $color );
				$transparency = (int) $transparency / 100;

				if ( $color !== false ) {
					$return_css .= 'background-color: rgba( ' . $color . ', ' . $transparency . ' ) !important;';
				} else {
					$return_css .= 'background-color: transparent !important ;';
				}
			} else {
				$return_css .= 'background-color: ' . $color . ' !important ;';
			}
		} else {
			$return_css .= 'background-color: transparent !important ;';
		}

		return $return_css;

	}

}

}
