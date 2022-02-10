<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

$this->fields = $this->fields + array(

        // ----------------- General ------------------ //

		'use_country' => array(
			'step'     => 'general',
			'source'   => 'settings',
			'type'     => 'checkbox',
			'label'    => __( "Enable GEO IP", 'complianz-gdpr' ),
			'comment'  => $this->premium_geo_ip
			              . __( 'If enabled, the cookie warning will not show for countries without a cookie law, and will adjust consent management depending on supported privacy laws',
					'complianz-gdpr' ),
			'table'    => true,
			'disabled' => true,
			'default'  => false,
			//setting this to true will set it always to true, as the get_cookie settings will see an empty value
		),

		'a_b_testing' => array(
			'source'   => 'settings',
			'step'     => 'general',
			'type'     => 'checkbox',
			'label'    => __( "Enable A/B testing", 'complianz-gdpr' ),
			'comment'  => $this->premium_ab_testing
			              . __( 'If enabled, the plugin will track which cookie banner has the best conversion rate.',
					'complianz-gdpr' ),
			'table'    => true,
			'disabled' => true,
			'default'  => false,
			//setting this to true will set it always to true, as the get_cookie settings will see an empty value
		),

		'use_cdb_api' => array(
			'source'   => 'settings',
			'step'     => 'general',
			'type'      => 'radio',
			'required'  => true,
			'default'   => 'yes',
			'options'   => $this->yes_no,
			'label'     => __( "Do you consent to the use of the cookiedatabase.org API?", 'complianz-gdpr' ),
			'help'   => __( "Without the API, you will have to manually describe all found cookies, their purpose, function, service and service types. ",
					'complianz-gdpr' ),
			  'comment' => sprintf( __( "Complianz provides your Cookie Policy with comprehensive cookie descriptions, supplied by %scookiedatabase.org%s. We connect to this open-source database using an external API, which sends the results of the cookiescan (a list of found cookies, used plugins and your domain) to cookiedatabase.org, for the sole purpose of providing you with accurate descriptions and keeping them up-to-date at a weekly schedule. For more information, read the %sPrivacy Statement%s",
					'complianz-gdpr' ),
					'<a target="_blank" href="https://cookiedatabase.org">', '</a>',
					'<a target="_blank" href="https://cookiedatabase.org/privacy-statement">',
					'</a>' ),
		),

		'use_cdb_links' => array(
			'source'   => 'settings',
			'step'     => 'general',
			'type'      => 'radio',
			'required'  => false,
			'default'   => 'yes',
			'options'   => $this->yes_no,
			'condition' => array( 'use_cdb_api' => 'yes' ),
			'label'     => __( "Do you want to hyperlink cookie names so visitors can find more information on Cookiedatabase.org?", 'complianz-gdpr' ),
			'tooltip'   => __("These links will be added with HTML attributes so it won't hurt SEO.", "complianz-gdpr"),
		),

		'high_contrast' => array(
			'source'   => 'settings',
			'step'     => 'general',
			'type'     => 'checkbox',
			'label'    => __( "Enable High Contrast mode", 'complianz-gdpr' ),
			'comment'  => __( 'If enabled, all the Complianz pages within the WordPress admin will be in high contrast.', 'complianz-gdpr' ),
			'table'    => true,
			'disabled' => false,
			'default'  => false,
		),

		'a_b_testing_duration' => array(
			'source'    => 'settings',
			'step'      => 'general',
			'type'      => 'number',
			'label'     => __( "Duration in days of the A/B testing period",
				'complianz-gdpr' ),
			'table'     => true,
			'disabled'  => true,
			'condition' => array( 'a_b_testing' => true ),
			'default'   => 30,
		),

        'use_document_css' => array(
            'step'    => 'general',
            'source'  => 'settings',
            'type'    => 'checkbox',
            'label'   => __( "Use document CSS by Complianz", 'complianz-gdpr' ),
            'table'   => true,
            'default' => true,
            'tooltip'    => __( "Disable to let your theme take over.", 'complianz-gdpr' ),
        ),

        'use_custom_document_css' => array(
            'step'    => 'general',
            'source'  => 'settings',
            'type'    => 'checkbox',
            'label'   => __( "Add custom document CSS", 'complianz-gdpr' ),
            'table'   => true,
            'default' => false,
            'tooltip' => __( "Enable if you want to add custom CSS for the documents", 'complianz-gdpr' ),
        ),

        'notification_from_email' => array(
            'step'               => 'general',
            'source'             => 'settings',
            'type'               => 'email',
            'label'              => __( "Notification sender email address", 'complianz-gdpr' ),
            'default'            => false,
            'tooltip'               => __( "When emails are sent, you can choose the sender email address here. Please note that it should have this website's domain as sender domain, otherwise the server might block the email from being sent.", 'complianz-gdpr' ),
            'table'              => true,
            'help' => __( "Email address used for Do Not Sell My Personal Information email notifications.", 'complianz-gdpr' ),
            'callback_condition' => array(
                'cmplz_dnsmpi_required',
            ),
        ),
		'notification_email_subject' => array(
			'step'               => 'general',
			'source'             => 'settings',
			'type'               => 'text',
			'label'              => __( "Notification email subject",
				'complianz-gdpr' ),
			'default'            => __( 'Your request has been processed',
				'complianz-gdpr' ),
			'table'              => true,
			'tooltip' => __( "Subject used for Do Not Sell My Personal Information email notifications.", 'complianz-gdpr' ),
			'callback_condition' => array(
				'cmplz_dnsmpi_required',
			),
		),

		'notification_email_content' => array(
			'step'               => 'general',
			'source'             => 'settings',
			'type'               => 'editor',
			'label'              => __( "Notification email content",
				'complianz-gdpr' ),
			'default'            => __( 'Hi {name}', 'complianz-gdpr' )
			                        . "<br><br>"
			                        . __( 'Your request has been processed successfully.', 'complianz-gdpr' ) . "<br><br>" . _x( 'Regards,', 'email signature', 'complianz-gdpr' )
			                        . '<br><br>{blogname}',
			'table'              => true,
			'tooltip' => __( "Email content used for Do Not Sell My Personal Information email notifications.", 'complianz-gdpr' ),
			'callback_condition' => array(
				'cmplz_dnsmpi_required',
			),
		),

        // ---------------- Cookie Blocker ----------------- //

		'disable_cookie_block' => array(
			'source'  => 'settings',
			'type'    => 'checkbox',
			'step'    => 'cookie-blocker',
			'table'   => true,
			'label'   => __( "Enable safe mode", 'complianz-gdpr' ),
			'default' => false,
			'comment'    => sprintf( __( 'When safe mode is enabled, all integrations will be disabled temporarily, please read %sthese instructions%s to debug the issue or ask support if needed.',
				'complianz-gdpr' ),
				'<a  target="_blank" href="https://complianz.io/debugging-issues/">', '</a>' )

		),

		'dont_use_placeholders' => array(
			'source'    => 'settings',
			'type'      => 'checkbox',
			'step'      => 'cookie-blocker',
			'table'     => true,
			'label'     => __( "Disable placeholder insertion", 'complianz-gdpr' ),
			'default'   => false,
			'tooltip'      => __( "If you experience styling issues with videos or iFrames you can disable the placeholder insertion, which in some themes can conflict with theme styling.", 'complianz-gdpr' ),
			'condition' => array(
				'disable_cookie_block' => false,
			),
		),

		'placeholder_style' => array(
			'source'    => 'settings',
			'type'      => 'select',
			'options'   => array(
				'minimal' => __("Default",'complianz-gdpr'),
				'light' => __("Light",'complianz-gdpr'),
				'color' => __("Full Color",'complianz-gdpr'),
				'dark' => __("Dark Mode",'complianz-gdpr'),
				'custom' => __("Custom",'complianz-gdpr'),
			),
			'disabled' => array(
				'light',
				'color',
				'dark',
				'custom',
			),
			'step'      => 'cookie-blocker',
			'table'     => true,
			'label'     => __( "Placeholder style", 'complianz-gdpr' ),
			'default'   => 'minimal',
			'tooltip'      => __( "You can choose your favorite placeholder style here.", 'complianz-gdpr' ),
			'comment'      => __( "You can change your placeholders manually or use Premium to do it for you.", 'complianz-gdpr' ).
			                  cmplz_read_more('https://complianz.io/changing-the-default-social-placeholders/'),
			'condition' => array(
				'disable_cookie_block' => false,
			),
		),

		'google-maps-format' => array(
			'source'    => 'settings',
			'type'      => 'select',
			'options'   => array(
				'1280x920' => "1280 x 920",
				'1280x500' => "1280 x 500",
				'500x500' => "500 x 500",
				'custom' => __("Custom",'complianz-gdpr'),
			),
			'step'      => 'cookie-blocker',
			'table'     => true,
			'label'     => __( "Google Maps placeholder format", 'complianz-gdpr' ),
			'default'   => '1280x920',
			'tooltip'      => __( "Select the optimal Google Maps placeholder ratio for your site.", 'complianz-gdpr' ),
			'condition' => array(
				'disable_cookie_block' => false,
			),
			'callback_condition' => array(
				'thirdparty_services_on_site' => 'google-maps',
			),
			'comment'=> __( "If you select custom, you need to add your custom image to your site.", 'complianz-gdpr' ).cmplz_read_more('https://complianz.io/changing-the-google-maps-placeholder/'),
		),

		'blocked_content_text' => array(
			'step'      => 'cookie-blocker',
			'source'       => 'settings',
			'type'         => 'text',
			'translatable' => true,
			'table'        => true,
			'label'        => __( "Blocked content text", 'complianz-gdpr' ),
			'default'      => _x( 'Click to accept marketing cookies and enable this content', 'Accept cookies on blocked content', 'complianz-gdpr' ),
			'tooltip'         => __( 'The blocked content text appears when for example a Youtube video is embedded.', 'complianz-gdpr' ),
			'condition'    => array(
				'disable_cookie_block' => false,
			)
		),

		'enable_cookieblocker_ajax' => array(
			'step'      => 'cookie-blocker',
			'source'   => 'settings',
			'type'     => 'checkbox',
			'label'    => __( "Enable cookie blocker for ajax loaded content", 'complianz-gdpr'),
			'table'    => true,
			'disabled' => false,
			'default'  => false,
			'tooltip'  => __( "When content is loaded with ajax, for example with a load more button or lightbox, this option could help blocking the service correctly", 'complianz-gdpr' ),
		),

        'set_cookies_on_root' => array(
            'source'  => 'settings',
            'step'    => 'cookie-blocker',
            'type'    => 'checkbox',
            'default' => false,
            'label'   => '',
            'help'    => '',
            'table'   => true,
            'condition' => array( 'hide_field' => true ),
        ),

        'cookie_domain' => array(
            'source'  => 'settings',
            'step'    => 'cookie-blocker',
            'type'    => 'text',
            'default' => false,
            'label'   => '',
            'help'    => '',
            'table'   => true,
            'condition' => array( 'hide_field' => true ),
        ),

        'cookie_expiry' => array(
            'source'  => 'settings',
            'step'    => 'cookie-blocker',
            'type'    => 'number',
            'default' => 365,
            'label'   => __( "Cookie banner expiration in days",
                'complianz-gdpr' ),
            'table'   => true,
        ),

		'disable_automatic_cookiescan' => array(
			'source'  => 'settings',
			'step'    => 'cookie-blocker',
			'type'    => 'checkbox',
			'default' => false,
			'label'   => __("Disable the automatic cookie scan.","complianz-gdpr"),
			'tooltip' => __( "You can disable the monthly automatic cookie scan here, and do only manual cookie scans.","complianz-gdpr"),
			'table'   => true,
		),

        // -------------- Data -------------- //

		'export_settings' => array(
		    'step'     => 'data',
			'source'   => 'settings',
			'disabled' => false,
			'type'     => 'button',
			'action'   => add_query_arg( array( 'page'=>'cmplz-settings','action' => 'cmplz_export_settings'), admin_url( 'admin.php') ),
			'post_get' => 'get',
			'label'    => __( "Export", 'complianz-gdpr' ),
			'table'    => true,
			'tooltip'  => __( 'You can use this to export your settings to another site',
				'complianz-gdpr' ),
		),

		'import_settings' => array(
            'step'     => 'data',
			'source'   => 'settings',
			'disabled' => true,
			'type'     => 'upload',
			'action'   => 'cmplz_import_settings',
			'label'    => __( "Import settings", 'complianz-gdpr' ),
			'table'    => true,
			'comment'  => sprintf( __( 'If you want to import your settings, please check out the %spremium version%s',
				'complianz-gdpr' ),
				'<a target="_blank" href="https://complianz.io">', "</a>" ),
		),

        'reset_settings' => array(
            'step'     => 'data',
            'warn'     => __( 'Are you sure? This will remove all Complianz data.',
                'complianz-gdpr' ),
            'source'   => 'settings',
            'type'     => 'button',
            'action'   => 'cmplz_reset_settings',
            'post_get' => 'post',
            'label'    => __( "Reset", 'complianz-gdpr' ),
            'red'      => true,
            'table'    => true,
            'tooltip'     => __( 'This will reset all settings to defaults. All data in the Complianz plugin will be deleted',
                'complianz-gdpr' ),
        ),

		'clear_data_on_uninstall' => array(
            'step'     => 'data',
			'source'  => 'settings',
			'type'    => 'checkbox',
			'label'   => __( "Clear all data from Complianz on uninstall",
				'complianz-gdpr' ),
			'default' => false,
			'tooltip'    => __( 'Enabling this option will delete all your settings, and the Complianz tables when you deactivate and remove Complianz.',
				'complianz-gdpr' ),
			'table'   => true,
		),

        'restart_tour' => array(
            'step'     => 'data',
            'source'   => 'settings',
            'type'     => 'button',
            'action'   => 'cmplz_restart_tour',
            'post_get' => 'post',
            'label'    => __( "Restart plugin tour", 'complianz-gdpr' ),
            'table'    => true,
        ),

        'disable_notifications' => array(
            'step'     => 'data',
            'source'   => 'settings',
            'type'     => 'checkbox',
            'label'    => __( "Disable notifications", 'complianz-gdpr' ),
            'tooltip'  => __( 'Disable all plus ones and warnings on your dashboard.',
                'complianz-gdpr' ),
            'table'    => true,
            'disabled' => false,
            'default'  => false,
            //setting this to true will set it always to true
        ),

        // ------------- Document Styling ------------- //

        'custom_document_css' => array(
            'step'      => 'document-styling',
            'source'    => 'settings',
            'type'      => 'css',
            'label'     => __( "Custom document CSS", 'complianz-gdpr' ),
            'default'   => '#cmplz-document h2 {} /* titles in complianz documents */'
                . "\n" . '#cmplz-document h2.annex{} /* titles in annexes */'
                . "\n" . '#cmplz-document .subtitle.annex{} /* subtitles in annexes */'
                . "\n" . '#cmplz-document, #cmplz-document p, #cmplz-document span, #cmplz-document li {} /* text */'
                . "\n" . '#cmplz-cookies-overview .cmplz-service-header {} /* service header in cookie policy */'
                . "\n" . '#cmplz-cookies-overview .cmplz-service-desc {} /* service description */'
                . "\n" . '#cmplz-document.impressum, #cmplz-document.cookie-statement, #cmplz-document.privacy-statement {} /* styles for impressum */',
            'help'      => sprintf(__('You can add additional custom CSS here. For tips and CSS lessons, check out our %sdocumentation%s', 'complianz-gdpr'), '<a target="_blank" href="https://complianz.io/?s=css">', '</a>'),
            'table'     => true,
            'condition' => array( 'use_custom_document_css' => true ),
        ),
	);
