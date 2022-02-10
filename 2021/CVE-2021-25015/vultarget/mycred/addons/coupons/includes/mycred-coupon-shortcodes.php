<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Load Coupon Shortcode
 * Renders the form that allows users to redeem coupons from.
 * @see http://codex.mycred.me/shortcodes/mycred_load_coupon/
 * @since 1.4
 * @version 1.4
 */
if ( ! function_exists( 'mycred_render_shortcode_load_coupon' ) ) :
	function mycred_render_shortcode_load_coupon( $atts, $content = NULL ) {

		if ( ! is_user_logged_in() )
			return $content;

		extract( shortcode_atts( array(
			'label'       => 'Coupon',
			'button'      => 'Apply Coupon',
			'placeholder' => ''
		), $atts, MYCRED_SLUG . '_load_coupon' ) );

		$mycred = mycred();
		if ( ! isset( $mycred->coupons ) )
			return '<p><strong>Coupon Add-on settings are missing! Please visit the myCRED > Settings page to save your settings before using this shortcode.</strong></p>';

		// Prep
		$user_id = get_current_user_id();

		$output  = '<div class="mycred-coupon-form">';

		// On submits
		if ( isset( $_POST['mycred_coupon_load']['token'] ) && wp_verify_nonce( $_POST['mycred_coupon_load']['token'], 'mycred-load-coupon' . $user_id ) ) {

			$coupon_code = sanitize_text_field( $_POST['mycred_coupon_load']['couponkey'] );
			$coupon_post = mycred_get_coupon_post( $coupon_code );
			if ( isset( $coupon_post->ID ) ) {

				$coupon      = mycred_get_coupon( $coupon_post->ID );

				// Attempt to use this coupon
				$load        = mycred_use_coupon( $coupon_code, $user_id );

				// Load myCRED in the type we are paying out for messages
				if ( isset( $coupon->point_type ) && $coupon->point_type != $mycred->cred_id )
					$mycred = mycred( $coupon->point_type );

				// That did not work out well, need to show an error message
				if ( ! mycred_coupon_was_successfully_used( $load ) ) {

					$message = mycred_get_coupon_error_message( $load, $coupon );
					$message = $mycred->template_tags_general( $message );
					$output .= '<div class="alert alert-danger">' . $message . '</div>';

				}

				// Success!
				else {

					//$message = $mycred->template_tags_amount( $mycred->coupons['success'], $coupon->value );
					$updated_coupon_value=$coupon->value;
					$updated_coupon_value=apply_filters('mycred_show_custom_coupon_value',$updated_coupon_value);
					$coupon_settings = mycred_get_addon_settings( 'coupons' ,  $coupon->point_type  );
					$message = $mycred->template_tags_amount( $coupon_settings['success'], $updated_coupon_value );   // without filter
					$message = str_replace( '%amount%', $mycred->format_creds( $updated_coupon_value ), $message );
					$output .= '<div class="alert alert-success">' . $message . '</div>';

				}

			}

			// Invalid coupon
			else {

				$message = mycred_get_coupon_error_message( 'invalid' );
				$message = $mycred->template_tags_general( $message );
				$output .= '<div class="alert alert-danger">' . $message . '</div>';

			}

		}

		if ( $label != '' )
			$label = '<label for="mycred-coupon-code">' . $label . '</label>';

		$output .= '
	<form action="" method="post" class="form-inline">
		<div class="form-group">
			' . $label . '
			<input type="text" name="mycred_coupon_load[couponkey]" placeholder="' . esc_attr( $placeholder ) . '" id="mycred-coupon-couponkey" class="form-control" value="" />
		</div>
		<div class="form-group">
			<input type="hidden" name="mycred_coupon_load[token]" value="' . wp_create_nonce( 'mycred-load-coupon' . $user_id ) . '" />
			<input type="submit" class="btn btn-primary" value="' . $button . '" />
		</div>
	</form>
</div>';

		return apply_filters( 'mycred_load_coupon', $output, $atts, $content );

	}
endif;
