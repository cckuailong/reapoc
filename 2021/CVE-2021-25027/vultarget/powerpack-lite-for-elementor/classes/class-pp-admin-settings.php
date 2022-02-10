<?php
namespace PowerpackElementsLite\Classes;

/**
 * Handles logic for the admin settings page.
 *
 * @since 1.0.0
 */
final class PP_Admin_Settings {
	/**
	 * Holds any errors that may arise from
	 * saving admin settings.
	 *
	 * @since 1.0.0
	 * @var array $errors
	 */
	public static $errors = array();

	public static $settings = array();

	/**
	 * Initializes the admin settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'plugins_loaded', __CLASS__ . '::init_hooks' );
	}

	/**
	 * Adds the admin menu and enqueues CSS/JS if we are on
	 * the plugin's admin settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init_hooks() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', __CLASS__ . '::menu', 601 );

		if ( isset( $_REQUEST['page'] ) && 'powerpack-settings' == $_REQUEST['page'] ) {
			//add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );
			self::save();
			self::reset_settings();
		}

		add_action( 'admin_init', __CLASS__ . '::refresh_instagram_access_token' );
	}

	/**
	 * Enqueues the needed CSS/JS for the builder's admin settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function styles_scripts() {
		// Styles
		//wp_enqueue_style( 'pp-admin-settings', POWERPACK_ELEMENTS_LITE_URL . 'assets/css/admin-settings.css', array(), POWERPACK_ELEMENTS_LITE_VER );
	}

	/**
	 * Get settings.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_settings() {
		$default_settings = array(
			'plugin_name'       => '',
			'plugin_desc'       => '',
			'plugin_author'     => '',
			'plugin_uri'        => '',
			'admin_label'       => '',
			'support_link'      => '',
			'hide_support'      => 'off',
			'hide_wl_settings'  => 'off',
			'hide_plugin'       => 'off',
			'google_map_api'    => '',
		);

		$settings = self::get_option( 'pp_elementor_settings', true );

		if ( ! is_array( $settings ) || empty( $settings ) ) {
			return $default_settings;
		}

		if ( is_array( $settings ) && ! empty( $settings ) ) {
			return array_merge( $default_settings, $settings );
		}
	}

	/**
	 * Get admin label from settings.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_admin_label() {
		return 'PowerPack';
	}

	/**
	 * Renders the update message.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function render_update_message() {
		if ( ! empty( self::$errors ) ) {
			foreach ( self::$errors as $message ) {
				echo '<div class="error"><p>' . esc_html( $message ) . '</p></div>';
			}
		} elseif ( ! empty( $_POST ) && ! isset( $_POST['email'] ) ) {
			echo '<div class="updated"><p>' . esc_html__( 'Settings updated!', 'powerpack' ) . '</p></div>';
		}
	}

	/**
	 * Adds an error message to be rendered.
	 *
	 * @since 1.0.0
	 * @param string $message The error message to add.
	 * @return void
	 */
	public static function add_error( $message ) {
		self::$errors[] = $message;
	}

