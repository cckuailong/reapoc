<?php
/**
 * The main class that manages the plugin.
 *
 * @package wpcf7r
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WPCF7r_Form_Helper - Adds contact form scripts and actions
 */
class WPCF7r_Form_Helper {

	/**
	 * Class Constructor
	 */
	public function __construct() {

		$this->plugin_url     = WPCF7_PRO_REDIRECT_BASE_URL;
		$this->assets_js_lib  = WPCF7_PRO_REDIRECT_BASE_URL . 'assets/lib/';
		$this->assets_js_url  = WPCF7_PRO_REDIRECT_BASE_URL . 'assets/js/';
		$this->assets_css_url = WPCF7_PRO_REDIRECT_BASE_URL . 'assets/css/';
		$this->build_js_url   = WPCF7_PRO_REDIRECT_BASE_URL . 'build/js/';
		$this->build_css_url  = WPCF7_PRO_REDIRECT_BASE_URL . 'build/css/';
		$this->extensions     = wpcf7_get_extensions();
		$this->add_actions();

	}

	/**
	 * Add Actions
	 */
	private function add_actions() {

		global $pagenow;

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'wpcf7_editor_panels', array( $this, 'add_panel' ) );
		add_action( 'wpcf7_after_save', array( $this, 'store_meta' ) );
		// add contact form scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'front_end_scripts' ) );
		// add contact form scripts for admin panel.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend' ) );
		add_action( 'wp_ajax_activate_wpcf7r_extension', array( $this, 'activate_extension' ) );
		add_action( 'wp_ajax_deactivate_wpcf7r_extension', array( $this, 'deactivate_plugin_license' ) );
		add_action( 'admin_init', array( $this, 'maybe_update_extensions_check' ), 10 );
		add_action( 'wp_ajax_wpcf7r_extension_update', array( $this, 'maybe_update_extension' ), 10 );
		add_action( 'wp_ajax_get_coupon', array( $this, 'get_coupon' ) );
		add_action( 'wpcf7_admin_notices', array( $this, 'render_discount_banner' ), 15, 3 );
		add_action( 'admin_init', array( $this, 'dismiss_ads' ) );

