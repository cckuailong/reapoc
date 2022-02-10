<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

 function cmplz_banner_color_schemes(){
	$schemes = array(
		'Wordpress' => array(
			'slider_background_color' => '#21759b',
			'slider_bullet_color' => '#f9f9f9',
			'slider_background_color_inactive' => '#F56E28',
			'accept_all_background_color' => '#21759b',
			'accept_all_text_color' => '#fff',
			'accept_all_border_color' => '#21759b',
			'functional_background_color' => '#fff',
			'functional_text_color' => '#21759b',
			'functional_border_color' => '#ffffff',
			'colorpalette_background_color' => '#fff',
			'colorpalette_text_color' => '#191e23',
			'button_background_color' => '#fff',
			'button_text_color' => '#21759b',
			'border_color' => '#21759b',
			'theme' => 'minimal',
		),

		//keep this one
		'tcf' => array(
			'colorpalette_background' => array(
				'color'     => '#ffffff',
				'border'    => '#333333',
			),
			'colorpalette_text' => array(
				'color'       => '#191e23',
				'hyperlink'   => '#191e23',
			),
			'colorpalette_toggles' => array(
				'background'    => '#33CC66',
				'bullet'        => '#ffffff',
				'inactive'      => '#CC0000',
			),

			'colorpalette_button_accept' => array(
				'background'    => '#333333',
				'border'        => '#333333',
				'text'          => '#ffffff',
			),

			'colorpalette_button_deny' => array(
				'background'    => '#ffffff',
				'border'        => '#ffffff',
				'text'          => '#333333',
			),
			'colorpalette_button_settings' => array(
				'background'    => '#ffffff',
				'border'        => '#333333',
				'text'          => '#333333',
			),
			'theme' => 'minimal',
		),
	);

	return $schemes;
}

function cmplz_get_banner_color_scheme_options(){
 	$schemes = cmplz_banner_color_schemes();
 	$schemes = array_keys($schemes);
 	$options = array();
 	foreach ($schemes as $scheme) {
 		$options[$scheme] = str_replace('-', ' ', ucfirst($scheme));
    }
 	return $options;
}


