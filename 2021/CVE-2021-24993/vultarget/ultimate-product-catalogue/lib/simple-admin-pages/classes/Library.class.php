<?php
if ( !class_exists( 'sapLibrary_2_6_1' ) ) {
/**
 * This library class loads and provides access to the correct version of the
 * Simple Admin Pages library.
 *
 * @since 1.0
 * @package Simple Admin Pages
 */
class sapLibrary_2_6_1 {

	// Version of the library
	private $version = '2.6.1';

	// A full URL to the library which is used to correctly link scripts and
	// stylesheets.
	public $lib_url;

	// A relative path to any custom library extension classes. When
	// instantiating a custom setting class, the library will search in its own
	// directory of classes adn also the $lib_extension_path. This way,
	// developers can add on their own classes without mixing them with the
	// default classes.
	public $lib_extension_path;

	// An array of pages to add to the admin menus
	public $pages = array();

	// Collects errors for debugging
	public $errors = array();

	// Set debug mode to true to stop and print errors found while processing.
	// @note This is not related to your PHP error reporting setting, but is an
	// internal error tracking mechanism to catch missing or malformed data
	// during development.
	public $debug_mode = false;

	public $available_themes = [
		'purple',
		'blue'
	];

	public $current_theme = 'blue';

	/**
	 * Initialize the library with the appropriate version
	 * @since 1.0
	 */
	public function __construct( $args ) {

		if ( ! defined( 'SAP_VERSION' ) ) {
			define( 'SAP_VERSION', '2.6.1' );
		}

		// If no URL path to the library is passed, we won't be able to add the
		// CSS and Javascript to the admin panel
		if ( !isset( $args['lib_url'] ) ) {
			$this->set_error(
				array(
					'id' 		=> 'no-lib-url',
					'desc'		=> 'No URL path to the library provided when the libary was created.',
					'var'		=> $args,
					'line'		=> __LINE__,
					'function'	=> __FUNCTION__
				)
			);
		} else {
			$this->lib_url = $args['lib_url'];
		}

		// Set a library extension path if passed
		if ( isset( $args['lib_extension_path'] ) ) {
			$this->lib_extension_path = $args['lib_extension_path'];
		}

		// Set the debug mode
		if ( isset( $args['debug_mode'] ) && $args['debug_mode'] === true ) {
			$this->debug_mode = true;
		}

		// Set the current theme
		if ( isset( $args['theme'] ) && in_array( $args['theme'], $this->available_themes ) ) {
			$this->current_theme = $args['theme'];
		}

		// Ensure we have access to WordPress' plugin functions
		require_once(ABSPATH . '/wp-admin/includes/plugin.php');

		// Load the required classes
		$this->load_class( 'sapAdminPage', 'AdminPage.class.php' );
		$this->load_class( 'sapAdminPageSection', 'AdminPageSection.class.php' );
		$this->load_class( 'sapAdminPageSetting', 'AdminPageSetting.class.php' );

		// Add the scripts to the admin pages
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * Load the class if it isn't already loaded
	 * @since 1.0
	 */
	private function load_class( $class, $file ) {

		if ( !class_exists( $this->get_versioned_classname( $class ) ) ) {
			require_once( $file );
		}
	}

	/**
	 * Return the version suffix for a class
	 * @since 1.0
	 */
	private function get_versioned_classname( $class ) {
		return $class . '_' . str_replace( '.', '_', $this->version );
	}

	/**
	 * Check if the correct version of a class exists
	 * @since 1.0
	 */
	private function versioned_class_exists( $class ) {
		if ( class_exists( $this->get_versioned_classname( $class ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Load the files for a specific setting type and return the class
	 * to use when instantiating the setting object.
	 *
	 * @since 1.0
	 */
	private function get_setting_classname( $type ) {

		switch( $type ) {

			case 'text' :
				require_once('AdminPageSetting.Text.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingText' );

			case 'number' :
				require_once('AdminPageSetting.Number.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingNumber' );

			case 'colorpicker' :
				require_once('AdminPageSetting.ColorPicker.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingColorPicker' );

			case 'textarea' :
				require_once('AdminPageSetting.Textarea.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingTextarea' );

			case 'select' :
				require_once('AdminPageSetting.Select.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingSelect' );

			case 'toggle' :
				require_once('AdminPageSetting.Toggle.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingToggle' );

			case 'image' :
				require_once('AdminPageSetting.Image.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingImage' );

			case 'radio' :
				require_once('AdminPageSetting.Radio.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingRadio' );

			case 'checkbox' :
				require_once('AdminPageSetting.Checkbox.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingCheckbox' );

			case 'infinite_table' :
				require_once('AdminPageSetting.InfiniteTable.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingInfiniteTable' );

			case 'count' :
				require_once('AdminPageSetting.Count.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingCount' );

			case 'post' :
				require_once('AdminPageSetting.SelectPost.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingSelectPost' );

			case 'menu' :
				require_once('AdminPageSetting.SelectMenu.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingSelectMenu' );

			case 'taxonomy' :
				require_once('AdminPageSetting.SelectTaxonomy.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingSelectTaxonomy' );

			case 'editor' :
				require_once('AdminPageSetting.Editor.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingEditor' );

			case 'html' :
				require_once('AdminPageSetting.HTML.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingHTML' );

			case 'scheduler' :
				require_once('AdminPageSetting.Scheduler.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingScheduler' );

			case 'opening-hours' :
				require_once('AdminPageSetting.OpeningHours.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingOpeningHours' );

			case 'address' :
				require_once('AdminPageSetting.Address.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingAddress' );

			case 'file-upload' :
				require_once('AdminPageSetting.FileUpload.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingFileUpload' );

			case 'ordering-table' :
				require_once('AdminPageSetting.Ordering.class.php');
				return $this->get_versioned_classname( 'sapAdminPageSettingOrdering' );

			case 'mcapikey' :
				require_once('AdminPageSetting.McApiKey.class.php');
				return $this->get_versioned_classname( 'mcfrtbAdminPageSettingMcApiKey' );

			case 'mclistmerge' :
				require_once('AdminPageSetting.McListMerge.class.php');
				return $this->get_versioned_classname( 'mcfrtbAdminPageSettingMcListMerge' );

			default :

				// Exit early if a custom type is declared without providing the
				// details to find the type class
				if ( ( !is_array( $type ) || !isset( $type['id'] ) ) ||
					( !isset( $type['class'] ) || !isset( $type['filename'] ) ) ) {
					return false;
				}

				// Load the custom type file. Look for the file in the library's
				// folder or check the custom library extension path.
				if ( file_exists( $type['filename'] ) ) {
					require_once( $type['filename'] );
				} elseif ( isset( $this->lib_extension_path ) && file_exists( $this->lib_extension_path . $type['filename'] ) ) {
					require_once( $this->lib_extension_path . '/' . $type['filename'] );
					if ( !class_exists( $type['class'] ) ) {
						return false;
					} else {
						return $type['class'];
					}
				} else {
					return false;
				}


				// Check that we've loaded the appropriate class
				if ( !$this->versioned_class_exists( $type['class'] ) ) {
					return false;
				}

				return $this->get_versioned_classname( $type['class'] );

		}

	}

	/**
	 * Initialize a page
	 * @since 1.0
	 *
	 * @todo perform some checks on args to ensure a valid page can be constructed
	 */
	public function add_page( $menu_location, $args = array() ) {

		// default should be 'options'
		$class = $this->get_versioned_classname( 'sapAdminPage' );

		if ( $menu_location == 'themes' ) {
			$this->load_class( 'sapAdminPageThemes', 'AdminPage.Themes.class.php' );
			$class = $this->get_versioned_classname( 'sapAdminPageThemes' );
		} elseif ( $menu_location == 'menu' ) {
			$this->load_class( 'sapAdminPageMenu', 'AdminPage.Menu.class.php' );
			$class = $this->get_versioned_classname( 'sapAdminPageMenu' );
		} elseif ( $menu_location == 'submenu' ) {
			$this->load_class( 'sapAdminPageSubmenu', 'AdminPage.Submenu.class.php' );
			$class = $this->get_versioned_classname( 'sapAdminPageSubmenu' );
		}

		if ( class_exists( $class ) ) {
			$this->pages[ $args['id'] ] = new $class( $args );
		}

	}

	/**
	 * Initialize a section
	 * @since 1.0
	 *
	 * @todo perform some checks on args to ensure a valid section can be constructed
	 */
	public function add_section( $page, $args = array() ) {

		if ( !isset( $this->pages[ $page ] ) ) {
			return false;
		} else {
			$args['page'] = $page;
		}

		$class = $this->get_versioned_classname( 'sapAdminPageSection' );
		if ( class_exists( $class ) ) {
			$this->pages[ $page ]->add_section( new $class( $args ) );
		}

	}

	/**
	 * Initialize a setting
	 *
	 * The type variable can be a string pointing to a pre-defined setting type,
	 * or an array consisting of an id, classname and filename which references
	 * a custom setting type. @sa get_setting_classname()
	 *
	 * @since 1.0
	 */
	public function add_setting( $page, $section, $type, $args = array() ) {

		if ( !isset( $this->pages[ $page ] ) || !isset( $this->pages[ $page ]->sections[ $section ] ) ) {
			return false;
		} else {
			$args['page'] = $page;
			$args['tab'] = $this->pages[$page]->sections[ $section ]->get_page_slug();
		}

		$class = $this->get_setting_classname( $type );
		if ( $class && class_exists( $class ) ) {
			$this->pages[ $page ]->sections[ $section ]->add_setting( new $class( $args ) );
		}

	}

	/**
	 * Register all page, section and settings content with WordPress
	 * @since 1.0
	 */
	public function add_admin_menus() {

		// If the library is run in debug mode, check for any errors in content,
		// print any errors found, and don't add the menu if there are errors
		if ( $this->debug_mode ) {
			$errors = array();
			foreach ( $this->pages as $page ) {
				foreach ( $page->sections as $section ) {
					if ( count( $section->errors ) ) {
						array_merge( $errors, $section->errors );
					}
					foreach ( $section->settings as $setting ) {
						if ( count( $setting->errors ) ) {
							$errors = array_merge( $errors, $setting->errors );
						}
					}
				}
			}
			if ( count( $errors ) ) {
				print_r( $errors );
				return;
			}
		}

		// Add the action hooks
		foreach ( $this->pages as $id => $page ) {
			add_action( 'admin_menu', array( $page, 'add_admin_menu' ) );
			add_action( 'admin_init', array( $page, 'register_admin_menu' ) );
		}
	}

	/**
	 * Port data from a previous version to the current version
	 *
	 * Version 2.0 of the library changes the structure of how it stores data.
	 * In order to upgrade the version of the library your plugin/theme is
	 * using, this method must be called after all of your pages and settings
	 * have been declared but before you run add_admin_menus().
	 *
	 * This method will loop over all of the settings data and port any existing
	 * data to the new data structure. It will check if the data has been ported
	 * first before it updates the data. The old data will be removed to keep
	 * the database clean.
	 *
	 * @var int target_version Which data version the library should update to.
	 * @since 2.0
	 */
	public function port_data( $target_version, $delete_old_data = true ) {

		// Port data to the storage structure in version 2
		if ( $target_version == 2 ) {

			foreach ( $this->pages as $page_id => $page ) {

				// Skip if this page has already been ported
				if ( get_option( $page_id ) !== false ) {
					continue;
				}

				$page_values = array();

				foreach ( $page->sections as $section ) {
					foreach ( $section->settings as $setting ) {
						$value = get_option( $setting->id );
						if ( $value !== false ) {
							$page_values[ $setting->id ] = $value;
						}
					}
				}

				if ( count( $page_values ) ) {
					$result = add_option( $page_id, $page_values );

					// Delete old data if the flag is set and the new data was
					// saved successfully.
					if ( $delete_old_data === true && $result !== false ) {
						foreach( $page_values as $setting_id => $setting_value ) {
							delete_option( $setting_id );
						}
					}

					// Reset settings values
					if ( $result === true ) {

						foreach ( $page->sections as $section ) {
							foreach ( $section->settings as $setting ) {
								$setting->set_value();
							}
						}

					}
				}
			}
		}

	}

	/**
	 * Enqueue the stylesheets and scripts
	 * @since 1.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		foreach ( $this->pages as $page_id => $page ) {

			// Only enqueue assets for the current page
			if ( strpos( $screen->base, $page_id ) !== false ) {

				// Theme specifc files
				switch ($this->current_theme) {
					case 'blue':
						wp_enqueue_style(
							'sap-admin-settings-css-blue-' . $this->version, 
							$this->lib_url . 'css/admin-settings-blue.css', 
							array(), 
							$this->version
						);
						break;

					case 'purple':
						wp_enqueue_style(
							'sap-admin-settings-css-purple-' . $this->version, 
							$this->lib_url . 'css/admin-settings-purple.css', 
							array(), 
							$this->version
						);
						break;
				}

				wp_enqueue_style( 'sap-admin-style-' . $this->version, $this->lib_url . 'css/admin.css', array(), $this->version );
				wp_enqueue_style( 'sap-spectrum-css-' . $this->version, $this->lib_url . 'css/spectrum.css', array(), $this->version );
				wp_enqueue_style( 'sap-admin-settings-css-' . $this->version, $this->lib_url . 'css/admin-settings.css', array(), $this->version );
				wp_enqueue_script( 'sap-spectrum-js-' . $this->version, $this->lib_url . 'js/spectrum.js', array( 'jquery' ), $this->version );
				wp_enqueue_script( 'sap-admin-settings-js-' . $this->version, $this->lib_url . 'js/admin-settings.js', array( 'jquery', 'sap-spectrum-js-' . $this->version ), $this->version );
				wp_enqueue_media();

				foreach ( $page->sections as $section ) {
					foreach ( $section->settings as $setting ) {
						foreach( $setting->scripts as $handle => $script ) {
							wp_enqueue_script( $handle, $this->lib_url . $script['path'], $script['dependencies'], $script['version'], $script['footer'] );
						}
						foreach( $setting->styles as $handle => $style ) {
							wp_enqueue_style( $handle, $this->lib_url . $style['path'], $style['dependencies'], $style['version'], $style['media'] );
						}
					}
				}
			}
		}
	}

	/**
	 * Set an error
	 * @since 1.0
	 */
	public function set_error( $error ) {
		$this->errors[] = array_merge(
			$error,
			array(
				'class'		=> get_class( $this ),
				'id'		=> $this->id,
				'backtrace'	=> debug_backtrace()
			)
		);
	}

}
} // endif;
