<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$theme_supports					= array(
	'logo' 				=> true,
	'slider' 			=> false,
	'counter' 			=> false,
	'subscribe' 		=> true,
	'social' 			=> true,
	'footer' 			=> true,
	'special_effects'	=> false,
);

if ( isset($_POST['niteoCS_active_color_'.$themeslug]) ) {
	update_option('niteoCS_active_color['.$themeslug.']', sanitize_hex_color( $_POST['niteoCS_active_color_'.$themeslug]));
}

if ( isset($_POST['niteoCS_font_color_'.$themeslug]) ) {
	update_option('niteoCS_font_color['.$themeslug.']', sanitize_hex_color($_POST['niteoCS_font_color_'.$themeslug]));
}

if ( isset($_POST['niteoCS_background_color_'.$themeslug]) ) {
	update_option('niteoCS_background_color['.$themeslug.']', sanitize_hex_color($_POST['niteoCS_background_color_'.$themeslug]));
}

if ( isset($_POST['niteoCS_social_background_color_'.$themeslug]) ) {
	update_option('niteoCS_social_background_color['.$themeslug.']', sanitize_hex_color($_POST['niteoCS_social_background_color_'.$themeslug]));
}

// set theme defaults
$banner_type     				= get_option('niteoCS_banner', '2');
$active_color      				= get_option('niteoCS_active_color['.$themeslug.']', '#f37004');
$font_color         			= get_option('niteoCS_font_color['.$themeslug.']', '#686868');
$background_color   			= get_option('niteoCS_background_color['.$themeslug.']', '#ffffff');
$social_background_color   		= get_option('niteoCS_social_background_color['.$themeslug.']', '#f8f8f8');

$heading_font = array(
    'family'        => get_option('niteoCS_font_headings['.$themeslug.']', 'Fjalla One'),
    'variant'       => get_option('niteoCS_font_headings_variant['.$themeslug.']', '700'),
    'size'          => get_option('niteoCS_font_headings_size['.$themeslug.']', '40'),
    'spacing'       => get_option('niteoCS_font_headings_spacing['.$themeslug.']', '0'),
);

$content_font = array(
    'family'        => get_option('niteoCS_font_content['.$themeslug.']', 'Average Sans'),
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

