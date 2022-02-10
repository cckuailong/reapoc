<?php
/**
 * Custom Facebook Feed Like Box Template
 * Display the Facebook page likebox
 *
 * @version 2.19 Custom Facebook Feed by Smash Balloon
 *
 */

use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Shortcode_Display;
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if($cff_is_group) return '';


$cff_like_box_faces 		= CFF_Utils::check_if_on( $atts[ 'likeboxfaces' ] );
$cff_like_box_border 		= CFF_Utils::check_if_on( $atts[ 'likeboxborder' ] );
$cff_like_box_cover 		= !CFF_Utils::check_if_on( $atts[ 'likeboxcover' ] );
$cff_like_box_small_header 	= CFF_Utils::check_if_on( $atts[ 'likeboxsmallheader' ] );
$cff_like_box_hide_cta 		= CFF_Utils::check_if_on( $atts[ 'likeboxhidebtn' ] );
$cff_likebox_width 			= CFF_Shortcode_Display::get_likebox_width( $atts );
$cff_likebox_height 		= CFF_Shortcode_Display::get_likebox_height( $atts, $cff_like_box_small_header, $cff_like_box_faces );
$lkbx_class 				= CFF_Shortcode_Display::get_likebox_classes( $atts );
$lkbx_tag 					= CFF_Shortcode_Display::get_likebox_tag( $atts );
$like_box_page_id 			= explode(",", str_replace(' ', '', $page_id) );

?>

<<?php echo $lkbx_tag . ' class="'. $lkbx_class  .'" '?>>
	<?php CFF_Shortcode_Display::print_gdpr_notice('Like Box'); ?>
	<iframe src="" class="fb_iframe_widget" data-likebox-id="<?php echo esc_attr($like_box_page_id[0]); ?>" data-likebox-width="<?php echo esc_attr($cff_likebox_width); ?>" data-likebox-header="<?php echo esc_attr($cff_like_box_small_header); ?>" data-hide-cover="<?php echo esc_attr($cff_like_box_cover); ?>" data-hide-cta="<?php echo esc_attr($cff_like_box_hide_cta); ?>" data-likebox-faces="<?php echo esc_attr($cff_like_box_faces); ?>" data-height="<?php echo esc_attr($cff_likebox_height); ?>" data-locale="<?php echo esc_attr($cff_locale); ?>" scrolling="no" allowTransparency="true" allow="encrypted-media" ></iframe>
</<?php echo $lkbx_tag ?>>