<?php
/**
 * Builder Customizer Tab
 *
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder\Tabs;

if(!defined('ABSPATH'))	exit;


class CFF_Builder_Customizer_Tab{

	/**
	 * Get Tabs Data
	 *
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @return array
	*/
	public static function get_customizer_tabs(){
		return [
			'customize' => [
				'id' 		=> 'customize',
				'heading' 	=> __( 'Customize', 'custom-facebook-feed' ),
				'sections'	=> CFF_Customize_Tab::get_sections()
			],
			'settings' => [
				'id' 		=> 'settings',
				'heading' 	=> __( 'Settings', 'custom-facebook-feed' ),
				'sections'	=> CFF_Settings_Tab::get_sections()
			]
		];
	}


	/**
	 * Text Size Options
	 *
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @return array
	*/
	public static function get_text_size_options(){
		return [
			'inherit' => __( 'Inherit', 'custom-facebook-feed' ),
			'10'		  => '10px',
			'11'		  => '11px',
			'12'		  => '12px',
			'13'		  => '13px',
			'14'		  => '14px',
			'15'		  => '15px',
			'16'		  => '16px',
			'18'		  => '18px',
			'20'		  => '20px',
			'24'		  => '24px',
			'28'		  => '28px',
			'32'		  => '32px',
			'36'		  => '36px',
			'42'		  => '42px',
			'48'		  => '48px',
			'54'		  => '54px',
			'60'		  => '60px',
		];
	}


	/**
	 * header Icons Options
	 *
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @return array
	*/
	public static function get_header_icons_options(){
		return [
			'facebook-square' 	=> 'Facebook 1',
			'facebook' 			=> 'Facebook 2',
			'calendar' 			=> 'Events 1',
			'calendar-o' 		=> 'Events 2',
			'picture-o' 		=> 'Photos',
			'users' 			=> 'People',
			'thumbs-o-up' 		=> 'Thumbs Up 1',
			'thumbs-up' 		=> 'Thumbs Up 2',
			'comment-o' 		=> 'Speech Bubble 1',
			'comment' 			=> 'Speech Bubble 2',
			'ticket' 			=> 'Ticket',
			'list-alt' 			=> 'News List',
			'file' 				=> 'File 1',
			'file-o' 			=> 'File 2',
			'file-text' 		=> 'File 3',
			'file-text-o' 		=> 'File 4',
			'youtube-play ' 		=> 'Video',
			'youtube-play' 		=> 'YouTube',
			'vimeo-square' 		=> 'Vimeo',
		];
	}

	/**
	 * Date Format Options
	 *
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @return array
	*/
	public static function get_date_format_options(){
		$original = strtotime('2016-07-25T17:30:00+0000');
		return [
			'1'			=> __('2 days ago','custom-facebook-feed'),
			'2'			=> date('F jS, g:i a', $original),
			'3'			=> date('F jS', $original),
			'4'			=> date('D F jS', $original),
			'5'			=> date('l F jS', $original),
			'6'			=> date('D M jS, Y', $original),
			'7'			=> date('l F jS, Y', $original),
			'8'			=> date('l F jS, Y - g:i a', $original),
			'9'			=> date("l M jS, 'y", $original),
			'10'		=> date('m.d.y', $original),
			'18'		=> date('m.d.y - G:i', $original),
			'11'		=> date('m/d/y', $original),
			'12'		=> date('d.m.y', $original),
			'19'		=> date('d.m.y - G:i', $original),
			'13'		=> date('d/m/y', $original),
			'14'		=> date('d-m-Y, G:i', $original),
			'15'		=> date('jS F Y, G:i', $original),
			'16'		=> date('d M Y, G:i', $original),
			'17'		=> date('l jS F Y, G:i', $original),
			'18'		=> date('Y-m-d', $original),
			'custom'	=> __('Custom','custom-facebook-feed')
		];
	}
}