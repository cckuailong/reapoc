<?php
	cmplz_notice( __( 'Below you will find the plugins currently detected and integrated with Complianz. Most plugins work by default, but you can also add a plugin to the script center or add it to the integration list.',
			'complianz-gdpr' )
	              . cmplz_read_more( 'https://complianz.io/developers-guide-for-third-party-integrations' ).' '.sprintf( __( "Enabled %s will be blocked on the front-end of your website until the user has given consent (opt-in), or after the user has revoked consent (opt-out). When possible a placeholder is activated. You can also disable or configure the placeholder to your liking.",
			'complianz-gdpr' ), __( "plugins", "complianz-gdpr" ) )
	              . cmplz_read_more( "https://complianz.io/blocking-recaptcha-manually/" ) );

	$fields = COMPLIANZ::$config->fields( 'integrations' );
	if ( count( $fields ) == 0 ) {
		cmplz_settings_overlay( __( 'No active plugins detected in the integrations list.', 'complianz-gdpr' ) );
	}
	?>
	<input type="hidden" name="cmplz_save_integrations_type_plugins" value="1">
	<?php
	COMPLIANZ::$field->get_fields( 'integrations' );
