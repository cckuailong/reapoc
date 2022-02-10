<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
$this->steps = array(
	'wizard' =>
		array(
			STEP_COMPANY => array(
				"id"    => "company",
				"title" => __( "General", 'complianz-gdpr' ),
				'sections' => array(
					1 => array(
				    'title' => __('Visitors', 'complianz-gdpr'),
				    'intro' => '<p>'. _x('The Complianz Wizard will guide you through the necessary steps to configure your website for privacy legislation around the world. We designed the wizard to be comprehensible, without making concessions in legal compliance.','intro first step', 'complianz-gdpr') .'</p>' .
				               _x('There are a few things to assist you during configuration:','intro first step', 'complianz-gdpr') .'<ul>'.
				               '<li>' . _x('Hover over the question mark behind certain questions for more information.', 'intro first step', 'complianz-gdpr').'</li>' .
		                   '<li>' . _x('Important notices and relevant articles are shown in the right column.', 'intro first step', 'complianz-gdpr').'</li>' .
		                   '<li>' . sprintf(_x('Our %sinstructions manual%s contains more detailed background information about every section and question in the wizard.','intro first step', 'complianz-gdpr'),'<a target="_blank" href="https://complianz.io/manual">', '</a>') .'</li>' .
		                   '<li>' . sprintf(_x('You can always %slog a support ticket%s if you need further assistance.','intro first step', 'complianz-gdpr'),'<a target="_blank" href="https://wordpress.org/support/plugin/complianz-gdpr/">', '</a>') .'</li></ul>',

			    ),
					2 => array(
						'id'    => 'general',
						'title' => __( 'Documents', 'complianz-gdpr' ),
						'intro' => '<p>'._x('Here you can select which legal documents you want to generate with Complianz. You can also use existing legal documents.', 'intro company info', 'complianz-gdpr').'</p>',
					),
					3 => array(
						'id' => 'impressum_info',
						'title' => __( 'Website information',
							'complianz-gdpr' ),
						'intro' => '<p>'._x( 'We need some information to be able to generate your documents.',
							'intro company info', 'complianz-gdpr' ).'</p>',
					),
					4 => array(
						'id' => 'impressum_info',
						'title' => __('Impressum', 'complianz-gdpr'),
						'region' => array('eu'),
					),
					6 => array(
						'title' => __( 'Purpose', 'complianz-gdpr' ),
					),
					8 => array(
						'region' => array( 'us' ),
						'id'     => 'details_per_purpose_us',
						'title'  => __( 'Details per purpose',
							'complianz-gdpr' ),
					),
					11 => array(
						'title' => __('Security & Consent', 'complianz-gdpr'),
					),
				),
			),

			STEP_COOKIES => array(
				"title"    => __( "Cookies", 'complianz-gdpr' ),
				"id"       => "cookies",
				'sections' => array(
					1 => array(
						'title' => __( 'Cookie scan', 'complianz-gdpr' ),
						'intro' =>
                            '<p>'.__( 'Complianz will scan several pages of your website for first-party cookies and known third-party scripts. The scan will be recurring monthly to keep you up-to-date!', 'complianz-gdpr' ) . '&nbsp;' .
                                  sprintf( __( 'For more information, %sread our 5 tips%s about the cookie scan.', 'complianz-gdpr'), '<a href="https://complianz.io/cookie-scan-results/" target="_blank">','</a>').'</p>',
					),
					2 => array(
						'title' => __( 'Statistics', 'complianz-gdpr' ),
						'intro' => '<p>'._x( 'Below you can choose to implement your statistics tooling with Complianz. We will add the needed snippets and control consent at the same time.',
							'intro statistics', 'complianz-gdpr' ) .cmplz_read_more("https://complianz.io/statistics-implementation") .'</p>'
					),
					3 => array(
						'title' => __( 'Statistics - configuration', 'complianz-gdpr' ),
							'intro' => '<p>'._x( 'If you choose Complianz to handle your statistics implementation, please delete the current implementation.',
								'intro statistics configuration', 'complianz-gdpr' ) .cmplz_read_more("https://complianz.io/statistics-implementation#configuration") .'</p>'
					),
					4 => array(
						'title' => __( 'Integrations', 'complianz-gdpr' ),
					),

					5 => array(
						'title' => __( 'Cookie descriptions', 'complianz-gdpr' ),
						'intro' => '<p>'
						           .__( 'Complianz provides your Cookie Policy with comprehensive cookie descriptions, supplied by cookiedatabase.org.','complianz-gdpr')
						           ."&nbsp;"
						           . __('We connect to this open-source database using an external API, which sends the results of the cookiescan (a list of found cookies, used plugins and your domain) to cookiedatabase.org, for the sole purpose of providing you with accurate descriptions and keeping them up-to-date at a weekly schedule.','complianz-gdpr')
					                .cmplz_read_more("https://complianz.io/our-cookiedatabase-a-new-initiative/")
						           .'</p>',

					),
					6 => array(
						'title' => __( 'Service descriptions', 'complianz-gdpr' ),
						'intro' => '<p>'._x( 'Below services use cookies on your website to add functionality. You can use cookiedatabase.org to synchronize information or edit the service if needed. Unknown services will be moderated and added by cookiedatabase.org as soon as possible.',
							'intro used cookies', 'complianz-gdpr' ).'</p>'
					),


				),
			),
			STEP_MENU    => array(
				"id"    => "menu",
				"title" => __( "Documents", 'complianz-gdpr' ),
				'intro' =>
					'<h1>' . _x( "Get ready to finish your configuration.",
						'intro menu', 'complianz-gdpr' ) . '</h1>' .
					'<p>'
					. _x( "Generate your documents, then you can add them to your menu directly or do it manually after the wizard is finished.",
						'intro menu', 'complianz-gdpr' ) . '</p>',
				'sections' => array(
					1 => array(
						'title' => __( 'Create documents', 'complianz-gdpr' ),
					),
					2 => array(
						'title' => __( 'Link to menu', 'complianz-gdpr' ),
						'intro' => __( 'It\'s possible to use region redirect when GEO IP is enabled, and you have multiple policies and statements.','complianz-gdpr' ).cmplz_read_more('https://complianz.io/how-to-redirect-your-policies-based-on-region/'),
					),
				),

			),
			STEP_FINISH  => array(
				"title" => __( "Finish", 'complianz-gdpr' ),
			),
		),
);
