<?php
/**
 * Class WPCF7r_Settings file.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact form 7 Redirect Settings panel
 */
class WPCF7r_Settings {
	public $product_url = WPCF7_PRO_REDIRECT_PLUGIN_PAGE_URL;

	public function __construct() {
		$this->page_slug = 'wpc7_redirect';
		$this->api       = new Qs_Api();

		add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
		add_action( 'admin_init', array( $this, 'wpcf7r_register_options' ) );
		add_filter( 'plugin_row_meta', array( $this, 'register_plugin_links' ), 10, 2 );

	}

	/**
	 * Deactivate the license - Disabled on v2.0
	 */
	public function deactivate_license() {
		$serial        = WPCF7r_Utils::get_serial_key();
		$activation_id = WPCF7r_Utils::get_activation_id();

		$this->api->deactivate_liscense( $activation_id, $serial );
		$this->reset_activation();

		wp_redirect( WPCF7r_Utils::get_plugin_settings_page_url() );
	}

	/**
	 * Register plugin options
	 */
	public function wpcf7r_register_options() {
		$this->fields = array();
		// $this->add_license_section();
		$this->add_settings_section();

		foreach ( $this->fields as $field ) {
			$args = array();
			add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), $this->page_slug, $field['section'], $field );
			// $args['sanitize_callback'] = array($this, 'validate_serial_key');
			register_setting( $this->page_slug, $field['uid'], $args );
		}
	}

	public function add_settings_section() {

		add_settings_section( 'settings_section', __( 'Global Settings', 'wpcf7-redirect' ), array( $this, 'section_callback' ), $this->page_slug );

		$this->fields = array_merge(
			$this->fields,
			array(
				array(
					'uid'          => 'wpcf_debug',
					'label'        => 'Debug',
					'section'      => 'settings_section',
					'type'         => 'checkbox',
					'options'      => false,
					'placeholder'  => '',
					'helper'       => '',
					'supplemental' => __( 'This will open the actions post type and display debug feature.', 'wpcf7-redirect' ),
					'default'      => '',
				),
			)
		);
	}

	/**
	 * add_license_section - Deprecated
	 */
	public function add_license_section() {
		add_settings_section( 'serial_section', __( 'License Information', 'wpcf7-redirect' ), array( $this, 'section_callback' ), $this->page_slug );

		$this->fields = array_merge(
			$this->fields,
			array(
				array(
					'uid'          => 'wpcf7r_serial_number',
					'label'        => 'Serial Number',
					'section'      => 'serial_section',
					'type'         => 'text',
					'options'      => false,
					'placeholder'  => 'Type your serial here',
					'helper'       => '',
					'supplemental' => __( 'This process will send your serial/domain to a 3rd party validation server to validate the key authenticity', 'wpcf7-redirect' ),
					'default'      => '',
				),
			)
		);

		return $fields;
	}

	/**
	 * Validate serial key process
	 *
	 * @param $serial
	 */
	public function validate_serial_key( $serial ) {
		if ( ! $serial ) {
			return;
		}

		$activation_id = WPCF7r_Utils::get_activation_id();

		if ( ! $activation_id ) {
			$is_valid = $this->api->activate_serial( $serial );
		} else {
			$is_valid = $this->api->validate_serial( $activation_id, $serial );
		}

		// serial was not valid
		if ( is_wp_error( $is_valid ) ) {
			$message = $is_valid->get_error_message();
			if ( is_object( $message ) && isset( $message->license_key ) ) {
				$message = $message->license_key[0];
			}
			add_settings_error(
				'wpcf7r_serial_number',
				'not-valid-serial',
				$message,
				'error'
			);
			$this->reset_activation();
			return false;
		} elseif ( ! $activation_id ) {
			// serial was valid, update the activation key for future validation
			$this->set_activation( $is_valid->data );
		}

		if ( isset( $_GET['deactivate'] ) ) {
			return '';
		}

		return $serial;
	}

	/**
	 * Delete all activation data - use in case activation validation or activation returns an error
	 */
	public function reset_activation() {
		delete_option( 'wpcf7r_activation_id' );
		delete_option( 'wpcf7r_activation_expiration' );
		delete_option( 'wpcf7r_activation_data' );

		WPCF7r_Utils::delete_serial_key();
	}

	/**
	 * Set all data related with the plugin activation
	 *
	 * @param $validation_data
	 */
	public function set_activation( $validation_data ) {
		update_option( 'wpcf7r_activation_id', $validation_data->activation_id );
		update_option( 'wpcf7r_activation_expiration', $validation_data->expire );
		update_option( 'wpcf7r_activation_data', $validation_data );
	}

	/**
	 * A function for displaying a field on the admin settings page
	 */
	public function field_callback( $arguments ) {
		$value = get_option( $arguments['uid'] ); // Get the current value, if there is one
		if ( ! $value ) { // If no value exists
			$value = $arguments['default']; // Set to our default
		}
		// Check which type of field we want
		switch ( $arguments['type'] ) {
			case 'text': // If it is a text field
			case 'password':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" class="widefat" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
				break;
			case 'checkbox': // If it is a text field
				$checked = checked( $value, '1', false );
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" class="widefat" %5$s/>', $arguments['uid'], $arguments['type'], $arguments['placeholder'], '1', $checked );
				break;
		}

		$helper       = $arguments['helper'];
		$supplimental = $arguments['supplemental'];

		// If there is help text
		if ( $helper ) {
			printf( '<span class="helper"> %s</span>', $helper ); // Show it
		}

		// If there is supplemental text
		if ( $supplimental ) {
			printf( '<p class="description">%s</p>', $supplimental ); // Show it
		}
	}

	/**
	 * Main call for creating the settings page
	 */
	public function create_plugin_settings_page() {
		// Add the menu item and page
		$page_title = 'Extensions';
		$menu_title = 'Extensions';
		$capability = 'manage_options';
		$callback   = array( $this, 'plugin_settings_page_content' );
		$icon       = 'dashicons-admin-plugins';
		$position   = 100;

		add_submenu_page(
			'wpcf7',
			$page_title,
			$page_title,
			$capability,
			$this->page_slug,
			$callback
		);
	}

	/**
	 * The setting page template HTML
	 */
	public function plugin_settings_page_content() {
		$wpcf7_extensions = new WPCF7R_Extensions();
		?>
		<section class="padbox">
			<div class="wrap wrap-wpcf7redirect">
				<h2>
					<span>
						<?php _e( 'Redirection For Contact Form 7', 'wpcf7-redirect' ); ?>
					</span>
				</h2>
				<div class="user-message">
					<ul>
						<li>The new and improved Submission Action System is how redirection for Contact Form 7 processes submission data.</li>
						<li>Submission Actions are like modules which can be added to each form.</li>
						<li>You can add multiple actions to a single form or a single action can be added to any form.</li>
						<li>Create custom login/registration forms, manage your conversion pixels, set conditional rules and much more.</li>
					</ul>
					<form method="post">
						<br/>
						<input type="submit" value="Check For Updates" class="button-primary" name="extensions-updates-check"/>
						<br/><br/>
						<lable>
							<input type="checkbox" checked="checked" name="update-banner" value="1">
							<?php _e( 'Get available deals and sales (no spam of any kind).' ); ?>
						</label>
					</form>
				</div>
				<div class="postbox extensions-list-wrap">
					<div class="padbox">
						<?php $wpcf7_extensions->display(); ?>
					</div>
				</div>
				<div class="postbox">
					<div class="padbox">
						<form method="POST" action="options.php" name="wpcfr7_settings">
							<?php
							do_action( 'before_settings_fields' );
							settings_fields( $this->page_slug );
							do_settings_sections( $this->page_slug );
							submit_button();
							?>
						</form>
						<?php if ( is_wpcf7r_debug() ) : ?>
							<input type="button" name="migrate_again" value="<?php _e( 'Migrate Again from Old Settings', 'wpcf7-redirect' ); ?>" class="migrate_again button button-secondary" />
							<input type="button" name="reset_all" value="<?php _e( 'Reset all Settings - BE CAREFUL! this will delete all Redirection for Contact Form 7 data.', 'wpcf7-redirect' ); ?>" class="cf7-redirect-reset button button-secondary" />

							<h3><?php _e( 'Recreate from Debug', 'wpcf7-redirect' ); ?></h3>
							<textarea id="debug-info" style="width:100%;"></textarea>
							<button class="reacreate-from-debug button button-primary"><?php _e( 'Recreate From Debug', 'wpcf7-redirect' ); ?></button>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Create a section on the admin settings page
	 */
	public function section_callback( $arguments ) {
		switch ( $arguments['id'] ) {
			case 'serial_section':
				echo sprintf( "In order to gain access to plugin updates, please enter your license key below. If you don't have a licence key, please <a href='%s' target='_blank'>click Here</a>.", $this->product_url );
				break;
		}
	}

	/**
	 * Add a link to the options page to the plugin description block.
	 */
	function register_plugin_links( $links, $file ) {
		if ( WPCF7_PRO_REDIRECT_BASE_NAME === $file ) {
			$links[] = WPCF7r_Utils::get_settings_link();
		}
		return $links;
	}
}
