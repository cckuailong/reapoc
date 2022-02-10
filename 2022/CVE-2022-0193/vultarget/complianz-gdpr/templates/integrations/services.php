<?php
	$thirdparty_active = cmplz_get_value( 'uses_thirdparty_services' ) === 'yes';
	$socialmedia_active = cmplz_get_value( 'uses_social_media' ) === 'yes' ;
	$uses_ad_cookies = cmplz_get_value( 'uses_ad_cookies' ) === 'yes';

	if ( ! $thirdparty_active && ! $socialmedia_active && !$uses_ad_cookies ) {
		$not_used = __( 'Third-party services and social media', 'complianz-gdpr' );
		$link     = '<a href="' . add_query_arg( array(
				'page'    => 'cmplz-wizard',
				'step'    => STEP_COOKIES,
				'section' => 4 ), admin_url( 'admin.php' ) ) . '">';
		cmplz_settings_overlay( sprintf( __( '%s are marked as not being used on your website in the %swizard%s.', 'complianz-gdpr' ), $not_used, $link, '</a>' ) );
	}

	cmplz_notice( sprintf( __( "Enabled %s will be blocked on the front-end of your website until the user has given consent (opt-in), or after the user has revoked consent (opt-out). When possible a placeholder is activated. You can also disable or configure the placeholder to your liking.", 'complianz-gdpr' ),
				__( "services", "complianz-gdpr" ) ) . cmplz_read_more( "https://complianz.io/blocking-recaptcha-manually/" ) );

	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		if ( defined( 'cmplz_free' ) && cmplz_free ) {
			cmplz_notice( sprintf( __( "reCaptcha is connected and will be blocked before consent. To change your settings, please visit %sIntegrations%s in the wizard. ", 'complianz-gdpr' ),
					'<a href="' . admin_url( 'admin.php?page=cmplz-wizard&step=2&section=4' ) . '">', '</a>' ) );
		} else {
			cmplz_notice( sprintf( __( "reCaptcha is connected and will be blocked before consent. To change your settings, please visit %sIntegrations%s in the wizard. ", 'complianz-gdpr' ),
					'<a href="' . admin_url( 'admin.php?page=cmplz-wizard&step=2&section=4' ) . '">', '</a>' ) );
		}
	}

	if ( $thirdparty_active ) {
		$thirdparty_services = COMPLIANZ::$config->thirdparty_services;
		unset( $thirdparty_services['google-fonts'] );

		if (cmplz_get_value('block_recaptcha_service') !== 'yes'){
			unset( $thirdparty_services['google-recaptcha'] );
		}

		$active_services = cmplz_get_value( 'thirdparty_services_on_site' );
		$i=0;
		foreach ( $thirdparty_services as $service => $label ) {
			$active = ( isset( $active_services[ $service ] ) && $active_services[ $service ] == 1 ) ? true : false;
			$args   = array(
				'first'     => false,
				"fieldname" => $service,
				"type"      => 'checkbox',
				"required"  => false,
				'default'   => '',
				'label'     => $label,
				'table'     => true,
				'disabled'  => false,
				'hidden'    => false,
				'cols'    => false,
			);

			COMPLIANZ::$field->checkbox( $args, $active );
			$i++;
		}
	}

	if ( $socialmedia_active ) {
		$socialmedia = COMPLIANZ::$config->thirdparty_socialmedia;
		$active_socialmedia = cmplz_get_value( 'socialmedia_on_site' );
		foreach ( $socialmedia as $service => $label ) {
			$active = ( isset( $active_socialmedia[ $service ] ) && $active_socialmedia[ $service ] == 1 ) ? true : false;
			$args = array(
				'first'     => false,
				"fieldname" => $service,
				"type"      => 'checkbox',
				"required"  => false,
				'default'   => '',
				'label'     => $label,
				'table'     => true,
				'disabled'  => false,
				'hidden'    => false,
				'cols'    => false,
			);

			COMPLIANZ::$field->checkbox( $args, $active );
		}
	}

	if ($uses_ad_cookies) {
		$args = array(
			'first'     => false,
			"fieldname" => 'advertising',
			"type"      => 'checkbox',
			"required"  => false,
			'default'   => '',
			'label'     => 'Google Ads/DoubleClick',
			'table'     => true,
			'disabled'  => false,
			'hidden'    => false,
			'cols'      => false,
		);

		COMPLIANZ::$field->checkbox( $args, $uses_ad_cookies );
	}
	?>
	<input type="hidden" name="cmplz_save_integrations_type_services" value="1">
