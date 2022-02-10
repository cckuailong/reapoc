<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$themeslug 				= $this->cmp_selectedTheme();

$downloadable_themes 	= $this->cmp_downloadable_themes();

$ajax_nonce 			= wp_create_nonce( 'cmp-coming-soon-ajax-secret' );

// check onces and wordpress rights, else DIE
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	if( !wp_verify_nonce($_POST['save_options_field'], 'save_options') || !current_user_can('publish_pages') ) {
		die('Sorry, but this request is invalid');
	}
	
	$this->cmp_purge_cache();

	// Handle ZIP UPLOAD
	if( isset($_POST['submit_theme']) ) {
		$this->cmp_theme_upload($_FILES['fileToUpload']);
		
	}

	if ( isset($_POST['cmp-activate']) && is_numeric($_POST['cmp-activate']) ) {
		update_option('niteoCS_activation',  sanitize_text_field($_POST['cmp-activate']));
	}

	if ( isset($_POST['cmp_status']) ) {
		update_option('niteoCS_status', $this->sanitize_checkbox($_POST['cmp_status']) );

	} else if ( !isset($_POST['submit_theme']) ) {
		update_option('niteoCS_status', '0');
	}


	if ( isset($_POST['cmp-status-pages']) && is_numeric($_POST['cmp-status-pages']) ) {
		if ( $_POST['cmp-status-pages'] === '0' ) {
			update_option('niteoCS_page_filter', '0');
		}

		if ( $_POST['cmp-status-pages'] === '1' ) {
			update_option('niteoCS_page_filter', '1');
			update_option('niteoCS_page_whitelist', '["-1"]');
			update_option('niteoCS_page_whitelist_custom', '[]');
		}
	}

	if ( isset($_POST['niteoCS_select_theme']) && in_array($_POST['niteoCS_select_theme'], $this->cmp_themes_available())) {
		update_option('niteoCS_theme', sanitize_text_field($_POST['niteoCS_select_theme']));
		$themeslug 	= $this->cmp_selectedTheme();

		if ( $_POST['niteoCS_select_theme'] === 'mosaic' ) {
			update_option( 'niteoCS_slider_effect', 'slice');
		}
	}

	if (isset($_POST['niteoCS_logo_type'])) {
		update_option('niteoCS_logo_type', sanitize_text_field($_POST['niteoCS_logo_type']));
	} 

	if (isset($_POST['niteoCS_logo_id']) && ( is_numeric($_POST['niteoCS_logo_id']) || empty($_POST['niteoCS_logo_id']))) {
		update_option('niteoCS_logo_id', sanitize_text_field($_POST['niteoCS_logo_id']));
	}

	if ( isset($_POST['niteoCS_logo_custom_size']) ) {
		update_option('niteoCS_logo_custom_size', $this->sanitize_checkbox( $_POST['niteoCS_logo_custom_size'] ));
	} else {
		update_option('niteoCS_logo_custom_size', '0');
	}

	if (isset($_POST['niteoCS_logo_size']) && ( is_numeric($_POST['niteoCS_logo_size']) || empty($_POST['niteoCS_logo_size']))) {
		update_option('niteoCS_logo_size', sanitize_text_field($_POST['niteoCS_logo_size']));
	}


	if (isset($_POST['niteoCS_text_logo'])) {
		update_option('niteoCS_text_logo', sanitize_text_field($_POST['niteoCS_text_logo']));
	}
	if (isset($_POST['niteoCS_logo_link'])) {
		update_option('niteoCS_logo_link', esc_url_raw($_POST['niteoCS_logo_link']));
	}

	if (isset($_POST['niteoCS_font_headings_'.$themeslug])) {
		update_option('niteoCS_font_headings['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_headings_'.$themeslug]));
	}

	if (isset($_POST['niteoCS_font_headings_variant_'.$themeslug])) {
		update_option('niteoCS_font_headings_variant['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_headings_variant_'.$themeslug]));
	}

	if (isset($_POST['niteoCS_font_headings_size_'.$themeslug])) {
		update_option('niteoCS_font_headings_size['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_headings_size_'.$themeslug]));
	}


	if (isset($_POST['niteoCS_font_headings_spacing_'.$themeslug])) {
		update_option('niteoCS_font_headings_spacing['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_headings_spacing_'.$themeslug]));
	}

	if (isset($_POST['niteoCS_font_content_'.$themeslug])) {
		update_option('niteoCS_font_content['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_content_'.$themeslug]));
	}

	if (isset($_POST['niteoCS_font_content_variant_'.$themeslug])) {
		update_option('niteoCS_font_content_variant['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_content_variant_'.$themeslug]));
	}

	if (isset($_POST['niteoCS_font_content_size_'.$themeslug])) {
		update_option('niteoCS_font_content_size['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_content_size_'.$themeslug]));
	}

	if (isset($_POST['niteoCS_font_content_lineheight_'.$themeslug])) {
		update_option('niteoCS_font_content_lineheight['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_content_lineheight_'.$themeslug]));
	}

	if (isset($_POST['niteoCS_font_content_spacing_'.$themeslug])) {
		update_option('niteoCS_font_content_spacing['.$themeslug.']', sanitize_text_field($_POST['niteoCS_font_content_spacing_'.$themeslug]));
	}

	if (isset($_POST['niteoCS_heading_animation_'.$themeslug])) {
		update_option('niteoCS_heading_animation['.$themeslug.']', sanitize_text_field($_POST['niteoCS_heading_animation_'.$themeslug]) );
	}

	if (isset($_POST['niteoCS_content_animation_'.$themeslug])) {
		update_option('niteoCS_content_animation['.$themeslug.']', sanitize_text_field($_POST['niteoCS_content_animation_'.$themeslug]) );
	}


	if (isset($_POST['niteoCS_banner']) && is_numeric($_POST['niteoCS_banner'])) {
		update_option('niteoCS_banner', sanitize_text_field($_POST['niteoCS_banner']));
	}

	if (isset($_POST['niteoCS_banner_color'])) {
		update_option('niteoCS_banner_color', sanitize_text_field($_POST['niteoCS_banner_color']));
	}


	if (isset($_POST['niteoCS_gradient'])) {
		update_option('niteoCS_gradient', sanitize_text_field($_POST['niteoCS_gradient']));
	}

	if (isset($_POST['niteoCS_banner_gradient_one'])) {
		update_option('niteoCS_banner_gradient_one', sanitize_text_field($_POST['niteoCS_banner_gradient_one']));
	}

	if (isset($_POST['niteoCS_banner_gradient_two'])) {
		update_option('niteoCS_banner_gradient_two', sanitize_text_field($_POST['niteoCS_banner_gradient_two']));
	}

	if (isset($_POST['niteoCS_banner_pattern'])) {
		update_option('niteoCS_banner_pattern', sanitize_text_field($_POST['niteoCS_banner_pattern']));
	}

	if (isset($_POST['niteoCS_banner_pattern_custom'])) {
		update_option('niteoCS_banner_pattern_custom', sanitize_text_field($_POST['niteoCS_banner_pattern_custom']));
	}

	if (isset($_POST['niteoCS_banner_video'])) {
		update_option('niteoCS_banner_video', sanitize_text_field($_POST['niteoCS_banner_video']));
	}

	if (isset($_POST['niteoCS_youtube_url'])) {
		update_option('niteoCS_youtube_url', sanitize_text_field($_POST['niteoCS_youtube_url']));
	}

	if (isset($_POST['niteoCS_vimeo_url'])) {
		update_option('niteoCS_vimeo_url', sanitize_text_field($_POST['niteoCS_vimeo_url']));
	}

	if (isset($_POST['niteoCS_video_thumb'])) {
		update_option('niteoCS_video_thumb', sanitize_text_field($_POST['niteoCS_video_thumb']));
	}

	if (isset($_POST['niteoCS_video_file_url'])) {
		update_option('niteoCS_video_file_url', sanitize_text_field($_POST['niteoCS_video_file_url']));
	}

	if ( isset($_POST['niteoCS_video_autoloop']) ) {
		update_option('niteoCS_video_autoloop', $this->sanitize_checkbox($_POST['niteoCS_video_autoloop']));
	} else {
		update_option('niteoCS_video_autoloop', '0');
	}

	if ( isset($_POST['niteoCS_banner_id']) ) {
			$numeric = true;
			$banner_ids_check = explode( ',', $_POST['niteoCS_banner_id'] );
			foreach ( $banner_ids_check as $id ) {

				if ( !is_numeric($id) ) {
					$numeric = false;
				}
			}

		if ( $numeric === true || $_POST['niteoCS_banner_id'] == '' ) {
			update_option('niteoCS_banner_id', sanitize_text_field($_POST['niteoCS_banner_id']));
		}

	}

	if (isset($_POST['unsplash_feed']) && is_numeric($_POST['unsplash_feed'])) {
		update_option('niteoCS_unsplash_feed', sanitize_text_field($_POST['unsplash_feed']));
	}

	if (isset($_POST['niteoCS_unsplash_0'])) {
		$url = $_POST['niteoCS_unsplash_0'];
		// if we have url sanitize url
		if (strpos($url, 'http://') !== false || strpos($url, 'https://') !== false) {
			update_option('niteoCS_unsplash_0', esc_url_raw($_POST['niteoCS_unsplash_0']));
		} else {
			// sanitize string
			update_option('niteoCS_unsplash_0', sanitize_text_field($_POST['niteoCS_unsplash_0']));
		}
	}

	if (isset($_POST['niteoCS_unsplash_2'])) {
		$url = $_POST['niteoCS_unsplash_2'];
		// if we have url sanitize url
		if (strpos($url, 'http://') !== false || strpos($url, 'https://') !== false) {
			update_option('niteoCS_unsplash_2', esc_url_raw($_POST['niteoCS_unsplash_2']));
		} else {
			// sanitize string
			update_option('niteoCS_unsplash_2', sanitize_text_field($_POST['niteoCS_unsplash_2']));
		}
	}

	if (isset($_POST['niteoCS_unsplash_3'])) {
		update_option('niteoCS_unsplash_3', sanitize_text_field($_POST['niteoCS_unsplash_3']));
	}

	if (isset($_POST['niteoCS_unsplash_1'])) {
		update_option('niteoCS_unsplash_1', sanitize_text_field($_POST['niteoCS_unsplash_1']));
	}

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

		if (isset($_POST['niteoCS_unsplash_feat'])) {
			update_option('niteoCS_unsplash_feat', $this->sanitize_checkbox($_POST['niteoCS_unsplash_feat']));
		} else {
			update_option('niteoCS_unsplash_feat', false);
		}
	}

	if (isset($_POST['niteoCS_favicon_id']) && ( is_numeric($_POST['niteoCS_favicon_id']) || empty($_POST['niteoCS_favicon_id']))) {
		update_option('niteoCS_favicon_id', sanitize_text_field($_POST['niteoCS_favicon_id']));
	}

	if (isset($_POST['niteoCS_seo_img_id']) && ( is_numeric($_POST['niteoCS_seo_img_id']) || empty($_POST['niteoCS_seo_img_id']))) {
		update_option('niteoCS_seo_img_id', sanitize_text_field($_POST['niteoCS_seo_img_id']));
	}


	if (isset($_POST['niteoCS_title'])) {
		update_option('niteoCS_title', sanitize_text_field($_POST['niteoCS_title']));
	}

	if (isset($_POST['niteoCS_descr'])) {
		update_option('niteoCS_descr', sanitize_text_field($_POST['niteoCS_descr']));
	}

	if (isset($_POST['niteoCS_analytics'])) {
		update_option('niteoCS_analytics', sanitize_text_field($_POST['niteoCS_analytics']));
	}

	if (isset($_POST['niteoCS_analytics_status'])) {
		update_option('niteoCS_analytics_status', sanitize_text_field($_POST['niteoCS_analytics_status']));
	}


	if (isset($_POST['niteoCS_analytics_other'])) {
		$js_code = $this->niteo_sanitize_html( $_POST['niteoCS_analytics_other'] );
		update_option('niteoCS_analytics_other', $js_code);
	}

	if ( isset($_POST['niteoCS_seo_visibility']) ) {
		update_option('niteoCS_seo_visibility', '0');
	} else {
		update_option('niteoCS_seo_visibility', '1');
	}

	if ( isset($_POST['niteoCS_seo_nocache']) ) {
		update_option('niteoCS_seo_nocache', $this->sanitize_checkbox( $_POST['niteoCS_seo_nocache'] ));
	} else {
		update_option('niteoCS_seo_nocache', '0');
	}


	if (isset($_POST['niteoCS_custom_css'])) {
		update_option('niteoCS_custom_css', $_POST['niteoCS_custom_css']);
	}


	if (isset($_POST['niteoCS_soc_title'])) {
		update_option('niteoCS_soc_title', sanitize_text_field($_POST['niteoCS_soc_title']));
	}
	if (isset($_POST['niteoCS_socialmedia'])) {
		update_option('niteoCS_socialmedia', sanitize_text_field($_POST['niteoCS_socialmedia']));
	}


	if (isset($_POST['niteoCS_body_title'])) {
		update_option('niteoCS_body_title', sanitize_text_field($_POST['niteoCS_body_title']));
	}

	if (isset($_POST['niteoCS_body'])) {
		update_option('niteoCS_body', $this->niteo_sanitize_html( $_POST['niteoCS_body']));
	}


	if (isset($_POST['niteoCS_copyright'])) {
		update_option('niteoCS_copyright', $this->niteo_sanitize_html( $_POST['niteoCS_copyright']));
	}

	if (isset($_POST['niteoCS_URL_redirect'])) {
		update_option('niteoCS_URL_redirect', esc_url_raw( $_POST['niteoCS_URL_redirect']));
	}

	if (isset($_POST['niteoCS_redirect_time'])) {
		update_option('niteoCS_redirect_time', sanitize_text_field( $_POST['niteoCS_redirect_time']));
	}

	// background overlay 
	if ( isset( $_POST['niteoCS_overlay'] ) ) {
		update_option( 'niteoCS_overlay', sanitize_text_field( $_POST['niteoCS_overlay'] ) );
	}

	if ( isset( $_POST['niteoCS_overlay_color'] ) ) {
		update_option( 'niteoCS_overlay[color]', sanitize_hex_color( $_POST['niteoCS_overlay_color'] ) );
	}

	if ( isset( $_POST['niteoCS_overlay_opacity'] ) ) {
		update_option( 'niteoCS_overlay[opacity]', sanitize_text_field( $_POST['niteoCS_overlay_opacity'] ) );
	}

	if ( isset( $_POST['niteoCS_overlay_gradient'] ) ) {
		update_option('niteoCS_overlay[gradient]', sanitize_text_field( $_POST['niteoCS_overlay_gradient'] ) );
	}

	if ( isset( $_POST['niteoCS_overlay_gradient_one'] ) ) {
		update_option('niteoCS_overlay[gradient_one]', sanitize_hex_color( $_POST['niteoCS_overlay_gradient_one'] ) );
	}

	if ( isset( $_POST['niteoCS_overlay_gradient_two'] ) ) {
		update_option('niteoCS_overlay[gradient_two]', sanitize_hex_color( $_POST['niteoCS_overlay_gradient_two'] ) );
	}

	if (isset($_POST['niteoCS_effect_blur']) && is_numeric($_POST['niteoCS_effect_blur'])) {
		update_option('niteoCS_effect_blur', sanitize_text_field($_POST['niteoCS_effect_blur']));
	}

	if ( isset($_POST['niteoCS_overlay_text_status']) && $_POST['niteoCS_overlay_text_status'] == 'on' ) {
		update_option('niteoCS_overlay_text[status]', '1' );

	} else if ( isset($_POST['niteoCS_overlay_text_status']) && $_POST['niteoCS_overlay_text_status'] == 'off' ) {
		update_option('niteoCS_overlay_text[status]', '0');
	}

	if ( isset($_POST['niteoCS_overlay_text_heading']) ) {
		update_option('niteoCS_overlay_text[heading]', $this->niteo_sanitize_html($_POST['niteoCS_overlay_text_heading']));
	}

	if ( isset($_POST['niteoCS_overlay_text_paragraph']) ) {
		update_option('niteoCS_overlay_text[paragraph]', $this->niteo_sanitize_html($_POST['niteoCS_overlay_text_paragraph']));
	}

	if ( isset($_POST['niteoCS_overlay_button_text']) ) {
		update_option('niteoCS_overlay_text[button_text]', sanitize_text_field($_POST['niteoCS_overlay_button_text']));
	}

	if ( isset($_POST['niteoCS_overlay_button_url']) ) {
		update_option('niteoCS_overlay_text[button_url]', sanitize_text_field($_POST['niteoCS_overlay_button_url']));
	}

}


// get Settings TAB
$niteoCS_URL_redirect 		= get_option('niteoCS_URL_redirect');
$niteoCS_redirect_time 		= get_option('niteoCS_redirect_time', '0');

// get Content Settings
$niteoCS_body_title 		= stripslashes(get_option('niteoCS_body_title', 'SOMETHING IS HAPPENING!'));
$niteoCS_body 				= stripslashes(get_option('niteoCS_body', ''));
$niteoCS_copyright			= stripslashes(get_option('niteoCS_copyright', 'Made by <a href="https://niteothemes.com">NiteoThemes</a> with love.'));
$niteoCS_soc_title			= stripslashes(get_option('niteoCS_soc_title', 'GET SOCIAL WITH US'));


// get SEO 
$niteoCS_favicon_id 		= get_option('niteoCS_favicon_id');
$niteoCS_seo_img_id 		= get_option('niteoCS_seo_img_id');
$niteoCS_title 				= stripslashes( get_option('niteoCS_title', get_bloginfo('name')) );
$niteoCS_descr				= stripslashes( get_option('niteoCS_descr', get_bloginfo('description'))) ;
$niteoCS_analytics_status 	= get_option('niteoCS_analytics_status', 'disabled');
$niteoCS_analytics 			= stripslashes(get_option('niteoCS_analytics', ''));
$niteoCS_analytics_other 	= get_option('niteoCS_analytics_other', '');
$seo_visibility 			= get_option('niteoCS_seo_visibility', get_option( 'blog_public', '1' ));
$seo_nocache 				= get_option('niteoCS_seo_nocache', '1');

// get Custom CSS
$niteoCS_custom_css 		= stripslashes(get_option('niteoCS_custom_css', ''));

//get theme specific settings
$niteoCS_logo_type 				= get_option('niteoCS_logo_type', 'text');
$niteoCS_logo_id				= get_option('niteoCS_logo_id');
$niteoCS_text_logo 				= stripslashes(get_option('niteoCS_text_logo', get_bloginfo( 'name', 'display' )));
$niteoCS_logo_custom_size		= get_option('niteoCS_logo_custom_size', '0');
$niteoCS_logo_size				= get_option('niteoCS_logo_size', '100');
$logo_link						= get_option('niteoCS_logo_link', get_site_url() );
$niteoCS_heading_animation 		= get_option('niteoCS_heading_animation['.$themeslug.']', 'fadeInDown');
$niteoCS_content_animation 		= get_option('niteoCS_content_animation['.$themeslug.']', 'fadeInUp');


$niteoCS_banner_custom_id		= get_option('niteoCS_banner_id');
$niteoCS_unsplash_feed			= get_option('niteoCS_unsplash_feed', '3');
$niteoCS_unsplash_0				= get_option('niteoCS_unsplash_0');
$niteoCS_unsplash_1				= get_option('niteoCS_unsplash_1');
$niteoCS_unsplash_2				= get_option('niteoCS_unsplash_2');
$niteoCS_unsplash_3				= get_option('niteoCS_unsplash_3');
$niteoCS_unsplash_category		= get_option('niteoCS_unsplash_category', 'buildings');
$niteoCS_cat_keyword    		= get_option('niteoCS_cat_keyword');
$niteoCS_banner_color			= get_option('niteoCS_banner_color', '#e5e5e5');
$niteoCS_gradient 				= get_option('niteoCS_gradient', '#ED5565:#D62739');
$niteoCS_gradient_one_custom 	= get_option('niteoCS_banner_gradient_one', '#e5e5e5');
$niteoCS_gradient_two_custom	= get_option('niteoCS_banner_gradient_two', '#e5e5e5');

if ( $niteoCS_gradient != 'custom' ) {
	$gradient = explode(":", $niteoCS_gradient);
	$niteoCS_gradient_one 			= $gradient[0];
	$niteoCS_gradient_two 			= $gradient[1];	
} else {
	$niteoCS_gradient_one 			= '';
	$niteoCS_gradient_two 			= '';
}

$niteoCS_banner_pattern     	= get_option('niteoCS_banner_pattern', 'sakura');
$niteoCS_banner_pattern_custom  = get_option('niteoCS_banner_pattern_custom');
$niteoCS_banner_video 			= get_option('niteoCS_banner_video');
$niteoCS_youtube_url 			= get_option('niteoCS_youtube_url');
$niteoCS_vimeo_url 				= get_option('niteoCS_vimeo_url');
$niteoCS_video_thumb 			= get_option('niteoCS_video_thumb');
$niteoCS_video_file_url 		= get_option('niteoCS_video_file_url');
$video_autoloop					= get_option('niteoCS_video_autoloop', '1');
$overlay  						= get_option('niteoCS_overlay', 'solid-color');
$overlay_color  				= get_option('niteoCS_overlay[color]', '#0a0a0a');
$overlay_opa 	 				= get_option('niteoCS_overlay[opacity]', '0.4');
$overlay_gradient   			= get_option('niteoCS_overlay[gradient]', '#d53369:#cbad6d');
$overlay_gradient_one_custom 	= get_option('niteoCS_overlay[gradient_one]', '#e5e5e5');
$overlay_gradient_two_custom 	= get_option('niteoCS_overlay[gradient_two]', '#e5e5e5');
$effect_blur 					= get_option('niteoCS_effect_blur', '0.0');

$overlay_text_status			= get_option('niteoCS_overlay_text[status]', '1');
$overlay_text_heading 			= stripslashes(get_option('niteoCS_overlay_text[heading]', 'NEW WEBSITE ON THE WAY!') );
$overlay_text_paragraph 		= stripslashes(get_option('niteoCS_overlay_text[paragraph]', '') );
$overlay_button_text 			= stripslashes(get_option('niteoCS_overlay_text[button_text]', 'Call to Action!') );
$overlay_button_url 			= get_option('niteoCS_overlay_text[button_url]', '');

// retrieve whitelist or blacklist settings
$page_filter 	= get_option('niteoCS_page_filter', '0');

// set status pages based on whitelist/blacklist settings
if ( $page_filter == '1' ) {
	$page_whitelist = get_option('niteoCS_page_whitelist', '[]');
	$page_whitelist_custom	= get_option('niteoCS_page_whitelist_custom', '[]');

	if ( $page_whitelist !== '["-1"]' || $page_whitelist_custom !== '[]' ) {
		// change it to custom BL/WL settings
		$page_filter = '2';
	} 
}

// create default social media if they do not exists
if ( !get_option('niteoCS_socialmedia') ) {
	$social_icons = array(
		'facebook',
		'twitter',
		'instagram',
		'skype',
		'500px',
		'deviantart',
		'behance',
		'dribbble',
		'pinterest',
		'linkedin',
		'tumblr',
		'youtube',
		'vimeo',
		'flickr',
		'soundcloud',
		'vk',
		'envelope-o',
		'whatsapp',
		'phone',
		'telegram',
		'xing',
		'github',
		'snapchat',
		'spotify',
		'discord',
		'goodreads',
		'rss',
		'tiktok',
		'imdb',
		'wikipedia',
		'twitch'
	);
	$i = 0;
	$socialmedia  = array();
	foreach ( $social_icons as $social ) {
		
		$social_field = get_option('niteoCS_'.$social);

		$socialmedia[$i]['name']  	= $social;
		$socialmedia[$i]['url']  	= $social_field;
		$socialmedia[$i]['active']  = '1';
		$socialmedia[$i]['hidden']  = $social_field ? '0' : '1';
		$socialmedia[$i]['order']  	= $i;
		$i++;
	}

	$niteoCS_socialmedia = json_encode( $socialmedia );

} else {
	$niteoCS_socialmedia = stripslashes( get_option('niteoCS_socialmedia') );
	$socialmedia = json_decode( $niteoCS_socialmedia, true );
}

$builder_theme = in_array( $themeslug, $this->cmp_builder_themes() );

//include theme defaults
if ( file_exists( $this->cmp_theme_dir($themeslug).$themeslug.'/'.$themeslug.'-defaults.php' ) ) {
	include ( $this->cmp_theme_dir($themeslug).$themeslug.'/'.$themeslug.'-defaults.php' );
} 

// set banner type again, if the themes are not upgraded after 2.9.5 release where this settings got theme settings independent
$banner_type = get_option('niteoCS_banner', '2');

// get logo url from id
if ( $niteoCS_logo_id != '' ) {
	$logo_url = wp_get_attachment_image_src($niteoCS_logo_id, 'large');
	if ( isset($logo_url[0]) ){
		$logo_url = $logo_url[0];
	}
}

// get favicon url from id
if ( $niteoCS_favicon_id != '' ) {
	$niteoCS_favicon_url = wp_get_attachment_image_src($niteoCS_favicon_id, 'thumbnail');
	if ( isset($niteoCS_favicon_url[0]) ){
		$niteoCS_favicon_url = $niteoCS_favicon_url[0];
	}
}

// get favicon url from id
if ( $niteoCS_seo_img_id != '' ) {
	$niteoCS_seo_img_url = wp_get_attachment_image_src($niteoCS_seo_img_id, 'medium');
	if ( isset($niteoCS_seo_img_url[0]) ){
		$niteoCS_seo_img_url = $niteoCS_seo_img_url[0];
	}
}

// get banner url from id
if ( $niteoCS_banner_pattern == 'custom' && $niteoCS_banner_pattern_custom != '' ) {
	$pattern_url = wp_get_attachment_image_src($niteoCS_banner_pattern_custom, 'thumbnail');
	if ( isset($pattern_url[0]) ){
		$pattern_url = $pattern_url[0];
	}
} else {
	$pattern_url = plugins_url('/img/patterns/'.esc_attr($niteoCS_banner_pattern).'.png', __FILE__);
}

// define patterns array
$patterns = array('fabric', 'gray_sand', 'green_dust_scratch', 'mirrored_squares', 'noisy', 'photography', 'playstation', 'sakura', 'white_sand', 'white_texture');

add_thickbox();

?>

<noscript>
	<div class='updated'>
		<p class="error"><?php _e('JavaScript appears to be disabled in your browser. For this plugin to work correctly, please enable JavaScript or switch to a more modern browser.', 'cmp-coming-soon-maintenance');?></p>
	</div>
	<style>
		.themes{display:none;}
	</style>
</noscript>



<div class="wrap cmp-coming-soon-maintenance content-settings">
	<h1></h1>
	
	<div id="icon-options-general" class="icon32">
		<br />
	</div>

	<div class="settings-wrap">
	<form method="post"	action="admin.php?page=cmp-settings&status=settings-saved" id="csoptions">
		
		<?php wp_nonce_field('save_options','save_options_field'); ?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active general" href="<?php echo admin_url(); ?>admin.php?page=cmp-settings#general" data-tab="general"><i class="fas fa-cog" aria-hidden="true"></i><?php _e('Settings', 'cmp-coming-soon-maintenance');?></a>

			<?php if ( !$builder_theme ) : ?>
				<a class="nav-tab content" href="<?php echo admin_url(); ?>admin.php?page=cmp-settings#content" data-tab="content"><i class="far fa-edit" aria-hidden="true"></i><?php _e('Content', 'cmp-coming-soon-maintenance');?></a>
			<?php endif; ?>
			<a class="nav-tab theme-setup" href="<?php echo admin_url(); ?>admin.php?page=cmp-settings#theme-setup" data-tab="theme-setup"><i class="fas fa-tools" aria-hidden="true"></i><?php _e('Customize', 'cmp-coming-soon-maintenance');?></a>

			<a class="nav-tab seo" href="<?php echo admin_url(); ?>admin.php?page=cmp-settings#seo" data-tab="seo"><i class="fas fa-chart-line"></i><?php _e('SEO & Analytics', 'cmp-coming-soon-maintenance');?></a>

			<a class="nav-tab custom_css" href="<?php echo admin_url(); ?>admin.php?page=cmp-settings#custom_css" data-tab="custom_css"><i class="fab fa-css3"></i><?php _e('Custom CSS', 'cmp-coming-soon-maintenance');?></a>
			
			<a class="nav-tab advanced" href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced"><i class="fas fa-cogs" aria-hidden="true"></i><?php _e('Advanced', 'cmp-coming-soon-maintenance');?></a>

			<a class="nav-tab theme-preview" href="<?php echo get_home_url(); ?>?cmp_preview=true" data-tab="theme-preview" target="_blank" ><i class="fas fa-external-link-alt"></i><?php _e('Preview', 'cmp-coming-soon-maintenance');?></a>

		</h2>

		<div class="cmp-settings-wrapper">

		<div class="cmp-inputs-wrapper">



		<?php 
		// get general settings
		if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-general.php' ) ) {
			require ( dirname(__FILE__) . '/inc/settings/settings-general.php' );
		}

		// include banner 
		if ( !class_exists('CMP_Addons') ) { ?>
			<a class="cmp-bundle-banner table-wrapper general" href="https://niteothemes.com/bundles/" target="_blank"><img src="<?php echo plugins_url('img/banner_bundle.jpg', __FILE__);?>" alt="CMP Bundle Banner"></a>
			<?php 
		}

		// add theme selector settings
		if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-theme-selector.php' ) ) {
			require ( dirname(__FILE__) . '/inc/settings/settings-theme-selector.php' );
		}

		if ( !$builder_theme ) {

			// get logo settings
			if ( isset( $theme_supports['logo'] ) && $theme_supports['logo'] ) {
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-logo.php' ) ) {
					require ( dirname(__FILE__) . '/inc/settings/settings-logo.php' );
				}

			} else { 
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-logo-disabled.php' ) ) {
					require ( dirname(__FILE__) . '/inc/settings/settings-logo-disabled.php' );
				}

			}
			
			// get content settings
			if ( !isset( $theme_supports['content'] ) || ( isset( $theme_supports['content']) && $theme_supports['content'] ) ) {
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-content.php' ) ) {
					require ( dirname(__FILE__) . '/inc/settings/settings-content.php' );
				}
			}

			// include custom theme content settings
			if ( file_exists( $this->cmp_theme_dir($themeslug).$themeslug.'/'.$themeslug.'-content_settings.php' ) ) {
				include ( $this->cmp_theme_dir($themeslug).$themeslug.'/'.$themeslug.'-content_settings.php' );
			}

			// get background settings
			if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-background.php' ) ) {
				require ( dirname(__FILE__) . '/inc/settings/settings-background.php' );
			}
			
			// get slider settings 
			if ( isset( $theme_supports['slider'] ) && $theme_supports['slider'] ) {
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-slider.php' ) ) {
					require  (dirname(__FILE__) . '/inc/settings/settings-slider.php' );
				}

			} else { 
				update_option('niteoCS_slider', '0');
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-slider-disabled.php' ) ) {
					require ( dirname(__FILE__) . '/inc/settings/settings-slider-disabled.php' );
				}
			} 

			// login icon
			if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-login-icon.php' ) ) {
				require ( dirname(__FILE__) . '/inc/settings/settings-login-icon.php' );
			}

			// Language Switcher
			if ( $this->translation_active() && file_exists(dirname(__FILE__) . '/inc/settings/settings-lang-switcher.php' ) ) {
				require ( dirname(__FILE__) . '/inc/settings/settings-lang-switcher.php' );
			} else if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-lang-switcher-disabled.php' ) ) {
				require ( dirname(__FILE__) . '/inc/settings/settings-lang-switcher-disabled.php' );
			}
			
			// get counter settings
			if ( isset( $theme_supports['counter'] ) && $theme_supports['counter'] ) {
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-counter.php') ) {
					require (dirname(__FILE__) . '/inc/settings/settings-counter.php');
				}

			} else { 
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-counter-disabled.php') ) {
					require (dirname(__FILE__) . '/inc/settings/settings-counter-disabled.php');
				}
			}

			// progress bar
			if ( isset( $theme_supports['progress_bar'] ) && $theme_supports['progress_bar'] ) {
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-progress-bar.php') ) {
					require (dirname(__FILE__) . '/inc/settings/settings-progress-bar.php');
				}

			} 

			// get subscribe settings
			if ( isset( $theme_supports['subscribe'] ) && $theme_supports['subscribe'] ) {
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-subscribe.php') ) {
					require (dirname(__FILE__) . '/inc/settings/settings-subscribe.php');
				}

			} else { 
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-subscribe-disabled.php') ) {
					require (dirname(__FILE__) . '/inc/settings/settings-subscribe-disabled.php');
				}

			}

			// get contact form settings
			if ( isset( $theme_supports['contact-form'] ) && $theme_supports['contact-form'] ) {
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-contact_form.php') ) {
					require (dirname(__FILE__) . '/inc/settings/settings-contact_form.php');
				}

			}
			
			// get social media settings
			if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-social-media.php' ) ) {
				require_once ( dirname(__FILE__) . '/inc/settings/settings-social-media.php' );
			}

			// get footer
			if ( isset( $theme_supports['footer'] ) && $theme_supports['footer'] ) {
				if ( file_exists( dirname(__FILE__) . '/inc/settings/settings-footer.php') ) {
					require_once ( dirname(__FILE__) . '/inc/settings/settings-footer.php' );
				}

			} else { 
				if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-footer-disabled.php' ) ) {
					require_once ( dirname(__FILE__) . '/inc/settings/settings-footer-disabled.php' );
				}

			}
		}

		// include builders related settings
		if ( defined('CMP_ADDONS_VERSION') && in_array( $this->cmp_selectedTheme(), $this->cmp_builder_themes() ) ) {
			if ( $builder_theme && file_exists( $this->cmp_theme_dir( $themeslug ).$themeslug .'/'.$themeslug.'-admin-settings.php' ) ) {
				require ( $this->cmp_theme_dir( $themeslug ).$themeslug .'/'.$themeslug.'-admin-settings.php' );
			}
		} else if ( in_array( $this->cmp_selectedTheme(), $this->cmp_builder_themes() ) ){
			$this->cmp_display_admin_notice('error', false, __('CMP - Bundle & Add-ons Manager must be enabled to access the Customize Settings.'));
		}

		// special effects for premium themes
		if ( in_array( $themeslug, $this->cmp_premium_themes_installed() ) || ( isset( $theme_supports['special_effects'] )  && $theme_supports['special_effects'] ) )  { 

			// get background effects settings
			if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-special_effects.php' ) ) {
				require  (dirname(__FILE__) . '/inc/settings/settings-special_effects.php' );
			}

		} else {

			if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-special_effects-disabled.php' ) ) {
				require ( dirname(__FILE__) . '/inc/settings/settings-special_effects-disabled.php' );
			}	
		}

		// include theme related settings
		if ( file_exists( $this->cmp_theme_dir( $themeslug ).$themeslug.'/'.$themeslug.'-settings.php' ) ) {
			require ( $this->cmp_theme_dir( $themeslug ).$themeslug.'/'.$themeslug.'-settings.php' );
		}
		
		// font selector settings
		if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-typography.php' ) && !$builder_theme ) {
			require ( dirname(__FILE__) . '/inc/settings/settings-typography.php' );
		}	

		// SEO settings
		if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-seo.php' ) ) {
			require ( dirname(__FILE__) . '/inc/settings/settings-seo.php' );
		}

		// CSS settings
		if ( file_exists(dirname(__FILE__) . '/inc/settings/settings-css.php' ) ) {
			require ( dirname(__FILE__) . '/inc/settings/settings-css.php' );
		}

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['niteoCS_builder_page_id'])) {
			do_action('cmp_save_settings', sanitize_text_field($_POST['niteoCS_builder_page_id']), $themeslug);
		} ?>

	</div> <!-- <div class="cmp-settings-wrapper"> -->

	</div> <!-- <div class="cmp-inputs-wrapper"> -->

	</form>

	<?php 
	// get sidebar with "widgets"
	if ( file_exists(dirname(__FILE__) . '/cmp-sidebar.php') ) {
		require (dirname(__FILE__) . '/cmp-sidebar.php');
	}
	?>
	</div>

</div> <!-- <div id="wrap"> -->

