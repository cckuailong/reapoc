<?php
/**
 * Prep the ReCAPTCHA library if needed.
 * Fires on the wp hook.
 */
function pmpro_init_recaptcha() {
	//don't load if setting is off
	global $recaptcha, $recaptcha_validated;
	$recaptcha = pmpro_getOption( 'recaptcha' );
	if ( empty( $recaptcha ) ) {
		return;
	}
	
	//don't load unless we're on the checkout page
	if ( ! pmpro_is_checkout() ) {
		return;
	}
	
	//check for validation
	$recaptcha_validated = pmpro_get_session_var( 'pmpro_recaptcha_validated' );
	if ( ! empty( $recaptcha_validated ) ) {
	    $recaptcha = false;
    }

	//captcha is needed. set up functions to output
	if($recaptcha) {
		global $recaptcha_publickey, $recaptcha_privatekey;
		
		require_once(PMPRO_DIR . '/includes/lib/recaptchalib.php' );
		
		if ( ! function_exists( 'pmpro_recaptcha_get_html' ) ) {
			function pmpro_recaptcha_get_html ($pubkey, $error = null, $use_ssl = false) {

				// Figure out language.
				$locale = get_locale();
				if(!empty($locale)) {
					$parts = explode("_", $locale);
					$lang = $parts[0];
				} else {
					$lang = "en";	
				}
				$lang = apply_filters( 'pmpro_recaptcha_lang', $lang );
	
				// Check which version of ReCAPTCHA we are using.
				$recaptcha_version = pmpro_getOption( 'recaptcha_version' ); 
	
				if( $recaptcha_version == '3_invisible' ) { ?>
					<div class="g-recaptcha" data-sitekey="<?php echo $pubkey;?>" data-size="invisible" data-callback="onSubmit"></div>
					<script type="text/javascript">															
						var pmpro_recaptcha_validated = false;
						var pmpro_recaptcha_onSubmit = function(token) {
							if ( pmpro_recaptcha_validated ) {
								jQuery('#pmpro_form').submit();
								return;
							} else {
								jQuery.ajax({
								url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
								type: 'GET',
								timeout: 30000,
								dataType: 'html',
								data: {
									'action': 'pmpro_validate_recaptcha',
									'g-recaptcha-response': token,
								},
								error: function(xml){
									alert('Error validating ReCAPTCHA.');
								},
								success: function(response){
									if ( response == '1' ) {
										pmpro_recaptcha_validated = true;
										
										//get a new token to be submitted with the form
										grecaptcha.execute();
									} else {
										pmpro_recaptcha_validated = false;
										
										//warn user validation failed
										alert( 'ReCAPTCHA validation failed. Try again.' );
										
										//get a new token to be submitted with the form
										grecaptcha.execute();
									}
								}
								});
							}						
						};
	
						var pmpro_recaptcha_onloadCallback = function() {
							// Render on main submit button.
							grecaptcha.render('pmpro_btn-submit', {
							'sitekey' : '<?php echo $pubkey;?>',
							'callback' : pmpro_recaptcha_onSubmit
							  });
							
							// Update other submit buttons.
							var submit_buttons = jQuery('.pmpro_btn-submit-checkout');
							submit_buttons.each(function() {
								if(jQuery(this).attr('id') != 'pmpro_btn-submit') {
									jQuery(this).click(function(event) {
										event.preventDefault();
										grecaptcha.execute();
									});
								}
							});
						};
					 </script>
					 <script type="text/javascript"
						 src="https://www.google.com/recaptcha/api.js?onload=pmpro_recaptcha_onloadCallback&hl=<?php echo $lang;?>&render=explicit" async defer>
					 </script>
				<?php } else { ?>
					<div class="g-recaptcha" data-callback="pmpro_recaptcha_validatedCallback" data-expired-callback="pmpro_recaptcha_expiredCallback" data-sitekey="<?php echo $pubkey;?>"></div>
					<script type="text/javascript">															
						var pmpro_recaptcha_validated = false;
						var pmpro_recaptcha_error_msg = "<?php esc_attr_e( 'Please check the ReCAPTCHA box to confirm you are not a bot.', 'paid-memberships-pro' ); ?>";
						
						// Validation callback.
						function pmpro_recaptcha_validatedCallback() {
							// ReCAPTCHA worked.
							pmpro_recaptcha_validated = true;
							
							// Re-enable the submit button.
							jQuery('.pmpro_btn-submit-checkout,.pmpro_btn-submit').removeAttr('disabled');

							// Hide processing message.
							jQuery('#pmpro_processing_message').css('visibility', 'hidden');
							
							// Hide error message.
							if ( jQuery('#pmpro_message').text() == pmpro_recaptcha_error_msg ) {
								jQuery( '#pmpro_message' ).hide();
								jQuery( '#pmpro_message_bottom' ).hide();
							}
						};
						
						// Expiration callback.
						function pmpro_recaptcha_expiredCallback() {
							pmpro_recaptcha_validated = false;
						}
						
						// Check validation on submit.
						jQuery(document).ready(function(){
							jQuery('#pmpro_form').submit(function(event){
								if( pmpro_recaptcha_validated == false ) {
									event.preventDefault();
									
									// Re-enable the submit button.
									jQuery('.pmpro_btn-submit-checkout,.pmpro_btn-submit').removeAttr('disabled');

									// Hide processing message.
									jQuery('#pmpro_processing_message').css('visibility', 'hidden');

									// error message
									jQuery( '#pmpro_message' ).text( pmpro_recaptcha_error_msg ).addClass( 'pmpro_error' ).removeClass( 'pmpro_alert' ).removeClass( 'pmpro_success' ).hide().fadeIn();
									jQuery( '#pmpro_message_bottom' ).hide().fadeIn();

									return false;
								} else {
									return true;
								}
							});
						});
					 </script>
					<script type="text/javascript"
						src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang;?>">
					</script>
				<?php }				
			}
		}	
		
		//for templates using the old recaptcha_get_html
		if( ! function_exists( 'recaptcha_get_html' ) ) {
			function recaptcha_get_html( $pubkey, $error = null, $use_ssl = false ) {
				return pmpro_recaptcha_get_html( $pubkey, $error, $use_ssl );
			}
		}
		
		$recaptcha_publickey = pmpro_getOption( 'recaptcha_publickey' );
		$recaptcha_privatekey = pmpro_getOption( 'recaptcha_privatekey' );
	}
}
add_action( 'wp', 'pmpro_init_recaptcha', 1 );

