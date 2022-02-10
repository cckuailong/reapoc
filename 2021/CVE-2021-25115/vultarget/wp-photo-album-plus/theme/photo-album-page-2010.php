<?php
/**
 * Template Name: Photo Album page
 *
 * A custom page template without sidebar.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
 
if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly (2010)" );
 
global $wppa_show_statistics;

get_header(); ?>

		<div id="container" class="one-column">
			<div id="content" role="main">
			
<?php /* wppa_statistics(); */ /* This would show the statistics at the to of the page */?>	
<?php $wppa_show_statistics = true; /* This will show the statistics within the wppa-container */?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-content -->
				</div><!-- #post-## -->
				<?php /*do_action( 'addthis_widget' ); */?>
				<?php comments_template( '', true ); ?>

<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #container -->
		<script type="text/javascript">
		/* <![CDATA[	*/
		jQuery(document).ready(function(){
			jQuery('#wppa-container-1').css('background-color', 'black');
			jQuery('#wppa-container-1').css('padding', '80px');
			jQuery('#wppa-container-1').css('margin-left', '-80px');
//			jQuery('.wppa-fulldesc').css('color', '#eef7e6');
//			jQuery('.wppa-fulltitle').css('color', '#eef7e6');
//	jQuery('.wppa-nav').css('background-color', '#ccc');		
	});
	/* ]]> */
		</script>

<?php get_footer(); ?>
