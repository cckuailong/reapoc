<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$theme_supports					= array(
	'logo' 				=> true,
	'slider' 			=> false,
	'counter' 			=> true,
	'counter_script' 	=> true,
	'subscribe' 		=> true,
	'social' 			=> true,
	'footer' 			=> true,
	'special_effects'	=> false,
);


if (isset($_POST['niteoCS_active_color_'.$themeslug])) {
	update_option('niteoCS_active_color['.$themeslug.']', sanitize_hex_color($_POST['niteoCS_active_color_'.$themeslug]));
}

if (isset($_POST['niteoCS_font_color_'.$themeslug])) {
	update_option('niteoCS_font_color['.$themeslug.']', sanitize_hex_color($_POST['niteoCS_font_color_'.$themeslug]));
}


if (isset($_POST['niteoCS_social_location']) && $_POST['niteoCS_social_location']) {
	update_option('niteoCS_social_location', sanitize_text_field($_POST['niteoCS_social_location']));
}



// get theme defaults
$banner_type		= get_option('niteoCS_banner', '2');
$niteoCS_gradient 	= get_option('niteoCS_gradient', '#1A2980:#26D0CE');
$active_color		= get_option('niteoCS_active_color['.$themeslug.']', '#e82e1e');
$font_color			= get_option('niteoCS_font_color['.$themeslug.']', '#ffffff');

$social_location 	= get_option('niteoCS_social_location', 'footer');

$heading_font = array(
    'family'        => get_option('niteoCS_font_headings['.$themeslug.']', 'Source Sans Pro'),
    'variant'       => get_option('niteoCS_font_headings_variant['.$themeslug.']', '700'),
    'size'          => get_option('niteoCS_font_headings_size['.$themeslug.']', '40'),
    'spacing'       => get_option('niteoCS_font_headings_spacing['.$themeslug.']', '0'),
);

$content_font = array(
    'family'        => get_option('niteoCS_font_content['.$themeslug.']', 'Maven Pro'),
    'variant'       => get_option('niteoCS_font_content_variant['.$themeslug.']', 'regular'),
    'size'          => get_option('niteoCS_font_content_size['.$themeslug.']', '17'),
    'spacing'       => get_option('niteoCS_font_content_spacing['.$themeslug.']', '0'),
    'line-height'   => get_option('niteoCS_font_content_lineheight['.$themeslug.']', '1.6'),
);


$heading_font['variant'] = ($heading_font['variant'] =='regular')  ? '400' : $heading_font['variant'];
$heading_font['variant'] = ($heading_font['variant'] =='italic')   ? '400' : $heading_font['variant'];
$content_font['variant'] = ($content_font['variant'] =='regular') ? '400' : $content_font['variant'];
$content_font['variant'] = ($content_font['variant'] =='italic')  ? '400' : $content_font['variant'];
$heading_font_style =  preg_split('/(?<=[0-9])(?=[a-z]+)/i', $heading_font['variant']); 
$content_font_style =  preg_split('/(?<=[0-9])(?=[a-z]+)/i', $content_font['variant']);