<?php
/*
Notes:
RegEx uses Positive lookahead.
The FSP message is displayed by default on the WP Reset Password Form/page and the BuddyPress Registration and frontend Profile page.
The FSP message is not displayed by default on the WP Profile page or the BuddyPress backend Profile page.
WordPress Password Reset Form: If the password does not meet the FSP RegEx requirements a new automatically generated WP password will be displayed.
WooCommerce: Already forces strong passwords. Not going to add anything for WooCommerce.
*/

// WP Password Reset Form/page
function bpsPro_fsp_password_reset_form_validation($errors, $user) {

	$BPS_FSP_options = get_option('bulletproof_security_options_fsp');

	if ( $BPS_FSP_options['bps_fsp_on_off'] == 'On' ) {

		$Quantifier = $BPS_FSP_options['bps_fsp_char_length'];
	   
		if ( $BPS_FSP_options['bps_fsp_lower_case'] == 1 ) {
			$bps_fsp_lower_case = '(?=.*[a-z])';
		} else {
			$bps_fsp_lower_case = '';
		}
	   
		if ( $BPS_FSP_options['bps_fsp_upper_case'] == 1 ) {
			$bps_fsp_upper_case = '(?=.*[A-Z])';
		} else {
			$bps_fsp_upper_case = '';
		}
	 
		if ( $BPS_FSP_options['bps_fsp_number'] == 1 ) {
			$bps_fsp_number = '(?=.*\d)';
		} else {
			$bps_fsp_number = '';
		}
	   
		if ( $BPS_FSP_options['bps_fsp_special_char'] == 1 ) {
			$bps_fsp_special_char = '(?=.*[\!\@\#\$\%\^\&\*\(\)\-\_\[\]\{\}\<\>\~\`\+\=\,\.\;\:\/\?\|\'\"\\\\])';
		} else {
			$bps_fsp_special_char = '';
		}   
	
		$RegEx = '/^'.$bps_fsp_lower_case.$bps_fsp_upper_case.$bps_fsp_number.$bps_fsp_special_char.'.{'.$Quantifier.',32}$/';
	
		if ( strlen( $_POST['pass1'] ) < $Quantifier || ! preg_match( $RegEx, $_POST['pass1'] ) ) {
			$errors->add( 'error', $BPS_FSP_options['bps_fsp_message'], 'bulletproof-security' );
		}

	return $errors;
	}
}

add_action( 'validate_password_reset', 'bpsPro_fsp_password_reset_form_validation', 10, 2 );

// WP Profile page password reset Form
// Note: The error is displayed at the top of the profile page
function bpsPro_fsp_profile_password_reset_form_validation($errors, $update, $user) {

	$BPS_FSP_options = get_option('bulletproof_security_options_fsp');

	if ( $BPS_FSP_options['bps_fsp_on_off'] == 'On' ) {

		$Quantifier = $BPS_FSP_options['bps_fsp_char_length'];
	   
		if ( $BPS_FSP_options['bps_fsp_lower_case'] == 1 ) {
			$bps_fsp_lower_case = '(?=.*[a-z])';
		} else {
			$bps_fsp_lower_case = '';
		}
	   
		if ( $BPS_FSP_options['bps_fsp_upper_case'] == 1 ) {
			$bps_fsp_upper_case = '(?=.*[A-Z])';
		} else {
			$bps_fsp_upper_case = '';
		}
	 
		if ( $BPS_FSP_options['bps_fsp_number'] == 1 ) {
			$bps_fsp_number = '(?=.*\d)';
		} else {
			$bps_fsp_number = '';
		}
	   
		if ( $BPS_FSP_options['bps_fsp_special_char'] == 1 ) {
			$bps_fsp_special_char = '(?=.*[\!\@\#\$\%\^\&\*\(\)\-\_\[\]\{\}\<\>\~\`\+\=\,\.\;\:\/\?\|\'\"\\\\])';
		} else {
			$bps_fsp_special_char = '';
		}   
	
		$RegEx = '/^'.$bps_fsp_lower_case.$bps_fsp_upper_case.$bps_fsp_number.$bps_fsp_special_char.'.{'.$Quantifier.',32}$/';
	
		if ( strlen( $_POST['pass1'] ) < $Quantifier || ! preg_match( $RegEx, $_POST['pass1'] ) ) {
			$errors->add( 'error', $BPS_FSP_options['bps_fsp_message'], 'bulletproof-security' );
		}

	return $errors;
	}
}

add_action( 'user_profile_update_errors', 'bpsPro_fsp_profile_password_reset_form_validation', 10, 3 );

// BuddyPress
// BP Registration Form/page message
function bpsPro_fsp_bp_registration_message() {

	$BPS_FSP_options = get_option('bulletproof_security_options_fsp');

	if ( $BPS_FSP_options['bps_fsp_on_off'] == 'On' ) {	
	
		$fsp_message = __( $BPS_FSP_options['bps_fsp_message'], 'bulletproof-security');
		echo '<p>' . $fsp_message . '</p>';
	}
}

add_action( 'bp_before_account_details_fields', 'bpsPro_fsp_bp_registration_message', 10, 0 );

