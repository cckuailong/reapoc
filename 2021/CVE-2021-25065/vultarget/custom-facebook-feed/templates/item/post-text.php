<?php
/**
 * Custom Facebook Feed Item : Post Text Template
 * Displays the custom feed item post text
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

$cff_title_styles = \CustomFacebookFeed\CFF_Parse::get_status_type( $news ) !== 'created_event' ? $this_class->get_style_attribute( 'post_text' ) : $this_class->get_style_attribute( 'event_title' );
$cff_title_format = CFF_Shortcode_Display::get_post_text_title_format( $atts );

if( !empty($post_text) ):
?>

<<?php echo $cff_title_format ?> class="cff-post-text" <?php echo $cff_title_styles; ?>>
	<span class="cff-text" data-color="<?php echo esc_attr($cff_posttext_link_color	) ?>">
		<?php
			if( $cff_title_link ):
			$text_link = CFF_Shortcode_Display::get_post_text_link( $cff_post_type, $this_class, $link, $PostID );
		?>
			<a class="cff-post-text-link" href="<?php echo esc_url($text_link); ?>" <?php echo $cff_title_styles . ' ' . $target  . ' ' . $cff_nofollow ;?> >
				<?php
					endif;
					echo CFF_Shortcode_Display::get_post_text_contenttext( $post_text, $cff_linebreak_el, $cff_title_link );
				?>
		<?php if( $cff_title_link ):  ?>
			</a>
		<?php endif;  ?>
	</span>
	<span class="cff-expand">... <a href="#" style="color: #<?php echo $cff_posttext_link_color; ?>"><span class="cff-more"><?php echo $atts[ 'seemoretext' ]; ?></span><span class="cff-less"><?php echo $atts[ 'seelesstext' ];  ?></span></a></span>

</<?php echo $cff_title_format ?>>

<?php endif;

CFF_Shortcode_Display::get_post_text_call_to_actions( $atts, $news, $cff_title_styles, $cff_posttext_link_color, $cff_nofollow_referrer);