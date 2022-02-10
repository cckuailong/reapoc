<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
// DEFAULT WORDPRESS EDITOR
$uaf_disbale_editor_font_list_value = get_option('uaf_disbale_editor_font_list');
if ($uaf_disbale_editor_font_list_value != 1):
	add_filter('mce_buttons_2', 'wp_editor_fontsize_filter');
	add_filter('tiny_mce_before_init', 'uaf_mce_before_init' );
endif;

function uaf_mce_before_init( $init_array ) {
	$theme_advanced_fonts = '';
	$fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$theme_advanced_fonts .= ucfirst(str_replace('_',' ', $fontName)) .'='.$fontName.';';
		endforeach;
	endif;
	
	$init_array['font_formats'] = $theme_advanced_fonts.'Andale Mono=Andale Mono, Times;Arial=Arial, Helvetica, sans-serif;Arial Black=Arial Black, Avant Garde;Book Antiqua=Book Antiqua, Palatino;Comic Sans MS=Comic Sans MS, sans-serif;Courier New=Courier New, Courier;Georgia=Georgia, Palatino;Helvetica=Helvetica;Impact=Impact, Chicago;Symbol=Symbol;Tahoma=Tahoma, Arial, Helvetica, sans-serif;Terminal=Terminal, Monaco;Times New Roman=Times New Roman, Times;Trebuchet MS=Trebuchet MS, Geneva;Verdana=Verdana, Geneva;Webdings=Webdings;Wingdings=Wingdings';
	return $init_array;
}

function wp_editor_fontsize_filter( $options ) {
	array_unshift( $options, 'fontsizeselect');
	array_unshift( $options, 'fontselect');
	return $options;
}

// DIVI CUSTOMIZER AND BUILDER (Tested with 4.0.9 and 4.0.9)
add_filter('et_websafe_fonts', 'uaf_send_fonts_divi_list',10,2);
function uaf_send_fonts_divi_list($fonts){
    $fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[$fontName] = array(
				'styles' 		=> '400',
				'character_set' => 'cyrillic,greek,latin',
				'type'			=> 'serif'
			);	
		endforeach;
	endif;
  	return array_merge($fonts_uaf,$fonts);
}

// SITE ORIGIN BUILDER
add_filter('siteorigin_widgets_font_families', 'uaf_send_fonts_siteorigin_list',10,2);
function uaf_send_fonts_siteorigin_list($fonts){
    $fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[$fontName] = $fontName;
		endforeach;
	endif;
  	return array_merge($fonts_uaf,$fonts);
}

// REDUX Framework
if (class_exists( 'Redux' ) ) {
   	$reduxUafObject = new Redux;	
   	$reduxArgs 		= $reduxUafObject::$args;
   	$reduxOptName	= array_key_first($reduxArgs);
	add_filter('redux/'.$reduxOptName.'/field/typography/custom_fonts', 'uaf_send_fonts_redux_list' );
}

function uaf_send_fonts_redux_list( $custom_fonts ) {
	$fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array('Use Any Fonts' => array());
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf['Use Any Fonts'][$fontName] = $fontName;
		endforeach;
	endif;
  	return $fonts_uaf;
}


// X Theme
add_filter('x_fonts_data', 'uaf_send_fonts_x_theme_list',10,2);
function uaf_send_fonts_x_theme_list($fonts){
    $fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[$fontName] = array(
												'source'  => 'Use Any Font',
												'family'  => $fontName,
												'stack'   => '"'.$fontName.'"',
												'weights' => array( '400' )
												);
		endforeach;
	endif;
  	return array_merge($fonts_uaf,$fonts);
}

// elementor
function uaf_send_fonts_elementor_list( $controls_registry ) {
    $fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array('Use Any Fonts' => array());
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[$fontName] = 'system';
		endforeach;
	endif;
  	
	$fonts = $controls_registry->get_control( 'font' )->get_settings( 'options' ); 
	$new_fonts = array_merge($fonts_uaf, $fonts );
	$controls_registry->get_control( 'font' )->set_settings( 'options', $new_fonts );
}
add_action( 'elementor/controls/controls_registered', 'uaf_send_fonts_elementor_list', 10, 1 );

