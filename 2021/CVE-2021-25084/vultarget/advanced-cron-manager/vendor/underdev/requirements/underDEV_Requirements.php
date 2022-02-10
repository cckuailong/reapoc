<?php
/**
 * Requirements checks for WordPress plugin
 * @autor   Kuba Mikita (jakub@underdev.it)
 * @version 1.3.3
 * @usage   see https://github.com/Kubitomakita/Requirements
 */

if ( ! class_exists( 'underDEV_Requirements' ) ) :

class underDEV_Requirements {

	const VERSION = '1.3.3';

	/**
	 * Plugin display name
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Array of checks
	 * @var array
	 */
	protected $checks;

	/**
	 * Array of check methods
	 * @var array
	 */
	private $check_methods;

	/**
	 * Array of errors
	 * @var array
	 */
	protected $errors = array();

	/**
	 * The library text domain
	 * @var string
	 */
	private $textdomain = 'underdev-requirements';

	/**
	 * Class constructor
	 * @param string $plugin_name plugin display name
	 * @param array  $to_check    checks to perform
	 */
	public function __construct( $plugin_name = '', $to_check = array() ) {

		$this->checks      = $to_check;
		$this->plugin_name = $plugin_name;

		// Load translations
		if ( function_exists( 'get_user_locale' ) ) {
			$locale = get_user_locale();
		} else {
			$locale = get_locale();
		}

		load_textdomain( $this->textdomain, dirname( __FILE__ ) . '/languages/' . $this->textdomain . '-' . $locale . '.mo' );

		// Add default checks
		$this->add_check( 'php', array( $this, 'check_php' ) );
		$this->add_check( 'php_extensions', array( $this, 'check_php_extensions' ) );
		$this->add_check( 'wp', array( $this, 'check_wp' ) );
		$this->add_check( 'plugins', array( $this, 'check_plugins' ) );
		$this->add_check( 'theme', array( $this, 'check_theme' ) );
		$this->add_check( 'function_collision', array( $this, 'check_function_collision' ) );
		$this->add_check( 'class_collision', array( $this, 'check_class_collision' ) );

	}

	/**
	 * Adds the new check
	 * @param  string $check_name name of the check
	 * @param  mixed  $callback   callable string or array
	 * @return $this
	 */
	public function add_check( $check_name, $callback ) {

		$this->check_methods[ $check_name ] = $callback;

		return $this;

	}

	/**
	 * Runs checks
	 * @return $this
	 */
	public function check() {

		foreach ( $this->checks as $thing_to_check => $comparsion ) {

			if ( isset( $this->check_methods[ $thing_to_check ] ) && is_callable( $this->check_methods[ $thing_to_check ] ) ) {
				call_user_func( $this->check_methods[ $thing_to_check ], $comparsion, $this );
			}

		}

		return $this;

	}

	/**
	 * Adds the error
	 * @return $this
	 */
	public function add_error( $error_message ) {

		$this->errors[] = $error_message;

		return $this;

	}

	/**
	 * Check if requirements has been satisfied
	 * @return boolean
	 */
	public function satisfied() {
		$this->check();
		return empty( $this->errors );
	}

	/**
	 * Displays notice for user about the plugin requirements
	 * @return void
	 */
	public function notice() {

		echo '<div class="error">';

			echo '<p>' . sprintf( __( '<strong>%s</strong> cannot be activated because it requires:', $this->textdomain ), esc_html( $this->plugin_name ) ) . '</p>';

			echo '<ul style="list-style: disc; padding-left: 20px;">';

				foreach ( $this->errors as $error ) {
					echo '<li>' . $error . '</li>';
				}

			echo '</ul>';

		echo '</div>';

	}

	/**
	 * Default check methods
	 */

	/**
	 * Check PHP version
	 * @param  string $version      version needed
	 * @param  object $requirements requirements class
	 * @return void
	 */
	public function check_php( $version, $requirements ) {

		if ( version_compare( phpversion(), $version, '<' ) ) {
			$requirements->add_error( sprintf( __( 'Minimum required version of PHP is %s. Your version is %s', $this->textdomain ), $version, phpversion() ) );
		}

	}

