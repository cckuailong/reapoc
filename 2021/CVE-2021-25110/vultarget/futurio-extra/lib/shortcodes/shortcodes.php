<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
  |----------------------------------------------------------
  | Posts
  |----------------------------------------------------------
 */
add_shortcode( 'futurio-posts', 'futurio_extra_posts_carousel_shortcode' );

function futurio_extra_posts_carousel_shortcode( $atts, $content = null ) {

	STATIC $i = 1;
	extract( shortcode_atts( array(
		'text_color'	 => '', // Theme default
		'columns'		 => '3', // 3* / 2 / 3 / 4 / 5
		'limit'			 => '6', // *6
		'category'		 => '', // Category ID
	), $atts, 'futurio-posts' ) );

	$category = explode( ',', $category );
	if ( empty( $category ) ) {
		return;
	}
  $columns = 12 / $columns;
	// setup query
	$paged		 = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$post_args	 = array(
		'posts_per_page'			 => $limit,
		'cat'					 => $category,
		'ignore_sticky_posts'	 => 1,
		'paged'					 => $paged,
	);
	// query database
	$post		 = new WP_Query( $post_args );
	ob_start();
	?>
	<div id="f-posts-shortcode-<?php echo absint( $i ); ?>" class="f-posts-shortcode" >
		<?php if ( $post->have_posts() ) : ?>
			<div class="row" >
				<?php while ( $post->have_posts() ) : ?>
					<?php $post->the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-md-' . absint( $columns ) ); ?>>
						<div class="news-item text-center">
							<?php futurio_thumb_img( 'futurio-med' ); ?>
							<div class="news-text-wrap">
								<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" style="color: ' . esc_attr( $text_color ) . '">', '</a></h2>' ); ?>
                <div class="f-line"></div>
								<div class="post-excerpt" style="color: <?php echo esc_attr( $text_color ) ?>">
									<?php the_excerpt(); ?>
								</div><!-- .post-excerpt -->
							</div><!-- .news-text-wrap -->

						</div><!-- .news-item -->
					</article>

				<?php endwhile; ?>
			</div><!-- .row -->
		<?php endif; ?>
	</div>				
	<?php
	wp_reset_postdata();
	$i++;
	
	return ob_get_clean();
}
