<?php
/**
 * The template for displaying Tutor Course Widget
 *
 * @package Tutor/Tempaltes
 * @version 1.3.1
 *
 */

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		?>
		<div class="<?php echo tutor_widget_course_loop_classes(); ?>">
			<?php tutor_load_template('loop.course'); ?>
		</div>
	<?php
	endwhile;
endif;