	/**
	 * Renders the admin settings menu.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function menu() {
		if ( is_main_site() || ! is_multisite() ) {

			$admin_label = self::get_admin_label();

			if ( current_user_can( 'delete_users' ) ) {

				$title = $admin_label;
				$cap   = 'delete_users';
				$slug  = 'powerpack-settings';
				$func  = __CLASS__ . '::render';

				add_submenu_page( 'elementor', $title, $title, $cap, $slug, $func );
			}
		}
	}

	public static function render() {
		include POWERPACK_ELEMENTS_LITE_PATH . 'includes/admin/admin-settings.php';
	}

	public static function get_tabs() {
		$settings = self::get_settings();

		return apply_filters( 'pp_elements_lite_admin_settings_tabs', array(
			'modules'   => array(
				'title'     => esc_html__( 'Elements', 'powerpack' ),
				'show'      => true,
				'cap'       => 'edit_posts',
				'file'      => POWERPACK_ELEMENTS_LITE_PATH . 'includes/admin/admin-settings-modules.php',
				'priority'  => 150,
			),
			'extensions'   => array(
				'title'     => esc_html__( 'Extensions', 'powerpack' ),
				'show'      => true,
				'cap'       => 'edit_posts',
				'file'      => POWERPACK_ELEMENTS_LITE_PATH . 'includes/admin/admin-settings-extensions.php',
				'priority'  => 200,
			),
			'integration'   => array(
				'title'         => esc_html__( 'Integration', 'powerpack' ),
				'show'          => true,
				'cap'           => ! is_network_admin() ? 'manage_options' : 'manage_network_plugins',
				'file'          => POWERPACK_ELEMENTS_LITE_PATH . 'includes/admin/admin-settings-integration.php',
				'priority'      => 300,
			),
		) );
	}

	public static function render_tabs( $current_tab ) {
		$tabs = self::get_tabs();
		$sorted_data = array();

		foreach ( $tabs as $key => $data ) {
			$data['key'] = $key;
			$sorted_data[ $data['priority'] ] = $data;
		}

		ksort( $sorted_data );

		foreach ( $sorted_data as $data ) {
			if ( $data['show'] ) {
				if ( isset( $data['cap'] ) && ! current_user_can( $data['cap'] ) ) {
					continue;
				}
				?>
				<a href="<?php echo esc_attr( self::get_form_action( '&tab=' . $data['key'] ) ); ?>" class="nav-tab<?php echo ( $current_tab == $data['key'] ? ' nav-tab-active' : '' ); ?>"><span><?php echo esc_html( $data['title'] ); ?></span></a>
				<?php
			}
		}
	}

	public static function render_setting_page() {
		$tabs = self::get_tabs();
		$current_tab = self::get_current_tab();

		if ( isset( $tabs[ $current_tab ] ) ) {
			$no_setting_file_msg = esc_html__( 'Setting page file could not be located.', 'powerpack' );

			if ( ! isset( $tabs[ $current_tab ]['file'] ) || empty( $tabs[ $current_tab ]['file'] ) ) {
				echo esc_html( $no_setting_file_msg );
				return;
			}

			if ( ! file_exists( $tabs[ $current_tab ]['file'] ) ) {
				echo esc_html( $no_setting_file_msg );
				return;
			}

			$render = ! isset( $tabs[ $current_tab ]['show'] ) ? true : $tabs[ $current_tab ]['show'];
			$cap = 'manage_options';

			if ( isset( $tabs[ $current_tab ]['cap'] ) && ! empty( $tabs[ $current_tab ]['cap'] ) ) {
				$cap = $tabs[ $current_tab ]['cap'];
			} else {
				$cap = ! is_network_admin() ? 'manage_options' : 'manage_network_plugins';
			}

			if ( ! $render || ! current_user_can( $cap ) ) {
				esc_html_e( 'You do not have permission to view this setting.', 'powerpack' );
				return;
			}

			include $tabs[ $current_tab ]['file'];
		}
	}

	/**
	 * Get current tab.
	 */
	public static function get_current_tab() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'modules';