add_filter('cmplz_fields_load_types', 'cmplz_add_cookiebanner_settings');
function cmplz_add_cookiebanner_settings($fields){

	$fields = $fields + array(

	        /* ----- General ----- */
			'title' => array(
				'step'        => 'general',
				'source'      => 'CMPLZ_COOKIEBANNER',
				'type'        => 'text',
				'label'       => __( "Cookie banner title", 'complianz-gdpr' ),
				'placeholder' => __( 'Descriptive title of the cookiebanner' ),
				'tooltip'        => __( 'For internal use only', 'complianz-gdpr' ),
			),

            'revoke' => array(
                'source'    => 'CMPLZ_COOKIEBANNER',
                'step'      => 'general',
                'type'      => 'text',
                'default'   => __( "Manage consent", 'complianz-gdpr' ),
                'placeholder'        => __( "Manage consent", 'complianz-gdpr' ),
                'label'     => __( "Text on the manage consent tab", 'complianz-gdpr' ),
                'tooltip'      => __( 'The tab will show after the visitor interacted with the banner, and can be used to make the cookie banner reappear.',
                    'complianz-gdpr' ),
            ),

            'hide_revoke' => array(
              'source'  => 'CMPLZ_COOKIEBANNER',
              'step'    => 'general',
              'type'    => 'checkbox',
              'default' => false,
              'label'   => __( "Hide manage consent tab", 'complianz-gdpr' ),
              'tooltip'    => __( 'If you want to hide the manage consent tab, enable this checkbox. The tab will normally appear after making a choice.', 'complianz-gdpr' ),
              'comment' => __( 'If you hide this button, you should at least leave the option to revoke consent on your cookie policy or Do Not Sell My Personal Information page', 'complianz-gdpr' ),
            ),

            'disable_cookiebanner' => array(
                'source'  => 'CMPLZ_COOKIEBANNER',
                'step'    => 'general',
                'type'    => 'checkbox',
                'label'   => __( "Disable cookie banner", 'complianz-gdpr' ),
                'default' => false,
            ),

			'default' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'general',
				'type'               => 'checkbox',
				'label'              => __( "Default cookie banner", 'complianz-gdpr' ),
				'help'               => __( 'When enabled, this is the cookie banner that is used for all visitors. Enabling it will disable this setting on the current default banner. Disabling it will enable randomly a different default banner.',
					'complianz-gdpr' ),

				'default'            => false,
				'callback_condition' => 'cmplz_ab_testing_enabled',
			),

            'hide_preview' => array(
                'source'  => 'CMPLZ_COOKIEBANNER',
                'step'    => 'general',
                'type'    => 'checkbox',
                'label'   => __( "Hide preview", 'complianz-gdpr' ),
                'default' => false,
            ),

            'use_custom_cookie_css' => array(
                'source'  => 'CMPLZ_COOKIEBANNER',
                'step'    => 'general',
                'type'    => 'checkbox',
                'label'   => __( "Use Custom CSS", 'complianz-gdpr' ),
                'default' => false,
                'help'   => __("You can customize the cookie banner with custom CSS for endless possibilities.","complianz-gdpr") . cmplz_read_more( 'https://complianz.io/docs/customization/' ),
                'comment'   => __("The custom CSS editor will appear at the bottom of this page when enabled.","complianz-gdpr"),

            ),

            /* ----- Appearance ----- */

            'position' => array(
                'step'    => 'appearance',
                'source'  => 'CMPLZ_COOKIEBANNER',
                'type'    => 'select',
                'label'   => __( "Position", 'complianz-gdpr' ),
                'options' => array(
                    'top'          => __( "Top", 'complianz-gdpr' ),
                    'center'       => __( "Center", 'complianz-gdpr' ),
                    'bottom-left'  => __( "Left", 'complianz-gdpr' ),
                    'bottom-right' => __( "Right", 'complianz-gdpr' ),
                    'bottom'       => __( "Bottom", 'complianz-gdpr' ),
                ),
                'default' => 'bottom-right',
            ),

            'theme' => array(
                'source'  => 'CMPLZ_COOKIEBANNER',
                'step'    => 'appearance',
                'type'    => 'select',
                'label'   => __( "Template", 'complianz-gdpr' ),
                'options' => array(
                    'block'    => __( "Block", 'complianz-gdpr' ),
                    'classic'  => __( "Classic", 'complianz-gdpr' ),
                    'edgeless' => __( "Edgeless", 'complianz-gdpr' ),
                    'minimal'  => __( "Minimal", 'complianz-gdpr' ),
                ),
                'default' => 'minimal',
            ),

            'banner_width' => array(
				'source'  => 'CMPLZ_COOKIEBANNER',
				'step'    => 'appearance',
				'type'    => 'number',
				'default' => '476',
				'validation_step' => 2,
				'label'   => __( "Min width of banner in pixels", 'complianz-gdpr' ),
			),

            'animation' => array(
                'source'  => 'CMPLZ_COOKIEBANNER',
                'step'    => 'appearance',
                'type'    => 'select',
                'label'   => __( "Animation", 'complianz-gdpr' ),
                'options' => array(
                    'none'    => __( "None", 'complianz-gdpr' ),
                    'fade'  => __( "Fade", 'complianz-gdpr' ),
                    'slide' => __( "Slide", 'complianz-gdpr' ),
                ),
                'default' => 'none',
            ),



			'checkbox_style' => array(
				'source'  => 'CMPLZ_COOKIEBANNER',
				'step'    => 'appearance',
				'type'    => 'select',
				'label'   => __( "Checkbox style", 'complianz-gdpr' ),
				'tooltip'    => __( "This style is for the checkboxes on the cookie banner, as well as on your policy for managing consent.", 'complianz-gdpr' ),
				'options' => array(
					'classic'    => __( "Classic", 'complianz-gdpr' ),
					'square'  => __( "Square", 'complianz-gdpr' ),
					'slider' => __( "Slider", 'complianz-gdpr' ),
				),
				'default' => 'slider',
				'condition' => array(
					'use_categories' => 'NOT no',
				)
			),

            'use_box_shadow' => array(
                'source'  => 'CMPLZ_COOKIEBANNER',
                'step'    => 'appearance',
                'type'    => 'checkbox',
                'label'   => __( "Box shadow", 'complianz-gdpr' ),
            ),

            /* ----- Customization ----- */
            'colorpalette_background' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'colorpicker',
                'master_label'  => __( "Cookie notice", 'complianz-gdpr' ),
                'label'         => __( "Background", 'complianz-gdpr' ),
                'default'       => array(
                    'color'     => '#f9f9f9',
                    'border'    => '#f9f9f9',
                ),
                'fields'        => array(
                    array(
                        'fieldname'     => 'color',
                        'label'         => __( "Background", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'border',
                        'label'         => __( "Border", 'complianz-gdpr' ),
                    ),
                ),
            ),

            'colorpalette_text' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'colorpicker',
                'label'         => __( "Text", 'complianz-gdpr' ),
                'default'      => array(
                    'color'       => '#191e23',
                    'hyperlink'   => '#191e23',
                ),
                'fields'        => array(
                    array(
                        'fieldname'     => 'color',
                        'label'         => __( "Color", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'hyperlink',
                        'label'         => __( "Hyperlink", 'complianz-gdpr' ),
                    ),
                ),
            ),

            'colorpalette_toggles' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'colorpicker',
                'label'         => __( "Toggles", 'complianz-gdpr' ),
                'default'       => array(
                    'background'    => '#21759b',
                    'bullet'        => '#ffffff',
                    'inactive'      => '#F56E28',
                ),
                'fields'        => array(
                    array(
                        'fieldname'     => 'background',
                        'label'         => __( "Background", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'bullet',
                        'label'         => __( "Bullet", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'inactive',
                        'label'         => __( "Inactive", 'complianz-gdpr' ),
                    ),
                ),
                'condition'     => array('checkbox_style' => 'slider'),
            ),

            'colorpalette_border_radius' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'borderradius',
                'default'       => array(
                    'top'       => '0',
                    'right'     => '0',
                    'bottom'    => '0',
                    'left'      => '0',
                    'type'      => 'px',
                ),
                'label'         => __( "Border radius", 'complianz-gdpr' ),
            ),

            'border_width' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'borderwidth',
                'default'       => array(
                    'top'       => '1',
                    'right'     => '1',
                    'bottom'    => '1',
                    'left'      => '1',
                ),
                'label'         => __( "Border width", 'complianz-gdpr' ),
            ),

            'colorpalette_button_accept' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'colorpicker',
                'master_label'  => __( "Buttons", 'complianz-gdpr' ),
                'label'         => __( "Accept", 'complianz-gdpr' ),
                'default'      => array(
                    'background'    => '#21759b',
                    'border'        => '#21759b',
                    'text'          => '#ffffff',
                ),
                'fields'        => array(
                    array(
                        'fieldname'     => 'background',
                        'label'         => __( "Background", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'border',
                        'label'         => __( "Border", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'text',
                        'label'         => __( "Text", 'complianz-gdpr' ),
                    ),
                ),
            ),

            'colorpalette_button_deny' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'colorpicker',
                'label'         => __( "Deny", 'complianz-gdpr' ),
                'default'      => array(
                    'background'    => '#f1f1f1',
                    'border'        => '#f1f1f1',
                    'text'          => '#21759b',
                ),
                'fields'        => array(
                    array(
                        'fieldname'     => 'background',
                        'label'         => __( "Background", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'border',
                        'label'         => __( "Border", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'text',
                        'label'         => __( "Text", 'complianz-gdpr' ),
                    ),
                ),
            ),

            'colorpalette_button_settings' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'colorpicker',
                'label'         => __( "Settings", 'complianz-gdpr' ),
                'default'      => array(
                    'background'    => '#f1f1f1',
                    'border'        => '#21759b',
                    'text'          => '#21759b',
                ),
                'fields'        => array(
                    array(
                        'fieldname'     => 'background',
                        'label'         => __( "Background", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'border',
                        'label'         => __( "Border", 'complianz-gdpr' ),
                    ),
                    array(
                        'fieldname'     => 'text',
                        'label'         => __( "Text", 'complianz-gdpr' ),
                    ),
                ),
            ),

            'buttons_border_radius' => array(
                'source'        => 'CMPLZ_COOKIEBANNER',
                'step'          => 'customization',
                'type'          => 'borderradius',
                'default'       => array(
                    'top'       => '5',
                    'right'     => '5',
                    'bottom'    => '5',
                    'left'      => '5',
                    'type'      => 'px',
                ),
                'label'         => __( "Border radius", 'complianz-gdpr' ),
            ),

            /* ----- Custom CSS ----- */
			'custom_css' => array(
				'source'    => 'CMPLZ_COOKIEBANNER',
				'step'      => 'custom_css',
				'type'      => 'css',
				'help'      => sprintf(__('You can add additional custom CSS here. For tips and CSS lessons, check out our %sdocumentation%s', 'complianz-gdpr'), '<a target="_blank" href="https://complianz.io/?s=css">', '</a>'),
				'label'     => '',
				'default'   => '.cc-message{}'
				               . "\n".' /* styles for the message box */'
				               . "\n"
				               . '.cc-dismiss{}'
				               . "\n".' /* styles for the dismiss button */'
				               . "\n" . '.cc-btn{}'
				               . "\n".' /* styles for buttons */' . "\n"
				               . '.cc-allow{} '
				               . "\n".'/* styles for the accept button */'
				               . "\n"
				               . '.cc-accept-all{} '
				               . "\n".'/* styles for the accept all button */'
				               . "\n"
				               . '.cc-window{} '
				               . "\n".'/* styles for the popup banner */'
				               . "\n"
				               . '.cc-window .cc-category{} '
				               . "\n".'/* styles for categories*/'
				               . "\n"
				               . '.cc-window .cc-check{} '
				               . "\n".'/* styles for the checkboxes with categories */'
				               . "\n"
				               . '.cc-revoke{} '
				               . "\n".'/* styles for the revoke / settings popup */'
				               . "\n"
				               . '.cmplz-slider-checkbox{} '
				               . "\n".'/* styles for the checkboxes */'
				               . "\n"
				               . '.cmplz-soft-cookiewall{} '
				               . "\n".'/* styles for the soft cookie wall */'
                               . "\n"
                               . "\n"
                               . "/* styles for the AMP notice */"
                               . "\n"
                               . '#cmplz-consent-ui, #cmplz-post-consent-ui {} '
				               . "\n".'/* styles for entire banner */'
                               . "\n"
                               . '#cmplz-consent-ui .cmplz-consent-message {} '
				               . "\n".'/* styles for the message area */'
                               . "\n"
                               . '#cmplz-consent-ui button, #cmplz-post-consent-ui button {} '
				               . "\n".'/* styles for the buttons */',
				'condition' => array( 'use_custom_cookie_css' => true ),
			),

			'tcf_intro' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'notice',
				'condition'          => array(
											'hide_field' => 'true' //random condition so it's hidden by default
										),
			),

            'header' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'label'              => __( "Title", 'complianz-gdpr' ),
                'placeholder'        => __( "Manage Cookie Consent", 'complianz-gdpr' ),
                'condition'          => array(
	                'position' => 'center OR bottom-left OR bottom-right',
                ),
            ),

			'soft_cookiewall' => array(
				'source'  => 'CMPLZ_COOKIEBANNER',
				'step'    => 'settings',
				'type'    => 'checkbox',
				'default' => false,
				'label'   => __( "Show as soft cookie wall", 'complianz-gdpr' ),
				'help'    => __( "A privacy-friendly cookie wall.", 'complianz-gdpr' ) . cmplz_read_more( 'https://complianz.io/the-soft-cookie-wall/' ),
				'tooltip' => __( 'After saving, a preview of the soft cookie wall will be shown for 3 seconds', 'complianz-gdpr' ),
				'condition'          => array(
					'type' => 'NOT optout',
				),
			),

			'use_categories' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'select',
				'options'            => array(
					'no' => __('Accept/Deny', 'complianz-gdpr'),
					'legacy' => __('Legacy categories', 'complianz-gdpr'),
					'hidden' => __('Accept all + view preferences', 'complianz-gdpr'),
					'visible' => __('Accept all + categories', 'complianz-gdpr'),
				),
				'label'              => __( "Categories", 'complianz-gdpr' ),
				'tooltip'               => __( 'With categories, you can let users choose which category of cookies they want to accept.', 'complianz-gdpr' ) . ' '
				                           . __( 'Depending on your settings and cookies you use, there can be two or three categories. With Tag Manager you can use more, custom categories.', 'complianz-gdpr' ),
				'help'    => cmplz_cookiebanner_category_conditional_helptext(),
				'default'            => 'hidden',
				'condition'          => array('type' => 'optin'),
				'callback_condition' => 'cmplz_uses_optin',
			),

			'use_categories_optinstats' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'select',
				'options'            => array(
					'no' => __('Accept/Deny', 'complianz-gdpr'),
					'legacy' => __('Legacy categories', 'complianz-gdpr'),
					'hidden' => __('Accept all + view preferences', 'complianz-gdpr'),
					'visible' => __('Accept all + categories', 'complianz-gdpr'),
				),
				'label'              => __( "Categories", 'complianz-gdpr' ),
				'tooltip'               => __( 'With categories, you can let users choose which category of cookies they want to accept.', 'complianz-gdpr' ) . ' '
				                           . __( 'Depending on your settings and cookies you use, there can be two or three categories. With Tag Manager you can use more, custom categories.', 'complianz-gdpr' ),
				'help'    => cmplz_cookiebanner_category_conditional_helptext(),
				'default'            => 'hidden',
				'condition'          => array('type' => 'optinstats'),
				'callback_condition' => 'cmplz_uses_optin',
			),

            'readmore_optin' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Cookie Policy", 'complianz-gdpr' ),
                'placeholder'        => __( "Cookie Policy", 'complianz-gdpr' ),
                'label'              => __( "Read more", 'complianz-gdpr' ),
                'condition'          => array( 'type' => 'NOT optout' ),
                'callback_condition' => 'cmplz_uses_optin',
            ),

            'readmore_impressum' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Impressum", 'complianz-gdpr' ),
                'placeholder'        => __( "Impressum", 'complianz-gdpr' ),
                'label'              => __( "Imprint", 'complianz-gdpr' ),
                'condition'          => array(
                    'type' => 'NOT optout',
                ),
                'callback_condition' => array(
                    'cmplz_uses_optin',
                    'cmplz_impressum_required',
                ),
            ),

			'accept_all' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'text',
				'default'            => __( "Accept all", 'complianz-gdpr' ),
				'label'              => __( "Accept all", 'complianz-gdpr' ),
                'placeholder'        => __( "Accept all", 'complianz-gdpr' ),
				'callback_condition' => 'cmplz_uses_optin',
                'condition'          => array(
                    'type'           => 'NOT optout',
                    'use_categories' => 'hidden OR visible',
                ),
			),

            'accept' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'text',
				'default'            => __( "Accept", 'complianz-gdpr' ),
				'label'              => __( "Accept", 'complianz-gdpr' ),
                'placeholder'        => __( "Accept", 'complianz-gdpr' ),
				'callback_condition' => 'cmplz_uses_optin',
				'condition'          => array(
				    'use_categories' => 'no',
                    'type'           => 'NOT optout',
                ),
			),

            'dismiss' => array(
                'step'               => 'settings',
                'source'             => 'CMPLZ_COOKIEBANNER',
                'type'               => 'text',
                'default'            => __( "Dismiss", 'complianz-gdpr' ),
                'label'              => __( "Dismiss cookies text", 'complianz-gdpr' ),
                'placeholder'        => __( "Dismiss", 'complianz-gdpr' ),

                'help'               => __( 'This button will reject all, but necessary cookies, and dismisses the cookie banner.',
                    'complianz-gdpr' ),
                'condition'          => array(
                    'type' => 'NOT optout',
                    'use_categories' => 'no OR hidden',
                ),
                'callback_condition' => 'cmplz_uses_optin',
            ),

            'category_functional' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Functional", 'complianz-gdpr' ),
                'placeholder'        => __( "Functional", 'complianz-gdpr' ),
                'label'              => __( "Functional", 'complianz-gdpr' ),
                'condition'          => array(
                    'use_categories' => 'NOT no',
                    'type'           => 'NOT optout',
                ),
                'callback_condition' => 'cmplz_uses_optin',
            ),

            'category_prefs' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Preferences", 'complianz-gdpr' ),
                'placeholder'        => __( "Preferences", 'complianz-gdpr' ),
                'label'              => __( "Preferences", 'complianz-gdpr' ),
                'condition'          => array( 'use_categories' => 'NOT no' ),
                'callback_condition' => array(
                    'cmplz_uses_optin',
                    'cmplz_uses_preferences_cookies',
                ),
            ),

            'category_stats' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Statistics", 'complianz-gdpr' ),
                'label'              => __( "Statistics", 'complianz-gdpr' ),
                'placeholder'        => __( "Statistics", 'complianz-gdpr' ),
                'tooltip'               => __( "It depends on your settings if this category is necessary, so it will show conditionally", 'complianz-gdpr' ),

                'condition'          => array(
                    'use_categories' => 'NOT no',
                    'type'           => 'NOT optout',
                ),
                'callback_condition' => array(
                    'cmplz_uses_optin',
                    'cmplz_uses_statistic_cookies',
                ),
            ),

            'category_all' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Marketing", 'complianz-gdpr' ),
                'label'              => __( "Marketing", 'complianz-gdpr' ),
                'placeholder'        => __( "Marketing", 'complianz-gdpr' ),
                'condition'          => array(
                    'use_categories' => 'NOT no',
                    'type'           => 'NOT optout',
                ),
                'callback_condition' => array(
                    'cmplz_uses_optin',
                    'cmplz_uses_marketing_cookies',
                ),
            ),

            'view_preferences'    => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Preferences", 'complianz-gdpr' ),
                'label'              => __( "Settings", 'complianz-gdpr' ),
                'placeholder'        => __( "Preferences", 'complianz-gdpr' ),

                'condition'          => array(
                    'use_categories' => 'hidden',
                    'type'           => 'NOT optout',
                ),
                'callback_condition' => 'cmplz_uses_optin',
            ),

            'save_preferences' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'text',
				'default'            => __( "Save preferences", 'complianz-gdpr' ),
                'placeholder'        => __( "Save preferences", 'complianz-gdpr' ),
				'label'              => __( "Save preferences", 'complianz-gdpr' ),
				'condition'          => array(
                    'use_categories' => 'hidden OR legacy OR visible',
					'type' => 'NOT optout',
				),
				'callback_condition' => 'cmplz_uses_optin',
			),

            'tagmanager_categories' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'label'              => __( "Custom Tag Manager categories", 'complianz-gdpr' ),
                'comment'               => __( 'Enter your custom Google Tag Manager categories, comma separated. Functional and Marketing are already set.',
                        'complianz-gdpr' ) . "<br><br>"
                    . cmplz_tagmanager_conditional_helptext()
                    . cmplz_read_more( 'https://complianz.io/configure-categories-tag-manager' ),

                'placeholder'        => __( 'Second category, Third category', 'complianz-gdpr' ),
                'callback_condition' => array(
                    'compile_statistics' => 'google-tag-manager',
                ),
                'condition'          => array(
                    'use_categories' => 'NOT no',
                    'type'           => 'NOT optout',
                ),
                'default'            => false,
            ),

            'message_optin' => array(
                'step'          => 'settings',
                'source'        => 'CMPLZ_COOKIEBANNER',
                'type'          => 'editor',
                'default'       => __( "We use cookies to optimize our website and our service.", 'complianz-gdpr' ),
                'label'         => __( "Cookie message", 'complianz-gdpr' ),
                'placeholder'        => __( "We use cookies to optimize our website and our service.", 'complianz-gdpr' ),
                'condition'     => array( 'type' => 'NOT optout' ),
            ),


			/*
			 *
			 * US settings
			 *
			 * */

            'accept_informational' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Accept", 'complianz-gdpr' ),
                'placeholder'        => __( "Accept", 'complianz-gdpr' ),
                'label'              => __( "Accept", 'complianz-gdpr' ),
                'callback_condition' => 'cmplz_uses_optout',
                'condition'          => array( 'type' => 'optout' ),
            ),

            'readmore_optout' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => 'Cookie Policy',
                'placeholder'        => __( "Cookie Policy", 'complianz-gdpr' ),
                'label'              => __( "Text on link to the Cookie Policy", 'complianz-gdpr' ),
                'condition'          => array( 'type' => 'optout' ),
            ),

            'readmore_privacy' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'text',
                'default'            => __( "Privacy Statement", 'complianz-gdpr' ),
                'placeholder'        => __( "Privacy Statement", 'complianz-gdpr' ),
                'label'              => __( "Text on link to Privacy Statement",
                    'complianz-gdpr' ),

                'callback_condition' => 'cmplz_uses_optout',
                'condition'          => array( 'type' => 'optout' ),
            ),

            'dismiss_on_scroll' => array(
                'source'             => 'CMPLZ_COOKIEBANNER',
                'step'               => 'settings',
                'type'               => 'checkbox',
                'label'              => __( "Dismiss on scroll", 'complianz-gdpr' ),
                'tooltip'               => __( 'When dismiss on scroll is enabled, the cookie banner will be dismissed as soon as the user scrolls.',
                    'complianz-gdpr' ),
                'default'            => false,
                //setting this to true will set it always to true, as the get_cookie settings will see an empty value
                'callback_condition' => 'cmplz_uses_optout',
                'condition'          => array( 'type' => 'optout' ),
            ),

			'dismiss_on_timeout' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'checkbox',
				'label'              => __( "Dismiss on time out", 'complianz-gdpr' ),
				'tooltip'               => __( 'When dismiss on time out is enabled, the cookie banner will be dismissed after 10 seconds, or the time you choose below.', 'complianz-gdpr' ),
				'default'            => false,
				//setting this to true will set it always to true, as the get_cookie settings will see an empty value
				'callback_condition' => 'cmplz_uses_optout',
                'condition'          => array( 'type' => 'optout' ),
			),

			'dismiss_timeout' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'number',
				'label'              => __( "Timeout in seconds",
					'complianz-gdpr' ),
				'default'            => 10,
				//setting this to true will set it always to true, as the get_cookie settings will see an empty value
				'callback_condition' => 'cmplz_uses_optout',
                'condition'          => array(
                    'type' => 'optout',
                    'dismiss_on_timeout' => true,
                    ),
			),

			/**
			 * The condition NOT CCPA is removed, because it will hide the policy when in combination with Canada, where the text will be needed.
			 */

			'readmore_optout_dnsmpi' => array(
				'source'             => 'CMPLZ_COOKIEBANNER',
				'step'               => 'settings',
				'type'               => 'text',
				'default'            => 'Do Not Sell My Personal Information',
                'placeholder'        => 'Do Not Sell My Personal Information',
				'label'              => __( "Text on link to the Do Not Sell My Personal Information page.", 'complianz-gdpr' ),
				'callback_condition' => 'cmplz_ccpa_applies',
				'help'               => __( 'This text is not shown in the preview, but is used instead of the Cookie Policy text when the region is US, and CCPA applies.', 'complianz-gdpr' ),
                'condition'          => array( 'type' => 'optout' ),
			),

			'message_optout' => array(
				'step'        => 'settings',
				'source'      => 'CMPLZ_COOKIEBANNER',
				'type'        => 'editor',
				'default'     => __( "We use cookies to optimize our website and our service.", 'complianz-gdpr' ),
				'placeholder' => __( "We use cookies to optimize our website and our service.", 'complianz-gdpr' ),
				'label'       => __( "Cookie message", 'complianz-gdpr' ),
				'condition'   => array( 'type' => 'optout' ),
			),
		);


	return $fields;
}
