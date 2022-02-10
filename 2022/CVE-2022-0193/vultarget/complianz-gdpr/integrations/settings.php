<?php
add_filter( 'cmplz_fields_load_types', 'cmplz_filter_integrations_field_types', 10, 1 );

function cmplz_filter_integrations_field_types( $fields ) {
	$fields = $fields + array(
			'thirdparty_scripts' => array(
				'source'                  => 'custom-scripts',
				'type'                    => 'textarea',
				'optional'                => true,
				'default'                 => '',
				'revoke_consent_onchange' => true,
				'placeholder'             => 'domain.com, script-url.js, /plugin-folder/content/',
				'label'                   => __( "(part of) URL's or unique string from the inline scripts of third-party scripts & plugins that should be blocked before consent.", 'complianz-gdpr' ),
				'help'                    => __( "We strongly advise you to read our instructions manual for the script center. Make sure you're not missing out on all of the possibilities!", 'complianz-gdpr' ).cmplz_read_more("https://complianz.io/the-script-center"),
				'table'                   => true,
			),

			'thirdparty_iframes' => array(
				'source'                  => 'custom-scripts',
				'type'                    => 'textarea',
				'optional'                => true,
				'default'                 => '',
				'placeholder'             => 'domain.com, domain.com',
				'revoke_consent_onchange' => true,
				'label'                   => __( "URL's from iFrames sources that should be blocked before consent.", 'complianz-gdpr' ),
				'table'                   => true,

			),

			'statistics_script' => array(
				'source'                  => 'custom-scripts',
				'type'                    => 'javascript',
				'default'                 => '',
				'revoke_consent_onchange' => true,
				'label'                   => __( "Statistics script", 'complianz-gdpr' ),
				'callback_condition'      => array(
					'compile_statistics' => 'NOT google-analytics,NOT matomo,NOT google-tag-manager,NOT no',
				),
				'table'                   => true,
			),

			'cookie_scripts' => array(
				'source'                  => 'custom-scripts',
				'type'                    => 'javascript',
				'optional'                => true,
				'default'                 => '',
				'revoke_consent_onchange' => true,
				'label'                   => __( "Scripts to add services, for example, Facebook Pixel, Hotjar, etcetera.", 'complianz-gdpr' ),
				'comment'   							=> __("As mentioned in the instructions manual. Remove &lt;script&gt; tags and check for errors.", "complianz-gdpr"),
				'table'                   => true,

			),

			'cookie_scripts_async' => array(
				'source'                  => 'custom-scripts',
				'type'                    => 'javascript',
				'optional'                => true,
				'default'                 => '',
				'revoke_consent_onchange' => true,
				'label'                   => __( "Async scripts to execute on consent", 'complianz-gdpr' ),
				'comment'   							=> __("As mentioned in the instructions manual. Remove &lt;script&gt; tags and check for errors.", "complianz-gdpr"),
				'table'                   => true,
			),

		);

	return $fields;
}
