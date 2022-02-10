<?php

/**
 * Register, display and save a textarea field setting in the admin menu
 *
 * @since 2.0.a.5
 * @package Simple Admin Pages
 */

class sapAdminPageSettingAddress_2_6_3 extends sapAdminPageSetting_2_6_3 {

	/*
	 * Size of this textarea
	 *
	 * This is put directly into a css class [size]-text,
	 * and setting this to 'large' will link into WordPress's existing textarea
	 * style for full-width textareas.
	 */
	public $size = 'small';

	/*
	 * A Google Maps API key for geolocating addresses
	 */
	public $api_key = '';

	/*
	 * A jQuery selector pointing to the input element where the Google Maps API
	 * key can be retrieved.
	 */
	public $api_key_selector = '';

	/**
	 * Scripts that must be loaded for this component
	 * @since 2.0.a.5
	 */
	public $scripts = array(
		'sap-address' => array(
			'path'			=> 'js/address.js',
			'dependencies'	=> array( 'jquery' ),
			'version'		=> SAP_VERSION,
			'footer'		=> true,
		),
	);

	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Translateable strings required for this component
	 * @since 2.0.a.8
	 */
	public $strings = array(
		'sep-action-links'	=> null, // _x( ' | ', 'separator between admin action links in address component', 'textdomain' ),
		'sep-lat-lon'		=> null, // _x( ', ', 'separates latitude and longitude', 'textdomain' ),
		'no-setting'		=> null, // __( 'No map coordinates set.', 'textdomain' ),
		'retrieving'		=> null, // __( 'Requesting new coordinates', 'textdomain' ),
		'select'			=> null, // __( 'Select a match below', 'textdomain' ),
		'view'				=> null, // __( 'View', 'textdomain' ),
		'retrieve'			=> null, // __( 'Retrieve map coordinates', 'textdomain' ),
		'remove'			=> null, // __( 'Remove map coordinates', 'textdomain' ),
		'try_again'			=> null, // __( 'Try again?', 'textdomain' ),
		'result_error'		=> null, // __( 'Error', 'textdomain' ),
		'result_invalid'	=> null, // __( 'Invalid request. Be sure to fill out the address field before retrieving coordinates.', 'textdomain' ),
		'result_denied'		=> null, // __( 'Request denied.', 'textdomain' ),
		'result_limit'		=> null, // __( 'Request denied because you are over your request quota.', 'textdomain' ),
		'result_empty'		=> null, // __( 'Nothing was found at that address', 'textdomain' ),
	);

	/**
	 * Escape the value to display it safely HTML textarea fields
	 * @since 2.0.a.5
	 */
	public function esc_value( $val ) {

		$escaped = array();
		$escaped['text'] = empty( $val['text'] ) ? '' : esc_textarea( $val['text'] );
		$escaped['lat'] = empty( $val['lat'] ) ? '' : esc_textarea( $val['lat'] );
		$escaped['lon'] = empty( $val['lon'] ) ? '' : esc_textarea( $val['lon'] );

		return $escaped;
	}

	/**
	 * Set the size of this textarea field
	 * @since 1.0
	 */
	public function set_size( $size ) {
		$this->size = esc_attr( $size );
	}

	/**
	 * Wrapper for the sanitization callback function.
	 *
	 * This just reduces code duplication for child classes that need a custom
	 * callback function.
	 * @since 2.0.a.5
	 */
	public function sanitize_callback_wrapper( $value ) {

		$sanitized = array();
		$sanitized['text'] = empty( $value['text'] ) ? '' : wp_kses_post( $value['text'] );
		$sanitized['lat'] = empty( $value['lat'] ) ? '' : sanitize_text_field( $value['lat'] );
		$sanitized['lon'] = empty( $value['lon'] ) ? '' : sanitize_text_field( $value['lon'] );

		return $sanitized;
	}

	/**
	 * Display this setting
	 * @since 2.0.a.5
	 */
	public function display_setting() {

		wp_localize_script(
			'sap-address',
			'sap_address',
			array(
				'strings' => $this->strings,
				'api_key' => $this->api_key,
				'api_key_selector' => $this->api_key_selector,
			)
		);

		$this->display_description();

		?>

		<fieldset <?php $this->print_conditional_data(); ?>>
			<div class="sap-address" id="<?php echo esc_attr( $this->id ); ?>">
				<textarea name="<?php echo esc_attr( $this->get_input_name() ); ?>[text]" id="<?php echo esc_attr( $this->get_input_name() ); ?>" class="<?php echo esc_attr( $this->size ); ?>-text"<?php echo !empty( $this->placeholder ) ? ' placeholder="' . esc_attr( $this->placeholder ) . '"' : ''; ?> <?php echo ( $this->disabled ? 'disabled' : ''); ?>><?php echo esc_textarea( $this->value['text'] ); ?></textarea>
				<p class="sap-map-coords-wrapper">
					<span class="dashicons dashicons-location-alt"></span>
					<span class="sap-map-coords">
					<?php if ( empty( $this->value['lat'] ) || empty( $this->value['lon'] ) ) : ?>
						<?php echo esc_html( $this->strings['no-setting'] ); ?>
					<?php else : ?>
						<?php echo esc_html( $this->value['lat'] . $this->strings['sep-lat-lon'] . $this->value['lon'] ); ?>
						<a href="//maps.google.com/maps?q=<?php echo esc_attr( $this->value['lat'] ) . ',' . esc_attr( $this->value['lon'] ); ?>" class="sap-view-coords" target="_blank"><?php echo esc_html( $this->strings['view'] ); ?></a>
					<?php endif; ?>
					</span>
				</p>
				<p class="sap-coords-action-wrapper">
					<a href="#" class="sap-get-coords">
						<?php echo esc_html( $this->strings['retrieve'] ); ?>
					</a>
					<?php echo $this->strings['sep-action-links']; ?>
					<a href="#" class="sap-remove-coords">
						<?php echo esc_html( $this->strings['remove'] ); ?>
					</a>
				</p>
				<input type="hidden" class="lat" name="<?php echo esc_attr( $this->get_input_name() ); ?>[lat]" value="<?php echo esc_attr( $this->value['lat'] ); ?>">
				<input type="hidden" class="lon" name="<?php echo esc_attr( $this->get_input_name() ); ?>[lon]" value="<?php echo esc_attr( $this->value['lon'] ); ?>">
			</div>
		</fieldset>

		<?php
	}

}
