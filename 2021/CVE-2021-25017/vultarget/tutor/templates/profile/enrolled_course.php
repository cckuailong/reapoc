<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

$show_enrolled_course = tutor_utils()->get_option('show_courses_completed_by_student');
if ( ! $show_enrolled_course){
    return;
}

$user_name = sanitize_text_field(get_query_var('tutor_student_username'));
$get_user = tutor_utils()->get_user_by_login($user_name);
$user_id = $get_user->ID;

$my_courses = tutor_utils()->get_enrolled_courses_by_user($user_id);
?>

<div class="tutor-courses ">
	<?php
	if ($my_courses && $my_courses->have_posts()):
		while ($my_courses->have_posts()):
			$my_courses->the_post();
			/**
			 * @hook tutor_course/archive/before_loop_course
			 * @type action
			 * Usage Idea, you may keep a loop within a wrap, such as bootstrap col
			 */
			do_action('tutor_course/archive/before_loop_course');

			tutor_load_template('loop.course');

			/**
			 * @hook tutor_course/archive/after_loop_course
			 * @type action
			 * Usage Idea, If you start any div before course loop, you can end it here, such as </div>
			 */
			do_action('tutor_course/archive/after_loop_course');

		endwhile;
		wp_reset_postdata();
	else : ?>
    <div>
        <p><?php _e("No course yet." , 'tutor'); ?></p>
    </div>
	<?php endif; ?>
</div>

