<?php
/**
 * Custom Facebook Feed Item : Shared Link
 * Displays the item shared link
 *
 * @version 2.19 Custom Facebook Feed by Smash Balloon
 *
 */
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Autolink;
use CustomFacebookFeed\CFF_Shortcode_Display;

$shared_link_class 			= ($cff_disable_link_box) ? ' cff-no-styles' : '';
$cff_link_styles_html 		= $this_class->get_style_attribute( 'shared_cap_link' );
$cff_link_desc_styles_html  = $this_class->get_style_attribute( 'shared_desclink' );
$cff_link_caption 			= CFF_Shortcode_Display::get_shared_link_caption( $news );
$cff_link_title_format 		= CFF_Shortcode_Display::get_shared_link_title_format( $atts );
$cff_link_title_styles 		= CFF_Shortcode_Display::get_shared_link_title_styles( $atts );
$cff_link_title_color 		= str_replace('#', '', $atts[ 'linktitlecolor' ]);


if($cff_post_type == 'link' || $cff_soundcloud || $cff_is_video_embed):
?>

<div class="cff-shared-link <?php echo $shared_link_class ?>" <?php echo $cff_link_box_styles; ?>>
	<div class="cff-text-link cff-no-image">
		<?php if( isset($news->name) ) : ?>
			<<?php echo $cff_link_title_format ?> class="cff-link-title" <?php echo $cff_link_title_styles; ?>>
				<a href="<?php echo esc_url($link) ?>" <?php echo $target.' '.$cff_nofollow_referrer; ?> style="color:#<?php echo $cff_link_title_color; ?>;"><?php echo $news->name; ?></a>
			</<?php echo $cff_link_title_format ?>>
		<?php endif; ?>

		<?php if( !empty( $cff_link_caption ) ) : ?>
			<p class="cff-link-caption" <?php echo $cff_link_styles_html ?>><?php echo $cff_link_caption ?></p>
		<?php endif; ?>

		<?php
			//Description Text
			if( $cff_show_desc ) :
				$description_text 		= CFF_Shortcode_Display::get_shared_link_description_text( $body_limit, $description_text, $cff_title_link, $cff_posttext_link_color );
				$cff_link_description 	= CFF_Shortcode_Display::get_shared_link_description( $cff_title_link, $description_text );
				if( $description_text != $cff_link_caption ):
		?>
			<span class="cff-post-desc" <?php echo $cff_link_desc_styles_html ?>><?php echo $cff_link_description; ?></span>
		<?php
				endif;
			endif;
		?>

	</div>
</div>

<?php endif; ?>