	/**
	 * Check PHP extensions
	 * @param  string $extensions   array of extension names
	 * @param  object $requirements requirements class
	 * @return void
	 */
	public function check_php_extensions( $extensions, $requirements ) {

		$missing_extensions = array();

		foreach ( $extensions as $extension ) {
			if ( ! extension_loaded( $extension ) ) {
				$missing_extensions[] = $extension;
			}
		}

		if ( ! empty( $missing_extensions ) ) {
			$requirements->add_error( sprintf(
				_n( 'PHP extension: %s', 'PHP extensions: %s', count( $missing_extensions ) ),
				implode( ', ', $missing_extensions ),
				$this->textdomain
			) );
		}

	}

	/**
	 * Check WordPress version
	 * @param  string $version      version needed
	 * @param  object $requirements requirements class
	 * @return void
	 */
	public function check_wp( $version, $requirements ) {

		if ( version_compare( get_bloginfo( 'version' ), $version, '<' ) ) {
			$requirements->add_error( sprintf( __( 'Minimum required version of WordPress is %s. Your version is %s', $this->textdomain ), $version, get_bloginfo( 'version' ) ) );
		}

	}

	/**
	 * Check if plugins are active and are in needed versions
	 * @param  array $plugins       array with plugins,
	 *                              where key is the plugin file and value is the version
	 * @param  object $requirements requirements class
	 * @return void
	 */
	public function check_plugins( $plugins, $requirements ) {

		$active_plugins_raw = wp_get_active_and_valid_plugins();

		if ( is_multisite() ) {
			$active_plugins_raw = array_merge( $active_plugins_raw, wp_get_active_network_plugins() );
		}

		$active_plugins          = array();
		$active_plugins_versions = array();

		foreach ( $active_plugins_raw as $plugin_full_path ) {
			$plugin_file                             = str_replace( WP_PLUGIN_DIR . '/', '', $plugin_full_path );
			$active_plugins[]                        = $plugin_file;
			$plugin_api_data                         = @get_file_data( $plugin_full_path, array( 'Version' ) );
			$active_plugins_versions[ $plugin_file ] = $plugin_api_data[0];
		}

		foreach ( $plugins as $plugin_file => $plugin_data ) {

			if ( ! in_array( $plugin_file, $active_plugins ) ) {
				$requirements->add_error( sprintf( __( 'Required plugin: %s', $this->textdomain ), $plugin_data['name'] ) );
			} else if ( version_compare( $active_plugins_versions[ $plugin_file ], $plugin_data['version'], '<' ) ) {
				$requirements->add_error( sprintf( __( 'Minimum required version of %s plugin is %s. Your version is %s', $this->textdomain ), $plugin_data['name'], $plugin_data['version'], $active_plugins_versions[ $plugin_file ] ) );
			}

		}

	}

	/**
	 * Check if theme is active
	 * @param  array  $needed_theme theme data
	 * @param  object $requirements requirements class
	 * @return void
	 */
	public function check_theme( $needed_theme, $requirements ) {

		$theme = wp_get_theme();

		if ( $theme->get_template() != $needed_theme['slug'] ) {
			$requirements->add_error( sprintf( __( 'Required theme: %s', $this->textdomain ), $needed_theme['name'] ) );
		}

	}

	/**
	 * Check function collision
	 * @param  array  $functions    function names
	 * @param  object $requirements requirements class
	 * @return void
	 */
	public function check_function_collision( $functions, $requirements ) {

		$collisions = array();

		foreach ( $functions as $function ) {
			if ( function_exists( $function ) ) {
				$collisions[] = $function;
			}
		}

		if ( ! empty( $collisions ) ) {
			$requirements->add_error( sprintf(
				_n( "Unable to register the following function because it already exists: %s", 'Unable to register the following functions because they already exist: %s', count( $collisions ) ),
				implode( ', ', $collisions ),
				$this->textdomain
			) );
		}

	}

	/**
	 * Check class collision
	 * @param  array  $classes      class names
	 * @param  object $requirements requirements class
	 * @return void
	 */
	public function check_class_collision( $classes, $requirements ) {

		$collisions = array();

		foreach ( $classes as $class ) {
			if ( class_exists( $class ) ) {
				$collisions[] = $class;
			}
		}

		if ( ! empty( $collisions ) ) {
			$requirements->add_error( sprintf(
				_n( "Unable to register the following class because it already exists: %s", 'Unable to register the following classes because they already exist: %s', count( $collisions ) ),
				implode( ', ', $collisions ),
				$this->textdomain
			) );
		}

	}

}

endif;
