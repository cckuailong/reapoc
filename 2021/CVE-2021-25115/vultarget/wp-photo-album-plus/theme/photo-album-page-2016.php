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
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly (2016)" );
 
get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
<?php /* if ( function_exists( 'wppa_statistics' ) ) wppa_statistics(); */ /* This would show the statistics at the to of the page */?>	
<?php $wppa_show_statistics = true; /* This will show the statistics within the wppa-container */?>
		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();

			// Include the page content template.
			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

			// End of the loop.
		endwhile;
		?>

		<style type="text/css">
			.content-area {
				width:100% !important;
			}
			.entry-content {
				margin-left:auto !important;
				margin-right:auto !important;
				max-width:90% !important;
			}
		</style>
		<script type="text/javascript">
			/* <![CDATA[	*/
			jQuery(document).ready(function(){
				jQuery('#wppa-container-1').css('background-color', 'black');
				jQuery('#wppa-container-1').css('padding', '80px');
				jQuery('#wppa-container-1').css('margin-left', '-80px');
			});
			/* ]]> */
		</script>
		
	</main><!-- .site-main -->

	<?php get_sidebar( 'content-bottom' ); ?>

</div><!-- .content-area -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
