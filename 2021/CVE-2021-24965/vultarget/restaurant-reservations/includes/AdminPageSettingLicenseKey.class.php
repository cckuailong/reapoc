<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbAdminPageSettingLicenseKey' ) ) {
/**
 * Add a setting to Simple Admin Pages to register and verify an
 * EDD Software Licensing key.
 *
 * This class is modelled on AdminPageSetting.class.php in the
 * Simple Admin Pages Library. But it doesn't extend that class
 * due to rules within the library about how versions are
 * managed.
 *
 * See: https://github.com/NateWr/simple-admin-pages
 *
 * @since 1.4.1
 */
class rtbAdminPageSettingLicenseKey {

	/**
	 * Scripts to load for this component
	 *
	 * @since 1.4.1
	 */
	public $scripts = array();

	/**
	 * Styles to load for this component
	 *
	 * @since 1.4.1
	 */
	public $styles = array();

	/**
	 * Product slug on the store
	 *
	 * @since 1.4.1
	 */
	public $product;

	/**
	 * Store URL which manages license data
	 *
	 * @since 1.4.1
	 */
	public $store_url;

	/**
	 * Translateable strings required for this component
	 *
	 * @since 1.4.1
	 */
	public $strings = array(
		'active'			=> null, // __( 'Active', 'textdomain' ),
		'expired'			=> null, // __( 'Expired', 'textdomain' ),
		'inactive'			=> null, // __( 'Inactive', 'textdomain' ),
		'expiry'			=> null, // _x( 'Expiry',  'Label before the expiration date of the license key', textdomain' ),
		'deactivate'		=> null, // __( 'Deactivate License', 'textdomain' ),
	);

	/**
	 * Initialize the setting
	 *
	 * @since 1.4.1
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );

		// Get any existing value
		$this->set_value();

		// Set an error if the object is missing necessary data
		if ( $this->missing_data() ) {
			$this->set_error();
		}

		// Process a license activation/deactivation
		add_filter( 'admin_init', array( $this, 'process_action' ), 100 );
	}

	/**
	 * Parse the arguments passed in the construction and assign them to
	 * internal variables.
	 *
	 * @since 1.4.1
	 */
	private function parse_args( $args ) {
		foreach ( $args as $key => $val ) {
			switch ( $key ) {

				case 'id' :
					$this->{$key} = esc_attr( $val );

				case 'title' :
					$this->{$key} = esc_attr( $val );

				case 'product' :
					$this->{$key} = sanitize_key( $val );

				case 'url' :
					$this->{$key} = sanitize_text_field( $val );

				default :
					$this->{$key} = $val;

			}
		}
	}

	/**
	 * Check for missing data when setup.
	 *
	 * @since 1.4.1
	 */
	private function missing_data() {

		// Required fields
		if ( empty( $this->id ) ) {
			$this->set_error(
				array(
					'type'		=> 'missing_data',
					'data'		=> 'id'
				)
			);
		}
		if ( empty( $this->title ) ) {
			$this->set_error(
				array(
					'type'		=> 'missing_data',
					'data'		=> 'title'
				)
			);
		}
	}

	/**
	 * Set a value
	 *
	 * @since 1.4.1
	 */
	public function set_value( $val = null ) {

		if ( $val === null ) {
			$option_group_value = get_option( $this->page );
			$val = isset( $option_group_value[ $this->id ] ) ? $option_group_value[ $this->id ] : '';
		}

		$this->value = $this->esc_value( $val );
	}

	/**
	 * Escape the value to display it in text fields and other input fields
	 *
	 * @since 1.4.1
	 */
	public function esc_value( $val ) {

		$value = array(
			'api_key'	=> '',
			'status'	=> false,
			'expiry'	=> false,
		);

		if ( empty( $val ) || empty( $val['api_key'] ) ) {
			return $value;
		}

		$value['api_key'] = esc_attr( $val['api_key'] );

		if ( !empty( $val['status'] ) ) {
			$value['status'] = esc_attr( $val['status'] );
		}

		if ( !empty( $val['expiry'] ) ) {
			$value['expiry'] = esc_attr( $val['expiry'] );
		}

		return $value;
	}

	/**
	 * Display this setting
	 *
	 * @since 1.4.1
	 */
	public function display_setting() {

		// Set a flag for the output
		$is_active = $this->value['status'] == 'valid' ? true : false;
		$status = empty( $this->value['status'] ) ? 'inactive' : $this->value['status'];
		$status_string = !empty( $this->strings[ $status] ) ? $this->strings[ $status ] : __( 'Invalid', 'restaurant-reservations' );

		// Compile activation/deactivation URL
		if ( !empty( $this->value['api_key'] ) ) {
			$url = add_query_arg(
				array(
					'id'		=> $this->id,
				)
			);
			if ( $is_active ) {
				$url = add_query_arg( 'action', 'deactivate', $url );
			} else {
				$url = add_query_arg( 'action', 'activate', $url );
			}
		}
		?>

		<div class="rtb-license-setting" data-id="<?php echo esc_attr( $this->id ); ?>">
			
			<input 
				name="<?php echo esc_attr( $this->get_input_name().'[api_key]' ); ?>" 
				type="text" 
				id="<?php echo esc_attr( $this->get_input_name().'[api_key]' ); ?>" 
				value="<?php echo esc_attr( $this->value['api_key'] ); ?>"
				<?php echo !empty( $this->placeholder ) ? ' placeholder="' . esc_attr( $this->placeholder ) . '"' : ''; ?> 
				class="regular-text">

			<?php if ( !empty( $this->value['api_key'] ) ) : ?>
			<span class="status <?php echo $is_active ? 'valid' : 'inactive'; ?>">
				<?php echo esc_html( $status_string ); ?>
			</span>

				<a href="<?php echo esc_url( $url ); ?>" class="button">
					<?php echo $is_active ? $this->strings['deactivate'] : $this->strings['activate']; ?>
				</a>

			<span class="spinner"></span>

			<?php endif;

		$this->display_description();

		?>

		</div>

		<?php
	}