		// define the alternative API for updating checking
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );

		add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );

	}

	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $arg
	 * @return bool|object
	 *
	 * @version  1.0.0
	 */
	public function check_info( $false, $action, $arg ) {
		$action_plugin = false;

		$api = new Qs_Api();

		foreach ( $this->extensions as $extension_type => $extension_details ) {

			$extension = new WPCF7R_Extension( $extension_details );

			if ( isset( $arg->slug ) && $arg->slug === $extension->get_slug() ) {
				$action_plugin = $extension->get_sku();
				break;
			}
		}

		if ( ( $action == 'query_plugins' || $action == 'plugin_information' ) && $action_plugin ) {

			$array_pattern = array(
				'/^([\*\s])*(\d\d\.\d\d\.\d\d\d\d[^\n]*)/m',
				'/^\n+|^[\t\s]*\n+/m',
				'/\n/',
			);

			$array_replace = array(
				'<h4>$2</h4>',
				'</div><div>',
				'</div><div>',
			);

			$changelog = $api->get_action_changelog( $action_plugin );

			$information = new stdClass;

			$information->sections['changelog'] = '<div>' . $changelog->changelog . '</div>';

			return $information;
		}

		return $false;
	}

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return object $ transient
	 *
	 * @version  1.0.0
	 */
	public function check_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		foreach ( $this->extensions as $extension_type => $extension_details ) {

			$extension = new WPCF7R_Extension( $extension_details );

			if ( $extension->has_update() ) {

				$update_url       = add_query_arg( 'update', true, $extension->get_extension_remote_file() );
				$obj              = new stdClass();
				$obj->id          = $extension->get_slug();
				$obj->slug        = $extension->get_slug();
				$obj->new_version = $extension->update_version();
				$obj->url         = $update_url;
				$obj->plugin      = $extension->get_extension_relative_path();
				$obj->package     = $update_url;
				$obj->tested      = WPCF7_PRO_REDIRECT_WP_TESTED;
				$obj->icons       = array(
					'2x' => $extension->get_icon(),
					'1x' => $extension->get_icon(),
				);

				$transient->response[ $extension->get_extension_relative_path() ] = $obj;
			}
		}

		return $transient;
	}
	/**
	 * Check if an extension requires an update
	 *
	 * @return void
	 */
	public function maybe_update_extension() {

		$extension_name = isset( $_POST['extension_name'] ) && $_POST['extension_name'] ? sanitize_text_field( $_POST['extension_name'] ) : '';

		if ( $extension_name ) {
			$extension = $this->get_extension_object( $extension_name );

			if ( $extension->has_update() ) {
				$results = $extension->save_extension_file();
			}

			$results['html'] = $extension->ajax_extension_html();
		}

		wp_send_json( $results );
	}

	/**
	 * Check if there is a need for update check.
	 *
	 * @return void
	 */
	public function maybe_update_extensions_check() {
		global $wp_version;

		$current = get_transient( 'update_wpcf7r_extensions' );

		if ( $current && ! isset( $_GET['force-check'] ) ) {
			return;
		}

		$current = new stdClass();

		$current->version_checked = $wp_version;
		$current->last_checked    = current_time( 'timestamp' );

		set_transient( 'update_wpcf7r_extensions', $current, HOUR_IN_SECONDS * 24 * 12 );

		$this->check_for_extensions_update();

	}

	/**
	 * Check for extension update from API.
	 *
	 * @return void
	 */
	public function check_for_extensions_update() {

		$api = new Qs_Api();

		foreach ( $this->extensions as $extension_type => $extension_details ) {

			$extension = new WPCF7R_Extension( $extension_details );

			if ( $extension->is_active() ) {

				$has_update = $api->extension_has_update( $extension );

				if ( $has_update ) {

					$extension->set_needs_update( $has_update );

				} else {

					$extension->set_updated();

				}
			}
		}
	}

	/**
	 * Get the extension object
	 *
	 * @param [string] $extention_name - the name of the extension.
	 * @return object - $extention_object - the extension object.
	 */
	public function get_extension_object( $extention_name ) {

		$extention_object = isset( $this->extensions[ $extention_name ] ) ? new WPCF7R_Extension( $this->extensions[ $extention_name ] ) : '';

		return $extention_object;

	}

	/**
	 * Deactivate plugin license.
	 *
	 * @return void
	 */
	public function deactivate_plugin_license() {

		if ( ! isset( $_POST['extension_name'] ) ) {

			$results = array(
				'message' => __( 'Missing name or serial', 'wpcf7-redirect' ),
			);

		} else {

			$extentsion_settings = $this->extensions[ $_POST['extension_name'] ];

			if ( $extentsion_settings ) {
				$extentsion = new WPCF7R_Extension( $extentsion_settings );
				$results    = $extentsion->deactivate_license();
			}
		}

		wp_send_json( $results );

	}

	/**
	 * Activate extension license.
	 *
	 * @return void
	 */
	public function activate_extension() {

		if ( ! isset( $_POST['extension_name'] ) || ! isset( $_POST['serial'] ) ) {
			$results = array(
				'message' => __( 'Missing name or serial', 'wpcf7-redirect' ),
			);
		} else {

			$extentsion_settings = $this->extensions[ $_POST['extension_name'] ];

			if ( $extentsion_settings ) {
				$extentsion = new WPCF7R_extension( $extentsion_settings );
				$serial     = sanitize_text_field( $_POST['serial'] );
				$results    = $extentsion->activate( $serial );
			} else {
				$results['extension_html'] = __( 'Somthing went wrong', 'wpcf7-redirect' );
			}
		}

		wp_send_json( $results );
	}

	/**
	 * Dismiss plugin ads
	 */
	public function dismiss_ads() {

		if ( isset( $_GET['wpcf7_redirect_dismiss_banner'] ) && '1' === $_GET['wpcf7_redirect_dismiss_banner'] ) {
			$banner_version = wpcf7r_get_banner_version() ? wpcf7r_get_banner_version() : 1;

			update_option( 'wpcf7_redirect_dismiss_banner', $banner_version );
		}

	}

	/**
	 * Only load scripts when contact form instance is created
	 */
	public function front_end_scripts() {

		wp_register_style( 'wpcf7-redirect-script-frontend', $this->build_css_url . 'wpcf7-redirect-frontend.min.css', '1.1' );
		wp_enqueue_style( 'wpcf7-redirect-script-frontend' );
		wp_register_script( 'wpcf7-redirect-script', $this->build_js_url . 'wpcf7-redirect-frontend-script.js', array( 'jquery' ), '1.1', true );
		wp_enqueue_script( 'wpcf7-redirect-script' );
		wp_localize_script( 'wpcf7-redirect-script', 'wpcf7r', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		// Load active extensions scripts and styles.
		$installed_extensions = wpcf7r_get_available_actions();

		foreach ( $installed_extensions as $installed_extension ) {
			if ( method_exists( $installed_extension['handler'], 'enqueue_frontend_scripts' ) ) {
				call_user_func( array( $installed_extension['handler'], 'enqueue_frontend_scripts' ) );
			}
		}

							// add support for other plugins
		do_action( 'wpcf7_redirect_enqueue_frontend', $this );
	}

	/**
	 * Check if the current page is the plugin settings page
	 */
	public function is_wpcf7_settings_page() {
		return isset( $_GET['page'] ) && 'wpc7_redirect' === $_GET['page'];
	}

	/**
	 * Check if the current admin post type is a lead post type.
	 *
	 * @return boolean
	 */
	public function is_wpcf7_lead_page() {
		return 'wpcf7r_leads' === get_post_type();
	}

	/**
	 * Check if the current admin page is accesibe plugin page.
	 *
	 * @return boolean
	 */
	public function is_accesibe_page() {
		$screen = get_current_screen();

		return 'toplevel_page_' . qs_get_plugin_display_name() === $screen->base;
	}

	/**
	 * Check if the current page is the contact form edit screen
	 */
	public function is_wpcf7_edit() {
		return wpcf7r_is_wpcf7_edit();
	}

	/**
	 * Load plugin textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wpcf7-redirect', false, basename( dirname( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Enqueue theme styles and scripts - back-end
	 */
	public function enqueue_backend() {

		if ( $this->is_wpcf7_edit() || $this->is_wpcf7_settings_page() || $this->is_wpcf7_lead_page() || $this->is_accesibe_page() ) {

			wp_enqueue_style( 'admin-build', $this->build_css_url . 'wpcf7-redirect-backend.css', array(), WPCF7_PRO_REDIRECT_PLUGIN_VERSION );
			wp_enqueue_script( 'admin-build-js', $this->build_js_url . 'wpcf7-redirect-backend-script.js', array(), WPCF7_PRO_REDIRECT_PLUGIN_VERSION, true );
			wp_enqueue_script(
				array(
					'jquery-ui-core',
					'jquery-ui-sortable',
					'wp-color-picker',
				)
			);

			// Load active extensions scripts and styles.
			$installed_extensions = wpcf7r_get_available_actions();

			foreach ( $installed_extensions as $installed_extension ) {
				if ( method_exists( $installed_extension['handler'], 'enqueue_backend_scripts' ) ) {
					call_user_func( array( $installed_extension['handler'], 'enqueue_backend_scripts' ) );
				}
			}

			// add support for other plugins.
			do_action( 'wpcf_7_redirect_admin_scripts', $this );
		}
	}

	/**
	 * Store form data.
	 *
	 * @param [object] $cf7  -contact form object.
	 */
	public function store_meta( $cf7 ) {

		$form = get_cf7r_form( $cf7->id() );
		$form->store_meta( $cf7 );
	}

	/**
	 * Adds a tab to the editor on the form edit page
	 *
	 * @param array $panels An array of panels. Each panel has a callback function.
	 */
	public function add_panel( $panels ) {

		// Disable plugin functionality for old contact form 7 installations.

		if ( wpcf7_get_cf7_ver() > 4.8 ) {

			$panels['redirect-panel'] = array(
				'title'    => __( 'Actions', 'wpcf7-redirect' ),
				'callback' => array( $this, 'create_panel_inputs' ),
			);

			$panels['extensions-panel'] = array(
				'title'    => __( 'Extensions', 'wpcf7-redirect' ),
				'callback' => array( $this, 'extensions_manager' ),
			);

			if ( is_wpcf7r_debug() ) {

				$panels['debug-panel'] = array(
					'title'    => __( 'Debug', 'wpcf7-redirect' ),
					'callback' => array( $this, 'wpcf7_debug' ),
				);

			}
		}

		return $panels;
	}

	/**
	 * Get the default fields
	 */
	public static function get_plugin_default_fields() {

		return array(
			array(
				'name' => 'redirect_type',
				'type' => 'text',
			),
		);
	}

	/**
	 * Handler to retrive banner to display
	 * At the moment used to display the pro version bannner
	 */
	public function banner() {
		$banner_manager = new Banner_Manager();
		$banner_manager->show_banner();
	}

	/**
	 * Create the panel inputs.
	 *
	 * @param object $cf7 - Contact form 7 post object.
	 */
	public function create_panel_inputs( $cf7 ) {

		$form = get_cf7r_form( $cf7->id() );

		$form->init();
	}

	/**
	 * Manage contact form 7 redirection pro extensions
	 *
	 * @param object $cf7 - Contact form 7 post object.
	 */
	public function extensions_manager( $cf7 ) {

		$this->extensions_manager = $this->get_extension_manager();
		$this->extensions_manager->init();

	}

	/**
	 * Get extension manager instance
	 *
	 * @return object - extensions manager object.
	 */
	public function get_extension_manager() {
		return isset( $this->extensions_manager ) ? $this->extensions_manager : new WPCF7R_Extensions();
	}

	/**
	 * Display debug tab on contact form
	 *
	 * @param $cf7 - Contact form 7 post object.
	 * @return void
	 */
	public function wpcf7_debug( $cf7 ) {

		global $wp_version;

		$form_json = self::get_debug_data( $cf7->id() );

		echo '<h3>' . __( 'Debug Information', 'wpcf7-redirect' ) . '</h3>';
		echo '<div>' . __( 'The debug information includes the following details', 'wpcf7-redirect' ) . '</div><ul>';
		echo '<li>' . __( 'Form post data.', 'wpcf7-redirect' ) . '</li>';
		echo '<li>' . __( 'Actions post data.', 'wpcf7-redirect' ) . '</li>';
		echo '<li>' . __( 'PHP Version', 'wpcf7-redirect' ) . '</li>';
		echo '<li>' . __( 'Installed plugins list', 'wpcf7-redirect' ) . '</li>';
		echo '<li>' . __( 'WordPress Version', 'wpcf7-redirect' ) . '</li>';
		echo '</ul>';
		echo '<div style="color:red;font-size:18px;margin-bottom:20px;">' . __( 'The data is used for debug purposes only!', 'wpcf7-redirect' ) . '</div>';
		echo "<div><textarea style='width:100%;height:200px;margin-bottom:40px;font-size:11px'>{$form_json}</textarea><br/></div>";
		echo '<input type="submit" class="button button-primary send-debug-info" value="Send Debug Report"/><br/><br/>';
		echo '<label><input type="checkbox" class="approve-debug" /> I approve sending debug information to Query Solutions support team.</label>';

	}

	/**
	 * Get encoded debug data
	 *
	 * @param $form_id - contact form 7 ID.
	 * @return void
	 */
	public static function get_debug_data( $form_id ) {
		global $wp_version;

		$cf7r_form = get_cf7r_form( (int) $form_id );

		$form_json = base64_encode(
			serialize(
				array(
					'actions'    => $cf7r_form->get_active_actions(),
					'form_meta'  => get_post_custom( $form_id ),
					'form_post'  => get_post( $form_id ),
					'plugins'    => json_encode( get_plugins() ),
					'phpver'     => phpversion(),
					'wp_version' => $wp_version,
					'site_url'   => home_url(),
				)
			)
		);

		return $form_json;
	}

	/**
	 * Render discount banner
	 *
	 * @param [object] $page - the current page.
	 * @param [object] $action - the required action.
	 * @param [object] $object - contact form 7 object.
	 */
	public function render_discount_banner( $page, $action, $object ) {

		if ( 'edit' === $action ) {
			$banner_version_dismissed = get_option( 'wpcf7_redirect_dismiss_banner' );
			$banner                   = wpcf7r_get_discount_banner();

			if ( $banner ) {
				update_option( 'wpcf7r_banner_version', $banner->version );

				if ( ! $banner_version_dismissed && wpcf7r_get_banner_version() !== $banner_version_dismissed ) {
					echo $banner->banner;
				}
			}
		}
	}

	/**
	 * Returns an html for displaying a link to the form.
	 *
	 * @param [int] $form_id - the if of the contact form 7 post.
	 * @return [string] - a link to the form edit screen.
	 */
	public static function get_cf7_link_html( $form_id ) {
		$form_post  = get_post( $form_id );
		$form_title = get_the_title( $form_id );
		$link       = get_edit_post_link( $form_id );

		if ( $form_post ) {
			return sprintf( "<a href='%s' target='_blank'>%s</a>", $link, $form_title );
		}

		return __( 'This form no longer exists', 'wpcf7-redirect' );
	}

	/**
	 * Get coupon ajax function
	 */
	public function get_coupon() {
		$data = $_POST['data'];

		$email = isset( $data['email'] ) && is_email( $data['email'] ) ? $data['email'] : false;

		if ( ! $email ) {
			$results = array(
				'status'  => 'rp-error',
				'message' => 'Please enter a valid email.',
			);

			wp_send_json( $results );
		} else {
			$ip     = $_SERVER['REMOTE_ADDR'];
			$url    = home_url();
			$accept = sanitize_text_field( $data['get_offers'] );
			$params = array(
				'ip_address' => $ip,
				'accept'     => $accept,
				'email'      => $email,
				'url'        => $url,
			);

			$params = http_build_query( $params );

			$endpoint = WPCF7_PRO_REDIRECT_PLUGIN_PAGE_URL . "wp-json/api-v1/get-coupon?{$params}";

			$response = wp_remote_post( $endpoint );

			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $body['message'] ) ) {
				$results = array(
					'status'  => 'rp-success',
					'message' => $body['message'],
				);
			} elseif ( isset( $body['redirect'] ) ) {
				$results = array(
					'status' => 'rp-success',
					'url'    => $body['redirect'],
				);
			}
		}

		wp_send_json( $results );
	}
}