/**
 * AJAX Method to Validate a ReCAPTCHA Response Token
 */
function pmpro_wp_ajax_validate_recaptcha() {
	require_once( PMPRO_DIR . '/includes/lib/recaptchalib.php' );
	
	$recaptcha_privatekey = pmpro_getOption( 'recaptcha_privatekey' );
	
	$reCaptcha = new pmpro_ReCaptcha( $recaptcha_privatekey );
	$resp      = $reCaptcha->verifyResponse( $_SERVER['REMOTE_ADDR'], $_REQUEST['g-recaptcha-response'] );

	if ( $resp->success ) {
	    pmpro_set_session_var( 'pmpro_recaptcha_validated', true );
		echo "1";
	} else {
		echo "0";
	}
	
	exit;	
} 
add_action( 'wp_ajax_nopriv_pmpro_validate_recaptcha', 'pmpro_wp_ajax_validate_recaptcha' );
add_action( 'wp_ajax_pmpro_validate_recaptcha', 'pmpro_wp_ajax_validate_recaptcha' );

function pmpro_after_checkout_reset_recaptcha() {
    pmpro_unset_session_var( 'pmpro_recaptcha_validated' );
}
add_action( 'pmpro_after_checkout', 'pmpro_after_checkout_reset_recaptcha' );