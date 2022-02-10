<?php

class BB_PowerPack_WPML {
    static public function init() {
        add_filter( 'wpml_beaver_builder_modules_to_translate', __CLASS__ . '::translate_fields', 10, 1 );
    }

    static public function translate_fields( $modules ) {
        $config = array(
            'pp-advanced-accordion' => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Accordion'
            ),
            'pp-advanced-menu'      => array(
                'fields'                => array(
                    array(
                        'field'             => 'custom_menu_text',
                        'type'              => __('Advanced Menu - Toggle Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    )
                )
            ),
            'pp-advanced-tabs'      => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Tabs'
            ),
            'pp-business-hours'      => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Business_Hours'
            ),
            'pp-notifications'      => array(
                'fields'                => array(
                    array(
                        'field'             => 'notification_content',
                        'type'              => __('Alert Box - Content', 'bb-powerpack-lite'),
                        'editor_type'       => 'TEXTAREA',
                    ),
                ),
            ),
            'pp-animated-headlines' => array(
                'fields'                => array(
                    array(
                        'field'             => 'before_text',
                        'type'              => __('Animated Headlines - Before Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'highlighted_text',
                        'type'              => __('Animated Headlines - Highlighted Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'rotating_text',
                        'type'              => __('Animated Headlines - Rotating Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'after_text',
                        'type'              => __('Animated Headlines - After Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    )
                )
            ),
            'pp-announcement-bar'   => array(
                'fields'                => array(
                    array(
                        'field'             => 'announcement_content',
                        'type'              => __('Annoucement Bar - Content', 'bb-powerpack-lite'),
                        'editor_type'       => 'TEXTAREA'
                    ),
                    array(
                        'field'             => 'announcement_link_text',
                        'type'              => __('Annoucement Bar - Link Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'announcement_link_url',
                        'type'              => __('Annoucement Bar - Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    )
                )
            ),
            'pp-contact-form'       => array(
                'fields'                => array(
                    array(
                        'field'             => 'custom_title',
                        'type'              => __('Contact Form - Custom Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'custom_description',
                        'type'              => __('Contact Form - Custom Description', 'bb-powerpack-lite'),
                        'editor_type'       => 'TEXTAREA'
                    ),
                    array(
                        'field'             => 'name_label',
                        'type'              => __('Contact Form - Custom Label - Name', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'email_label',
                        'type'              => __('Contact Form - Custom Label - Email', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'phone_label',
                        'type'              => __('Contact Form - Custom Label - Phone', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'subject_label',
                        'type'              => __('Contact Form - Custom Label - Subject', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'message_label',
                        'type'              => __('Contact Form - Custom Label - Message', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'success_message',
                        'type'              => __('Contact Form - Success Message', 'bb-powerpack-lite'),
                        'editor_type'       => 'TEXTAREA'
                    ),
                    array(
                        'field'             => 'success_url',
                        'type'              => __('Contact Form - Success URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'btn_text',
                        'type'              => __('Contact Form - Button Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                )
            ),
            'pp-content-grid'       => array(
                'fields'                => array(
                    array(
                        'field'             => 'more_link_text',
                        'type'              => __('Content Grid - Button Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'all_filter_label',
                        'type'              => __('Content Grid - All Filter Label', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'meta_separator',
                        'type'              => __('Content Grid - Meta Separator', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'no_results_message',
                        'type'              => __('Content Grid - No Results Message', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                )
            ),
            'pp-content-tiles'      => array(
                'fields'                => array(
                    array(
                        'field'             => 'no_results_message',
                        'type'              => __('Content Tiles - No Results Message', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                )
			),
			'pp-contact-form-7'		=> array(
				'fields'				=> array(
					array(
						'field'				=> 'custom_title',
						'type'				=> __('Contact Form 7 Styler - Custom Title', 'bb-powerpack-lite'),
						'editor-type'		=> 'LINE'
					),
					array(
						'field'				=> 'custom_description',
						'type'				=> __('Contact Form 7 Styler - Custom Description', 'bb-powerpack-lite'),
						'editor-type'		=> 'AREA'
					),
				)
			),
            'pp-dual-button'        => array(
                'fields'                => array(
                    array(
                        'field'             => 'button_1_title',
                        'type'              => __('Dual Button 1 - Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'button_link_1',
                        'type'              => __('Dual Button 1 - Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'button_2_title',
                        'type'              => __('Dual Button 2 - Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'button_link_2',
                        'type'              => __('Dual Button 2 - Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                )
            ),
            'pp-fancy-heading'      => array(
                'fields'                => array(
                    array(
                        'field'             => 'heading_title',
                        'type'              => __('Fancy Heading - Heading Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                )
            ),
            'pp-filterable-gallery' => array(
                'fields'                => array(
                    array(
                        'field'             => 'custom_all_text',
                        'type'              => __('Filterable Gallery - Custom All Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                ),
                'integration-class'     => 'WPML_PP_Filterable_Gallery'
            ),
            'pp-flipbox'            => array(
                'fields'                => array(
                    array(
                        'field'             => 'front_title',
                        'type'              => __('FlipBox - Front Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'front_description',
                        'type'              => __('FlipBox - Front Description', 'bb-powerpack-lite'),
                        'editor_type'       => 'VISUAL'
                    ),
                    array(
                        'field'             => 'back_title',
                        'type'              => __('FlipBox - Back Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'back_description',
                        'type'              => __('FlipBox - Back Description', 'bb-powerpack-lite'),
                        'editor_type'       => 'VISUAL'
                    ),
                    array(
                        'field'             => 'link_text',
                        'type'              => __('FlipBox - Button Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'link',
                        'type'              => __('FlipBox - Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                )
			),
			'pp-gravity-form'		=> array(
				'fields'				=> array(
					array(
						'field'				=> 'custom_title',
						'type'				=> __('Gravity Form Styler - Custom Title', 'bb-powerpack-lite'),
						'editor_type'		=> 'LINE'
					),
					array(
						'field'				=> 'custom_description',
						'type'				=> __('Gravity Form Styler - Custom Description', 'bb-powerpack-lite'),
						'editor_type'		=> 'TEXTAREA'
					),
				)
			),
            'pp-highlight-box'      => array(
                'fields'                => array(
                    array(
                        'field'             => 'box_content',
                        'type'              => __('Highlight Box - Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'TEXTAREA'
                    ),
                    array(
                        'field'             => 'box_link',
                        'type'              => __('Highlight Box - Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    )
                )
            ),
            'pp-hover-cards'      => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Hover_Cards'
            ),
            'pp-hover-cards-2'      => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Hover_Cards_2'
            ),
            'pp-iconlist'       => array(
                'fields'            => array(
                    array(
                        'field'         => 'list_items',
                        'type'          => __('Icon List - Item', 'bb-powerpack-lite'),
                        'editor_type'   => 'LINE'
                    )
                ),
                'integration-class'     => 'WPML_PP_Icon_List'
            ),
            'pp-image'              => array(
                'fields'                => array(
                    array(
                        'field'             => 'photo_url',
                        'type'              => __('Image - Photo URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'caption',
                        'type'              => __('Image - Caption', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'link_url',
                        'type'              => __('Image - Link URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                )
            ),
            'pp-image-panels'       => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Image_Panels'
            ),
            'pp-infobox'            => array(
                'fields'                => array(
                    array(
                        'field'             => 'title_prefix',
                        'type'              => __('InfoBox - Prefix', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'title',
                        'type'              => __('InfoBox - Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'description',
                        'type'              => __('InfoBox - Description', 'bb-powerpack-lite'),
                        'editor_type'       => 'VISUAL'
                    ),
                    array(
                        'field'             => 'pp_infobox_read_more_text',
                        'type'              => __('InfoBox - Button Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'link',
                        'type'              => __('InfoBox - Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                )
            ),
            'pp-infolist'           => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Infolist'
            ),
            'pp-logos-grid'      => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Logos_Grid'
            ),
            'pp-modal-box'          => array(
                'fields'                => array(
                    array(
                        'field'             => 'modal_title',
                        'type'              => __('Modal Box - Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'modal_type_video',
                        'type'              => __('Modal Box - Embed Code / URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'modal_type_url',
                        'type'              => __('Modal Box - URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'modal_type_content',
                        'type'              => __('Modal Box - Content', 'bb-powerpack-lite'),
                        'editor_type'       => 'VISUAL'
                    ),
                    array(
                        'field'             => 'modal_type_html',
                        'type'              => __('Modal Box - Raw HTML', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'button_text',
                        'type'              => __('Modal Box - Button Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                )
            ),
            'pp-pullquote'          => array(
                'fields'                => array(
                    array(
                        'field'             => 'pullquote_content',
                        'type'              => __('Pullquote - Quote', 'bb-powerpack-lite'),
                        'editor_type'       => 'TEXTAREA'
                    ),
                    array(
                        'field'             => 'pullquote_title',
                        'type'              => __('Pullquote - Name', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                )
            ),
            'pp-restaurant-menu'    => array(
                'fields'                => array(
                    array(
                        'field'             => 'menu_heading',
                        'type'              => __('Restaurant Menu - Menu Heading', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'currency_symbol',
                        'type'              => __('Restaurant Menu - Currency Symbol', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                )
            ),
            'pp-info-banner'        => array(
                'fields'                => array(
                    array(
                        'field'             => 'banner_title',
                        'type'              => __('Smart Banner - Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'banner_description',
                        'type'              => __('Smart Banner - Description', 'bb-powerpack-lite'),
                        'editor_type'       => 'TEXTAREA'
                    ),
                    array(
                        'field'             => 'button_text',
                        'type'              => __('Smart Banner - Button Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'button_link',
                        'type'              => __('Smart Banner - Button Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                )
            ),
            'pp-smart-button'       => array(
                'fields'                => array(
                    array(
                        'field'             => 'text',
                        'type'              => __('Smart Button - Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'link',
                        'type'              => __('Smart Button - Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    )
                )
            ),
            'pp-heading'            => array(
                'fields'                => array(
                    array(
                        'field'             => 'heading_title',
                        'type'              => __('Smart Heading - Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'heading_title2',
                        'type'              => __('Smart Heading - Secondary Title', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'heading_sub_title',
                        'type'              => __('Smart Heading - Description', 'bb-powerpack-lite'),
                        'editor_type'       => 'VISUAL'
                    ),
                    array(
                        'field'             => 'heading_link',
                        'type'              => __('Smart Heading - Link', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                )
            ),
            'pp-subscribe-form'     => array(
                'fields'                => array(
                    array(
                        'field'             => 'service_account',
                        'type'              => __('Subscribe Form - Account Name', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'api_url',
                        'type'              => __('Subscribe Form - API URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'api_key',
                        'type'              => __('Subscribe Form - API Key', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'box_content',
                        'type'              => __('Subscribe Form - Content', 'bb-powerpack-lite'),
                        'editor_type'       => 'VISUAL'
                    ),
                    array(
                        'field'             => 'input_name_placeholder',
                        'type'              => __('Subscribe Form - Name Field Placeholder Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'input_email_placeholder',
                        'type'              => __('Subscribe Form - Email Field Placeholder Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
					),
					array(
						'field'				=> 'checkbox_field_text',
						'type'				=> __('Subscribe Form - Checkbox Field Text', 'bb-powerpack-lite'),
						'editor_type'		=> 'LINE'
					),
                    array(
                        'field'             => 'btn_text',
                        'type'              => __('Subscribe Form - Button Text', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'success_message',
                        'type'              => __('Subscribe Form - Success Message', 'bb-powerpack-lite'),
                        'editor_type'       => 'VISUAL'
                    ),
                    array(
                        'field'             => 'success_url',
                        'type'              => __('Subscribe Form - Success URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                )
            ),
            'pp-team'               => array(
                'fields'                => array(
                    array(
                        'field'             => 'member_name',
                        'type'              => __('Team - Name', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'member_designation',
                        'type'              => __('Team - Designation', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'member_description',
                        'type'              => __('Team - Description', 'bb-powerpack-lite'),
                        'editor_type'       => 'VISUAL'
                    ),
                    array(
                        'field'             => 'link_url',
                        'type'              => __('Team - LINK URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'email',
                        'type'              => __('Team - Email', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'facebook_url',
                        'type'              => __('Team - Facebook URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'twiiter_url',
                        'type'              => __('Team - Twiiter URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'googleplus_url',
                        'type'              => __('Team - Google Plus URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'pinterest_url',
                        'type'              => __('Team - Pinterest URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'linkedin_url',
                        'type'              => __('Team - Linkedin URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'youtube_url',
                        'type'              => __('Team - Youtube URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'instagram_url',
                        'type'              => __('Team - Instagram URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'vimeo_url',
                        'type'              => __('Team - Vimeo URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'github_url',
                        'type'              => __('Team - Github URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'dribbble_url',
                        'type'              => __('Team - Dribbble URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'tumblr_url',
                        'type'              => __('Team - Tumblr URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'flickr_url',
                        'type'              => __('Team - Flickr URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                    array(
                        'field'             => 'wordpress_url',
                        'type'              => __('Team - WordPress URL', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINK'
                    ),
                )
            ),
            'pp-testimonials'       => array(
                'fields'                => array(
                    array(
                        'field'             => 'heading',
                        'type'              => __('Testimonials - Heading', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                ),
                'integration-class' => 'WPML_PP_Testimonials',
            ),
            'pp-timeline'      => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Timeline'
            ),
            'pp-pricing-table'      => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Pricing_Table'
            ),
            'pp-table'      => array(
                'fields'                => array(),
                'integration-class'     => 'WPML_PP_Table'
            ),
            'pp-restaurant-menu'      => array(
                'fields'                => array(
                    array(
                        'field'             => 'menu_heading',
                        'type'              => __('Restaurant / Services Menu - Heading', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                    array(
                        'field'             => 'currency_symbol',
                        'type'              => __('Restaurant / Services Menu - Currency Symbol', 'bb-powerpack-lite'),
                        'editor_type'       => 'LINE'
                    ),
                ),
                'integration-class'     => 'WPML_PP_Restaurant_Menu'
			),
			'pp-caldera-form'		=> array(
				'fields'				=> array(
					array(
						'field'				=> 'custom_title',
						'type'				=> __('Caledra Form Styler - Custom Title', 'bb-powerpack-lite'),
						'editor-type'		=> 'LINE'
					),
					array(
						'field'				=> 'custom_description',
						'type'				=> __('Caldera Form Styler - Custom Description', 'bb-powerpack-lite'),
						'editor-type'		=> 'AREA'
					),
				)
			),
			'pp-ninja-form'		=> array(
				'fields'				=> array(
					array(
						'field'				=> 'custom_title',
						'type'				=> __('Ninja Form Styler - Custom Title', 'bb-powerpack-lite'),
						'editor-type'		=> 'LINE'
					),
					array(
						'field'				=> 'custom_description',
						'type'				=> __('Ninja Form Styler - Custom Description', 'bb-powerpack-lite'),
						'editor-type'		=> 'AREA'
					),
				)
			),
			'pp-wpforms'		=> array(
				'fields'				=> array(
					array(
						'field'				=> 'custom_title',
						'type'				=> __('WPForms Styler - Custom Title', 'bb-powerpack-lite'),
						'editor-type'		=> 'LINE'
					),
					array(
						'field'				=> 'custom_description',
						'type'				=> __('WPForms Styler - Custom Description', 'bb-powerpack-lite'),
						'editor-type'		=> 'AREA'
					),
				)
			),
        );

        foreach ( $config as $module_name => $module_fields ) {
            $module_fields['conditions'] = array( 'type' => $module_name );
            $modules[$module_name] = $module_fields;
        }

		self::init_classes();

        return $modules;
    }

    static private function init_classes() {
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-accordion.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-business-hours.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-filterable-gallery.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-hover-cards-2.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-hover-cards.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-icon-list.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-image-panels.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-logos-grid.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-tabs.php';
		require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-testimonials.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-timeline.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-pricing-table.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-table.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-restaurant-menu.php';
        require_once BB_POWERPACK_DIR . 'classes/wpml/class-wpml-pp-infolist.php';
	}
}

BB_PowerPack_WPML::init();