// Beaver Builder and Themes (Tested with 2.3.0.1 )
add_filter('fl_theme_system_fonts', 'uaf_send_fonts_beaver_builder_list',10,2);
add_filter('fl_builder_font_families_system', 'uaf_send_fonts_beaver_builder_list',10,2);
function uaf_send_fonts_beaver_builder_list($fonts){
    $fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[$fontName] = array(
												'fallback'  => 'Verdana, Arial, sans-serif',
												'weights'  => array('400')
												);
		endforeach;
	endif;
  	return array_merge($fonts_uaf,$fonts);
}

// Themify Builder
add_filter('themify_get_web_safe_font_list', 'uaf_send_fonts_themify_builder_list',10,2);
function uaf_send_fonts_themify_builder_list($fonts){
    $fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[] = array(
				'value' => $fontName,
				'name' => $fontName
			);
		endforeach;
	endif;
  	return array_merge($fonts_uaf,$fonts);
}

// GENERATE PRESS Tested With Version: 2.4.1
add_filter( 'generate_typography_default_fonts', function( $fonts ) {
    $fonts_uaf = uaf_get_font_families();
  	return array_merge($fonts_uaf,$fonts);
});

// ASTRA THEME ver 2.2.1
add_action( 'astra_customizer_font_list', 'uaf_astra_customizer_font_list');
function uaf_astra_customizer_font_list( $value ) {
	$fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		echo '<optgroup label="Use Any Font">';
		foreach ($fontsData as $fontName=>$fontData):
			echo '<option value="' .$fontName. '">' . $fontName. '</option>';
		endforeach;
	endif;
}


// oceanwp Theme 1.7.4
if ( !function_exists( 'ocean_add_custom_fonts' ) ) {
	function ocean_add_custom_fonts() {
		$fonts_uaf = uaf_get_font_families();
		return $fonts_uaf;
	}
}

// Oxygen Builder
add_action("ct_builder_ng_init", "uaf_oxygen_builder_font_list");
function uaf_oxygen_builder_font_list() {
	$fonts_uaf = uaf_get_font_families();
	$output = json_encode( $fonts_uaf );
	$output = htmlspecialchars( $output, ENT_QUOTES );
	echo "elegantCustomFonts=$output;";
}

// KIRKI CUSTOMIZER FRAMEWORK //Like FLATSOME THEME
add_filter( 'kirki/fonts/standard_fonts', 'uaf_kirki_custom_fonts', 20 );
function uaf_kirki_custom_fonts($standard_fonts) {
	$fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[$fontData['font_name']] = array(
				'label' 		=> $fontName.' [Use Any Font]',
				'variants' 		=> array('regular'),
				'stack'			=> $fontName
			);	
		endforeach;
	endif;
	return array_merge_recursive( $fonts_uaf, $standard_fonts );
}

// REVOLUTION SLIDER
add_filter( 'revslider_data_get_font_familys', 'uaf_revslider_custom_fonts', 20 );
function uaf_revslider_custom_fonts($fonts) {
	$fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[] = array(
				'type' => 'Use Any Font',
				'version' => 'Serif Fonts',
				'label' => $fontName
			);
		endforeach;
	endif;
  	return array_merge($fonts_uaf,$fonts);
}

// FOR WP BAKERY VISUAL BUILDER  (JS Composer)
add_filter('vc_google_fonts_get_fonts_filter', 'uaf_wpbakery_custom_fonts');
function uaf_wpbakery_custom_fonts($fonts) {
	$fontsData		= uaf_group_fontdata_by_fontname(uaf_get_uploaded_font_data());
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $fontName=>$fontData):
			$fonts_uaf[] = array(
				'font_family' => $fontName,
				'font_types' => '400 regular:400:normal',
				'font_styles' => 'regular'
			);
		endforeach;
	endif;
	$fonts_uaf = json_decode (json_encode ($fonts_uaf), FALSE);
  	return array_merge($fonts_uaf,$fonts);
}

// FOR THE7 and presscore options framework
add_filter('presscore_options_get_safe_fonts', 'uaf_presscore_options_custom_fonts');
function uaf_presscore_options_custom_fonts($fonts) {
	 $fontsData = uaf_get_font_families();
	 if (!empty($fontsData)):
		foreach ($fontsData as $key=>$fontName):
			$fonts_uaf[$fontName] = $fontName;
		endforeach;
	endif;
  	 return array_merge($fonts_uaf,$fonts);
}