<?php
/**
 * Styling Tab
 * Contains different controls for the individual Elements
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Tabs;

if(!defined('ABSPATH'))	exit;

class CFF_Styling_Tab{
	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_author(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'authorsize',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'style'				=> ['.cff-post-item-author-name' => 'font-size:{{value}}px!important;'],
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'authorcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-author-name' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			]
		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_text(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'textsize',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Size', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-text' => 'font-size:{{value}}px!important;'],
				'stacked'			=> 'true',
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'textcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'style'				=> ['.cff-post-item-text,[data-preview-colorscheme="light"] .cff-post-item-text a,[data-preview-colorscheme="dark"] .cff-post-item-text a' => 'color:{{value}};'],
				'heading' 			=> __( 'Color', 'custom-facebook-feed' ),
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'number',
				'id' 				=> 'textlength',
				'stacked'			=> 'true',
				'fieldSuffix' 		=> __( 'characters', 'custom-facebook-feed' ),
				'placeholder' 		=> '200',
				'layout' 			=> 'half',
				'description' 		=> __( 'Max Length', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 10,
				'bottom' 			=> 10,
			],
			[
				'type' 				=> 'switcher',
				'id' 				=> 'textlink',
				'label' 			=> __( 'Link text to Facebook post', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'labelStrong'		=> 'true',
				'options'			=> [
					'enabled'	=> 'on',
					'disabled'	=> ''
				]
			],
		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_date(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 15,
			],
			[
				'type' 				=> 'heading',
				'stacked'			=> 'true',
				'heading' 			=> __( 'Format', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'dateformat',
				'strongHeading'		=> 'false',
				'stacked'			=> 'true',
				'options'			=> CFF_Builder_Customizer_Tab::get_date_format_options()
			],
			[
				'type' 				=> 'text',
				'id' 				=> 'datecustom',
				'condition'			=> ['dateformat' => ['custom']],
				'conditionHide'		=> true,
				'stacked'			=> 'true',
				'placeholder'		=> 'Eg. F j, Y',
			],
			[
				'type' 				=> 'heading',
				'stacked'			=> 'true',
				'condition'			=> ['dateformat' => ['custom']],
				'conditionHide'		=> true,
				'heading' 			=> sprintf( __( '%sLearn more about custom formats %s', 'custom-facebook-feed' ), '<a class="sb-customizer-ctrl-link" href="https://smashballoon.com/doc/date-formatting-reference/" target="_blank" rel="noopener">', '</a>' ),
			],
			[
				'type' 				=> 'checkbox',
				'id' 				=> 'beforedateenabled',
				'label' 			=> __( 'Add text before date', 'custom-facebook-feed' ),
				'reverse'			=> 'true',
				'stacked'			=> 'true',
				'options'			=> [
					'enabled'	=> 'on',
					'disabled'	=> 'off'
				]
			],
			[
				'type' 				=> 'text',
				'id' 				=> 'beforedate',
				'condition'			=> ['beforedateenabled' => ['on']],
				'conditionHide'		=> true,
				'stacked'			=> 'true',
			],
			[
				'type' 				=> 'checkbox',
				'id' 				=> 'afterdateenabled',
				'label' 			=> __( 'Add text after date', 'custom-facebook-feed' ),
				'reverse'			=> 'true',
				'stacked'			=> 'true',
				'options'			=> [
					'enabled'	=> 'on',
					'disabled'	=> 'off'
				]
			],
			[
				'type' 				=> 'text',
				'id' 				=> 'afterdate',
				'condition'			=> ['afterdateenabled' => ['on']],
				'conditionHide'		=> true,
				'stacked'			=> 'true',
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 25,
				'bottom' 			=> 10,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'datesize',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'style'				=> ['.cff-post-item-date' => 'font-size:{{value}}px!important;'],
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'datecolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-date' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			]

		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_media(){

		return [
			[
				'type' 				=> 'checkbox',
				'id' 				=> 'oneimage',
				'label' 			=> __( 'Use only one image per post', 'custom-facebook-feed' ),
				'reverse'			=> 'true',
				'options'			=> [
					'enabled'	=> 'on',
					'disabled'	=> ''
				]
			],
		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_social(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 		=> 'toggleset',
				'id' 		=> 'iconstyle',
				'heading' 	=> __( 'Icon Theme', 'custom-facebook-feed' ),
				'options'	=> [
					[
						'value' => 'auto',
						'icon' => 'cog',
						'label' => __( 'Auto', 'custom-facebook-feed' )
					],
					[
						'value' => 'light',
						'icon' => 'sun',
						'label' => __( 'Light', 'custom-facebook-feed' )
					],
					[
						'value' => 'dark',
						'icon' => 'moon',
						'label' => __( 'Dark', 'custom-facebook-feed' )
					]
				]
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 10,
				'bottom' 			=> 0,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Color', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'socialtextcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-meta-txt' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'sociallinkcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Link', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-meta-link' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'socialbgcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Background', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-meta-bg' => 'background:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 15,
				'bottom' 			=> 15,
			],
			[
				'type' 				=> 'switcher',
				'id' 				=> 'expandcomments',
				'label' 			=> __( 'Expand Comments box by default', 'custom-facebook-feed' ),
				'ajaxAction'		=> 'feedPreviewRender',
				'stacked'			=> 'true',
				'labelStrong'		=> 'true',
				'options'			=> [
					'enabled'	=> 'on',
					'disabled'	=> 'off'
				]
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 15,
				'bottom' 			=> 15,
			],
			[
				'type' 				=> 'switcher',
				'id' 				=> 'hidecommentimages',
				'label' 			=> __( 'Hide Comment Avatars', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'labelStrong'		=> 'true',
				'options'			=> [
					'enabled'	=> 'on',
					'disabled'	=> ''
				]
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 15,
				'bottom' 			=> 15,
			],
			[
				'type' 				=> 'switcher',
				'id' 				=> 'lightboxcomments',
				'label' 			=> __( 'Show comments in Lightbox', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'labelStrong'		=> 'true',
				'options'			=> [
					'enabled'	=> 'on',
					'disabled'	=> ''
				]
			],

		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_eventtitle(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 20,
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'eventtitlesize',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'style'				=> ['.cff-post-event-title' => 'font-size:{{value}}px!important;'],
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'eventtitlecolor',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-event-title' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			]
		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_eventdetails(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 20,
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'eventdetailssize',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'style'				=> ['.cff-post-event-location' => 'font-size:{{value}}px!important;'],
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'eventdetailscolor',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-event-location' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			]
		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_link(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Text', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'linksize',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'style'				=> ['.cff-post-item-action-txt' => 'font-size:{{value}}px!important;'],
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'linkcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-action-txt' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 15,
				'bottom' 			=> 15,
			],
			[
				'type' 				=> 'switcher',
				'id' 				=> 'showfacebooklink',
				'layout'			=> 'half',
				'reverse'			=> 'true',
				'heading' 			=> __( 'View on Facebook link', 'custom-facebook-feed' ),
				'description' 		=> __( 'Toggle “View on Facebook” link below each post', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'labelStrong'		=> 'true',
				'switcherTop'		=> 'true',
				'options'			=> [
					'enabled'	=> 'true',
					'disabled'	=> 'false'
				]
			],
			[
				'type' 				=> 'text',
				'id' 				=> 'facebooklinktext',
				'condition'			=> ['showfacebooklink' => ['true']],
				'strongHeading'		=> 'false',
				'stacked'			=> 'true',
				'child'				=> 'true',
				'description' 			=> __( 'Customize Text', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 15,
				'bottom' 			=> 15,
			],
			[
				'type' 				=> 'switcher',
				'id' 				=> 'showsharelink',
				'layout'			=> 'half',
				'reverse'			=> 'true',
				'heading' 			=> __( 'Share link', 'custom-facebook-feed' ),
				'description' 		=> __( 'Toggle “Share” link below each post', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'labelStrong'		=> 'true',
				'switcherTop'		=> 'true',
				'options'			=> [
					'enabled'	=> 'true',
					'disabled'	=> 'false'
				]
			],
			[
				'type' 				=> 'text',
				'id' 				=> 'sharelinktext',
				'condition'			=> ['showsharelink' => ['true']],
				'strongHeading'		=> 'false',
				'stacked'			=> 'true',
				'child'				=> 'true',
				'description' 			=> __( 'Customize Text', 'custom-facebook-feed' ),
			],
		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_desc(){

		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 20,
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'descsize',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'desccolor',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Color', 'custom-facebook-feed' ),
				'stacked'			=> 'true'
			]
		];
	}


	/**
	 * Get Customize Tab Individual Elements Nested Section
	 * @since 4.0
	 * @return array
	*/
	static function post_styling_sharedlinks(){
		return [
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Box Style', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'linkbgcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'condition'			=> ['disablelinkbox' => ['off']],
				'heading' 			=> __( 'Background Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-link-ctn[data-linkbox="off"]' => 'background:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'linkbordercolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'condition'			=> ['disablelinkbox' => ['off']],
				'heading' 			=> __( 'Border Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-link-ctn[data-linkbox="off"]' => 'border-color:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'checkbox',
				'id' 				=> 'disablelinkbox',
				'label' 			=> __( 'Remove Background/Border', 'custom-facebook-feed' ),
				'reverse'			=> 'true',
				'options'			=> [
					'enabled'	=> 'on',
					'disabled'	=> 'off'
				]
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 5,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Link Title', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'linktitlesize',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'style'				=> ['.cff-post-item-link-a' => 'font-size:{{value}}px!important;'],
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'linktitlecolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-link-a' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Link URL', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'linkurlsize',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'style'				=> ['.cff-post-item-link-small' => 'font-size:{{value}}px!important;'],
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'linkurlcolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-link-small' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'separator',
				'top' 				=> 20,
				'bottom' 			=> 5,
			],
			[
				'type' 				=> 'heading',
				'heading' 			=> __( 'Link Description', 'custom-facebook-feed' ),
			],
			[
				'type' 				=> 'select',
				'id' 				=> 'linkdescsize',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Size', 'custom-facebook-feed' ),
				'stacked'			=> 'true',
				'style'				=> ['.cff-post-item-link-description' => 'font-size:{{value}}px!important;'],
				'options'			=> CFF_Builder_Customizer_Tab::get_text_size_options()
			],
			[
				'type' 				=> 'colorpicker',
				'id' 				=> 'linkdesccolor',
				'layout' 			=> 'half',
				'strongHeading'		=> 'false',
				'heading' 			=> __( 'Text Color', 'custom-facebook-feed' ),
				'style'				=> ['.cff-post-item-link-description' => 'color:{{value}}!important;'],
				'stacked'			=> 'true'
			],
			[
				'type' 				=> 'number',
				'id' 				=> 'desclength',
				'stacked'			=> 'true',
				'fieldSuffix' 		=> __( 'characters', 'custom-facebook-feed' ),
				'placeholder' 		=> '400',
				'layout' 			=> 'half',
				'description' 		=> __( 'Maximum Text Length', 'custom-facebook-feed' ),
			],

		];
	}


}