<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

$GLOBALS['uaf_current_version']				= '6.2';


$GLOBALS['uaf_fix_settings'] 				= array(
													'allowedFontFormats' 	=> array ('ttf','otf','woff'),
													'allowedFontSize'		=> 25, // IN MB
													'serverUrl'				=> array(
																					'default' => 'https://server2.dnesscarkey.org',
																					'alternative' => 'https://server3.dnesscarkey.org'
																				),
													'supported_multi_lang_plugins' => array(
																					'polylang/polylang.php', // POLYLANG
																					'polylang-pro/polylang.php', // POLYLANG PRO
																					'sitepress-multilingual-cms/sitepress.php' //WPML
																						),
													'font_weight_variations'=> array(
																					'100' => 'Thin 100',
																					'200' => 'Extra light 200',
																					'300' => 'Light 300',
																					'400' => 'Regular/Normal 400',
																					'500' => 'Medium 500',
																					'600' => 'Semi Bold 600',
																					'700' => 'Bold 700',
																					'800' => 'Extra Bold 800',
																					'900' => 'Black 900'
																					)

													);

$GLOBALS['uaf_user_settings'] 				= array(															
													'uaf_api_key'					=> '',
													'uaf_server_url_type'			=> 'default',
													'uaf_activated_url'				=> '',
													'uaf_site_url'					=> '',
													'uaf_uploader_type'				=> 'js', // js or php (all small)
													'uaf_font_display_property' 	=> 'auto',
													'uaf_enable_multi_lang_support' => '0',
													'uaf_disbale_editor_font_list'	=> '0',
													'uaf_use_absolute_font_path'	=> '0',
													'uaf_hide_key'					=> 'no'
												);
?>