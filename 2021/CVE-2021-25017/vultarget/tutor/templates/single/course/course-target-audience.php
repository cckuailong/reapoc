<?php
/**
 * Template for displaying course audience
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */


do_action('tutor_course/single/before/audience');

$target_audience = tutor_course_target_audience();

if ( empty($target_audience)){
	return;
}

if (is_array($target_audience) && count($target_audience)){
	?>

	<div class="tutor-single-course-segment  tutor-course-target-audience-wrap">

        <h4 class="tutor-segment-title"><?php _e('Target Audience', 'tutor'); ?></h4>

		<div class="tutor-course-target-audience-content">
			<ul class="tutor-course-target-audience-items tutor-custom-list-style">
				<?php
				foreach ($target_audience as $audience){
					echo "<li>{$audience}</li>";
				}
				?>
			</ul>
		</div>
	</div>

<?php } ?>

<?php do_action('tutor_course/single/after/audience'); ?>

