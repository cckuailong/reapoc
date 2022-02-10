<?php
/**
 * BNFW License setting Handler.
 *
 * @since v1.4
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Add License page.
 *
 * @since 1.4
 */
function bnfw_add_license_page() {
	add_submenu_page(
		'edit.php?post_type=bnfw_notification',
		esc_html__( 'Add-on Licenses', 'bnfw' ),
		esc_html__( 'Add-on Licenses', 'bnfw' ),
		'bnfw',
		'bnfw-license',
		'bnfw_render_license_page'
	);
}

add_action( 'admin_menu', 'bnfw_add_license_page', 11 );

/**
 * Render license page.
 *
 * @since 1.4
 */
function bnfw_render_license_page() {
	$settings = apply_filters( 'bnfw_settings_licenses', array() );
	ob_start(); ?>

	<div class="wrap">
		<h2><?php esc_html_e( 'BNFW Add-on Licenses', 'bnfw' ); ?></h2>

		<form method="post" action="options.php" class="bnfw-form">
			<?php
			settings_errors();
			settings_fields( 'bnfw-license-settings' );
			do_settings_sections( 'bnfw-license' );

			if ( ! empty( $settings ) ) {
				submit_button( esc_html__( 'Save License', 'bnfw' ) );
			} else {
				$store_url = 'https://betternotificationsforwp.com/downloads/';

				if ( bnfw_is_tracking_allowed() ) {
					$store_url .= "?utm_source=WP%20Admin%20Submenu%20Item%20-%20Add-on%20Licenses&amp;utm_medium=referral";
				}

				_e( '<br>You have no BNFW Premium Add-ons installed yet.<br>You can buy add-ons from the <a href="' . $store_url . '" target="_blank">Store</a>.<br>Once purchased, install and activate the add-on(s) like you would any other WordPress plugin. Then return here and enter your license key.', 'bnfw' );
			}
			?>
		</form>
	</div>

	<?php echo ob_get_clean();
}

function bnfw_license_settings() {
	$settings = apply_filters( 'bnfw_settings_licenses', array() );

	if ( ! empty( $settings ) ) {

		add_settings_section(
			'bnfw_license_section',           // Section ID
			esc_html__( '', 'bnfw' ),         // Title above settings section
			'__return_false',                 // Name of function that renders a description of the settings section
			'bnfw-license'                    // Page to show on
		);

		foreach ( $settings as $option ) {
			$name = isset( $option['name'] ) ? $option['name'] : '';
			add_settings_field(
				'bnfw_licenses[' . $option['id'] . ']',
				$name,
				'bnfw_license_key_callback',
				'bnfw-license',
				'bnfw_license_section',
				array(
					'id'          => isset( $option['id'] ) ? $option['id'] : null,
					'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
					'name'        => isset( $option['name'] ) ? $option['name'] : null,
					'size'        => isset( $option['size'] ) ? $option['size'] : null,
					'options'     => isset( $option['options'] ) ? $option['options'] : '',
					'std'         => isset( $option['std'] ) ? $option['std'] : '',
					'min'         => isset( $option['min'] ) ? $option['min'] : null,
					'max'         => isset( $option['max'] ) ? $option['max'] : null,
					'step'        => isset( $option['step'] ) ? $option['step'] : null,
					'chosen'      => isset( $option['chosen'] ) ? $option['chosen'] : null,
					'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : null,
					'allow_blank' => isset( $option['allow_blank'] ) ? $option['allow_blank'] : true,
					'readonly'    => isset( $option['readonly'] ) ? $option['readonly'] : false,
					'faux'        => isset( $option['faux'] ) ? $option['faux'] : false,
				)
			);
		}

		register_setting(
			'bnfw-license-settings',
			'bnfw_licenses'
		);
	}
}

add_action( 'admin_init', 'bnfw_license_settings', 11 );

/**
 * Register the new license field type
 *
 * @return  void
 */
function bnfw_license_key_callback( $args ) {
	$bnfw_options = get_option( 'bnfw_licenses' );

	if ( isset( $bnfw_options[ $args['id'] ] ) ) {
		$value = $bnfw_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="bnfw_licenses[' . $args['id'] . ']" name="bnfw_licenses[' . $args['id'] . ']" value="' . esc_attr( $value ) . '">';

	if ( 'valid' == get_option( $args['options']['is_valid_license_option'] ) ) {
		$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . esc_attr__( 'Deactivate License', 'bnfw' ) . '">';
	}

	$html .= '<label for="bnfw_licenses[' . $args['id'] . ']"> ' . esc_html( $args['desc'] ) . '</label>';

	echo $html;
}
