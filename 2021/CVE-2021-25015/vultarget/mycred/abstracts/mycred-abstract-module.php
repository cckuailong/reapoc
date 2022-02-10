<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Module class
 * @see http://codex.mycred.me/classes/mycred_module/
 * @since 0.1
 * @version 1.3.5
 */
if ( ! class_exists( 'myCRED_Module' ) ) :
	abstract class myCRED_Module {

		// Module ID (unique)
		public $module_id;

		// Core settings & functions
		public $core;

		// Module name (string)
		public $module_name;

		// Option ID (string|array)
		public $option_id;

		// Labels (array)
		public $labels;

		// Register (bool)
		public $register;

		// Screen ID (string)
		public $screen_id;

		// Menu Position (int)
		public $menu_pos;

		// Default preferences
		public $default_prefs = array();

		// Is Main Cred Type
		public $is_main_type = true;

		// myCRED Type used
		public $mycred_type;

		// All registered point types
		public $point_types;

		// Current users ID
		public $current_user_id;

		// Current time
		public $now;

		// Pages
		public $pages = array();

		// Submenu Inside Main menu
		public $main_menu;

		/**
		 * Construct
		 */
		public function __construct( $module_id = '', $args = array(), $type = MYCRED_DEFAULT_TYPE_KEY ) {

			// Module ID is required
			if ( empty( $module_id ) ) wp_die( 'myCRED_Module() Error. A Module ID is required!' );

			$this->module_id = $module_id;
			$this->core      = mycred( $type );

			if ( ! empty( $type ) ) {
				$this->core->cred_id = sanitize_text_field( $type );
				$this->mycred_type   = $this->core->cred_id;
			}

			if ( $this->mycred_type != MYCRED_DEFAULT_TYPE_KEY )
				$this->is_main_type = false;

			$this->point_types = mycred_get_types();

			// Default arguments
			$defaults = array(
				'module_name' => '',
				'option_id'   => '',
				'defaults'    => array(),
				'labels'      => array(
					'menu'        => '',
					'page_title'  => ''
				),
				'register'    => true,
				'screen_id'   => '',
				'add_to_core' => false,
				'accordion'   => false,
				'cap'         => 'plugin',
				'menu_pos'    => 10,
				'main_menu'   => false
			);
			$args = wp_parse_args( $args, $defaults );

			$this->module_name     = $args['module_name'];
			$this->option_id       = $args['option_id'];

			if ( ! $this->is_main_type )
				$this->option_id .= '_' . $this->mycred_type;

			$this->settings_name = 'myCRED-' . $this->module_name;
			if ( ! $this->is_main_type )
				$this->settings_name .= '-' . $this->mycred_type;

			$this->labels          = $args['labels'];
			$this->register        = $args['register'];
			$this->screen_id       = $args['screen_id'];

			if ( ! $this->is_main_type && ! empty( $this->screen_id ) )
				$this->screen_id = 'mycred_' . $this->mycred_type . substr( $this->screen_id, 6 );

			$this->add_to_core     = $args['add_to_core'];
			$this->accordion       = $args['accordion'];
			$this->cap             = $args['cap'];
			$this->menu_pos        = $args['menu_pos'];

			$this->default_prefs   = $args['defaults'];
			$this->now             = current_time( 'timestamp' );
			$this->main_menu       = $args['main_menu'];

			$this->set_settings();

		}

		/**
		 * Set Settings
		 * @since 0.1
		 * @version 1.2.1
		 */
		public function set_settings() {

			$module = $this->module_name;

			// Reqest not to register any settings
			if ( $this->register === false ) {

				// If settings does not exist apply defaults
				if ( ! isset( $this->core->$module ) )
					$this->$module = $this->default_prefs;

				// Else append settings
				else
					$this->$module = $this->core->$module;

				// Apply defaults in case new settings have been applied
				if ( ! empty( $this->default_prefs ) )
					$this->$module = wp_parse_args( $this->$module, $this->default_prefs );

			}

			// Request to register settings
			else {

				// Option IDs must be provided
				if ( ! empty( $this->option_id ) ) {

					// Array = more then one
					if ( is_array( $this->option_id ) ) {

						// General settings needs not to be loaded
						$pattern = 'mycred_pref_core';
						//$matches = array_filter( $this->option_id, function( $a ) use ( $pattern ) { return preg_grep( $a, $pattern ); } );
						//if ( ! empty( $matches ) )
							$this->$module = $this->core;

						// Loop and grab
						foreach ( $this->option_id as $option_id => $option_name ) {

							$settings = mycred_get_option( $option_name, false );

							if ( $settings === false && array_key_exists( $option_id, $defaults ) )
								$this->$module[ $option_name ] = $this->default_prefs[ $option_id ];
							else
								$this->$module[ $option_name ] = $settings;

							// Apply defaults in case new settings have been applied
							if ( array_key_exists( $option_id, $this->default_prefs ) )
								$this->$module[ $option_name ] = wp_parse_args( $this->$module[ $option_name ], $this->default_prefs[ $option_id ] );

						}

					}

					// String = one
					else {

						// General settings needs not to be loaded
						if ( str_replace( 'mycred_pref_core', '', $this->option_id ) == '' )
							$this->$module = $this->core;

						// Grab the requested option
						else {

							$this->$module = mycred_get_option( $this->option_id, false );

							if ( $this->$module === false && ! empty( $this->default_prefs ) )
								$this->$module = $this->default_prefs;

							// Apply defaults in case new settings have been applied
							if ( ! empty( $this->default_prefs ) )
								$this->$module = wp_parse_args( $this->$module, $this->default_prefs );

						}

					}

					if ( is_array( $this->$module ) ) {

						foreach ( $this->$module as $key => $value ) {
							$this->$key = $value;
						}

					}

				}

			}

		}

		/**
		 * Load
		 * @since 0.1
		 * @version 1.0.1
		 */
		public function load() {

			if ( ! empty( $this->screen_id ) && ! empty( $this->labels['menu'] ) ) {
				add_action( 'mycred_add_menu',         array( $this, 'add_menu' ), $this->menu_pos );
				add_action( 'admin_init',              array( $this, 'set_entries_per_page' ) );
			}

			if ( $this->register === true && ! empty( $this->option_id ) )
				add_action( 'mycred_admin_init',       array( $this, 'register_settings' ), $this->menu_pos );

			if ( $this->add_to_core === true ) {
				add_action( 'mycred_after_core_prefs', array( $this, 'after_general_settings' ) );
				add_filter( 'mycred_save_core_prefs',  array( $this, 'sanitize_extra_settings' ), 90, 3 );
			}

			add_action( 'mycred_pre_init',             array( $this, 'module_pre_init' ) );
			add_action( 'mycred_init',                 array( $this, 'module_init' ) );
			add_action( 'mycred_admin_init',           array( $this, 'module_admin_init' ), $this->menu_pos+1 );
			add_action( 'mycred_widgets_init',         array( $this, 'module_widgets_init' ) );
			add_action( 'mycred_admin_enqueue',        array( $this, 'scripts_and_styles' ), $this->menu_pos );

		}

		/**
		 * myCRED Ready
		 * No longer available as of version 1.4
		 * @since 1.1.1
		 * @version 1.0
		 */
		public function module_ready() { }

		/**
		 * Plugins Loaded (pre init)
		 * @since 0.1
		 * @version 1.0
		 */
		public function module_pre_init() { }

		/**
		 * Init
		 * @since 0.1
		 * @version 1.0
		 */
		public function module_init() { }

		/**
		 * Admin Init
		 * @since 0.1
		 * @version 1.0
		 */
		public function module_admin_init() { }

		/**
		 * Widgets Init
		 * @since 0.1
		 * @version 1.0
		 */
		public function module_widgets_init() { }

		/**
		 * Get
		 * @since 0.1
		 * @version 1.0
		 */
		public function get() { }

		/**
		 * Call
		 * Either runs a given class method or function.
		 * Defaults to an empty array if class/function does not exist
		 * @since 0.1
		 * @version 1.0.3
		 */
		public function call( $call, $callback, $return = NULL ) {

			// Class
			if ( is_array( $callback ) && class_exists( $callback[0] ) ) {

				$class   = $callback[0];
				$methods = get_class_methods( $class );
				if ( in_array( $call, $methods ) ) {
					$new = new $class( $this );
					return $new->$call( $return );
				}

			}

			// Function
			elseif ( ! is_array( $callback ) ) {

				if ( function_exists( $callback ) ) {

					if ( $return !== NULL )
						return call_user_func( $callback, $return, $this );
					else
						return call_user_func( $callback, $this );

				}

			}

			if ( $return !== NULL )
				return array();

		}

		/**
		 * If Installed
		 * Checks if hooks have been installed
		 *
		 * @returns (bool) true or false
		 * @since 0.1
		 * @version 1.0
		 */
		public function is_installed() {

			$module_name = $this->module_name;
			if ( $this->$module_name === false ) return false;
			return true;

		}

		/**
		 * Is Active
		 * @param $key (string) required key to check for
		 * @returns (bool) true or false
		 * @since 0.1
		 * @version 1.1
		 */
		public function is_active( $key = '' ) {

			$module    = $this->module_name;
			$is_active = false;

			if ( ! isset( $this->active ) && ! empty( $key ) ) {

				if ( isset( $this->$module['active'] ) )
					$active = $this->$module['active'];

				if ( in_array( $key, $active ) )
					$is_active = true;

			}

			elseif ( isset( $this->active ) && ! empty( $key ) ) {

				if ( in_array( $key, $this->active ) )
					$is_active = true;

			}

			return apply_filters( 'mycred_module_is_active', $is_active, $module, $key, $this );

		}

		/**
		 * Add Admin Menu Item
		 * @since 0.1
		 * @version 1.3
		 */
		public function add_menu() {

			// In case we are using the Master Template feautre on multisites, and this is not the main
			// site in the network, bail.
			if ( $this->module_id != 'myCRED_Log_Module' && mycred_override_settings() && ! mycred_is_main_site() ) return;

			// Minimum requirements
			if ( ! empty( $this->labels ) && ! empty( $this->screen_id ) ) {

				// Menu Slug
				$menu_slug  = ( ! $this->is_main_type ) ? MYCRED_SLUG . '_' . $this->mycred_type : MYCRED_SLUG;
				$label_menu = $label_title = 'Surprise';

				// Capability
				$capability = ( $this->cap == 'plugin' ) ? $this->core->get_point_admin_capability() : $this->core->get_point_editor_capability();

				// Menu Label
				if ( isset( $this->labels['menu'] ) )
					$label_menu = $this->labels['menu'];

				elseif ( isset( $this->labels['page_title'] ) )
					$label_menu = $this->labels['page_title'];

				// Page Title
				if ( isset( $this->labels['page_title'] ) )
					$label_title = $this->labels['page_title'];

				elseif ( isset( $this->labels['menu'] ) )
					$label_title = $this->labels['menu'];

				if ( $this->main_menu ) 
					$menu_slug = MYCRED_MAIN_SLUG;

				// Add Submenu Page
				$page = add_submenu_page(
					$menu_slug,
					$label_menu,
					$label_title,
					$capability,
					$this->screen_id,
					array( $this, 'admin_page' )
				);

				add_action( 'admin_print_styles-' . $page, array( $this, 'settings_page_enqueue' ) );
				add_action( 'load-' . $page,               array( $this, 'screen_options' ) );

			}

		}

		/**
		 * Register Scripts & Styles
		 * @since 1.7
		 * @version 1.0
		 */
		public function scripts_and_styles() { }

		/**
		 * Save Log Entries per page
		 * @since 0.1
		 * @version 1.0.1
		 */
		public function set_entries_per_page() {

			if ( ! isset( $_REQUEST['wp_screen_options']['option'] ) || ! isset( $_REQUEST['wp_screen_options']['value'] ) ) return;

			$settings_key = 'mycred_epp_' . $_GET['page'];

			if ( $_REQUEST['wp_screen_options']['option'] == $settings_key ) {
				$value = absint( $_REQUEST['wp_screen_options']['value'] );
				mycred_update_user_meta( get_current_user_id(), $settings_key, '', $value );
			}

		}

		/**
		 * Register Settings
		 * @since 0.1
		 * @version 1.1
		 */
		public function register_settings() {

			if ( empty( $this->option_id ) || $this->register === false ) return;

			register_setting( $this->settings_name, $this->option_id, array( $this, 'sanitize_settings' ) );

		}

		/**
		 * Screen Options
		 * @since 1.4
		 * @version 1.0
		 */
		public function screen_options() {

			$this->set_entries_per_page();

		}

		/**
		 * Add Metabox Class
		 * @since 1.7
		 * @version 1.0
		 */
		public function metabox_classes( $classes ) {

			$classes[] = 'mycred-metabox';

			return $classes;

		}

		/**
		 * Enqueue Scripts & Styles
		 * Scripts and styles to enqueu on module admin pages.
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function settings_page_enqueue() {

			wp_dequeue_script( 'bpge_admin_js_acc' );

			// Load Accordion
			if ( $this->accordion ) {

				wp_enqueue_style( 'mycred-admin' );
				wp_enqueue_script( 'mycred-accordion' );

?>
<!-- myCRED Accordion Styling -->
<style type="text/css">
h4:before { float:right; padding-right: 12px; font-size: 14px; font-weight: normal; color: silver; }
h4.ui-accordion-header.ui-state-active:before { content: "<?php _e( 'click to close', 'mycred' ); ?>"; }
h4.ui-accordion-header:before { content: "<?php _e( 'click to open', 'mycred' ); ?>"; }
</style>
<?php

			}

			$this->settings_header();

		}

		/**
		 * Settings Header
		 * Scripts and styles to insert after scripts are printed.
		 * @since 0.1
		 * @version 1.2
		 */
		public function settings_header() { }

		/**
		 * Admin Page
		 * @since 0.1
		 * @version 1.0
		 */
		public function admin_page() { }

		/**
		 * Update Notice
		 * @since 1.4
		 * @version 1.0
		 */
		public function update_notice( $get = 'settings-updated', $class = 'updated', $message = '' ) {

			if ( empty( $message ) )
				$message = __( 'Settings Updated', 'mycred' );

			if ( isset( $_GET[ $get ] ) )
				echo '<div class="' . $class . '"><p>' . $message . '</p></div>';

		}

		/**
		 * Sanitize Settings
		 * @since 0.1
		 * @version 1.0
		 */
		public function sanitize_settings( $post ) {

			return $post;

		}

		/**
		 * After General Settings
		 * @since 0.1
		 * @version 1.0
		 */
		public function after_general_settings( $mycred = NULL ) { }

		/**
		 * Sanitize Core Settings
		 * @since 0.1
		 * @version 1.0
		 */
		public function sanitize_extra_settings( $new_data, $data, $core ) {

			return $new_data;

		}

		/**
		 * Input Field Name Value
		 * @since 0.1
		 * @version 1.0
		 */
		public function field_name( $name = '' ) {

			if ( is_array( $name ) ) {

				$array = array();
				foreach ( $name as $parent => $child ) {

					if ( ! is_numeric( $parent ) )
						$array[] = $parent;

					if ( ! empty( $child ) && ! is_array( $child ) )
						$array[] = $child;

				}
				$name = '[' . implode( '][', $array ) . ']';

			}
			else {

				$name = '[' . $name . ']';

			}

			if ( $this->add_to_core === true )
				$name = '[' . $this->module_name . ']' . $name;

			if ( $this->option_id != '' )
				return $this->option_id . $name;

			return 'mycred_pref_core' . $name;

		}

		/**
		 * Input Field Id Value
		 * @since 0.1
		 * @version 1.0
		 */
		public function field_id( $id = '' ) {

			if ( is_array( $id ) ) {

				$array = array();
				foreach ( $id as $parent => $child ) {

					if ( ! is_numeric( $parent ) )
						$array[] = str_replace( '_', '-', $parent );

					if ( ! empty( $child ) && ! is_array( $child ) )
						$array[] = str_replace( '_', '-', $child );

				}
				$id = implode( '-', $array );

			}
			else {

				$id = str_replace( '_', '-', $id );

			}

			if ( $this->add_to_core === true )
				$id = 'prefs' . $id;

			$id = strtolower( $this->module_name ) . $id;
			$id = strtolower( $id );
			$id = str_replace( array( '[', ']' ), '', $id );
			$id = str_replace( array( '_', '-' ), '', $id );

			return $id;

		}

		/**
		 * Available Template Tags
		 * @since 1.4
		 * @version 1.0
		 */
		public function available_template_tags( $available = array() ) {

			return $this->core->available_template_tags( $available );

		}

		/**
		 * Get Settings URL
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function get_settings_url( $module = '' ) {

			$variables = array( 'page' => MYCRED_SLUG . '-settings' );
			if ( ! empty( $module ) )
				$variables['open-tab'] = $module;

			$url = add_query_arg( $variables, admin_url( 'admin.php' ) );

			return esc_url( $url );

		}

		/**
		 * Request to Entry
		 * @since 1.6
		 * @version 1.0
		 */
		public function request_to_entry( $request = array() ) {

			if ( empty( $request ) ) return false;

			$entry = new stdClass();

			$entry->id      = -1;
			$entry->ref     = $request['ref'];
			$entry->ref_id  = $request['ref_id'];
			$entry->user_id = $request['user_id'];
			$entry->time    = $this->now;
			$entry->entry   = $request['entry'];
			$entry->data    = $request['data'];
			$entry->ctype   = $request['type'];

			return $entry;

		}

	}
endif;
