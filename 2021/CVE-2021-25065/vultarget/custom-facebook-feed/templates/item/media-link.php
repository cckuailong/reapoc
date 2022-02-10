<?php
/**
 * Custom Facebook Feed Item : Media Link
 * Displays the custom feed item media link
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


if( $cff_show_media_link && ($cff_post_type == 'photo' || $cff_post_type == 'video' || $cff_album) ):

	$media_link_txt = CFF_Shortcode_Display::get_media_link_text( $atts, $cff_post_type, $cff_album );
	$media_link_icon = CFF_Shortcode_Display::get_media_link_icon( $cff_post_type, $cff_album );

?>
<p class="cff-media-link">
	<a href="<?php echo esc_url($link) ?>" <?php echo $target; ?> style="color: #<?php echo $cff_posttext_link_color ?>">
		<span style="padding-right: 5px;" class="fa fas fa-<?php echo $media_link_icon ?>"></span><?php echo $media_link_txt ?>
	</a>
</p>
<?php
endif;
