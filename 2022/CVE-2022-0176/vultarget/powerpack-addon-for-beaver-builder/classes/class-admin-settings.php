<?php

if ( !class_exists( 'BB_PowerPack_Admin_Settings' ) ) {
    /**
     * Handles logic for the admin settings page.
     *
     * @since 1.1.5
     */
    final class BB_PowerPack_Admin_Settings {
        /**
    	 * Holds any errors that may arise from
    	 * saving admin settings.
    	 *
    	 * @since 1.1.5
    	 * @var array $errors
    	 */
    	static public $errors = array();

        /**
         * Holds the templates.
         *
         * @since 1.1.8
         * @var array
         */
    	static public $templates = array();

        /**
         * Holds the templates count.
         *
         * @since 1.1.8
         * @var array
         */
    	static public $templates_count;

    	/**
    	 * Initializes the admin settings.
    	 *
    	 * @since 1.1.5
    	 * @return void
    	 */
    	static public function init()
    	{
            self::$templates_count = array(
                'page'  => 0,
                'row'   => 0,
            );
    		add_action( 'plugins_loaded', __CLASS__ . '::init_hooks' );
    	}

    	/**
    	 * Adds the admin menu and enqueues CSS/JS if we are on
    	 * the plugin's admin settings page.
    	 *
    	 * @since 1.1.5
    	 * @return void
    	 */
    	static public function init_hooks()
    	{
    		if ( ! is_admin() ) {
    			return;
    		}

            add_action( 'admin_menu',           __CLASS__ . '::menu' );
            add_action( 'network_admin_menu',   __CLASS__ . '::menu' );

    		if ( isset( $_REQUEST['page'] ) && 'ppbb-settings' == $_REQUEST['page'] ) {
                add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );
    			self::save();
    		}
    	}

        /**
    	 * Enqueues the needed CSS/JS for the builder's admin settings page.
    	 *
    	 * @since 1.0
    	 * @return void
    	 */
    	static public function styles_scripts()
    	{
    		// Styles
    		wp_enqueue_style( 'pp-admin-settings', BB_POWERPACK_URL . 'assets/css/admin-settings.css', array(), BB_POWERPACK_VER );
    	}

        /**
    	 * Renders the admin settings menu.
    	 *
    	 * @since 1.1.5
    	 * @return void
    	 */
    	static public function menu()
    	{
            if ( current_user_can( 'manage_options' ) ) {

    			$title = __('PowerPack', 'bb-powerpack-lite');
    			$cap   = 'manage_options';
    			$slug  = 'ppbb-settings';
    			$func  = __CLASS__ . '::render';

    			add_submenu_page( 'options-general.php', $title, $title, $cap, $slug, $func );
    		}

            if ( current_user_can( 'manage_network_plugins' ) ) {

                $title = __('PowerPack', 'bb-powerpack-lite');
        		$cap   = 'manage_network_plugins';
        		$slug  = 'ppbb-settings';
        		$func  = __CLASS__ . '::render';

        		add_submenu_page( 'settings.php', $title, $title, $cap, $slug, $func );
            }
    	}

        /**
    	 * Renders the admin settings.
    	 *
    	 * @since 1.1.5
    	 * @return void
    	 */
    	static public function render()
    	{
    		include BB_POWERPACK_DIR . 'includes/admin-settings.php';
    	}

        /**
    	 * Renders the update message.
    	 *
    	 * @since 1.1.5
    	 * @return void
    	 */
    	static public function render_update_message()
    	{
    		if ( ! empty( self::$errors ) ) {
    			foreach ( self::$errors as $message ) {
    				echo '<div class="error"><p>' . $message . '</p></div>';
    			}
    		}
    		else if( ! empty( $_POST ) && ! isset( $_POST['email'] ) ) {
    			echo '<div class="updated"><p>' . __( 'Settings updated!', 'bb-powerpack-lite' ) . '</p></div>';
    		}
    	}

        /**
    	 * Renders the action for a form.
    	 *
    	 * @since 1.1.5
    	 * @param string $type The type of form being rendered.
    	 * @return void
    	 */
    	static public function get_form_action( $type = '' )
    	{
    		if ( is_network_admin() ) {
    			return network_admin_url( '/settings.php?page=ppbb-settings' . $type );
    		}
    		else {
    			return admin_url( '/options-general.php?page=ppbb-settings' . $type );
    		}
    	}

        /**
    	 * Adds an error message to be rendered.
    	 *
    	 * @since 1.1.5
    	 * @param string $message The error message to add.
    	 * @return void
    	 */
    	static public function add_error( $message )
    	{
    		self::$errors[] = $message;
    	}

        /**
    	 * Returns an option from the database for
    	 * the admin settings page.
    	 *
    	 * @since 1.1.5
    	 * @param string $key The option key.
         * @param bool $network_override Multisite template override check.
    	 * @return mixed
    	 */
        static public function get_option( $key, $network_override = true )
        {
        	if ( is_network_admin() ) {
        		$value = get_site_option( $key );
        	}
            elseif ( ! $network_override && is_multisite() ) {
                $value = get_site_option( $key );
            }
            elseif ( $network_override && is_multisite() ) {
                $value = get_option( $key );
                $value = false === $value ? get_site_option( $key ) : $value;
            }
            else {
        		$value = get_option( $key );
        	}

            return $value;
        }

        /**
    	 * Updates an option from the admin settings page.
    	 *
    	 * @since 1.1.5
    	 * @param string $key The option key.
    	 * @param mixed $value The value to update.
    	 * @param bool $network_override Multisite template override check.
    	 * @return mixed
    	 */
        static public function update_option( $key, $value, $network_override = true )
        {
        	if ( is_network_admin() ) {
        		update_site_option( $key, $value );
        	}
            // Delete the option if network overrides are allowed and the override checkbox isn't checked.
    		else if ( $network_override && is_multisite() && ! isset( $_POST['bb_powerpack_override_ms'] ) ) {
    			delete_option( $key );
    		}
            else {
        		update_option( $key, $value );
        	}
        }

        /**
    	 * Delete an option from the admin settings page.
    	 *
    	 * @since 1.1.5
    	 * @param string $key The option key.
    	 * @param mixed $value The value to delete.
    	 * @return mixed
    	 */
        static public function delete_option( $key )
        {
        	if ( is_network_admin() ) {
        		delete_site_option( $key );
        	} else {
        		delete_option( $key );
        	}
        }

        /**
    	 * Saves the admin settings.
    	 *
    	 * @since 1.0
    	 * @return void
    	 */
    	static public function save()
    	{
    		// Only admins can save settings.
    		if ( ! current_user_can('manage_options') ) {
    			return;
    		}

			self::save_integration();
    		self::save_extensions();
		}
		
		/**
		 * Saves integrations.
		 *
		 * @since 1.2.5
		 * @access private
		 * @return void
		 */
		static private function save_integration()
		{
			if ( isset( $_POST['bb_powerpack_fb_app_id'] ) && ( ! isset( $_POST['bb_powerpack_license_deactivate'] ) && ! isset( $_POST['bb_powerpack_license_activate'] ) ) ) {
				
				// Validate App ID.
				if ( ! empty( $_POST['bb_powerpack_fb_app_id'] ) ) {
					$response = wp_remote_get( 'https://graph.facebook.com/' . $_POST['bb_powerpack_fb_app_id'] );
					$error = '';

					if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
						$error = __( 'Facebook App ID is not valid.', 'bb-powerpack-lite' );
					}

					if ( ! empty( $error ) ) {
						wp_die( $error, __( 'Facebook SDK', 'bb-powerpack-lite' ), array( 'back_link' => true ) );
					}
				}

				self::update_option( 'bb_powerpack_fb_app_id', trim( $_POST['bb_powerpack_fb_app_id'] ), false );
			}
		}

        /**
         * Saves the extensions settings.
         *
         * @since 1.1.6
         * @access private
         * @return void
         */
        static private function save_extensions()
        {
            if ( isset( $_POST['pp-extensions-nonce'] ) && wp_verify_nonce( $_POST['pp-extensions-nonce'], 'pp-extensions' ) ) {

                if ( isset( $_POST['bb_powerpack_quick_preview'] ) ) {
                    self::update_option( 'bb_powerpack_quick_preview', 1 );
                } else {
                    self::update_option( 'bb_powerpack_quick_preview', 2 );
                }

                if ( isset( $_POST['bb_powerpack_search_box'] ) ) {
                    self::update_option( 'bb_powerpack_search_box', 1 );
                } else {
                    self::update_option( 'bb_powerpack_search_box', 2 );
                }

                if ( isset( $_POST['bb_powerpack_extensions'] ) && is_array( $_POST['bb_powerpack_extensions'] ) ) {
                    self::update_option( 'bb_powerpack_extensions', $_POST['bb_powerpack_extensions'] );
                }

                if ( ! isset( $_POST['bb_powerpack_extensions'] ) ) {
                    self::update_option( 'bb_powerpack_extensions', array('disabled') );
                }
            }
        }

        /**
    	 * Returns an array of all PowerPack extensions which are enabled.
    	 *
    	 * @since 1.1.5
    	 * @return array
    	 */
        static public function get_enabled_extensions()
        {
            $enabled_extensions = self::get_option( 'bb_powerpack_extensions', true );

            if ( is_array( $enabled_extensions ) ) {

                if ( ! isset( $enabled_extensions['row'] ) ) {
                    $enabled_extensions['row'] = array();
                }
                if ( ! isset( $enabled_extensions['col'] ) ) {
                    $enabled_extensions['col'] = array();
                }

            }

            if ( ! $enabled_extensions || ! is_array( $enabled_extensions ) ) {

                $enabled_extensions = pp_extensions();

            }

            return $enabled_extensions;
        }
    }

    BB_PowerPack_Admin_Settings::init();
}
