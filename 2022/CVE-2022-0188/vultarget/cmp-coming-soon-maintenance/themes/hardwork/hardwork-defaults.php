<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$theme_supports = array(
    'logo'              => true,
    'slider'            => false,
    'counter'           => false,
    'subscribe'         => false,
    'social'            => true,
    'footer'            => false,
    'special_effects'   => false,
);


if ( isset( $_POST['niteoCS_font_color_'.$themeslug] ) ) {
	update_option('niteoCS_font_color['.$themeslug.']', sanitize_hex_color($_POST['niteoCS_font_color_'.$themeslug]));
}

if ( isset( $_POST['niteoCS_footer_background_'.$themeslug] ) ) {
    update_option('niteoCS_footer_background['.$themeslug.']', sanitize_hex_color( $_POST['niteoCS_footer_background_'.$themeslug]) );
}


if ( isset( $_POST['niteoCS_footer_background_opacity_'.$themeslug] ) ) {
    update_option('niteoCS_footer_background_opacity['.$themeslug.']', sanitize_text_field( $_POST['niteoCS_footer_background_opacity_'.$themeslug]) );
}


$banner_type        = get_option('niteoCS_banner', '2');
$banner_color		= get_option('niteoCS_banner_color['.$themeslug.']', '#e5e5e5');
$font_color			= get_option('niteoCS_font_color['.$themeslug.']', '#ffffff');
$footer_background  = get_option('niteoCS_footer_background['.$themeslug.']', '#000000');
$footer_opacity     = get_option('niteoCS_footer_background_opacity['.$themeslug.']', '0.4');

$heading_font = array(
    'family'        => get_option('niteoCS_font_headings['.$themeslug.']', 'Playfair Display'),
    'variant'       => get_option('niteoCS_font_headings_variant['.$themeslug.']', '700'),
    'size'          => get_option('niteoCS_font_headings_size['.$themeslug.']', '40'),
    'spacing'       => get_option('niteoCS_font_headings_spacing['.$themeslug.']', '0'),
);

$content_font = array(
    'family'        => get_option('niteoCS_font_content['.$themeslug.']', 'Montserrat'),
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