	/**
	 * Display a description for this setting
	 *
	 * @since 1.4.1
	 */
	public function display_description() {

		if ( !empty( $this->description ) ) : ?>

			<p class="description"><?php echo $this->description; ?></p>

		<?php endif;
	}

	/**
	 * Generate an option input field name, using the grouped schema.
	 *
	 * @since 1.4.1
	 */
	public function get_input_name() {
		return esc_attr( $this->page ) . '[' . esc_attr( $this->id ) . ']';
	}


	/**
	 * Sanitize the array of text inputs for this setting
	 *
	 * @since 1.4.1
	 */
	public function sanitize_callback_wrapper( $values ) {

		$output = array(
			'api_key'	=> '',
			'status'	=> false,
			'expiry'	=> false,
		);

		if ( empty( $values ) || empty( $values['api_key'] ) ) {
			return $output;
		}

		$output['api_key'] = trim( sanitize_text_field( $values['api_key'] ) );

		// Clear status and expiry when a license key has changed
		global $rtb_controller;
		$old = $rtb_controller->settings->get_setting( $this->id );
		if ( empty( $old['api_key'] ) || $old['api_key'] !== $output['api_key'] ) {
			return $output;
		}

		// Preserve old status values
		$output = array_merge( $old, $output );

		return $output;
	}

	/**
	 * Add and register this setting
	 *
	 * @since 1.4.1
	 */
	public function add_settings_field( $section_id ) {

		add_settings_field(
			$this->id,
			$this->title,
			array( $this, 'display_setting' ),
			$this->tab,
			$section_id
		);

	}

	/**
	 * Set an error
	 *
	 * @since 1.4.1
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

	/**
	 * Process a license activation if requested
	 *
	 * @since 1.4.1
	 */
	public function process_action() {

		if ( !current_user_can( 'manage_options' ) || empty( $_GET['tab'] ) || $_GET['tab'] !== 'rtb-licenses' || empty( $_GET['action'] ) || empty( $_GET['id'] ) || $_GET['id'] !== $this->id ) {
			return;
		}

		$params = array();
		$params['edd_action'] = $_GET['action'] === 'activate' ? 'activate_license' : 'deactivate_license';
		$params['license'] = sanitize_text_field( $this->value['api_key'] );
		$params['item_name'] = urlencode( $this->product );

		$response = wp_remote_get( add_query_arg( $params, $this->store_url ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) {
			$url = remove_query_arg( array( 'id', 'action' ) );
			$url = add_query_arg( 'license_result', 'response_wp_error', $url );
			header( 'Location: ' . esc_url_raw( $url ) );
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );


		if ( $params['edd_action'] == 'activate' ) {
			$result = $this->process_activation_response( $license_data );
		} else {
			$result = $this->process_activation_response( $license_data );
		}

		// Construct a URL to redirect back to the tab
		$url = remove_query_arg( array( 'id', 'action' ) );
		$url = add_query_arg(
			array(
				'license_result' 	=> $result ? 1 : 0,
				'action'			=> $_GET['action'] == 'activate' ? 'activate' : 'deactivate',
			),
			$url
		);

		// If the result failed maybe add note on why
		if ( !$result && !empty( $license_data->error ) ) {
			$url = add_query_arg( 'result_error', $license_data->error, $url );
		}

		header( 'Location: ' . esc_url_raw( $url ) );

	}

	/**
	 * Process the response to an activation request
	 *
	 * @since 1.4.1
	 */
	public function process_activation_response( $license_data ) {

		if ( ( !empty( $license_data->error ) && ( $license_data->error == 'missing' || $license_data->error == 'item_name_mismatch' ) ) || $license_data->license == 'invalid' ) {
			$this->value['status'] = 'invalid';
			$this->value['expiry'] = false;
		} else {
			$this->value['status'] = $license_data->license;
			$this->value['expiry'] = $license_data->expires;
		}

		$rtb_settings = get_option( $this->page );
		$rtb_settings[ $this->id ] = $this->value;

		update_option( $this->page, $rtb_settings );

		return $license_data->license == 'valid' || $license_data->license == 'deactivated';
	}

	/**
	 * Process the response to an deactivation request
	 *
	 * @since 1.4.1
	 */
	public function process_deactivation_response( $license_data ) {

		if ( $license_data->license !== 'deactivated' ) {
			return false;
		} else {
			$this->value['status'] = false;
			$this->value['expiry'] = false;
		}

		$rtb_settings = get_option( $this->page );
		$rtb_settings[ $this->id ] = $this->value;

		update_option( $this->page, $rtb_settings );

		return true;
	}

}
} // endif;
