<?php
/**
 * Custom Facebook Feed Item : Author Template
 * Displays the item author
 *
 * @version 2.19 Custom Facebook Feed by Smash Balloon
 *
 */
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
use CustomFacebookFeed\CFF_Shortcode_Display;

//Author Style

$cff_author_styles = $this_class->get_style_attribute( 'author' );
$cff_from_id 	= isset($news->from->id) ? $news->from->id : '';
if( isset($cff_from_id) ) :

	$cff_author_name 		= CFF_Shortcode_Display::get_author_name( $news );
	$cff_author_link_atts 	= CFF_Shortcode_Display::get_author_link_atts( $news, $target, $cff_nofollow, $cff_author_styles  );
	$cff_author_link_el 	= CFF_Shortcode_Display::get_author_link_el( $news );
	$post_text_story 		= CFF_Shortcode_Display::get_author_post_text_story( $post_text_story, $cff_author_name  );
	$author_src_class 		= CFF_Shortcode_Display::get_author_pic_src_class( $news, $atts );
	$cff_author_img_src 	= $author_src_class[ 'image' ];
	$cff_author_src 		= $author_src_class[ 'real_image' ];
	$cff_author_img_class 	= $author_src_class[ 'class' ];


?>
<div class="cff-author">
	<div class="cff-author-text">
		<?php if($cff_show_date && $cff_date_position !== 'above' && $cff_date_position !== 'below'): ?>
			<div class="cff-page-name cff-author-date" <?php echo $cff_author_styles ?>>
				<<?php echo $cff_author_link_el . $cff_author_link_atts ?>><?php echo $cff_author_name ?></<?php echo $cff_author_link_el ?>>
				<span class="cff-story"> <?php echo $post_text_story ?></span>
			</div>
			<?php echo $cff_date ?>
		<?php else: ?>
			<span class="cff-page-name" <?php echo $cff_author_styles ?>>
				<<?php echo $cff_author_link_el . $cff_author_link_atts ?>><?php echo $cff_author_name ?></<?php echo $cff_author_link_el ?>>
				<span class="cff-story"> <?php echo $post_text_story ?></span>
			</span>
		<?php endif; ?>
	</div>
	<div class="cff-author-img <?php echo $cff_author_img_class; ?>" data-avatar="<?php echo esc_url( $cff_author_src ) ?>">
		<<?php echo $cff_author_link_el . $cff_author_link_atts ?>><img src="<?php echo esc_url($cff_author_img_src) ?>" title="<?php echo esc_attr($cff_author_name) ?>" alt="<?php echo esc_attr($cff_author_name) ?>" width=40 height=40 onerror="this.style.display='none'"></<?php echo $cff_author_link_el ?>>
	</div>
</div>
<?php else: ?>
	<div class="cff-author cff-no-author-info">
		<div class="cff-author-text">
			<?php if($cff_show_date && $cff_date_position !== 'above' && $cff_date_position !== 'below'): ?>
				<?php if(!empty($post_text_story)):  ?>
					<div class="cff-page-name cff-author-date"><span class="cff-story"> <?php echo $post_text_story ?></span></div>
					<?php echo $cff_date ?>
				<?php endif; ?>
			<?php else: ?>
				<?php if(!empty($post_text_story)):  ?>
					<span class="cff-page-name"><span class="cff-story"> <?php echo $post_text_story ?></span></span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="cff-author-img"></div>
	</div>
<?php
endif;