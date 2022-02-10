<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// set option for plugin update process
if ( !get_option( 'niteoCS_version' ) ) {
	update_option( 'niteoCS_version', CMP_VERSION );
}

$pre_update_version = get_option('niteoCS_version');


if ( version_compare($pre_update_version, CMP_VERSION ) < 0 ) {

	$premium_themes = $this->cmp_premium_themes_installed();
	// delete transients for theme updates, to ensure the updates for latest cmp versions runs again
	foreach ( $premium_themes as $theme_slug ) {
		delete_transient( $theme_slug.'_updatecheck' );
	}

	// Lower than 2.0
	if ( version_compare( $pre_update_version, '2.0' ) < 0 ) {
		if ( get_option('niteoCS_subscribers_list') ) {
			$subscribe_list = get_option('niteoCS_subscribers_list');
	
			if ( is_array( $subscribe_list ) && count( $subscribe_list ) > 0 && !array_key_exists ('id', $subscribe_list[0]) ) {
				$i = 1;
				foreach( $subscribe_list as &$sub ){
					$sub['id'] = $i;
					$sub = array('id' => $sub['id']) + $sub;
					// check if ip address is set
					if (!array_key_exists('ip_address', $sub)) {
						$sub['ip_address'] = 'nodata';
					}
					$i++;
				}
				update_option('niteoCS_subscribers_list', $subscribe_list);
			}
		}
	
	}
	// Lower than 3.0
	if ( version_compare( $pre_update_version, '3.0' ) < 0 ) {

		$current_theme 	= get_option('niteoCS_theme', 'countdown');

		if ( get_option('niteoCS_analytics') && get_option('niteoCS_analytics') != '' ) {
			update_option('niteoCS_analytics_status', 'google');
		}

		// migrate overlay color and opacity settings after update 2.8
		$overlay_color 		= get_option('niteoCS_overlay_color['.$current_theme.']');
		$overlay_opacity 	= get_option('niteoCS_overlay_opacity['.$current_theme.']');

		if ( $overlay_color ) {
			update_option( 'niteoCS_overlay['.$current_theme.']', 'color' );
			update_option( 'niteoCS_overlay['.$current_theme.'][color]', $overlay_color );
			delete_option('niteoCS_overlay_color['.$current_theme.']');
		}

		if ( $overlay_opacity ) {
			update_option( 'niteoCS_overlay['.$current_theme.'][opacity]', $overlay_opacity );
			delete_option('niteoCS_overlay_opacity['.$current_theme.']');
		}

		// 2.9.3 version - migrate logo per theme settings to only one settings for all themes
		$logo_type 			= get_option('niteoCS_logo_type['.$current_theme.']');
		$niteoCS_logo_id 	= get_option('niteoCS_logo_id['.$current_theme.']');
		$niteoCS_text_logo 	= get_option('niteoCS_text_logo['.$current_theme.']');

		if ( $logo_type ) {
			update_option( 'niteoCS_logo_type', $logo_type );
			delete_option('niteoCS_logo_type['.$current_theme.']');
		}

		if ( $niteoCS_logo_id ) {
			update_option( 'niteoCS_logo_id', $niteoCS_logo_id );
			delete_option('niteoCS_logo_id['.$current_theme.']');
		}

		if ( $niteoCS_text_logo ) {
			update_option( 'niteoCS_text_logo', $niteoCS_text_logo );
			delete_option('niteoCS_text_logo['.$current_theme.']');
		}

		// 2.9.5 update - migrate graphic background to indepenedent theme settings
		$settings = array( 'niteoCS_banner['.$current_theme.']', 'niteoCS_banner_id['.$current_theme.']', 'niteoCS_unsplash_feed['.$current_theme.']', 'niteoCS_unsplash_0['.$current_theme.']', 'niteoCS_unsplash_2['.$current_theme.']', 'niteoCS_unsplash_3['.$current_theme.']', 'niteoCS_unsplash_1['.$current_theme.']', 'niteoCS_banner_video['.$current_theme.']', 'niteoCS_youtube_url['.$current_theme.']', 'niteoCS_vimeo_url['.$current_theme.']', 'niteoCS_video_file_url['.$current_theme.']', 'niteoCS_video_thumb['.$current_theme.']', 'niteoCS_banner_pattern['.$current_theme.']', 'niteoCS_banner_pattern_custom['.$current_theme.']', 'niteoCS_banner_color['.$current_theme.']', 'niteoCS_gradient['.$current_theme.']', 'niteoCS_banner_gradient_one['.$current_theme.']', 'niteoCS_banner_gradient_two['.$current_theme.']', 'niteoCS_effect_blur['.$current_theme.']', 'niteoCS_overlay['.$current_theme.']', 'niteoCS_overlay['.$current_theme.'][color]', 'niteoCS_overlay['.$current_theme.'][gradient]', 'niteoCS_overlay['.$current_theme.'][gradient_one]', 'niteoCS_overlay['.$current_theme.'][gradient_two]', 'niteoCS_overlay['.$current_theme.'][opacity]', 'niteoCS_banner['.$current_theme.']', 'niteoCS_slider_count['.$current_theme.']', 'niteoCS_slider_effect['.$current_theme.']', 'niteoCS_slider_auto['.$current_theme.']', 'niteoCS_special_effect['.$current_theme.']', 'niteoCS_special_effect['.$current_theme.'][constellation][color]' );

		foreach ( $settings as $name ) {
			$value = get_option( $name );
			if ( $value !== false ) {
				$new_settings = str_replace('['.$current_theme.']', '', $name);
				update_option( $new_settings, $value );
				if ( $name != 'niteoCS_banner['.$current_theme.']' ) {
					delete_option( $name );
				}
			}	
		}
	}


	// update CMP activation status from false to 0 after update 3.1.2
	if ( version_compare( $pre_update_version, '3.1.2' ) < 0 ) {
		if ( get_option('niteoCS_status') == false ) {
			update_option('niteoCS_status', '0');
		}
	}

	// update translation strings
	if ( version_compare( $pre_update_version, '3.8.8' ) < 0 ) {

		if ( get_option('niteoCS_translation') ) {
			$current = json_decode( get_option('niteoCS_translation'), TRUE );

			$translation = array(
				0 => array('id' => 0, 'name' => 'Counter Seconds Label', 'string' => __('Seconds', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[0]['translation']) ? $current[0]['translation'] : __('Seconds', 'cmp-coming-soon-maintenance') ),
				1 => array('id' => 1, 'name' => 'Counter Minutes Label', 'string' => __('Minutes', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[1]['translation']) ? $current[1]['translation'] :  __('Minutes', 'cmp-coming-soon-maintenance') ),
				2 => array('id' => 2, 'name' => 'Counter Hours Label', 'string' => __('Hours', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[2]['translation']) ? $current[2]['translation'] :   __('Hours', 'cmp-coming-soon-maintenance') ),
				3 => array('id' => 3, 'name' => 'Counter Days Label', 'string' => __('Days', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[3]['translation']) ? $current[3]['translation'] :   __('Days', 'cmp-coming-soon-maintenance') ),
				4 => array('id' => 4, 'name' => 'Subscribe Form Placeholder', 'string' => __('Insert your email address.', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[4]['translation']) ? $current[4]['translation'] :   __('Insert your email address.', 'cmp-coming-soon-maintenance') ),
				5 => array('id' => 5, 'name' => 'Subscribe Response Duplicate', 'string' => __('Oops! This email address is already on our list.', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[5]['translation']) ? $current[5]['translation'] :   __('Oops! This email address is already on our list.', 'cmp-coming-soon-maintenance') ),
				6 => array('id' => 6, 'name' => 'Subscribe Response Not Valid', 'string' => __('Oops! We need a valid email address. Please try again.', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[6]['translation']) ? $current[6]['translation'] :   __('Oops! We need a valid email address. Please try again.', 'cmp-coming-soon-maintenance') ),
				7 => array('id' => 7, 'name' => 'Subscribe Response Thanks', 'string' => __('Thank you! Your sign up request was successful.', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[7]['translation']) ? $current[7]['translation'] :   __('Thank you! Your sign up request was successful.', 'cmp-coming-soon-maintenance') ),
				8 => array('id' => 8, 'name' => 'Subscribe Submit Button Label', 'string' => __('Submit', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[8]['translation']) ? $current[8]['translation'] :   __('Submit', 'cmp-coming-soon-maintenance') ),
				9 => array('id' => 9, 'name' => 'CMP Eclipse Theme: Scroll Text', 'string' => __('Scroll', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[8]['translation']) ? $current[9]['translation'] :   __('Scroll', 'cmp-coming-soon-maintenance') ),
				10 => array('id' => 10, 'name' => 'Subscribe Form First Name Placeholder', 'string' => __('First Name', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[10]['translation']) ? $current[10]['translation'] :   __('First Name', 'cmp-coming-soon-maintenance') ),
				11 => array('id' => 11, 'name' => 'Subscribe Form Last Name Placeholder', 'string' => __('Last Name', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[11]['translation']) ? $current[11]['translation'] :   __('Last Name', 'cmp-coming-soon-maintenance') ),
				12 => array('id' => 12, 'name' => 'Subscribe', 'string' => __('Subscribe', 'cmp-coming-soon-maintenance'), 'translation' => isset($current[12]['translation']) ? $current[12]['translation'] :   __('Subscribe', 'cmp-coming-soon-maintenance') ),
				13 => array('id' => 13, 'name' => 'Subscribe GDPR Checkbox', 'string' => __('You must agree with our Terms and Conditions.', 'cmp-coming-soon-maintenance'), 'translation' =>  __('You must agree with our Terms and Conditions.', 'cmp-coming-soon-maintenance') ),
				14 => array('id' => 14, 'name' => 'Subscribe Missing Email', 'string' => __('Oops! Email is empty.', 'cmp-coming-soon-maintenance'), 'translation' =>  __('Oops! Email is empty.', 'cmp-coming-soon-maintenance') ),
			);

			update_option('niteoCS_translation', wp_json_encode( $translation ));
		}
	
	}

	// update social icons to social settings 
	if ( get_option('niteoCS_socialmedia') ) {
		$niteoCS_socialmedia = stripslashes( get_option('niteoCS_socialmedia') );
		$socialmedia = json_decode( $niteoCS_socialmedia, true );
		$update = false;

		// add soundcloud and phone social media in 2.2 update
		if ( !$this->niteo_in_array_r( 'soundcloud', $socialmedia, true ) ) {
			$soundcloud  = array(
				'name' 		=> 'soundcloud',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 17,
			);
			array_push( $socialmedia, $soundcloud );
			$update = true;
		}

		// add whatsapp and phone social media in 2.3 update
		if ( !$this->niteo_in_array_r( 'whatsapp', $socialmedia, true ) ) {
			$whatsapp  = array(
				'name' 		=> 'whatsapp',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 18,
			);
			array_push( $socialmedia, $whatsapp );

			$phone  = array(
				'name' 		=> 'phone',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 19,
			);
			array_push( $socialmedia, $phone );
			$update = true;
		}

		// add telegram social media in 2.6.6 update
		if ( !$this->niteo_in_array_r( 'telegram', $socialmedia, true ) ) {
			$telegram  = array(
				'name' 		=> 'telegram',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 20,
			);
			array_push( $socialmedia, $telegram );
			$update = true;
		}

		// add telegram social media in 2.8.7 update
		if ( !$this->niteo_in_array_r( 'xing', $socialmedia, true ) ) {
			$xing  = array(
				'name' 		=> 'xing',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 21,
			);
			array_push( $socialmedia, $xing );
			$update = true;
		}

		// add github social media in 3.1 update
		if ( !$this->niteo_in_array_r( 'github', $socialmedia, true ) ) {
			$github  = array(
				'name' 		=> 'github',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 22,
			);
			array_push( $socialmedia, $github );
			$update = true;
		}

		// add snapchat social media in 3.1 update
		if ( !$this->niteo_in_array_r( 'snapchat', $socialmedia, true ) ) {
			$snapchat  = array(
				'name' 		=> 'snapchat',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 23,
			);
			array_push( $socialmedia, $snapchat );
			$update = true;
		}

		// add spotify social media in 3.3.2 update
		if ( !$this->niteo_in_array_r( 'spotify', $socialmedia, true ) ) {
			$spotify  = array(
				'name' 		=> 'spotify',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 24,
			);
			array_push( $socialmedia, $spotify );
			$update = true;
		}
		// add Discord social media in 3.8.4 update
		if ( !$this->niteo_in_array_r( 'discord', $socialmedia, true ) ) {
			$discord  = array(
				'name' 		=> 'discord',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 25,
			);
			array_push( $socialmedia, $discord );
			$update = true;
		}
		// add Goodreads social media in 3.9.0 update
		if ( !$this->niteo_in_array_r( 'goodreads', $socialmedia, true ) ) {
			$discord  = array(
				'name' 		=> 'goodreads',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 26,
			);
			array_push( $socialmedia, $discord );
			$update = true;
		}
		// add rss social media in 3.9.4 update
		if ( !$this->niteo_in_array_r( 'rss', $socialmedia, true ) ) {
			$rss  = array(
				'name' 		=> 'rss',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 27,
			);
			array_push( $socialmedia, $rss );
			$update = true;
		}

		// add tiktok social media in 4.0.7 update
		if ( !$this->niteo_in_array_r( 'tiktok', $socialmedia, true ) ) {
			$tiktok  = array(
				'name' 		=> 'tiktok',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 28,
			);
			array_push( $socialmedia, $tiktok );
			$update = true;
		}
		// add imdb social media in 4.0.11 update
		if ( !$this->niteo_in_array_r( 'imdb', $socialmedia, true ) ) {
			$icon  = array(
				'name' 		=> 'imdb',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 29,
			);
			array_push( $socialmedia, $icon );
			$update = true;
		}
		// add wikipedia social media in 4.0.11 update
		if ( !$this->niteo_in_array_r( 'wikipedia', $socialmedia, true ) ) {
			$icon  = array(
				'name' 		=> 'wikipedia',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 30,
			);
			array_push( $socialmedia, $icon );
			$update = true;
		}

		// add wikipedia social media in 4.0.13 update
		if ( !$this->niteo_in_array_r( 'twitch', $socialmedia, true ) ) {
			$icon  = array(
				'name' 		=> 'twitch',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 31,
			);
			array_push( $socialmedia, $icon );
			$update = true;
		}

		if ( $update == true ) {
			update_option('niteoCS_socialmedia', json_encode( $socialmedia) );
		}

	}

	if ( version_compare( $pre_update_version, '3.6' ) < 0 ) {
		// delete unzip theme from cmp-premium-dir
		if ( file_exists( CMP_PREMIUM_THEMES_DIR ) ) {
			$files = glob(CMP_PREMIUM_THEMES_DIR . '/*');
			$premium_themes = array_column( $this->cmp_premium_themes(), 'name' );

			foreach( $files as $file ) {
				if ( is_file($file) ){
					unlink($file);
				} else {
					if ( !in_array(basename($file), $premium_themes)) {
						array_map('unlink', glob("$file/*.*"));
						rmdir($file);
					}
					
				}
			}
		}
	}

	// bump version for next udpate check
	update_option( 'niteoCS_version', CMP_VERSION );

}