// BP Registration Form/page prevalidation
function bpsPro_fsp_bp_registration_form_validation() {
global $bp;

	$BPS_FSP_options = get_option('bulletproof_security_options_fsp');

	if ( $BPS_FSP_options['bps_fsp_on_off'] == 'On' ) {

		$Quantifier = $BPS_FSP_options['bps_fsp_char_length'];
	   
		if ( $BPS_FSP_options['bps_fsp_lower_case'] == 1 ) {
			$bps_fsp_lower_case = '(?=.*[a-z])';
		} else {
			$bps_fsp_lower_case = '';
		}
	   
		if ( $BPS_FSP_options['bps_fsp_upper_case'] == 1 ) {
			$bps_fsp_upper_case = '(?=.*[A-Z])';
		} else {
			$bps_fsp_upper_case = '';
		}
	 
		if ( $BPS_FSP_options['bps_fsp_number'] == 1 ) {
			$bps_fsp_number = '(?=.*\d)';
		} else {
			$bps_fsp_number = '';
		}
	   
		if ( $BPS_FSP_options['bps_fsp_special_char'] == 1 ) {
			$bps_fsp_special_char = '(?=.*[\!\@\#\$\%\^\&\*\(\)\-\_\[\]\{\}\<\>\~\`\+\=\,\.\;\:\/\?\|\'\"\\\\])';
		} else {
			$bps_fsp_special_char = '';
		}   
	
		$RegEx = '/^'.$bps_fsp_lower_case.$bps_fsp_upper_case.$bps_fsp_number.$bps_fsp_special_char.'.{'.$Quantifier.',32}$/';
	
		if ( strlen( $_POST['signup_password'] ) < $Quantifier || ! preg_match( $RegEx, $_POST['signup_password'] ) ) {
			   $bp->signup->errors['signup_password'] = __( $BPS_FSP_options['bps_fsp_message'], 'bulletproof-security' );
		}
	}
}

add_action( 'bp_signup_pre_validate', 'bpsPro_fsp_bp_registration_form_validation', 10, 0 );

// BP Member Profile Form/page message. Displays message on the General Settings tab only.
function bpsPro_fsp_bp_profile_password_reset_message() {

	$BPS_FSP_options = get_option('bulletproof_security_options_fsp');

	if ( $BPS_FSP_options['bps_fsp_on_off'] == 'On' ) {	

		if ( preg_match( '/.*\/settings\/$/', esc_html($_SERVER['REQUEST_URI']) ) ) {
		
			$fsp_message = __( $BPS_FSP_options['bps_fsp_message'], 'bulletproof-security');
			echo '<p>' . $fsp_message . '</p>';
		}
	}
}

add_action( 'bp_template_content', 'bpsPro_fsp_bp_profile_password_reset_message', 10, 0 );

// BP Member Profile password reset Form/page post validation.
function bpsPro_fsp_bp_profile_password_reset_validation() {
global $bp, $pass_error, $feedback;

	$BPS_FSP_options = get_option('bulletproof_security_options_fsp');

	if ( $BPS_FSP_options['bps_fsp_on_off'] == 'On' ) {

		$Quantifier = $BPS_FSP_options['bps_fsp_char_length'];
	   
		if ( $BPS_FSP_options['bps_fsp_lower_case'] == 1 ) {
			$bps_fsp_lower_case = '(?=.*[a-z])';
		} else {
			$bps_fsp_lower_case = '';
		}
	   
		if ( $BPS_FSP_options['bps_fsp_upper_case'] == 1 ) {
			$bps_fsp_upper_case = '(?=.*[A-Z])';
		} else {
			$bps_fsp_upper_case = '';
		}
	 
		if ( $BPS_FSP_options['bps_fsp_number'] == 1 ) {
			$bps_fsp_number = '(?=.*\d)';
		} else {
			$bps_fsp_number = '';
		}
	   
		if ( $BPS_FSP_options['bps_fsp_special_char'] == 1 ) {
			$bps_fsp_special_char = '(?=.*[\!\@\#\$\%\^\&\*\(\)\-\_\[\]\{\}\<\>\~\`\+\=\,\.\;\:\/\?\|\'\"\\\\])';
		} else {
			$bps_fsp_special_char = '';
		}   
	
		$RegEx = '/^'.$bps_fsp_lower_case.$bps_fsp_upper_case.$bps_fsp_number.$bps_fsp_special_char.'.{'.$Quantifier.',32}$/';
	
		if ( strlen( $_POST['pass1'] ) < $Quantifier || ! preg_match( $RegEx, $_POST['pass1'] ) ) {
		   $pass_error				= false;
		   $feedback_type			= 'error';
		   $feedback				= array();
		   $feedback['pass_error']	= __( $BPS_FSP_options['bps_fsp_message'], 'bulletproof-security' );
		   
		   bp_core_add_message( implode( "\n", $feedback ), $feedback_type );
		   
		   return $feedback;
		}
	}
}

add_action( 'bp_core_general_settings_after_save', 'bpsPro_fsp_bp_profile_password_reset_validation', 10, 0 );

?>