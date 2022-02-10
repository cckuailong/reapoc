<?php
namespace PowerpackElementsLite\Classes;

class PP_Elements_WPML {
    public function __construct()
    {
		add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'translate_fields' ) );
		$this->type = 'widgetType';
	}
	
	public function translate_fields ( $widgets ) {
		$widgets['pp-advanced-accordion']   = [
			'conditions'        => [ $this->type => 'pp-advanced-accordion' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Advanced_Accordion',
		];
		$widgets[ 'pp-business-hours' ]       = [
			'conditions' => [ $this->type => 'pp-business-hours' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Business_Hours'
		];
		$widgets['pp-buttons']              = [
			'conditions'        => [ $this->type => 'pp-buttons' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Buttons',
		];
		$widgets[ 'pp-caldera-forms' ]        = [
			'conditions' => [ $this->type => 'pp-caldera-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Caldera Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Caldera Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA'
				],
			],
		];
		$widgets[ 'pp-contact-form-7' ]       = [
			'conditions' => [ $this->type => 'pp-contact-form-7' ],
			'fields'     => [
				[
					'field'       => 'form_title_text',
					'type'        => __( 'Contact Form 7 - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'form_description_text',
					'type'        => __( 'Contact Form 7 - Description', 'powerpack' ),
					'editor_type' => 'AREA'
				],
			],
		];
		$widgets['pp-content-ticker']       = [
			'conditions'        => [ $this->type => 'pp-content-ticker' ],
			'fields'            => [
				[
					'field'       => 'heading',
					'type'        => __( 'Content Ticker - Heading Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_Content_Ticker',
		];
		$widgets[ 'pp-counter' ]              = [
			'conditions' => [ $this->type => 'pp-counter' ],
			'fields'     => [
				[
					'field'       => 'starting_number',
					'type'        => __( 'Counter - Starting Number', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'ending_number',
					'type'        => __( 'Counter - Ending Number', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'number_prefix',
					'type'        => __( 'Counter - Number Prefix', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'number_suffix',
					'type'        => __( 'Counter - Number Suffix', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'counter_title',
					'type'        => __( 'Counter - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'counter_subtitle',
					'type'        => __( 'Counter - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE'
				],
			],
		];
		$widgets[ 'pp-divider' ]              = [
			'conditions' => [ $this->type => 'pp-divider' ],
			'fields'     => [
				[
					'field'       => 'divider_text',
					'type'        => __( 'Divider - Divider Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
			],
		];
		$widgets[ 'pp-dual-heading' ]         = [
			'conditions' => [ $this->type => 'pp-dual-heading' ],
			'fields'     => [
				[
					'field'       => 'first_text',
					'type'        => __( 'Dual Heading - First Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'second_text',
					'type'        => __( 'Dual Heading - Second Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Dual Heading - Link', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
		];
		$widgets['pp-fancy-heading']        = [
			'conditions' => [ $this->type => 'pp-fancy-heading' ],
			'fields'     => [
				[
					'field'       => 'heading_text',
					'type'        => __( 'Fancy Heading - Heading Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Fancy Heading - Link', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
		];
		$widgets['pp-flipbox']              = [
			'conditions' => [ $this->type => 'pp-flipbox' ],
			'fields'     => [
				[
					'field'       => 'title_front',
					'type'        => __( 'Flip Box - Front Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_front',
					'type'        => __( 'Flip Box - Front Description', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'title_back',
					'type'        => __( 'Flip Box - Back Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_back',
					'type'        => __( 'Flip Box - Back Description', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Flip Box - Link', 'powerpack' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'flipbox_button_text',
					'type'        => __( 'Flip Box - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-fluent-forms']         = [
			'conditions' => [ $this->type => 'pp-fluent-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Fluent Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Fluent Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-formidable-forms']     = [
			'conditions' => [ $this->type => 'pp-formidable-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Formidable Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Formidable Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets[ 'pp-gravity-forms' ]        = [
			'conditions' => [ $this->type => 'pp-gravity-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Gravity Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Gravity Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA'
				],
			],
		];
		$widgets[ 'pp-image-hotspots' ]       = [
			'conditions' => [ $this->type => 'pp-image-hotspots' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Image_Hotspots'
		];
		$widgets[ 'pp-icon-list' ]            = [
			'conditions' => [ $this->type => 'pp-icon-list' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Icon_List'
		];
		$widgets['pp-image-accordion']      = [
			'conditions'        => [ $this->type => 'pp-image-accordion' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Image_Accordion',
		];
		$widgets[ 'pp-image-comparison' ]     = [
			'conditions' => [ $this->type => 'pp-image-comparison' ],
			'fields'     => [
				[
					'field'       => 'before_label',
					'type'        => __( 'Image Comparision - Before Label', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'after_label',
					'type'        => __( 'Image Comparision - After Label', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				
			],
		];
		$widgets[ 'pp-info-box' ]             = [
			'conditions' => [ $this->type => 'pp-info-box' ],
			'fields'     => [
				[
					'field'       => 'icon_text',
					'type'        => __( 'Info Box - Icon Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'heading',
					'type'        => __( 'Info Box - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'sub_heading',
					'type'        => __( 'Info Box - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'description',
					'type'        => __( 'Info Box - Description', 'powerpack' ),
					'editor_type' => 'AREA'
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Info Box - Link', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Info Box - Button Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				
			],
		];
		$widgets[ 'pp-info-box-carousel' ]    = [
			'conditions' => [ $this->type => 'pp-info-box-carousel' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Info_Box_Carousel'
		];
		$widgets[ 'pp-info-list' ]            = [
			'conditions' => [ $this->type => 'pp-info-list' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Info_List'
		];
		$widgets[ 'pp-info-table' ]           = [
			'conditions' => [ $this->type => 'pp-info-table' ],
			'fields'     => [
				[
					'field'       => 'icon_text',
					'type'        => __( 'Info Table - Icon Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'heading',
					'type'        => __( 'Info Table - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'sub_heading',
					'type'        => __( 'Info Table - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'description',
					'type'        => __( 'Info Table - Description', 'powerpack' ),
					'editor_type' => 'AREA'
				],
				[
					'field'       => 'sale_badge_text',
					'type'        => __( 'Info Table - Sale Badge Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Info Table - Link', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Info Table - Button Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				
			],
		];
		$widgets[ 'pp-instafeed' ]            = [
			'conditions' => [ $this->type => 'pp-instafeed' ],
			'fields'     => [
				[
					'field'       => 'insta_link_title',
					'type'        => __( 'Instafeed - Link Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				'insta_profile_url' => [
					'field'       => 'url',
					'type'        => __( 'Instafeed - Instagram Profile URL', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'load_more_button_text',
					'type'        => __( 'Instafeed - Load More Button Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
			],
		];
		$widgets[ 'pa-link-effects' ]         = [
			'conditions' => [ $this->type => 'pa-link-effects' ],
			'fields'     => [
				[
					'field'       => 'text',
					'type'        => __( 'Link Effects - Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'secondary_text',
					'type'        => __( 'Link Effects - Secondary Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Link Effects - Link', 'powerpack' ),
					'editor_type' => 'LINE'
				],
			],
		];
		$widgets[ 'pp-logo-carousel' ]        = [
			'conditions' => [ $this->type => 'pp-logo-carousel' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Logo_Carousel'
		];
		$widgets[ 'pp-logo-grid' ]            = [
			'conditions' => [ $this->type => 'pp-logo-grid' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Logo_Grid'
		];
		$widgets[ 'pp-ninja-forms' ]          = [
			'conditions' => [ $this->type => 'pp-ninja-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Ninja Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Ninja Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA'
				],
			],
		];
		$widgets[ 'pp-price-menu' ]           = [
			'conditions' => [ $this->type => 'pp-price-menu' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Price_Menu'
		];
		$widgets[ 'pp-pricing-table' ]        = [
			'conditions' => [ $this->type => 'pp-pricing-table' ],
			'fields'     => [
				[
					'field'       => 'table_title',
					'type'        => __( 'Pricing Table - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'table_subtitle',
					'type'        => __( 'Pricing Table - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'table_price',
					'type'        => __( 'Pricing Table - Price', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'table_original_price',
					'type'        => __( 'Pricing Table - Origibal Price', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'table_duration',
					'type'        => __( 'Pricing Table - Duration', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'ribbon_title',
					'type'        => __( 'Pricing Table - Ribbon Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'table_button_text',
					'type'        => __( 'Pricing Table - Button Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Pricing Table - Link', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'table_additional_info',
					'type'        => __( 'Pricing Table - Additional Info', 'powerpack' ),
					'editor_type' => 'AREA'
				],
			],
			'integration-class' => 'WPML_PP_Pricing_Table'
		];
		$widgets[ 'pp-promo-box' ]            = [
			'conditions' => [ $this->type => 'pp-promo-box' ],
			'fields'     => [
				[
					'field'       => 'heading',
					'type'        => __( 'Promo Box - Heading', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'sub_heading',
					'type'        => __( 'Promo Box - Sub Heading', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'content',
					'type'        => __( 'Promo Box - Description', 'powerpack' ),
					'editor_type' => 'AREA'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Promo Box - Button Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Promo Box - link', 'powerpack' ),
					'editor_type' => 'LINE'
				],
			],
		];
		$widgets['pp-scroll-image']         = [
			'conditions' => [ $this->type => 'pp-scroll-image' ],
			'fields'     => [
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Scroll Image - URL', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
		];
		$widgets[ 'pp-team-member' ]          = [
			'conditions' => [ $this->type => 'pp-team-member' ],
			'fields'     => [
				[
					'field'       => 'team_member_name',
					'type'        => __( 'Team Member - Name', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'team_member_position',
					'type'        => __( 'Team Member - Position', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'team_member_description',
					'type'        => __( 'Team Member - Description', 'powerpack' ),
					'editor_type' => 'VISUAL'
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Team Member - URL', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
			'integration-class' => 'WPML_PP_Team_Member'
		];
		$widgets[ 'pp-team-member-carousel' ] = [
			'conditions' => [ $this->type => 'pp-team-member-carousel' ],
			'fields'     => [],
			'integration-class' => 'WPML_PP_Team_Member_Carousel'
		];
		$widgets['pp-twitter-buttons']      = [
			'conditions' => [ $this->type => 'pp-twitter-buttons' ],
			'fields'     => [
				[
					'field'       => 'profile',
					'type'        => __( 'Twitter Button - Profile URL or Username', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'recipient_id',
					'type'        => __( 'Twitter Button - Recipient Id', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'default_text',
					'type'        => __( 'Twitter Button - Default Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'hashtag_url',
					'type'        => __( 'Twitter Button - Hashtag URL or #hashtag', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'via',
					'type'        => __( 'Twitter Button - Via (twitter handler)', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'share_text',
					'type'        => __( 'Twitter Button - Custom Share Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'share_url',
					'type'        => __( 'Twitter Button - Custom Share URL', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
		];
		$widgets['pp-twitter-grid']         = [
			'conditions' => [ $this->type => 'pp-twitter-grid' ],
			'fields'     => [
				[
					'field'       => 'url',
					'type'        => __( 'Twitter Grid - Collection URL', 'powerpack' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'tweet_limit',
					'type'        => __( 'Twitter Grid - Tweet Limit', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-twitter-timeline']     = [
			'conditions' => [ $this->type => 'pp-twitter-timeline' ],
			'fields'     => [
				[
					'field'       => 'username',
					'type'        => __( 'Twitter Timeline - Username', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'tweet_limit',
					'type'        => __( 'Twitter Timeline - Tweet Limit', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-twitter-tweet']        = [
			'conditions' => [ $this->type => 'pp-twitter-tweet' ],
			'fields'     => [
				[
					'field'       => 'tweet_url',
					'type'        => __( 'Twitter Tweet - Tweet URL', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
		];
		$widgets[ 'pp-wpforms' ]              = [
			'conditions' => [ $this->type => 'pp-wpforms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'WPForms - Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'WPForms - Description', 'powerpack' ),
					'editor_type' => 'AREA'
				],
			],
		];

		$this->init_classes();
		
		return $widgets;
	}
	
	private function init_classes() {
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-advanced-accordion.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-business-hours.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-buttons.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-content-ticker.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-image-hotspots.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-icon-list.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-image-accordion.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-info-box-carousel.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-info-list.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-logo-carousel.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-logo-grid.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-price-menu.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-pricing-table.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-team-member.php';
		require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/wpml/class-wpml-pp-team-member-carousel.php';
	}
}

$pp_elements_wpml = new PP_Elements_WPML();
