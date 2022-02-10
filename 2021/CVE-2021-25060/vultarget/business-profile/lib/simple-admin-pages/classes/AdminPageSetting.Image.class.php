<?php

/**
 * Register, display and save an image field setting in the admin menu
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingImage_2_6_3 extends sapAdminPageSetting_2_6_3 {

	public $sanitize_callback = 'absint';

	/**
	 * Scripts that must be loaded for this component
	 * @since 2.1.0
	 */
	public $scripts = array(
		'sap-image' => array(
			'path'			=> 'js/image.js',
			'dependencies'	=> array( 'jquery' ),
			'version'		=> SAP_VERSION,
			'footer'		=> true,
		),
	);

	/**
	 * Translateable strings required for this component
	 * @since 2.1.0
	 */
	public $strings = array(
		'add_image'			=> null, // __( 'Add Image', 'textdomain' ),
		'change_image'		=> null, // __( 'Change Image', 'textdomain' ),
		'remove_image'		=> null, // __( 'Remove Image', 'textdomain' ),
	);

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {
		$image_url = $this->value ? wp_get_attachment_url( $this->value ) : '';
		?>

		<fieldset <?php $this->print_conditional_data(); ?>>
			<div class="sap-image-wrapper <?php echo $this->value ? 'sap-image-wrapper-has-image' : 'sap-image-wrapper-no-image'; ?>" data-id="sap-<?php echo esc_attr( $this->id ); ?>">
				<input name="<?php echo esc_attr( $this->get_input_name() ); ?>" type="hidden" id="sap-<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $this->value ); ?>">
				<img src="<?php echo esc_attr( $image_url ); ?>">
				<button class="button sap-image-btn-add" id="sap-<?php echo esc_attr( $this->id ); ?>-add"><?php echo esc_html( $this->strings['add_image'] ); ?></button>
				<button class="button sap-image-btn-change" id="sap-<?php echo esc_attr( $this->id ); ?>-change"><?php echo esc_html( $this->strings['change_image'] ); ?></button>
				<button class="button sap-image-btn-remove" id="sap-<?php echo esc_attr( $this->id ); ?>-remove"><?php echo esc_html( $this->strings['remove_image'] ); ?></button>
			</div>
		</fieldset>

		<?php

		// global $wp_scripts;
		// print_r( $wp_scripts );

		$this->display_description();

	}

}