		return $current_tab;
	}

	/**
	 * Renders the action for a form.
	 *
	 * @since 1.0.0
	 * @param string $type The type of form being rendered.
	 * @return void
	 */
	public static function get_form_action( $type = '' ) {
		return admin_url( '/admin.php?page=powerpack-settings' . $type );
	}

	/**
	 * Returns an option from the database for
	 * the admin settings page.
	 *
	 * @since 1.0.0
	 * @param string $key The option key.
	 * @return mixed
	 */
	public static function get_option( $key, $network_override = true, $default = null ) {
		if ( is_network_admin() ) {
			$value = get_site_option( $key );
		} elseif ( ! $network_override && is_multisite() ) {
			$value = get_site_option( $key );
		} elseif ( $network_override && is_multisite() ) {
			$value = get_option( $key );
			$value = ( false === $value || ( is_array( $value ) && in_array( 'disabled', $value ) && get_option( 'pp_override_ms' ) != 1 ) ) ? get_site_option( $key ) : $value;
		} else {
			$value = get_option( $key );
		}

		if ( empty( $value ) && ! is_null( $default ) ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Updates an option from the admin settings page.
	 *
	 * @since 1.0.0
	 * @param string $key The option key.
	 * @param mixed $value The value to update.
	 * @return mixed
	 */
	public static function update_option( $key, $value, $network_override = true ) {
		if ( is_network_admin() ) {
			update_site_option( $key, $value );
		} elseif ( $network_override && is_multisite() && ! isset( $_POST['pp_override_ms'] ) ) {
			// Delete the option if network overrides are allowed and the override checkbox isn't checked.
			delete_option( $key );
		} else {
			update_option( $key, $value );
		}
	}

	/**
	 * Delete an option from the admin settings page.
	 *
	 * @since 1.0.0
	 * @param string $key The option key.
	 * @param mixed $value The value to delete.
	 * @return mixed
	 */
	public static function delete_option( $key ) {
		if ( is_network_admin() ) {
			delete_site_option( $key );
		} else {
			delete_option( $key );
		}
	}

	public static function save() {
		// Only admins can save settings.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		self::save_modules();
		self::save_extensions();
		self::save_integration();
		self::save_tracking();

		do_action( 'pp_admin_after_settings_saved' );
	}

	/**
	 * Saves integrations.
	 *
	 * @since 2.5.4
	 * @access private
	 * @return void
	 */
	private static function save_integration() {
		if ( isset( $_POST['pp_instagram_access_token'] ) ) {
			self::update_option( 'pp_instagram_access_token', trim( $_POST['pp_instagram_access_token'] ), false );
		}
	}

	private static function save_modules() {
		if ( ! isset( $_POST['pp-modules-settings-nonce'] ) || ! wp_verify_nonce( $_POST['pp-modules-settings-nonce'], 'pp-modules-settings' ) ) {
			return;
		}

		if ( isset( $_POST['pp_enabled_modules'] ) ) {
			update_site_option( 'pp_elementor_modules', $_POST['pp_enabled_modules'] );
		} else {
			update_site_option( 'pp_elementor_modules', 'disabled' );
		}
	}

	public static function save_extensions() {
		if ( ! isset( $_POST['pp-extensions-settings-nonce'] ) || ! wp_verify_nonce( $_POST['pp-extensions-settings-nonce'], 'pp-extensions-settings' ) ) {
			return;
		}

		if ( isset( $_POST['pp_enabled_extensions'] ) ) {
			update_option( 'pp_elementor_extensions', $_POST['pp_enabled_extensions'] );
		} else {
			update_option( 'pp_elementor_extensions', 'disabled' );
		}
	}

	private static function save_tracking() {
		if ( isset( $_POST['pp_allowed_tracking'] ) ) {
			self::update_option( 'pp_allowed_tracking', sanitize_text_field( $_POST['pp_allowed_tracking'] ), true );
		} else {
			self::delete_option( 'pp_allowed_tracking' );
		}
	}

	public static function reset_settings() {
		if ( isset( $_GET['reset_modules'] ) ) {
			delete_site_option( 'pp_elementor_modules' );
			self::$errors[] = esc_html__( 'Modules settings updated!', 'powerpack' );
		}

		if ( isset( $_GET['reset_extensions'] ) ) {
			delete_site_option( 'pp_elementor_extensions' );
			self::$errors[] = esc_html__( 'Extension settings updated!', 'powerpack' );
		}
	}

	/**
	* Refresh instagram token after 30 days.
	*
	* @since 2.5.4
	*/
	public static function refresh_instagram_access_token() {
		$access_token         = trim( \PowerpackElementsLite\Classes\PP_Admin_Settings::get_option( 'instagram_access_token' ) );
		$updated_access_token = 'ppe_updated_instagram_access_token';
	
		if ( empty( $access_token ) ) {
			return;
		}
	
		$updated = get_transient( $updated_access_token );
	
		if ( ! empty( $updated ) ) {
			return;
		}
	
		$endpoint_url = add_query_arg(
			[
				'access_token' => $access_token,
				'grant_type'   => 'ig_refresh_token',
			],
			'https://graph.instagram.com/refresh_access_token'
		);
	
		$response = wp_remote_get( $endpoint_url );
	
		if ( ! $response || 200 !== wp_remote_retrieve_response_code( $response ) || is_wp_error( $response ) ) {
			set_transient( $updated_access_token, 'error', DAY_IN_SECONDS );
			return;
		}
	
		$body = wp_remote_retrieve_body( $response );
	
		if ( ! $body ) {
			set_transient( $updated_access_token, 'error', DAY_IN_SECONDS );
			return;
		}
	
		$body = json_decode( $body, true );
	
		if ( empty( $body['access_token'] ) || empty( $body['expires_in'] ) ) {
			set_transient( $updated_access_token, 'error', DAY_IN_SECONDS );
			return;
		}
	
		set_transient( $updated_access_token, 'updated', 30 * DAY_IN_SECONDS );
	}
}

PP_Admin_Settings::init();
